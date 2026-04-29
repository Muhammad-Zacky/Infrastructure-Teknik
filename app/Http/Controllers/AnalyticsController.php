<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Infrastructure;
use App\Models\Entity;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
public function index()
{
    $user = auth()->user();
    $infrastructuresQuery = Infrastructure::with(['entity', 'breakdownLogs' => fn($q) => $q->orderBy('created_at', 'desc')]);

    // 1. Logika Tren Laporan (30 Hari Terakhir)
    $trendData = \App\Models\BreakdownLog::selectRaw('DATE(created_at) as date, COUNT(*) as count')
        ->where('created_at', '>=', now()->subDays(30))
        ->when($user->role !== 'superadmin', fn($q) => $q->whereHas('infrastructure', fn($i) => $i->where('entity_id', $user->entity_id)))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    $trendLabels = $trendData->pluck('date')->map(fn($d) => date('d M', strtotime($d)));
    $trendCounts = $trendData->pluck('count');

    // 2. Logika Chart Batang & Pie (Role Based) - MAJOR FIX: Optimize N+1 queries
    if ($user->role === 'superadmin') {
        // Load all entities with their infrastructure counts in one query
        $entities = Entity::with('infrastructures')->get();
        $labels = [];
        $ready = [];
        $breakdown = [];

        foreach ($entities as $e) {
            $labels[] = $e->name;
            $ready[] = $e->infrastructures->where('status', 'available')->count();
            $breakdown[] = $e->infrastructures->where('status', 'breakdown')->count();
        }
        $infrastructures = $infrastructuresQuery->get();
    } else {
        // For operators, use direct aggregation
        $labels = ['Peralatan', 'Fasilitas', 'Utilitas'];
        $cats = ['equipment', 'facility', 'utility'];
        $ready = [];
        $breakdown = [];

        foreach ($cats as $c) {
            $ready[] = Infrastructure::where('entity_id', $user->entity_id)->where('category', $c)->where('status', 'available')->count();
            $breakdown[] = Infrastructure::where('entity_id', $user->entity_id)->where('category', $c)->where('status', 'breakdown')->count();
        }
        $infrastructures = $infrastructuresQuery->where('entity_id', $user->entity_id)->get();
    }

    $chartData = [
        'labels' => $labels,
        'ready' => $ready,
        'breakdown' => $breakdown,
        'trendLabels' => $trendLabels,
        'trendCounts' => $trendCounts,
        'entity_name' => $user->entity->name ?? 'Regional 2'
    ];

    return view('admin.analytics.index', compact('chartData', 'user', 'infrastructures'));
}
}
