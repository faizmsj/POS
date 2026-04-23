<x-layouts.app>
    <div class="space-y-4 sm:space-y-6">
        @if (!empty($categoryTableMissing))
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                Tabel kategori belum tersedia di database aktif. Halaman produk tetap bisa dipakai, tetapi relasi kategori akan nonaktif sampai migrasi kategori dijalankan.
            </div>
        @endif

        <div class="flex flex-col gap-1">
            <h1 class="text-xl sm:text-2xl font-semibold">Manajemen Produk</h1>
            <p class="text-xs sm:text-sm text-slate-500">Kelola produk, kategori, dan harga per cabang.</p>
        </div>

        <section class="bg-white rounded-2xl sm:rounded-3xl shadow-sm p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-4 mb-4 sm:mb-6">
                <div>
                    <h2 class="text-base sm:text-lg font-semibold">Daftar Produk</h2>
                    <p class="text-xs sm:text-sm text-slate-500">Ringkasan produk dan stok per cabang.</p>
                </div>
            </div>

            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-y-2 text-sm">
                    <thead class="text-sm text-slate-500 uppercase tracking-[0.15em]">
                        <tr>
                            <th class="pb-3">Nama</th>
                            <th class="pb-3">SKU</th>
                            <th class="pb-3">Kategori</th>
                            <th class="pb-3">Harga Beli</th>
                            <th class="pb-3">Harga Jual</th>
                            <th class="pb-3">Cabang</th>
                            <th class="pb-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr class="bg-slate-50 rounded-3xl">
                                <td class="py-4">{{ $product->name }}</td>
                                <td>{{ $product->sku }}</td>
                                <td>{{ !empty($categoryTableMissing) ? '-' : ($product->category?->name ?? '-') }}</td>
                                <td>Rp {{ number_format($product->cost_price, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($product->base_price, 0, ',', '.') }}</td>
                                <td>
                                    @foreach ($product->branches as $branch)
                                        <div>{{ $branch->branch->name }} ({{ $branch->stock }})</div>
                                    @endforeach
                                </td>
                                <td class="text-right">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('products.edit', $product) }}" class="text-blue-600 hover:underline">Edit</a>
                                        <form action="{{ route('products.destroy', $product) }}" method="POST"
                                            onsubmit="return confirm('Hapus produk ini?');">
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

            <!-- Mobile Cards -->
            <div class="space-y-3 lg:hidden">
                @foreach ($products as $product)
                    <div class="pos-stack-card">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-semibold text-sm">{{ $product->name }}</h3>
                            <div class="flex gap-2">
                                <a href="{{ route('products.edit', $product) }}" class="text-xs text-blue-600">Edit</a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST"
                                    onsubmit="return confirm('Hapus?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-rose-600 hover:text-rose-700">Hapus</button>
                                </form>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500">SKU: {{ $product->sku }}</p>
                        <p class="text-xs text-slate-500">Kategori: {{ !empty($categoryTableMissing) ? '-' : ($product->category?->name ?? '-') }}</p>
                        <p class="text-xs text-slate-500">Beli: Rp {{ number_format($product->cost_price, 0, ',', '.') }}</p>
                        <p class="text-xs text-slate-500">Jual: Rp {{ number_format($product->base_price, 0, ',', '.') }}</p>
                        <div class="mt-2 space-y-1 text-xs text-slate-500">
                            @foreach ($product->branches as $branch)
                                <div>{{ $branch->branch->name }}: stok {{ number_format($branch->stock, 0, ',', '.') }}</div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="bg-white rounded-2xl sm:rounded-3xl shadow-sm p-4 sm:p-6">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-base sm:text-lg font-semibold">{{ $editing ? 'Edit Produk' : 'Tambah Produk Baru' }}</h2>
                @if ($editing)
                    <a href="{{ route('products.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Batal</a>
                @endif
            </div>
            <form action="{{ $editing ? route('products.update', $editing) : route('products.store') }}" method="POST"
                class="grid gap-3 sm:gap-4 grid-cols-1 lg:grid-cols-2">
                @csrf
                @if ($editing)
                    @method('PATCH')
                @endif
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1">Nama Produk</label>
                    <input type="text" name="name" value="{{ old('name', $editing?->name) }}"
                        class="w-full rounded-2xl border border-slate-200 px-3 sm:px-4 py-2 sm:py-3 text-sm" required>
                </div>
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku', $editing?->sku) }}"
                        class="w-full rounded-2xl border border-slate-200 px-3 sm:px-4 py-2 sm:py-3 text-sm" required>
                </div>
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1">Barcode</label>
                    <input type="text" name="barcode" value="{{ old('barcode', $editing?->barcode) }}"
                        class="w-full rounded-2xl border border-slate-200 px-3 sm:px-4 py-2 sm:py-3 text-sm">
                </div>
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1">Kategori</label>
                    <select name="category_id"
                        class="w-full rounded-2xl border border-slate-200 px-3 sm:px-4 py-2 sm:py-3 text-sm" @disabled(!empty($categoryTableMissing))>
                        <option value="">{{ !empty($categoryTableMissing) ? 'Kategori belum tersedia' : 'Pilih kategori' }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $editing?->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1">Harga Beli</label>
                    <input type="number" name="cost_price" value="{{ old('cost_price', $editing?->cost_price) }}"
                        class="w-full rounded-2xl border border-slate-200 px-3 sm:px-4 py-2 sm:py-3 text-sm">
                </div>
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1">Harga Jual</label>
                    <input type="number" name="base_price" value="{{ old('base_price', $editing?->base_price) }}"
                        class="w-full rounded-2xl border border-slate-200 px-3 sm:px-4 py-2 sm:py-3 text-sm">
                </div>
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1">Cabang Default</label>
                    <select name="branch_id"
                        class="w-full rounded-2xl border border-slate-200 px-3 sm:px-4 py-2 sm:py-3 text-sm">
                        <option value="">Tidak ada</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" @selected(old('branch_id', $editing?->branches?->first()?->branch_id) == $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1">Stok Awal Cabang</label>
                    <input type="number" name="stock" value="{{ old('stock', $editing?->branches?->first()?->stock) }}"
                        class="w-full rounded-2xl border border-slate-200 px-3 sm:px-4 py-2 sm:py-3 text-sm">
                </div>
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1">Harga Jual Cabang</label>
                    <input type="number" name="selling_price" value="{{ old('selling_price', $editing?->branches?->first()?->selling_price) }}"
                        class="w-full rounded-2xl border border-slate-200 px-3 sm:px-4 py-2 sm:py-3 text-sm">
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                    <textarea name="description" rows="3"
                        class="w-full rounded-2xl border border-slate-200 px-3 sm:px-4 py-2 sm:py-3 text-sm">{{ old('description', $editing?->description) }}</textarea>
                </div>
                <div class="lg:col-span-2 text-center sm:text-right">
                    <button type="submit"
                        class="w-full sm:w-auto rounded-2xl bg-[#111827] px-4 sm:px-6 py-2.5 sm:py-3 text-xs sm:text-sm font-semibold text-white hover:bg-[#0f172a] transition">{{ $editing ? 'Perbarui' : 'Tambah' }}
                        Produk</button>
                </div>
            </form>
        </section>
    </div>
</x-layouts.app>
