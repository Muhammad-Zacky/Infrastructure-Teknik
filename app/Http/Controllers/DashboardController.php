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

        // PERBAIKAN: Mengarahkan ke folder admin/dashboard.blade.php
        return view('admin.dashboard', compact('stats', 'infrastructures', 'allInfrastructures', 'recentLogs', 'allActiveBreakdowns', 'areaName'));
    }
}
