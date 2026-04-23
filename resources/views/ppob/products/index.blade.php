<x-layouts.app :title="'Produk PPOB'">
    <div class="space-y-6">
        <section class="pos-panel">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-950">Katalog produk PPOB</h2>
                    <p class="mt-1 text-sm text-slate-500">Pulsa, data, e-money, voucher, dan produk digital lainnya.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach ($productCategories as $category)
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">{{ $category }}</span>
                    @endforeach
                </div>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-[1.25fr_0.75fr]">
            <section class="pos-panel">
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @forelse ($products as $product)
                        <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-[0_12px_30px_rgba(15,23,42,0.06)]">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">{{ $product->category ?: 'Umum' }}</div>
                                    <div class="mt-3 text-base font-semibold text-slate-950">{{ $product->name }}</div>
                                    <div class="mt-1 text-sm text-slate-500">{{ $product->provider?->name }} | {{ $product->code }}</div>
                                </div>
                                <form action="{{ route('ppob.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Hapus produk PPOB ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-semibold text-rose-500 hover:text-rose-600">Hapus</button>
                                </form>
                            </div>
                            <div class="mt-5 text-lg font-semibold text-slate-950">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                            <div class="mt-1 text-sm text-slate-400">Modal Rp {{ number_format($product->cost, 0, ',', '.') }} | Margin {{ number_format($product->margin_percent, 1) }}%</div>
                        </div>
                    @empty
                        <div class="sm:col-span-2 xl:col-span-3 rounded-[24px] border border-dashed border-slate-300 bg-slate-50 px-5 py-10 text-center text-sm text-slate-500">
                            Belum ada produk PPOB.
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="pos-panel">
                <h2 class="text-lg font-semibold text-slate-950">Tambah produk PPOB</h2>
                <form action="{{ route('ppob.products.store') }}" method="POST" class="mt-5 space-y-4">
                    @csrf
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Provider</label>
                        <select name="provider_id" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" required>
                            @foreach ($providers as $provider)
                                <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Kode Produk</label>
                        <input type="text" name="code" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" required>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Nama Produk</label>
                        <input type="text" name="name" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" required>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Kategori</label>
                        <input type="text" name="category" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" placeholder="Data, Pulsa, E-Money">
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Harga Beli</label>
                            <input type="number" step="0.01" name="cost" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" required>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Harga Jual</label>
                            <input type="number" step="0.01" name="price" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" required>
                        </div>
                    </div>
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Simpan Produk PPOB
                    </button>
                </form>
            </section>
        </div>
    </div>
</x-layouts.app>
