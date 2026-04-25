<x-layouts.app>
    @php
        $showing = $categories->firstWhere('id', (int) request('show'));
        $formAutoOpen = $editing || old('_modal') === 'category-form-modal';
    @endphp

    <div class="space-y-6">
        @if (!empty($categoryTableMissing))
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                Tabel kategori belum tersedia pada database aktif. Jalankan migrasi kategori agar modul kategori dapat digunakan penuh.
            </div>
        @endif

        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex flex-col gap-1">
                <h1 class="text-xl sm:text-2xl font-semibold">Manajemen Kategori Produk</h1>
                <p class="text-xs sm:text-sm text-slate-500">Buat, edit, dan lihat detail kategori melalui modal.</p>
            </div>
            <button type="button" data-modal-open="#category-form-modal" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800" @disabled(!empty($categoryTableMissing))>
                Tambah Kategori
            </button>
        </div>

        <section class="bg-white rounded-2xl sm:rounded-3xl shadow-sm p-4 sm:p-6">
            <h2 class="text-base sm:text-lg font-semibold mb-4">Kategori</h2>

            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-y-2 text-sm">
                    <thead class="text-xs text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="pb-3 px-2">Nama</th>
                            <th class="pb-3 px-2">Slug</th>
                            <th class="pb-3 px-2">Status</th>
                            <th class="pb-3 px-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr class="bg-slate-50 rounded-2xl">
                                <td class="py-3 px-2 font-semibold text-slate-950">{{ $category->name }}</td>
                                <td class="py-3 px-2">{{ $category->slug }}</td>
                                <td class="py-3 px-2">{{ $category->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                                <td class="py-3 px-2 text-right">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('categories.index', ['show' => $category->id]) }}" class="text-blue-600 hover:underline">Lihat</a>
                                        <a href="{{ route('categories.edit', $category) }}" class="text-slate-700 hover:underline">Edit</a>
                                        <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?');">
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

            @if ($categories->isEmpty())
                <div class="mt-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                    Belum ada kategori yang tersedia.
                </div>
            @endif

            <div class="space-y-3 lg:hidden">
                @foreach ($categories as $category)
                    <div class="bg-slate-50 rounded-2xl p-3 border border-slate-200">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-semibold text-sm">{{ $category->name }}</h3>
                            <div class="flex gap-2 text-xs font-semibold">
                                <a href="{{ route('categories.index', ['show' => $category->id]) }}" class="text-blue-600">Lihat</a>
                                <a href="{{ route('categories.edit', $category) }}" class="text-slate-700">Edit</a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Hapus?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600">Hapus</button>
                                </form>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500">Slug: {{ $category->slug }}</p>
                        <p class="text-xs text-slate-500">Status: {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <div id="category-form-modal" data-modal data-modal-auto-open="{{ $formAutoOpen ? 'true' : 'false' }}" class="pos-modal-backdrop">
        <div class="pos-modal-panel">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-slate-950">{{ $editing ? 'Edit Kategori' : 'Tambah Kategori' }}</h2>
                    <p class="mt-1 text-sm text-slate-500">Pengelolaan kategori kini memakai modal yang lebih ringkas.</p>
                </div>
                <button type="button" data-modal-close class="pos-modal-close">×</button>
            </div>
            <form action="{{ $editing ? route('categories.update', $editing) : route('categories.store') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="_modal" value="category-form-modal">
                @if ($editing)
                    @method('PATCH')
                @endif
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $editing?->name) }}" class="pos-form-input" @disabled(!empty($categoryTableMissing)) required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $editing?->slug) }}" class="pos-form-input" @disabled(!empty($categoryTableMissing)) required>
                </div>
                <div class="pos-modal-actions">
                    <button type="button" data-modal-close class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Tutup</button>
                    <button type="submit" class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white disabled:bg-slate-300" @disabled(!empty($categoryTableMissing))>{{ $editing ? 'Perbarui Kategori' : 'Simpan Kategori' }}</button>
                </div>
            </form>
        </div>
    </div>

    @if ($showing)
        <div data-modal data-modal-auto-open="true" class="pos-modal-backdrop">
            <div class="pos-modal-panel">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-950">Detail Kategori</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ $showing->name }}</p>
                    </div>
                    <a href="{{ route('categories.index') }}" class="pos-modal-close">×</a>
                </div>
                <div class="mt-6 grid gap-3">
                    <div class="rounded-[22px] bg-slate-50 p-4"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Slug</div><div class="mt-2 text-slate-700">{{ $showing->slug }}</div></div>
                    <div class="rounded-[22px] bg-slate-50 p-4"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Status</div><div class="mt-2 text-slate-700">{{ $showing->is_active ? 'Aktif' : 'Nonaktif' }}</div></div>
                </div>
                <div class="pos-modal-actions">
                    <a href="{{ route('categories.index') }}" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Tutup</a>
                    <a href="{{ route('categories.edit', $showing) }}" class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Edit Kategori</a>
                </div>
            </div>
        </div>
    @endif
</x-layouts.app>
