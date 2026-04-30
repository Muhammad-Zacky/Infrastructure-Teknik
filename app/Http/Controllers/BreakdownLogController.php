<?php

namespace App\Http\Controllers;

use App\Models\BreakdownLog;
use App\Models\Infrastructure;
use App\Http\Requests\StoreBreakdownLogRequest;
use App\Http\Requests\UpdateBreakdownLogRequest;
use App\Helpers\ResponseMessage;
use Illuminate\Support\Facades\Storage;

class BreakdownLogController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // JIKA YANG LOGIN ADALAH SUPERADMIN (TAMPILAN RIWAYAT LOG GLOBAL)
        if ($user->role === 'superadmin') {
            // MAJOR FIX: Add pagination (20 items per page for admin view) and handle soft-deleted infrastructures
            $logs = BreakdownLog::with(['infrastructure' => fn($q) => $q->withTrashed()->with('entity')])->latest()->paginate(20);

            // For the export report
            $allInfrastructures = Infrastructure::with('entity')->get();
            $recentBreakdowns = BreakdownLog::with(['infrastructure' => fn($q) => $q->withTrashed()->with('entity')])
                ->where('repair_status', '!=', 'resolved')
                ->latest()
                ->get();

            return view('admin.breakdowns.index_admin', compact('logs', 'allInfrastructures', 'recentBreakdowns'));
        }

        // JIKA YANG LOGIN ADALAH OPERATOR (TAMPILAN EXCEL KESIAPAN ALAT)
        else {
            // MAJOR FIX: Add pagination for operator view (15 items per page)
            $infrastructures = Infrastructure::with('entity')
                ->where('entity_id', $user->entity_id)
                ->latest()
                ->paginate(15);

            // Ambil log yang belum 'resolved' (selesai) untuk cabang tersebut
            $activeBreakdowns = BreakdownLog::where('repair_status', '!=', 'resolved')
                ->whereHas('infrastructure', function($q) use ($user) {
                    $q->where('entity_id', $user->entity_id);
                })
                ->get()
                ->keyBy('infrastructure_id');

            // For the export report
            $allInfrastructures = Infrastructure::with('entity')
                ->where('entity_id', $user->entity_id)
                ->get();
            $recentBreakdowns = BreakdownLog::with(['infrastructure' => fn($q) => $q->withTrashed()->with('entity')])
                ->where('repair_status', '!=', 'resolved')
                ->whereHas('infrastructure', function($q) use ($user) {
                    $q->where('entity_id', $user->entity_id);
                })
                ->latest()
                ->get();

            return view('admin.breakdowns.index_operator', compact('infrastructures', 'activeBreakdowns', 'allInfrastructures', 'recentBreakdowns'));
        }
    }

    /**
     * MENAMPILKAN FORMULIR PELAPORAN INSIDEN (FUNGSI BARU)
     */
    public function create()
    {
        $user = auth()->user();

        // Ambil infrastruktur yang statusnya masih 'available'
        // Jika Superadmin, tampilkan semua. Jika bukan, tampilkan yang di entitasnya saja.
        if ($user->role === 'superadmin') {
            $infrastructures = Infrastructure::with('entity')->where('status', 'available')->get();
        } else {
            $infrastructures = Infrastructure::with('entity')
                ->where('entity_id', $user->entity_id)
                ->where('status', 'available')
                ->get();
        }

        return view('admin.breakdowns.create', compact('infrastructures'));
    }

    public function store(StoreBreakdownLogRequest $request)
    {
        $user = auth()->user();

        // Get the infrastructure and validate it exists
        $infrastructure = Infrastructure::findOrFail($request->infrastructure_id);

        // Authorization check: Operator cannot create breakdown for other entity's infrastructure
        if ($user->role !== 'superadmin' && $infrastructure->entity_id !== $user->entity_id) {
            abort(403, 'Unauthorized: Anda tidak bisa membuat laporan untuk aset di cabang lain.');
        }

        // Prevent duplicate active tickets
        $activeTicketExists = BreakdownLog::where('infrastructure_id', $infrastructure->id)
            ->where('repair_status', '!=', 'resolved')
            ->exists();

        if ($activeTicketExists) {
            return redirect()->back()->withErrors(['infrastructure_id' => 'Aset ini sedang dalam status Breakdown dan memiliki laporan perbaikan yang belum selesai.'])->withInput();
        }

        // 1. Catat ke Log Kerusakan (Tahap awal: reported - FIXED dari 'troubleshooting')
        BreakdownLog::create([
            'infrastructure_id' => $request->infrastructure_id,
            'issue_detail' => $request->issue_detail,
            'repair_status' => 'reported',
            'vendor_pic' => $request->vendor_pic,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        // 2. Ubah status alat menjadi breakdown
        Infrastructure::where('id', $request->infrastructure_id)->update(['status' => 'breakdown']);

        return redirect()->back()->with('success', ResponseMessage::BREAKDOWN_CREATED);
    }

    public function update(UpdateBreakdownLogRequest $request, $id)
    {
        $user = auth()->user();
        $log = BreakdownLog::findOrFail($id);

        // Authorization check: Operator cannot update breakdown log for other entity
        if ($user->role !== 'superadmin' && $log->infrastructure->entity_id !== $user->entity_id) {
            abort(403, 'Unauthorized: Anda tidak bisa mengupdate laporan dari cabang lain.');
        }

        // 2. Ambil semua request data kecuali token form
        $dataToUpdate = $request->except(['_token', '_method']);

        // Add audit trail
        $dataToUpdate['updated_by'] = $user->id;

        // Workflow Validation: Strict sequential progression
        $statusOrder = ['reported' => 1, 'order_part' => 2, 'on_progress' => 3, 'resolved' => 4];

        if (isset($request->repair_status)) {
            $currentStatus = $log->repair_status;
            $newStatus = $request->repair_status;

            // Prevent skipping steps forward
            if ($statusOrder[$newStatus] > $statusOrder[$currentStatus] + 1) {
                return redirect()->back()->withErrors(['repair_status' => "Lompatan status tidak diizinkan! Harap ikuti alur: Reported -> Order Part -> On Progress -> Resolved."])->withInput();
            }
        }

        // 3. Logika Upload Bukti Fisik
        if ($request->hasFile('document_proof')) {
            $file = $request->file('document_proof');
            $filename = 'proof_' . $log->infrastructure->code_name . '_' . time() . '.' . $file->getClientOriginalExtension();
            // Simpan file baru ke folder public/assets/proofs
            $dataToUpdate['document_proof'] = $file->storeAs('assets/proofs', $filename, 'public');
        }

        if ($request->repair_status === 'resolved') {
            $dataToUpdate['resolved_at'] = now();
        }

        // 4. Update data ke database (Status dan Deretan Tanggal)
        $log->update($dataToUpdate);

        // 5. Jika status perbaikan "resolved", kembalikan status alat jadi "available"
        if ($request->repair_status === 'resolved') {
            $log->infrastructure->update(['status' => 'available']);
            return redirect()->back()->with('success', ResponseMessage::BREAKDOWN_RESOLVED);
        } else {
            $log->infrastructure->update(['status' => 'breakdown']);
        }

        return redirect()->back()->with('success', ResponseMessage::BREAKDOWN_UPDATED);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $log = BreakdownLog::findOrFail($id);

        // Authorization check: Operator cannot delete breakdown log for other entity
        if ($user->role !== 'superadmin' && $log->infrastructure->entity_id !== $user->entity_id) {
            abort(403, 'Unauthorized: Anda tidak bisa menghapus laporan dari cabang lain.');
        }

        // (Soft Deletes: File bukti fisik tidak dihapus dari storage agar tetap tersedia jika data di-restore)

        // CRITICAL FIX: Check if there are other unresolved breakdowns for this infrastructure
        // Only mark as available if no other active breakdowns exist
        $otherActiveBreakdowns = BreakdownLog::where('infrastructure_id', $log->infrastructure_id)
            ->where('id', '!=', $log->id)
            ->where('repair_status', '!=', 'resolved')
            ->count();

        if ($otherActiveBreakdowns === 0) {
            $log->infrastructure->update(['status' => 'available']);
        }

        // Hapus laporan
        $log->delete();

        return redirect()->back()->with('success', ResponseMessage::BREAKDOWN_DELETED);
    }
}
