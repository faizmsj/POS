<x-layouts.app :title="'Akses Pengguna'">
    <div class="flex flex-col gap-6">
        <div class="flex flex-col gap-1">
            <h1 class="text-2xl font-semibold">Akses Pengguna</h1>
            <p class="text-sm text-slate-500">Kelola akun admin, manager, dan kasir beserta penempatan cabangnya.</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <section class="rounded-3xl bg-white p-6 shadow-sm lg:col-span-2">
                <div class="mb-4 grid gap-4 md:grid-cols-3">
                    <div class="rounded-[24px] bg-slate-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Total User</div>
                        <div class="mt-2 text-2xl font-semibold text-slate-950">{{ $users->count() }}</div>
                    </div>
                    <div class="rounded-[24px] bg-blue-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-500">Kasir</div>
                        <div class="mt-2 text-2xl font-semibold text-blue-950">{{ $users->where('role', 'cashier')->count() }}</div>
                    </div>
                    <div class="rounded-[24px] bg-emerald-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-500">Admin/Owner</div>
                        <div class="mt-2 text-2xl font-semibold text-emerald-950">{{ $users->whereIn('role', ['admin', 'owner'])->count() }}</div>
                    </div>
                </div>

                <div class="hidden overflow-x-auto lg:block">
                    <table class="w-full border-separate border-spacing-y-2 text-left">
                        <thead class="text-sm uppercase tracking-[0.15em] text-slate-500">
                            <tr>
                                <th class="pb-3">Nama</th>
                                <th class="pb-3">Email</th>
                                <th class="pb-3">Role</th>
                                <th class="pb-3">Cabang</th>
                                <th class="pb-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr class="rounded-3xl bg-slate-50">
                                    <td class="py-4 font-semibold text-slate-950">{{ $user->name }}</td>
                                    <td class="text-slate-600">{{ $user->email }}</td>
                                    <td>
                                        <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-700">{{ ucfirst($user->role) }}</span>
                                    </td>
                                    <td class="text-slate-600">{{ $user->branch?->name ?? '-' }}</td>
                                    <td class="text-right">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('users.edit', $user) }}" class="text-blue-600 hover:underline">Edit</a>
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus pengguna ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-600 hover:underline">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="space-y-3 lg:hidden">
                    @foreach ($users as $user)
                        <div class="pos-stack-card">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="font-semibold text-slate-950">{{ $user->name }}</div>
                                    <div class="mt-1 text-xs text-slate-500">{{ $user->email }}</div>
                                </div>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ ucfirst($user->role) }}</span>
                            </div>
                            <div class="mt-3 text-sm text-slate-500">Cabang: {{ $user->branch?->name ?? '-' }}</div>
                            <div class="mt-4 flex gap-3">
                                <a href="{{ route('users.edit', $user) }}" class="text-sm font-semibold text-blue-600">Edit</a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus pengguna ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-semibold text-rose-600">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-3xl bg-white p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold">{{ $editing ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</h2>
                    @if ($editing)
                        <a href="{{ route('users.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Batal</a>
                    @endif
                </div>

                <form action="{{ $editing ? route('users.update', $editing) : route('users.store') }}" method="POST" class="space-y-4">
                    @csrf
                    @if ($editing)
                        @method('PATCH')
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Nama</label>
                        <input type="text" name="name" value="{{ old('name', $editing?->name) }}" class="mt-2 w-full rounded-3xl border border-slate-200 px-4 py-3" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Email</label>
                        <input type="email" name="email" value="{{ old('email', $editing?->email) }}" class="mt-2 w-full rounded-3xl border border-slate-200 px-4 py-3" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Role</label>
                        <select name="role" class="mt-2 w-full rounded-3xl border border-slate-200 px-4 py-3" required>
                            @foreach ($roles as $role => $label)
                                <option value="{{ $role }}" @selected(old('role', $editing?->role ?? 'cashier') === $role)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Cabang</label>
                        <select name="branch_id" class="mt-2 w-full rounded-3xl border border-slate-200 px-4 py-3">
                            <option value="">Tanpa cabang khusus</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @selected((string) old('branch_id', $editing?->branch_id) === (string) $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">{{ $editing ? 'Password Baru' : 'Password' }}</label>
                        <input type="password" name="password" class="mt-2 w-full rounded-3xl border border-slate-200 px-4 py-3" {{ $editing ? '' : 'required' }}>
                        @if ($editing)
                            <p class="mt-1 text-xs text-slate-500">Kosongkan jika tidak ingin mengganti password.</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="mt-2 w-full rounded-3xl border border-slate-200 px-4 py-3" {{ $editing ? '' : 'required' }}>
                    </div>

                    <button type="submit" class="w-full rounded-3xl bg-[#111827] px-5 py-3 text-sm font-semibold text-white hover:bg-[#0f172a]">
                        {{ $editing ? 'Perbarui Pengguna' : 'Simpan Pengguna' }}
                    </button>
                </form>
            </section>
        </div>
    </div>
</x-layouts.app>
