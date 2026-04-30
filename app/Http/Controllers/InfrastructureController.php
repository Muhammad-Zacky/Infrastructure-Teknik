<?php

namespace App\Http\Controllers;

use App\Models\Infrastructure;
use App\Models\Entity;
use App\Http\Requests\StoreInfrastructureRequest;
use App\Http\Requests\UpdateInfrastructureRequest;
use App\Helpers\ResponseMessage;
use Illuminate\Support\Facades\Storage;

class InfrastructureController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Tampilkan semua untuk superadmin, filter berdasarkan cabang untuk operator
        if ($user->role === 'superadmin') {
            $infrastructures = Infrastructure::with('entity')->latest()->get();
        } else {
            $infrastructures = Infrastructure::with('entity')
                ->where('entity_id', $user->entity_id)
                ->latest()
                ->get();
        }

        return view('admin.infrastructures.index', compact('infrastructures'));
    }

    public function create()
    {
        $user = auth()->user();

        // JIKA SUPERADMIN: Bisa pilih semua cabang. JIKA OPERATOR: Hanya cabang dia sendiri.
        if ($user->role === 'superadmin') {
            $entities = Entity::all();
        } else {
            $entities = Entity::where('id', $user->entity_id)->get();
        }

        // Mengambil daftar tipe alat unik berdasarkan kategori untuk dropdown
        $typeCategoryMap = Infrastructure::select('type', 'category')
                            ->distinct()
                            ->pluck('category', 'type')
                            ->toArray();

        return view('admin.infrastructures.create', compact('entities', 'typeCategoryMap'));
    }

    public function store(StoreInfrastructureRequest $request)
    {
        $user = auth()->user();

        $validated = $request->validated();

        // 2. Proteksi Entity ID dengan validation (CRITICAL FIX)
        // Jika superadmin, entity_id wajib dari form. Jika operator, entity_id dipaksa dari sistem (keamanan).
        if ($user->role === 'superadmin') {
            $targetEntityId = $request->entity_id;
        } else {
            $targetEntityId = $user->entity_id;
        }

        // 3. Logika Tipe Alat (Dropdown vs Input Baru)
        $finalType = $request->type_select === 'new' ? $request->type_new : $request->type_select;

        if (empty($finalType)) {
            return back()->withErrors(['type_new' => 'Jenis alat wajib diisi!'])->withInput();
        }

        // 4. Proses Upload Gambar
        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'infra_' . preg_replace('/[^A-Za-z0-9\-]/', '', $validated['code_name']) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $imagePath = $file->storeAs('assets/infrastructures', $filename, 'public');
        }

        // 5. Simpan ke Database dengan audit trail
        Infrastructure::create([
            'entity_id' => $targetEntityId,
            'category'  => $validated['category'],
            'type'      => $finalType,
            'code_name' => $validated['code_name'],
            'status'    => $validated['status'],
            'quantity'  => 1,
            'image'     => $imagePath,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        return redirect()->route('admin.infrastructures.index')
            ->with('success', ResponseMessage::INFRASTRUCTURE_CREATED);
    }

    public function edit(Infrastructure $infrastructure)
    {
        $user = auth()->user();

        // Proteksi: Operator tidak boleh edit alat dari cabang lain
        if ($user->role !== 'superadmin' && $infrastructure->entity_id !== $user->entity_id) {
            return redirect()->route('admin.infrastructures.index')->with('error', ResponseMessage::UNAUTHORIZED_OTHER_BRANCH);
        }

        if ($user->role === 'superadmin') {
            $entities = Entity::all();
        } else {
            $entities = Entity::where('id', $user->entity_id)->get();
        }

        $typeCategoryMap = Infrastructure::select('type', 'category')
                            ->distinct()
                            ->pluck('category', 'type')
                            ->toArray();

        return view('admin.infrastructures.edit', compact('infrastructure', 'entities', 'typeCategoryMap'));
    }

    public function update(UpdateInfrastructureRequest $request, Infrastructure $infrastructure)
    {
        $user = auth()->user();

        // Proteksi keamanan di backend
        if ($user->role !== 'superadmin' && $infrastructure->entity_id !== $user->entity_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validated();

        // 2. Tentukan Entity ID
        // Operator tidak akan bisa mengubah entity_id meskipun form diakali (inspect element)
        $targetEntityId = $user->role === 'superadmin' ? $request->entity_id : $infrastructure->entity_id;

        // 3. Logika Tipe Alat
        $finalType = $request->type_select === 'new' ? $request->type_new : $request->type_select;

        // Jika type menggunakan form lama yang tidak punya select, pakai request->type langsung
        if (empty($finalType) && $request->filled('type')) {
            $finalType = $request->type;
        }

        if (empty($finalType)) {
            return back()->withErrors(['type_new' => 'Jenis alat wajib diisi!'])->withInput();
        }

        // 4. Siapkan array data untuk diupdate (Lebih aman dari Mass Assignment)
        $dataToUpdate = [
            'entity_id' => $targetEntityId,
            'category'  => $validated['category'],
            'type'      => $finalType,
            'code_name' => $validated['code_name'],
            'status'    => $validated['status'],
            'quantity'  => 1,
        ];

        // 5. Proses Ganti Foto
        if ($request->hasFile('image')) {
            // Cek dan hapus foto lama dari brankas storage agar tidak penuh
            if ($infrastructure->image && Storage::disk('public')->exists($infrastructure->image)) {
                Storage::disk('public')->delete($infrastructure->image);
            }
            // Simpan foto baru dengan format nama unik
            $file = $request->file('image');
            $filename = 'infra_' . preg_replace('/[^A-Za-z0-9\-]/', '', $validated['code_name']) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $dataToUpdate['image'] = $file->storeAs('assets/infrastructures', $filename, 'public');
        }

        // Add audit trail
        $dataToUpdate['updated_by'] = $user->id;

        // 6. Eksekusi Update
        $infrastructure->update($dataToUpdate);

        return redirect()->route('admin.infrastructures.index')
            ->with('success', ResponseMessage::INFRASTRUCTURE_UPDATED);
    }

    public function destroy(Infrastructure $infrastructure)
    {
        $user = auth()->user();

        // Proteksi Hapus
        if ($user->role !== 'superadmin' && $infrastructure->entity_id !== $user->entity_id) {
            return redirect()->route('admin.infrastructures.index')->with('error', ResponseMessage::UNAUTHORIZED_OTHER_BRANCH);
        }

        // (Soft Deletes: File bukti fisik gambar tidak dihapus dari storage agar tetap tersedia jika data di-restore)

        $infrastructure->delete();

        return redirect()->route('admin.infrastructures.index')
            ->with('success', ResponseMessage::INFRASTRUCTURE_DELETED_WITH_FILE);
    }
}
