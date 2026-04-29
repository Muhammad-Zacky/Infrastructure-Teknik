<?php

namespace App\Http\Controllers;

use App\Models\Infrastructure;
use App\Models\BreakdownLog;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function process(Request $request)
    {
        $user = auth()->user();
        $format = $request->input('format', 'pdf');
        
        $infraQuery = Infrastructure::with('entity');
        $logQuery = BreakdownLog::with(['infrastructure' => fn($q) => $q->withTrashed()->with('entity')]);

        // 1. Role Filtering
        if ($user->role !== 'superadmin') {
            $infraQuery->where('entity_id', $user->entity_id);
            $logQuery->whereHas('infrastructure', function ($q) use ($user) {
                $q->where('entity_id', $user->entity_id);
            });
        } elseif ($request->filled('entity_id')) {
            $infraQuery->where('entity_id', $request->entity_id);
            $logQuery->whereHas('infrastructure', function ($q) use ($request) {
                $q->where('entity_id', $request->entity_id);
            });
        }

        // 2. Date Range Filtering (for Logs)
        if ($request->filled('start_date')) {
            $logQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $logQuery->whereDate('created_at', '<=', $request->end_date);
        }

        // 3. Category Filtering
        if ($request->filled('category')) {
            $infraQuery->where('category', $request->category);
            $logQuery->whereHas('infrastructure', function ($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        // 4. Status Filtering
        if ($request->filled('status')) {
            $infraQuery->where('status', $request->status);
            
            if ($request->status == 'available') {
                $logQuery->where('repair_status', 'resolved');
            } elseif ($request->status == 'breakdown') {
                $logQuery->where('repair_status', '!=', 'resolved');
            }
        } else {
            // Default behavior if status not specified and no date range: only show active breakdowns
            if (!$request->filled('start_date') && !$request->filled('end_date')) {
                $logQuery->where('repair_status', '!=', 'resolved');
            }
        }

        $allInfrastructures = $infraQuery->get();
        $allActiveBreakdowns = $logQuery->latest()->get();

        return view('admin.export.render', compact('allInfrastructures', 'allActiveBreakdowns', 'format'));
    }
}
