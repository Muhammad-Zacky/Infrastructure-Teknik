<?php

namespace App\Http\Controllers;

use App\Models\Infrastructure;
use App\Models\BreakdownLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Siapkan Query Dasar
        $infraQuery = Infrastructure::with('entity');
        $logQuery = BreakdownLog::with(['infrastructure' => fn($q) => $q->withTrashed()->with('entity')])->where('repair_status', '!=', 'resolved');

        // Filter berdasarkan Role
        if ($user->role === 'superadmin') {
            $areaName = 'Pusat (Seluruh Regional)';
        } else {
            // Filter HANYA area operator yang login
            $infraQuery->where('entity_id', $user->entity_id);
            $logQuery->whereHas('infrastructure', function ($q) use ($user) {
                $q->where('entity_id', $user->entity_id);
            });
            $areaName = $user->entity->name ?? 'Area Tidak Diketahui';
        }

        // MAJOR FIX: Optimize statistics with single query using withCount + raw query
        $allInfrastructures = $infraQuery->get();
        $stats = [
            'total' => $allInfrastructures->count(),
            'available' => $allInfrastructures->where('status', 'available')->count(),
            'breakdown' => $allInfrastructures->where('status', 'breakdown')->count(),
        ];

        // Ambil Data Infrastruktur untuk ditampilkan di Dashboard (max 10)
        $infrastructures = $infraQuery->latest()->limit(10)->get();

        // Ambil 5 Log Kerusakan Terbaru di areanya untuk UI Dashboard
        $recentLogs = (clone $logQuery)->latest()->take(5)->get();
        
        // Ambil SEMUA Log Kerusakan yang belum resolved untuk Laporan PDF/Excel
        $allActiveBreakdowns = $logQuery->latest()->get();

        // -------------------------------------------------------------
        // ANALYTICS DATA MERGE
        // -------------------------------------------------------------
        
        // 1. Logika Tren Laporan (30 Hari Terakhir)
        $trendData = BreakdownLog::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->when($user->role !== 'superadmin', fn($q) => $q->whereHas('infrastructure', fn($i) => $i->where('entity_id', $user->entity_id)))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $trendLabels = $trendData->pluck('date')->map(fn($d) => date('d M', strtotime($d)));
        $trendCounts = $trendData->pluck('count');

        // 2. Logika Chart Distribusi
        if ($user->role === 'superadmin') {
            $entities = \App\Models\Entity::with('infrastructures')->get();
            $labels = [];
            $ready = [];
            $breakdown = [];

            foreach ($entities as $e) {
                $labels[] = $e->name;
                $ready[] = $e->infrastructures->where('status', 'available')->count();
                $breakdown[] = $e->infrastructures->where('status', 'breakdown')->count();
            }
        } else {
            $labels = ['Peralatan', 'Fasilitas', 'Utilitas'];
            $cats = ['equipment', 'facility', 'utility'];
            $ready = [];
            $breakdown = [];

            foreach ($cats as $c) {
                $ready[] = $allInfrastructures->where('category', $c)->where('status', 'available')->count();
                $breakdown[] = $allInfrastructures->where('category', $c)->where('status', 'breakdown')->count();
            }
        }

        $chartData = [
            'labels' => $labels,
            'ready' => $ready,
            'breakdown' => $breakdown,
            'trendLabels' => $trendLabels,
            'trendCounts' => $trendCounts,
            'entity_name' => $areaName,
        ];

        // PERBAIKAN: Mengarahkan ke folder admin/dashboard.blade.php
        return view('admin.dashboard', compact('stats', 'infrastructures', 'allInfrastructures', 'recentLogs', 'allActiveBreakdowns', 'areaName', 'chartData'));
    }
}
