<x-layouts.app>
    @php
        $showing = $products->firstWhere('id', (int) request('show'));
        $productModalAutoOpen = $editing || old('_modal') === 'product-form-modal';
        $productShowAutoOpen = $showing !== null;
    @endphp

    <div class="space-y-4 sm:space-y-6">
        @if (!empty($categoryTableMissing))
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                Tabel kategori belum tersedia di database aktif. Halaman produk tetap bisa dipakai, tetapi relasi kategori akan nonaktif sampai migrasi kategori dijalankan.
            </div>
        @endif

        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex flex-col gap-1">
                <h1 class="text-xl font-semibold sm:text-2xl">Manajemen Produk</h1>
                <p class="text-xs text-slate-500 sm:text-sm">Kelola produk, kategori, stok cabang, dan gambar produk untuk menu kasir.</p>
            </div>
            <button type="button" data-modal-open="#product-form-modal"
                class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                Tambah Produk
            </button>
        </div>

        <section class="rounded-2xl bg-white p-4 shadow-sm sm:rounded-3xl sm:p-6">
            <div class="mb-4 flex flex-col gap-1">
                <h2 class="text-base font-semibold sm:text-lg">Daftar Produk</h2>
                <p class="text-xs text-slate-500 sm:text-sm">Semua produk di bawah ini bisa dilihat, diedit, dan dibuka detailnya tanpa pindah halaman.</p>
            </div>

            <div class="hidden overflow-x-auto lg:block">
                <table class="w-full border-separate border-spacing-y-2 text-left text-sm">
                    <thead class="text-sm uppercase tracking-[0.15em] text-slate-500">
                        <tr>
                            <th class="pb-3">Foto</th>
                            <th class="pb-3">Nama</th>
                            <th class="pb-3">SKU</th>
                            <th class="pb-3">Kategori</th>
                            <th class="pb-3">Harga Jual</th>
                            <th class="pb-3">Cabang</th>
                            <th class="pb-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr class="rounded-3xl bg-slate-50">
                                <td class="py-4">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-14 w-14 rounded-2xl object-cover shadow-sm">
                                </td>
                                <td class="py-4">
                                    <div class="font-semibold text-slate-950">{{ $product->name }}</div>
                                    <div class="mt-1 text-xs text-slate-500">{{ $product->barcode ?: 'Tanpa barcode' }}</div>
                                </td>
                                <td>{{ $product->sku }}</td>
                                <td>{{ !empty($categoryTableMissing) ? '-' : ($product->category?->name ?? '-') }}</td>
                                <td>Rp {{ number_format($product->base_price, 0, ',', '.') }}</td>
                                <td>
                                    @foreach ($product->branches as $branch)
                                        <div>{{ $branch->branch->name }} ({{ $branch->stock }})</div>
                                    @endforeach
                                </td>
                                <td class="text-right">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('products.index', ['show' => $product->id]) }}" class="text-blue-600 hover:underline">Lihat</a>
                                        <a href="{{ route('products.edit', $product) }}" class="text-slate-700 hover:underline">Edit</a>
                                        <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Hapus produk ini?');">
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
                @foreach ($products as $product)
                    <div class="pos-stack-card">
                        <div class="mb-3 flex items-start gap-3">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-16 w-16 rounded-2xl object-cover shadow-sm">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h3 class="font-semibold text-sm text-slate-950">{{ $product->name }}</h3>
                                        <p class="mt-1 text-xs text-slate-500">{{ $product->sku }}</p>
                                    </div>
                                    <div class="flex gap-2 text-xs font-semibold">
                                        <a href="{{ route('products.index', ['show' => $product->id]) }}" class="text-blue-600">Lihat</a>
                                        <a href="{{ route('products.edit', $product) }}" class="text-slate-700">Edit</a>
                                        <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Hapus produk ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-slate-500">Kategori: {{ !empty($categoryTableMissing) ? '-' : ($product->category?->name ?? '-') }}</p>
                            </div>
                        </div>
                        <div class="grid gap-1 text-xs text-slate-500">
                            <div>Harga jual: Rp {{ number_format($product->base_price, 0, ',', '.') }}</div>
                            <div>Harga beli: Rp {{ number_format($product->cost_price, 0, ',', '.') }}</div>
                            @foreach ($product->branches as $branch)
                                <div>{{ $branch->branch->name }}: stok {{ number_format($branch->stock, 0, ',', '.') }}</div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <div id="product-form-modal" data-modal data-modal-auto-open="{{ $productModalAutoOpen ? 'true' : 'false' }}" class="pos-modal-backdrop">
        <div class="pos-modal-panel pos-modal-panel-lg">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-slate-950">{{ $editing ? 'Edit Produk' : 'Tambah Produk Baru' }}</h2>
                    <p class="mt-1 text-sm text-slate-500">Isi informasi utama, gambar produk, dan stok cabang pada satu form.</p>
                </div>
                <button type="button" data-modal-close class="pos-modal-close">×</button>
            </div>

            <form action="{{ $editing ? route('products.update', $editing) : route('products.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="_modal" value="product-form-modal">
                @if ($editing)
                    @method('PATCH')
                @endif

                <div class="pos-modal-grid">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Nama Produk</label>
                        <input type="text" name="name" value="{{ old('name', $editing?->name) }}" class="pos-form-input" required>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">SKU</label>
                        <input type="text" name="sku" value="{{ old('sku', $editing?->sku) }}" class="pos-form-input" required>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Barcode</label>
                        <input type="text" name="barcode" value="{{ old('barcode', $editing?->barcode) }}" class="pos-form-input">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Upload Foto Produk</label>
                        <input type="file" name="image" accept="image/*" class="pos-form-input">
                        <p class="mt-1 text-xs text-slate-500">Upload JPG, PNG, atau WEBP. Maksimal 3 MB.</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">URL Foto Produk</label>
                        <input type="text" name="image_url" value="{{ old('image_url', $editing?->meta['image_url'] ?? '') }}" placeholder="https://example.com/produk.jpg" class="pos-form-input">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Kategori</label>
                        <select name="category_id" class="pos-form-input" @disabled(!empty($categoryTableMissing))>
                            <option value="">{{ !empty($categoryTableMissing) ? 'Kategori belum tersedia' : 'Pilih kategori' }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $editing?->category_id) == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Harga Beli</label>
                        <input type="number" name="cost_price" value="{{ old('cost_price', $editing?->cost_price) }}" class="pos-form-input">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Harga Jual</label>
                        <input type="number" name="base_price" value="{{ old('base_price', $editing?->base_price) }}" class="pos-form-input">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Cabang Default</label>
                        <select name="branch_id" class="pos-form-input">
                            <option value="">Tidak ada</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @selected(old('branch_id', $editing?->branches?->first()?->branch_id) == $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Stok Awal Cabang</label>
                        <input type="number" name="stock" value="{{ old('stock', $editing?->branches?->first()?->stock) }}" class="pos-form-input">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Harga Jual Cabang</label>
                        <input type="number" name="selling_price" value="{{ old('selling_price', $editing?->branches?->first()?->selling_price) }}" class="pos-form-input">
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Deskripsi</label>
                    <textarea name="description" rows="4" class="pos-form-input">{{ old('description', $editing?->description) }}</textarea>
                </div>

                <div class="rounded-[24px] border border-slate-200 bg-slate-50 p-4">
                    <div class="text-sm font-semibold text-slate-900">Preview gambar</div>
                    <div class="mt-3 flex items-center gap-4">
                        <img src="{{ $editing?->image_url ?? ($showing?->image_url ?? (new \App\Models\Product(['name' => 'Preview']))->image_url) }}" alt="Preview produk" class="h-20 w-20 rounded-3xl object-cover shadow-sm">
                        <div class="text-sm text-slate-500">Gambar yang diupload atau URL yang diisi akan langsung dipakai di menu kasir.</div>
                    </div>
                </div>

                <div class="pos-modal-actions">
                    <button type="button" data-modal-close class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300">
                        Tutup
                    </button>
                    <button type="submit" class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        {{ $editing ? 'Perbarui Produk' : 'Simpan Produk' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if ($showing)
        <div id="product-show-modal" data-modal data-modal-auto-open="{{ $productShowAutoOpen ? 'true' : 'false' }}" class="pos-modal-backdrop">
            <div class="pos-modal-panel">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-950">Detail Produk</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ $showing->name }}</p>
                    </div>
                    <a href="{{ route('products.index') }}" class="pos-modal-close">×</a>
                </div>

                <div class="mt-6 grid gap-5 sm:grid-cols-[180px_1fr]">
                    <img src="{{ $showing->image_url }}" alt="{{ $showing->name }}" class="h-44 w-full rounded-[28px] object-cover shadow-sm">
                    <div class="space-y-4">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-[22px] bg-slate-50 p-4">
                                <div class="text-xs uppercase tracking-[0.18em] text-slate-400">SKU</div>
                                <div class="mt-2 text-sm font-semibold text-slate-950">{{ $showing->sku }}</div>
                            </div>
                            <div class="rounded-[22px] bg-slate-50 p-4">
                                <div class="text-xs uppercase tracking-[0.18em] text-slate-400">Barcode</div>
                                <div class="mt-2 text-sm font-semibold text-slate-950">{{ $showing->barcode ?: '-' }}</div>
                            </div>
                            <div class="rounded-[22px] bg-slate-50 p-4">
                                <div class="text-xs uppercase tracking-[0.18em] text-slate-400">Harga Beli</div>
                                <div class="mt-2 text-sm font-semibold text-slate-950">Rp {{ number_format($showing->cost_price, 0, ',', '.') }}</div>
                            </div>
                            <div class="rounded-[22px] bg-slate-50 p-4">
                                <div class="text-xs uppercase tracking-[0.18em] text-slate-400">Harga Jual</div>
                                <div class="mt-2 text-sm font-semibold text-slate-950">Rp {{ number_format($showing->base_price, 0, ',', '.') }}</div>
                            </div>
                        </div>

                        <div class="rounded-[24px] border border-slate-200 p-4">
                            <div class="text-sm font-semibold text-slate-900">Stok per cabang</div>
                            <div class="mt-3 space-y-2 text-sm text-slate-600">
                                @forelse ($showing->branches as $branch)
                                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                                        <span>{{ $branch->branch->name }}</span>
                                        <span class="font-semibold text-slate-950">{{ number_format($branch->stock, 0, ',', '.') }}</span>
                                    </div>
                                @empty
                                    <div class="rounded-2xl bg-slate-50 px-4 py-3">Belum ada stok cabang.</div>
                                @endforelse
                            </div>
                        </div>

                        <div class="rounded-[24px] border border-slate-200 p-4">
                            <div class="text-sm font-semibold text-slate-900">Deskripsi</div>
                            <div class="mt-2 text-sm leading-6 text-slate-600">{{ $showing->description ?: 'Belum ada deskripsi produk.' }}</div>
                        </div>
                    </div>
                </div>

                <div class="pos-modal-actions">
                    <a href="{{ route('products.index') }}" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300">
                        Tutup
                    </a>
                    <a href="{{ route('products.edit', $showing) }}" class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Edit Produk
                    </a>
                </div>
            </div>
        </div>
    @endif
</x-layouts.app>
