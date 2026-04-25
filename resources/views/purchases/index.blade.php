<x-layouts.app :title="'Pembelian & FIFO'">
    @php
        $showing = $purchases->firstWhere('id', (int) request('show'));
        $formAutoOpen = old('_modal') === 'purchase-form-modal';
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex flex-col gap-1">
                <h1 class="text-2xl font-semibold text-slate-950">Pembelian & FIFO</h1>
                <p class="text-sm text-slate-500">Catat pembelian sebagai batch FIFO dan buka detail transaksi lewat modal.</p>
            </div>
            <button type="button" data-modal-open="#purchase-form-modal" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                Catat Pembelian
            </button>
        </div>

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

                        <div class="mt-4 flex flex-wrap gap-3">
                            <a href="{{ route('purchases.index', ['show' => $purchase->id]) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-slate-300">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="rounded-[24px] border border-dashed border-slate-300 bg-slate-50 px-5 py-10 text-center text-sm text-slate-500">
                        Belum ada pembelian yang tercatat.
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <div id="purchase-form-modal" data-modal data-modal-auto-open="{{ $formAutoOpen ? 'true' : 'false' }}" class="pos-modal-backdrop">
        <div class="pos-modal-panel">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-slate-950">Catat pembelian baru</h2>
                    <p class="mt-1 text-sm text-slate-500">Input ini akan menambah stok cabang dan membentuk batch FIFO.</p>
                </div>
                <button type="button" data-modal-close class="pos-modal-close">×</button>
            </div>

            <form action="{{ route('purchases.store') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="_modal" value="purchase-form-modal">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Cabang</label>
                    <select name="branch_id" class="pos-form-input" required>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" @selected((string) old('branch_id') === (string) $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Supplier</label>
                    <select name="supplier_id" class="pos-form-input">
                        <option value="">Umum</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected((string) old('supplier_id') === (string) $supplier->id)>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Tanggal Pembelian</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', now()->toDateString()) }}" class="pos-form-input" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Produk</label>
                    <select name="product_id" class="pos-form-input" required>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected((string) old('product_id') === (string) $product->id)>{{ $product->name }} ({{ $product->sku }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Kuantitas</label>
                        <input type="number" step="1" name="quantity" value="{{ old('quantity', 1) }}" class="pos-form-input" required>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Harga Per Unit</label>
                        <input type="number" step="0.01" name="unit_cost" value="{{ old('unit_cost', 0) }}" class="pos-form-input" required>
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Pajak</label>
                    <input type="number" step="0.01" name="tax" value="{{ old('tax', 0) }}" class="pos-form-input">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Catatan</label>
                    <textarea name="notes" rows="3" class="pos-form-input">{{ old('notes') }}</textarea>
                </div>
                <div class="pos-modal-actions">
                    <button type="button" data-modal-close class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Tutup</button>
                    <button type="submit" class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Simpan Pembelian</button>
                </div>
            </form>
        </div>
    </div>

    @if ($showing)
        <div data-modal data-modal-auto-open="true" class="pos-modal-backdrop">
            <div class="pos-modal-panel pos-modal-panel-lg">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-950">Detail Pembelian</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ $showing->reference }} - {{ $showing->branch->name }}</p>
                    </div>
                    <a href="{{ route('purchases.index') }}" class="pos-modal-close">×</a>
                </div>

                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[22px] bg-slate-50 p-4"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Supplier</div><div class="mt-2 font-semibold text-slate-950">{{ $showing->supplier?->name ?? 'Supplier umum' }}</div></div>
                    <div class="rounded-[22px] bg-slate-50 p-4"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Tanggal</div><div class="mt-2 font-semibold text-slate-950">{{ $showing->purchase_date->format('d M Y') }}</div></div>
                    <div class="rounded-[22px] bg-slate-50 p-4"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Subtotal</div><div class="mt-2 font-semibold text-slate-950">Rp {{ number_format($showing->subtotal, 0, ',', '.') }}</div></div>
                    <div class="rounded-[22px] bg-slate-50 p-4"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Total</div><div class="mt-2 font-semibold text-slate-950">Rp {{ number_format($showing->total, 0, ',', '.') }}</div></div>
                </div>

                <div class="mt-5 rounded-[24px] border border-slate-200 bg-slate-50 p-4">
                    <div class="text-sm font-semibold text-slate-900">Batch FIFO</div>
                    <div class="mt-4 space-y-3">
                        @foreach ($showing->batches as $batch)
                            <div class="flex items-center justify-between gap-3 rounded-2xl bg-white px-4 py-4 text-sm">
                                <div>
                                    <div class="font-medium text-slate-900">{{ $batch->product?->name ?? 'Produk' }}</div>
                                    <div class="text-slate-500">{{ number_format($batch->quantity, 0, ',', '.') }} pcs x Rp {{ number_format($batch->unit_cost, 0, ',', '.') }}</div>
                                </div>
                                <div class="font-semibold text-slate-950">Rp {{ number_format($batch->total_cost, 0, ',', '.') }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pos-modal-actions">
                    <a href="{{ route('purchases.index') }}" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Tutup</a>
                </div>
            </div>
        </div>
    @endif
</x-layouts.app>
