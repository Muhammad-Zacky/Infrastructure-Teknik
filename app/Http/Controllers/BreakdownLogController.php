<?php

namespace App\Http\Controllers;

use App\Models\BreakdownLog;
use App\Models\Infrastructure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BreakdownLogController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // JIKA YANG LOGIN ADALAH SUPERADMIN (TAMPILAN RIWAYAT LOG GLOBAL)
        if ($user->role === 'superadmin') {
            $logs = BreakdownLog::with(['infrastructure.entity'])->latest()->get();
            return view('admin.breakdowns.index_admin', compact('logs'));
        } 
        
        // JIKA YANG LOGIN ADALAH OPERATOR (TAMPILAN EXCEL KESIAPAN ALAT)
        else {
            $infrastructures = Infrastructure::with('entity')
                ->where('entity_id', $user->entity_id)
                ->get();

            // Ambil log yang belum 'resolved' (selesai) untuk cabang tersebut
            $activeBreakdowns = BreakdownLog::where('repair_status', '!=', 'resolved')
                ->whereHas('infrastructure', function($q) use ($user) {
                    $q->where('entity_id', $user->entity_id);
                })
                ->get()
                ->keyBy('infrastructure_id');

            return view('admin.breakdowns.index_operator', compact('infrastructures', 'activeBreakdowns'));
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'infrastructure_id' => 'required|exists:infrastructures,id',
            'issue_detail' => 'required|string',
            'vendor_pic' => 'required|string',
        ]);

        // 1. Catat ke Log Kerusakan (Tahap awal: troubleshooting)
        BreakdownLog::create([
            'infrastructure_id' => $request->infrastructure_id,
            'issue_detail' => $request->issue_detail,
            'repair_status' => 'troubleshooting',
            'vendor_pic' => $request->vendor_pic,
        ]);

        // 2. Ubah status alat menjadi breakdown
        Infrastructure::where('id', $request->infrastructure_id)->update(['status' => 'breakdown']);

        return redirect()->back()->with('success', 'Laporan kerusakan berhasil dicatat. Status alat kini Breakdown.');
    }

    public function update(Request $request, $id)
    {
        $log = BreakdownLog::findOrFail($id);
        
        // 1. Validasi Inputan (Status, File Bukti Opsional, dan Tanggal-tanggal)
        $request->validate([
            'repair_status'     => 'required|string',
            'document_proof'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // Maks 5MB
            'troubleshoot_date' => 'nullable|date',
            'ba_date'           => 'nullable|date',
            'work_order_date'   => 'nullable|date',
            'pr_po_date'        => 'nullable|date',
            'sparepart_date'    => 'nullable|date',
            'start_work_date'   => 'nullable|date',
            'com_test_date'     => 'nullable|date',
            'resolved_date'     => 'nullable|date',
        ]);

        // 2. Ambil semua request data kecuali token form
        $dataToUpdate = $request->except(['_token', '_method']);

        // 3. Logika Upload Bukti Fisik
        if ($request->hasFile('document_proof')) {
            // Hapus file lama dari storage jika sebelumnya sudah pernah upload
            if ($log->document_proof) {
                Storage::disk('public')->delete($log->document_proof);
            }
            // Simpan file baru ke folder public/assets/proofs
            $dataToUpdate['document_proof'] = $request->file('document_proof')->store('assets/proofs', 'public');
        }

        // 4. Update data ke database (Status dan Deretan Tanggal)
        $log->update($dataToUpdate);

        // 5. Jika status perbaikan "resolved", kembalikan status alat jadi "available"
        if ($request->repair_status === 'resolved') {
            $log->infrastructure->update(['status' => 'available']);
            return redirect()->back()->with('success', 'Pekerjaan selesai! Alat telah kembali beroperasi (Ready).');
        } else {
            $log->infrastructure->update(['status' => 'breakdown']);
        }

        return redirect()->back()->with('success', 'Status progres & data tanggal berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $log = BreakdownLog::findOrFail($id);
        
        // Bersihkan file bukti di storage agar tidak memenuhi harddisk server
        if ($log->document_proof) {
            Storage::disk('public')->delete($log->document_proof);
        }

        // Kembalikan status alat jadi available sebelum log dihapus
        $log->infrastructure->update(['status' => 'available']);
        
        // Hapus laporan
        $log->delete();

        return redirect()->back()->with('success', 'Laporan beserta lampirannya berhasil dihapus dan status alat dikembalikan ke Ready.');
    }
}
