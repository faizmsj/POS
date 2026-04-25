<x-layouts.app :title="'Akses Pengguna'">
    @php
        $showing = $users->firstWhere('id', (int) request('show'));
        $formAutoOpen = $editing || old('_modal') === 'user-form-modal';
    @endphp

    <div class="flex flex-col gap-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex flex-col gap-1">
                <h1 class="text-2xl font-semibold">Akses Pengguna</h1>
                <p class="text-sm text-slate-500">Kelola akun admin, manager, dan kasir beserta penempatan cabangnya.</p>
            </div>
            <button type="button" data-modal-open="#user-form-modal" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                Tambah Pengguna
            </button>
        </div>

        <section class="rounded-3xl bg-white p-6 shadow-sm">
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
                            <th class="pb-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            @php($canManageUser = $actor->hasRole('owner') || !$user->hasRole('owner'))
                            <tr class="rounded-3xl bg-slate-50">
                                <td class="py-4 font-semibold text-slate-950">{{ $user->name }}</td>
                                <td class="text-slate-600">{{ $user->email }}</td>
                                <td><span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-700">{{ ucfirst($user->role) }}</span></td>
                                <td class="text-slate-600">{{ $user->branch?->name ?? '-' }}</td>
                                <td class="text-right">
                                    @if ($canManageUser)
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('users.index', ['show' => $user->id]) }}" class="text-blue-600 hover:underline">Lihat</a>
                                            <a href="{{ route('users.edit', $user) }}" class="text-slate-700 hover:underline">Edit</a>
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus pengguna ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-600 hover:underline">Hapus</button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs font-semibold text-slate-400">Owner only</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="space-y-3 lg:hidden">
                @foreach ($users as $user)
                    @php($canManageUser = $actor->hasRole('owner') || !$user->hasRole('owner'))
                    <div class="pos-stack-card">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="font-semibold text-slate-950">{{ $user->name }}</div>
                                <div class="mt-1 text-xs text-slate-500">{{ $user->email }}</div>
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ ucfirst($user->role) }}</span>
                        </div>
                        <div class="mt-3 text-sm text-slate-500">Cabang: {{ $user->branch?->name ?? '-' }}</div>
                        @if ($canManageUser)
                            <div class="mt-4 flex gap-3 text-sm font-semibold">
                                <a href="{{ route('users.index', ['show' => $user->id]) }}" class="text-blue-600">Lihat</a>
                                <a href="{{ route('users.edit', $user) }}" class="text-slate-700">Edit</a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus pengguna ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600">Hapus</button>
                                </form>
                            </div>
                        @else
                            <div class="mt-4 text-sm font-semibold text-slate-400">Owner only</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <div id="user-form-modal" data-modal data-modal-auto-open="{{ $formAutoOpen ? 'true' : 'false' }}" class="pos-modal-backdrop">
        <div class="pos-modal-panel">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-slate-950">{{ $editing ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</h2>
                    <p class="mt-1 text-sm text-slate-500">Pengaturan akses user sekarang tampil dalam modal yang lebih fokus.</p>
                </div>
                <button type="button" data-modal-close class="pos-modal-close">×</button>
            </div>

            <form action="{{ $editing ? route('users.update', $editing) : route('users.store') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="_modal" value="user-form-modal">
                @if ($editing)
                    @method('PATCH')
                @endif

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $editing?->name) }}" class="pos-form-input" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $editing?->email) }}" class="pos-form-input" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Role</label>
                    <select name="role" class="pos-form-input" required>
                        @foreach ($roles as $role => $label)
                            <option value="{{ $role }}" @selected(old('role', $editing?->role ?? 'cashier') === $role)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Cabang</label>
                    <select name="branch_id" class="pos-form-input">
                        <option value="">Tanpa cabang khusus</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" @selected((string) old('branch_id', $editing?->branch_id) === (string) $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ $editing ? 'Password Baru' : 'Password' }}</label>
                    <div class="relative">
                        <input id="user-password" type="password" name="password" class="pos-form-input pr-24" {{ $editing ? '' : 'required' }}>
                        <button type="button" data-password-toggle="#user-password" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-xl px-3 py-1.5 text-xs font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-slate-900">
                            Lihat
                        </button>
                    </div>
                    @if ($editing)
                        <p class="mt-1 text-xs text-slate-500">Kosongkan jika tidak ingin mengganti password.</p>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password</label>
                    <div class="relative">
                        <input id="user-password-confirmation" type="password" name="password_confirmation" class="pos-form-input pr-24" {{ $editing ? '' : 'required' }}>
                        <button type="button" data-password-toggle="#user-password-confirmation" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-xl px-3 py-1.5 text-xs font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-slate-900">
                            Lihat
                        </button>
                    </div>
                </div>
                <div class="pos-modal-actions">
                    <button type="button" data-modal-close class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Tutup</button>
                    <button type="submit" class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">{{ $editing ? 'Perbarui Pengguna' : 'Simpan Pengguna' }}</button>
                </div>
            </form>
        </div>
    </div>

    @if ($showing)
        <div data-modal data-modal-auto-open="true" class="pos-modal-backdrop">
            <div class="pos-modal-panel">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-950">Detail Pengguna</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ $showing->name }}</p>
                    </div>
                    <a href="{{ route('users.index') }}" class="pos-modal-close">×</a>
                </div>

                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[22px] bg-slate-50 p-4"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Email</div><div class="mt-2 text-slate-700">{{ $showing->email }}</div></div>
                    <div class="rounded-[22px] bg-slate-50 p-4"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Role</div><div class="mt-2 text-slate-700">{{ ucfirst($showing->role) }}</div></div>
                    <div class="rounded-[22px] bg-slate-50 p-4 sm:col-span-2"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Cabang</div><div class="mt-2 text-slate-700">{{ $showing->branch?->name ?? 'Tanpa cabang khusus' }}</div></div>
                </div>

                <div class="pos-modal-actions">
                    <a href="{{ route('users.index') }}" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Tutup</a>
                    @if ($actor->hasRole('owner') || !$showing->hasRole('owner'))
                        <a href="{{ route('users.edit', $showing) }}" class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Edit Pengguna</a>
                    @endif
                </div>
            </div>
        </div>
    @endif
</x-layouts.app>
