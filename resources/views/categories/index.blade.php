<x-layouts.app>
    <div class="space-y-6">
        @if (!empty($categoryTableMissing))
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                Tabel kategori belum tersedia pada database aktif. Jalankan migrasi kategori agar modul kategori dapat digunakan penuh.
            </div>
        @endif

        <div class="flex flex-col gap-1">
            <h1 class="text-xl sm:text-2xl font-semibold">Manajemen Kategori Produk</h1>
            <p class="text-xs sm:text-sm text-slate-500">Buat dan kelola kategori untuk produk Anda.</p>
        </div>

        <div class="grid gap-4 sm:gap-6 grid-cols-1 lg:grid-cols-3">
            <section class="lg:col-span-2 bg-white rounded-2xl sm:rounded-3xl shadow-sm p-4 sm:p-6">
                <h2 class="text-base sm:text-lg font-semibold mb-4">Kategori</h2>

                <!-- Desktop Table -->
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
                                    <td class="py-3 px-2">{{ $category->name }}</td>
                                    <td class="py-3 px-2">{{ $category->slug }}</td>
                                    <td class="py-3 px-2">{{ $category->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                                    <td class="py-3 px-2 text-right">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('categories.edit', $category) }}" class="text-xs sm:text-sm text-blue-600 hover:underline">Edit</a>
                                            <form action="{{ route('categories.destroy', $category) }}" method="POST"
                                                onsubmit="return confirm('Hapus kategori ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-xs sm:text-sm text-rose-600 hover:underline">Hapus</button>
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

                <!-- Mobile Cards -->
                <div class="space-y-3 lg:hidden">
                    @foreach ($categories as $category)
                        <div class="bg-slate-50 rounded-2xl p-3 border border-slate-200">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-semibold text-sm">{{ $category->name }}</h3>
                                <div class="flex gap-2">
                                    <a href="{{ route('categories.edit', $category) }}" class="text-xs text-blue-600">Edit</a>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST"
                                        onsubmit="return confirm('Hapus?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-xs text-rose-600 hover:text-rose-700">Hapus</button>
                                    </form>
                                </div>
                            </div>
                            <p class="text-xs text-slate-500">Slug: {{ $category->slug }}</p>
                            <p class="text-xs text-slate-500">Status: {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="bg-white rounded-2xl sm:rounded-3xl shadow-sm p-4 sm:p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-base sm:text-lg font-semibold">{{ $editing ? 'Edit Kategori' : 'Tambah Kategori' }}</h2>
                    @if ($editing)
                        <a href="{{ route('categories.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Batal</a>
                    @endif
                </div>
                <form action="{{ $editing ? route('categories.update', $editing) : route('categories.store') }}" method="POST" class="space-y-3 sm:space-y-4">
                    @csrf
                    @if ($editing)
                        @method('PATCH')
                    @endif
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1">Nama</label>
                        <input type="text" name="name" value="{{ old('name', $editing?->name) }}"
                            class="w-full rounded-2xl border border-slate-200 px-3 sm:px-4 py-2 sm:py-3 text-sm"
                            @disabled(!empty($categoryTableMissing))
                            required>
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1">Slug</label>
                        <input type="text" name="slug" value="{{ old('slug', $editing?->slug) }}"
                            class="w-full rounded-2xl border border-slate-200 px-3 sm:px-4 py-2 sm:py-3 text-sm"
                            @disabled(!empty($categoryTableMissing))
                            required>
                    </div>
                    <button type="submit"
                        class="w-full rounded-2xl bg-[#111827] px-3 sm:px-5 py-2.5 sm:py-3 text-xs sm:text-sm font-semibold text-white hover:bg-[#0f172a] transition disabled:cursor-not-allowed disabled:bg-slate-300"
                        @disabled(!empty($categoryTableMissing))>{{ $editing ? 'Perbarui' : 'Simpan' }}
                        Kategori</button>
                </form>
            </section>
        </div>
    </div>
</x-layouts.app>
