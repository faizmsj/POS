<x-layouts.app>
    @php
        $showing = $branches->firstWhere('id', (int) request('show'));
        $formAutoOpen = $editing || old('_modal') === 'branch-form-modal';
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex flex-col gap-1">
                <h1 class="text-2xl font-semibold">Manajemen Cabang</h1>
                <p class="text-sm text-slate-500">Kelola data cabang, alamat, dan konfigurasi toko dari satu layar.</p>
            </div>
            <button type="button" data-modal-open="#branch-form-modal" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                Tambah Cabang
            </button>
        </div>

        <section class="bg-white rounded-3xl shadow-sm p-6">
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
                            <th class="pb-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($branches as $branch)
                            <tr class="bg-slate-50 rounded-3xl">
                                <td class="py-4 font-semibold text-slate-950">{{ $branch->name }}</td>
                                <td>{{ $branch->code }}</td>
                                <td>{{ $branch->address ?: '-' }}</td>
                                <td>{{ $branch->phone ?: '-' }}</td>
                                <td>{{ $branch->is_active ? 'Aktif' : 'Tidak aktif' }}</td>
                                <td class="text-right">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('branches.index', ['show' => $branch->id]) }}" class="text-blue-600 hover:underline">Lihat</a>
                                        <a href="{{ route('branches.edit', $branch) }}" class="text-slate-700 hover:underline">Edit</a>
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
                        <div class="mt-4 flex gap-3 text-sm font-semibold">
                            <a href="{{ route('branches.index', ['show' => $branch->id]) }}" class="text-blue-600">Lihat</a>
                            <a href="{{ route('branches.edit', $branch) }}" class="text-slate-700">Edit</a>
                            <form action="{{ route('branches.destroy', $branch) }}" method="POST" onsubmit="return confirm('Hapus cabang ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600">Hapus</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <div id="branch-form-modal" data-modal data-modal-auto-open="{{ $formAutoOpen ? 'true' : 'false' }}" class="pos-modal-backdrop">
        <div class="pos-modal-panel">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-slate-950">{{ $editing ? 'Edit Cabang' : 'Tambah Cabang Baru' }}</h2>
                    <p class="mt-1 text-sm text-slate-500">Atur identitas cabang tanpa meninggalkan halaman daftar.</p>
                </div>
                <button type="button" data-modal-close class="pos-modal-close">×</button>
            </div>
            <form action="{{ $editing ? route('branches.update', $editing) : route('branches.store') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="_modal" value="branch-form-modal">
                @if ($editing)
                    @method('PATCH')
                @endif
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $editing?->name) }}" class="pos-form-input" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kode</label>
                    <input type="text" name="code" value="{{ old('code', $editing?->code) }}" class="pos-form-input" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
                    <input type="text" name="address" value="{{ old('address', $editing?->address) }}" class="pos-form-input">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $editing?->phone) }}" class="pos-form-input">
                </div>
                <div class="pos-modal-actions">
                    <button type="button" data-modal-close class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Tutup</button>
                    <button type="submit" class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">{{ $editing ? 'Perbarui Cabang' : 'Simpan Cabang' }}</button>
                </div>
            </form>
        </div>
    </div>

    @if ($showing)
        <div data-modal data-modal-auto-open="true" class="pos-modal-backdrop">
            <div class="pos-modal-panel">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-950">Detail Cabang</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ $showing->name }}</p>
                    </div>
                    <a href="{{ route('branches.index') }}" class="pos-modal-close">×</a>
                </div>
                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[22px] bg-slate-50 p-4"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Kode</div><div class="mt-2 font-semibold text-slate-950">{{ $showing->code }}</div></div>
                    <div class="rounded-[22px] bg-slate-50 p-4"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Status</div><div class="mt-2 font-semibold text-slate-950">{{ $showing->is_active ? 'Aktif' : 'Nonaktif' }}</div></div>
                    <div class="rounded-[22px] bg-slate-50 p-4 sm:col-span-2"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Alamat</div><div class="mt-2 text-slate-700">{{ $showing->address ?: '-' }}</div></div>
                    <div class="rounded-[22px] bg-slate-50 p-4 sm:col-span-2"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Telepon</div><div class="mt-2 text-slate-700">{{ $showing->phone ?: '-' }}</div></div>
                </div>
                <div class="pos-modal-actions">
                    <a href="{{ route('branches.index') }}" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Tutup</a>
                    <a href="{{ route('branches.edit', $showing) }}" class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Edit Cabang</a>
                </div>
            </div>
        </div>
    @endif
</x-layouts.app>
