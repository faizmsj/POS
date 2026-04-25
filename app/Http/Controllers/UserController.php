<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(?User $editing = null)
    {
        $actor = auth()->user();

        if ($editing) {
            $this->authorizeUserManagement($editing);
        }

        return view('users.index', [
            'users' => User::with('branch')->orderBy('name')->get(),
            'branches' => Branch::where('is_active', true)->orderBy('name')->get(),
            'editing' => $editing,
            'roles' => $this->assignableRoles($actor),
            'actor' => $actor,
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
            'branch_id' => 'nullable|exists:branches,id|required_if:role,manager,cashier',
            'role' => 'required|in:'.implode(',', array_keys($this->assignableRoles(auth()->user()))),
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (in_array($validated['role'], ['owner', 'admin'], true)) {
            $validated['branch_id'] = $validated['branch_id'] ?? null;
        }

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeUserManagement($user);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'branch_id' => 'nullable|exists:branches,id|required_if:role,manager,cashier',
            'role' => 'required|in:'.implode(',', array_keys($this->assignableRoles(auth()->user()))),
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if ((int) $user->id === (int) auth()->id() && $validated['role'] !== $user->role) {
            return redirect()->route('users.index')->with('error', 'Role akun yang sedang Anda gunakan tidak dapat diubah dari halaman ini.');
        }

        if (in_array($user->role, ['owner', 'admin'], true)
            && ! in_array($validated['role'], ['owner', 'admin'], true)
            && User::whereIn('role', ['owner', 'admin'])->count() <= 1) {
            return redirect()->route('users.index')->with('error', 'Minimal satu owner/admin harus tetap tersedia.');
        }

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        if (in_array($validated['role'], ['owner', 'admin'], true)) {
            $validated['branch_id'] = $validated['branch_id'] ?? null;
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $this->authorizeUserManagement($user);

        if ((int) $user->id === (int) auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Akun yang sedang Anda gunakan tidak dapat dihapus.');
        }

        if (User::whereIn('role', ['owner', 'admin'])->count() <= 1 && in_array($user->role, ['owner', 'admin'], true)) {
            return redirect()->route('users.index')->with('error', 'Minimal satu owner/admin harus tetap tersedia.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    private function assignableRoles(User $actor): array
    {
        if ($actor->hasRole('owner')) {
            return [
                'owner' => 'Owner',
                'admin' => 'Admin',
                'manager' => 'Manager',
                'cashier' => 'Kasir',
            ];
        }

        return [
            'admin' => 'Admin',
            'manager' => 'Manager',
            'cashier' => 'Kasir',
        ];
    }

    private function authorizeUserManagement(User $user): void
    {
        $actor = auth()->user();

        if ($actor->hasRole('owner')) {
            return;
        }

        if ($actor->hasRole('admin') && $user->hasRole('owner')) {
            abort(403, 'Akun owner hanya dapat dikelola oleh owner.');
        }
    }
}
