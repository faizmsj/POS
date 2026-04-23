<x-layouts.app :title="'Pembelian & FIFO'">
    <div class="space-y-6">
        <section class="grid gap-4 md:grid-cols-3">
            <article class="pos-panel">
                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Total Pembelian</div>
                <div class="mt-3 text-3xl font-semibold text-slate-950">{{ $purchaseSummary['count'] }}</div>
                <div class="mt-2 text-sm text-slate-500">Transaksi tercatat</div>
            </article>
            <article class="pos-panel">
                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Nilai Pembelian</div>
                <div class="mt-3 text-3xl font-semibold text-slate-950">Rp {{ number_format($purchaseSummary['total'], 0, ',', '.') }}</div>
                <div class="mt-2 text-sm text-slate-500">Akumulasi seluruh periode</div>
            </article>
            <article class="pos-panel">
                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Hari Ini</div>
                <div class="mt-3 text-3xl font-semibold text-slate-950">Rp {{ number_format($purchaseSummary['today'], 0, ',', '.') }}</div>
                <div class="mt-2 text-sm text-slate-500">Pembelian dengan batch FIFO</div>
            </article>
        </section>

        <div class="grid gap-6 xl:grid-cols-[1.3fr_0.9fr]">
            <section class="pos-panel">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-950">Riwayat pembelian</h2>
                        <p class="mt-1 text-sm text-slate-500">Setiap transaksi otomatis membentuk batch FIFO untuk inventaris.</p>
                    </div>
                </div>

                <div class="mt-5 space-y-4">
                    @forelse ($purchases as $purchase)
                        <div class="rounded-[24px] border border-slate-200 bg-white p-5">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <div class="text-sm font-semibold text-blue-600">{{ $purchase->reference }}</div>
                                    <div class="mt-1 text-lg font-semibold text-slate-950">{{ $purchase->branch->name }}</div>
                                    <div class="mt-1 text-sm text-slate-500">{{ $purchase->supplier?->name ?? 'Supplier umum' }} | {{ $purchase->purchase_date->format('d M Y') }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-semibold text-slate-950">Rp {{ number_format($purchase->total, 0, ',', '.') }}</div>
                                    <div class="mt-1 text-sm text-slate-400">{{ $purchase->batches->count() }} batch</div>
                                </div>
                            </div>

                            <div class="mt-4 rounded-[20px] bg-slate-50 px-4 py-4">
                                @foreach ($purchase->batches as $batch)
                                    <div class="flex items-center justify-between gap-3 text-sm {{ $loop->last ? '' : 'mb-3 border-b border-slate-200 pb-3' }}">
                                        <div>
                                            <div class="font-medium text-slate-900">{{ $batch->product?->name ?? 'Produk' }}</div>
                                            <div class="text-slate-500">{{ number_format($batch->quantity, 0, ',', '.') }} pcs x Rp {{ number_format($batch->unit_cost, 0, ',', '.') }}</div>
                                        </div>
                                        <div class="font-semibold text-slate-950">Rp {{ number_format($batch->total_cost, 0, ',', '.') }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[24px] border border-dashed border-slate-300 bg-slate-50 px-5 py-10 text-center text-sm text-slate-500">
                            Belum ada pembelian yang tercatat.
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="pos-panel">
                <h2 class="text-lg font-semibold text-slate-950">Catat pembelian baru</h2>
                <p class="mt-1 text-sm text-slate-500">Input ini akan menambah stok cabang dan membentuk batch FIFO.</p>

                <form action="{{ route('purchases.store') }}" method="POST" class="mt-5 space-y-4">
                    @csrf
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Cabang</label>
                        <select name="branch_id" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" required>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Supplier</label>
                        <select name="supplier_id" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">
                            <option value="">Umum</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Tanggal Pembelian</label>
                        <input type="date" name="purchase_date" value="{{ old('purchase_date', now()->toDateString()) }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" required>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Produk</label>
                        <select name="product_id" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" required>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Kuantitas</label>
                            <input type="number" step="1" name="quantity" value="{{ old('quantity', 1) }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" required>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Harga Per Unit</label>
                            <input type="number" step="0.01" name="unit_cost" value="{{ old('unit_cost', 0) }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" required>
                        </div>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Pajak</label>
                        <input type="number" step="0.01" name="tax" value="{{ old('tax', 0) }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Catatan</label>
                        <textarea name="notes" rows="3" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">{{ old('notes') }}</textarea>
                    </div>
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Simpan Pembelian
                    </button>
                </form>
            </section>
        </div>
    </div>
</x-layouts.app>
