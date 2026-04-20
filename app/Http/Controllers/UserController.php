<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Entity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('entity')->latest()->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $entities = Entity::all();
        return view('admin.users.create', compact('entities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|min:8|confirmed',
            'role'      => 'required|in:superadmin,operator',
            'entity_id' => 'required_if:role,operator|nullable|exists:entities,id',
        ]);

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'entity_id' => $request->role === 'superadmin' ? null : $request->entity_id,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Akun operator berhasil didaftarkan.');
    }

    public function edit(User $user)
    {
        $entities = Entity::all();
        return view('admin.users.edit', compact('user', 'entities'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role'      => 'required|in:superadmin,operator',
            'entity_id' => 'required_if:role,operator|nullable|exists:entities,id',
        ]);

        $data = [
            'name'      => $request->name,
            'email'     => $request->email,
            'role'      => $request->role,
            'entity_id' => $request->role === 'superadmin' ? null : $request->entity_id,
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Data akun berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun yang sedang Anda gunakan.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil dihapus.');
    }
}
