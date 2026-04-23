<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(?User $editing = null)
    {
        return view('users.index', [
            'users' => User::with('branch')->orderBy('name')->get(),
            'branches' => Branch::where('is_active', true)->orderBy('name')->get(),
            'editing' => $editing,
            'roles' => $this->roles(),
        ]);
    }

    public function edit(User $user)
    {
        return $this->index($user);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'branch_id' => 'nullable|exists:branches,id',
            'role' => 'required|in:owner,admin,manager,cashier',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'branch_id' => 'nullable|exists:branches,id',
            'role' => 'required|in:owner,admin,manager,cashier',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if (User::whereIn('role', ['owner', 'admin'])->count() <= 1 && in_array($user->role, ['owner', 'admin'], true)) {
            return redirect()->route('users.index')->with('error', 'Minimal satu owner/admin harus tetap tersedia.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    private function roles(): array
    {
        return [
            'owner' => 'Owner',
            'admin' => 'Admin',
            'manager' => 'Manager',
            'cashier' => 'Kasir',
        ];
    }
}
