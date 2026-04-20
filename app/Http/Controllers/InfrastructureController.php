<?php

namespace App\Http\Controllers;

use App\Models\Infrastructure;
use App\Models\Entity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class InfrastructureController extends Controller 
{
    public function index() 
    {
        $user = auth()->user();

        if ($user->role === 'superadmin') {
            $infrastructures = Infrastructure::with('entity')->latest()->get();
        } else {
            $infrastructures = Infrastructure::with('entity')
                ->where('entity_id', $user->entity_id)
                ->latest()->get();
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
        
        $typeCategoryMap = Infrastructure::select('type', 'category')
                            ->distinct()
                            ->pluck('category', 'type')
                            ->toArray();

        return view('admin.infrastructures.create', compact('entities', 'typeCategoryMap'));
    }

    public function store(Request $request) 
    {
        $user = auth()->user();

        // Validasi dasar
        $request->validate([
            'category'  => 'required|in:equipment,facility,utility',
            'code_name' => 'required|unique:infrastructures',
            'status'    => 'required|in:available,breakdown',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        // Jika superadmin, entity_id wajib dari form. Jika operator, entity_id dipaksa dari sistem (keamanan).
        $targetEntityId = $user->role === 'superadmin' ? $request->entity_id : $user->entity_id;

        if ($user->role === 'superadmin' && empty($targetEntityId)) {
            return back()->withErrors(['entity_id' => 'Entitas/Cabang wajib dipilih!'])->withInput();
        }

        $finalType = $request->type_select === 'new' ? $request->type_new : $request->type_select;
        
        if (empty($finalType)) {
            return back()->withErrors(['type_new' => 'Jenis alat wajib diisi!'])->withInput();
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('assets/infrastructures', 'public');
        }

        Infrastructure::create([
            'entity_id' => $targetEntityId, // Menggunakan ID yang sudah diamankan
            'category'  => $request->category,
            'type'      => $finalType,
            'code_name' => $request->code_name,
            'status'    => $request->status,
            'quantity'  => 1,
            'image'     => $imagePath,
        ]);

        return redirect()->route('admin.infrastructures.index')
            ->with('success', 'Aset baru berhasil didaftarkan di wilayah Anda.');
    }

    public function edit(Infrastructure $infrastructure)
    {
        $user = auth()->user();

        // Proteksi: Operator tidak boleh edit alat cabang lain
        if ($user->role !== 'superadmin' && $infrastructure->entity_id !== $user->entity_id) {
            return redirect()->route('admin.infrastructures.index')->with('error', 'Akses ditolak! Ini bukan aset di wilayah Anda.');
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

    public function update(Request $request, Infrastructure $infrastructure)
    {
        $user = auth()->user();

        if ($user->role !== 'superadmin' && $infrastructure->entity_id !== $user->entity_id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'category'  => 'required|in:equipment,facility,utility',
            'code_name' => 'required|unique:infrastructures,code_name,' . $infrastructure->id,
            'status'    => 'required|in:available,breakdown',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $targetEntityId = $user->role === 'superadmin' ? $request->entity_id : $infrastructure->entity_id;

        $finalType = $request->type_select === 'new' ? $request->type_new : $request->type_select;
        
        if (empty($finalType)) {
            return back()->withErrors(['type_new' => 'Jenis alat wajib diisi!'])->withInput();
        }

        $data = $request->all();
        $data['entity_id'] = $targetEntityId;
        $data['type'] = $finalType;
        $data['quantity'] = 1;

        if ($request->hasFile('image')) {
            if ($infrastructure->image) {
                Storage::disk('public')->delete($infrastructure->image);
            }
            $data['image'] = $request->file('image')->store('assets/infrastructures', 'public');
        }

        $infrastructure->update($data);

        return redirect()->route('admin.infrastructures.index')
            ->with('success', 'Data inventaris berhasil diperbarui.');
    }

    public function destroy(Infrastructure $infrastructure) 
    {
        $user = auth()->user();
        
        if ($user->role !== 'superadmin' && $infrastructure->entity_id !== $user->entity_id) {
            return redirect()->route('admin.infrastructures.index')->with('error', 'Akses ditolak!');
        }

        if ($infrastructure->image) {
            Storage::disk('public')->delete($infrastructure->image);
        }

        $infrastructure->delete();

        return redirect()->route('admin.infrastructures.index')
            ->with('success', 'Aset telah dihapus dari database.');
    }

    public function deleteAll()
    {
        if (auth()->user()->role !== 'superadmin') {
            return back()->with('error', 'Akses ditolak!');
        }

        $infrastructuresWithImages = Infrastructure::whereNotNull('image')->get();
        foreach ($infrastructuresWithImages as $item) {
            Storage::disk('public')->delete($item->image);
        }

        Schema::disableForeignKeyConstraints();
        \App\Models\BreakdownLog::truncate();
        Infrastructure::truncate();
        Schema::enableForeignKeyConstraints();

        return redirect()->route('admin.infrastructures.index')
            ->with('success', 'Seluruh database infrastruktur telah dibersihkan total.');
    }
}
