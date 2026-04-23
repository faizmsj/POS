<x-layouts.app>
    <div class="flex flex-col gap-6">
        <div class="flex flex-col gap-1">
            <h1 class="text-2xl font-semibold">Manajemen Cabang</h1>
            <p class="text-sm text-slate-500">Kelola data cabang, alamat, dan konfigurasi toko.</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <section class="lg:col-span-2 bg-white rounded-3xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Cabang Aktif</h2>
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full text-left border-separate border-spacing-y-2">
                        <thead class="text-sm text-slate-500 uppercase tracking-[0.15em]">
                            <tr>
                                <th class="pb-3">Nama</th>
                                <th class="pb-3">Kode</th>
                                <th class="pb-3">Alamat</th>
                                <th class="pb-3">Telepon</th>
                                <th class="pb-3">Status</th>
                                <th class="pb-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($branches as $branch)
                                <tr class="bg-slate-50 rounded-3xl">
                                    <td class="py-4">{{ $branch->name }}</td>
                                    <td>{{ $branch->code }}</td>
                                    <td>{{ $branch->address }}</td>
                                    <td>{{ $branch->phone }}</td>
                                    <td>{{ $branch->is_active ? 'Aktif' : 'Tidak aktif' }}</td>
                                    <td class="text-right">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('branches.edit', $branch) }}" class="text-blue-600 hover:underline">Edit</a>
                                            <form action="{{ route('branches.destroy', $branch) }}" method="POST" onsubmit="return confirm('Hapus cabang ini?');">
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
                    @foreach($branches as $branch)
                        <div class="pos-stack-card">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="font-semibold text-slate-950">{{ $branch->name }}</div>
                                    <div class="mt-1 text-xs text-slate-500">{{ $branch->code }}</div>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $branch->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $branch->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                            <div class="mt-3 space-y-1 text-sm text-slate-500">
                                <p>{{ $branch->address ?: '-' }}</p>
                                <p>{{ $branch->phone ?: '-' }}</p>
                            </div>
                            <div class="mt-4 flex gap-3">
                                <a href="{{ route('branches.edit', $branch) }}" class="text-sm font-semibold text-blue-600">Edit</a>
                                <form action="{{ route('branches.destroy', $branch) }}" method="POST" onsubmit="return confirm('Hapus cabang ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-semibold text-rose-600">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="bg-white rounded-3xl shadow-sm p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold">{{ $editing ? 'Edit Cabang' : 'Tambah Cabang Baru' }}</h2>
                    @if ($editing)
                        <a href="{{ route('branches.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Batal</a>
                    @endif
                </div>
                <form action="{{ $editing ? route('branches.update', $editing) : route('branches.store') }}" method="POST" class="space-y-4">
                    @csrf
                    @if ($editing)
                        @method('PATCH')
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Nama</label>
                        <input type="text" name="name" value="{{ old('name', $editing?->name) }}" class="mt-2 w-full rounded-3xl border border-slate-200 px-4 py-3" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Kode</label>
                        <input type="text" name="code" value="{{ old('code', $editing?->code) }}" class="mt-2 w-full rounded-3xl border border-slate-200 px-4 py-3" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Alamat</label>
                        <input type="text" name="address" value="{{ old('address', $editing?->address) }}" class="mt-2 w-full rounded-3xl border border-slate-200 px-4 py-3">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone', $editing?->phone) }}" class="mt-2 w-full rounded-3xl border border-slate-200 px-4 py-3">
                    </div>
                    <button type="submit" class="w-full rounded-3xl bg-[#111827] px-5 py-3 text-sm font-semibold text-white hover:bg-[#0f172a]">{{ $editing ? 'Perbarui Cabang' : 'Simpan Cabang' }}</button>
                </form>
            </section>
        </div>
    </div>
</x-layouts.app>
