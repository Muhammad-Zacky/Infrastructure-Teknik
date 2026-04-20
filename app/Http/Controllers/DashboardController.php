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
        $logQuery = BreakdownLog::with('infrastructure.entity')->where('repair_status', '!=', 'resolved');

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

        // Hitung Statistik
        $stats = [
            'total' => (clone $infraQuery)->count(),
            'available' => (clone $infraQuery)->where('status', 'available')->count(),
            'breakdown' => (clone $infraQuery)->where('status', 'breakdown')->count(),
        ];

        // Ambil Data Infrastruktur untuk ditampilkan di Dashboard
        $infrastructures = $infraQuery->latest()->get();

        // Ambil 5 Log Kerusakan Terbaru di areanya
        $recentBreakdowns = $logQuery->latest()->take(5)->get();

        // PERBAIKAN: Mengarahkan ke folder admin/dashboard.blade.php
        return view('admin.dashboard', compact('stats', 'infrastructures', 'recentBreakdowns', 'areaName'));
    }
}
