<x-layouts.app :title="'Transaksi PPOB'">
    @php
        $showing = $transactions->firstWhere('id', (int) request('show'));
        $formAutoOpen = old('_modal') === 'ppob-transaction-form-modal';
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex flex-col gap-1">
                <h1 class="text-2xl font-semibold text-slate-950">Transaksi PPOB</h1>
                <p class="text-sm text-slate-500">Pantau transaksi digital dan buat transaksi baru langsung dari modal.</p>
            </div>
            <button type="button" data-modal-open="#ppob-transaction-form-modal" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                Buat Transaksi
            </button>
        </div>

        <section class="grid gap-4 md:grid-cols-4">
            <article class="pos-panel">
                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Transaksi</div>
                <div class="mt-3 text-3xl font-semibold text-slate-950">{{ $transactionSummary['count'] }}</div>
            </article>
            <article class="pos-panel">
                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Sukses</div>
                <div class="mt-3 text-3xl font-semibold text-emerald-700">{{ $transactionSummary['success'] }}</div>
            </article>
            <article class="pos-panel">
                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Pending</div>
                <div class="mt-3 text-3xl font-semibold text-amber-600">{{ $transactionSummary['pending'] }}</div>
            </article>
            <article class="pos-panel">
                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Omzet</div>
                <div class="mt-3 text-3xl font-semibold text-slate-950">Rp {{ number_format($transactionSummary['amount'], 0, ',', '.') }}</div>
            </article>
        </section>

        <section class="pos-panel">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-950">Riwayat transaksi PPOB</h2>
                    <p class="mt-1 text-sm text-slate-500">Pantau provider, produk, cabang, dan status transaksi digital.</p>
                </div>
            </div>

            <div class="mt-5 space-y-4">
                @forelse ($transactions as $transaction)
                    <div class="rounded-[24px] border border-slate-200 bg-white p-5">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <div class="text-sm font-semibold text-blue-600">{{ $transaction->external_reference ?? $transaction->id }}</div>
                                <div class="mt-1 text-lg font-semibold text-slate-950">{{ $transaction->product?->name ?? 'Produk PPOB' }}</div>
                                <div class="mt-1 text-sm text-slate-500">{{ $transaction->provider?->name ?? 'Provider' }} | {{ $transaction->branch?->name ?? 'Cabang' }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-semibold text-slate-950">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</div>
                                <div class="mt-2 inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ in_array($transaction->status, ['completed', 'success']) ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ strtoupper($transaction->status) }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-3">
                            <a href="{{ route('ppob.transactions.index', ['show' => $transaction->id]) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-slate-300">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="rounded-[24px] border border-dashed border-slate-300 bg-slate-50 px-5 py-10 text-center text-sm text-slate-500">
                        Belum ada transaksi PPOB yang tercatat.
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <div id="ppob-transaction-form-modal" data-modal data-modal-auto-open="{{ $formAutoOpen ? 'true' : 'false' }}" class="pos-modal-backdrop">
        <div class="pos-modal-panel">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-slate-950">Buat transaksi PPOB</h2>
                    <p class="mt-1 text-sm text-slate-500">Pilih cabang dan produk digital yang ingin diproses.</p>
                </div>
                <button type="button" data-modal-close class="pos-modal-close">×</button>
            </div>

            <form action="{{ route('ppob.transactions.store') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="_modal" value="ppob-transaction-form-modal">
                @if (auth()->user()->hasRole('cashier'))
                    <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Cabang</label>
                        <div class="pos-form-input bg-slate-50 text-slate-700">
                            {{ auth()->user()->branch?->name ?? 'Cabang kasir' }}
                        </div>
                    </div>
                @else
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Cabang</label>
                        <select name="branch_id" class="pos-form-input" required>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @selected((string) old('branch_id') === (string) $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Produk PPOB</label>
                    <select name="product_id" class="pos-form-input" required @disabled($products->isEmpty())>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected((string) old('product_id') === (string) $product->id)>{{ $product->provider?->name }} - {{ $product->name }} (Rp {{ number_format($product->price, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="pos-modal-actions">
                    <button type="button" data-modal-close class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Tutup</button>
                    <button type="submit" class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:bg-slate-300" @disabled($products->isEmpty())>
                        Proses Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if ($showing)
        <div data-modal data-modal-auto-open="true" class="pos-modal-backdrop">
            <div class="pos-modal-panel">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-950">Detail Transaksi PPOB</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ $showing->product?->name ?? 'Produk PPOB' }}</p>
                    </div>
                    <a href="{{ route('ppob.transactions.index') }}" class="pos-modal-close">×</a>
                </div>

                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[22px] bg-slate-50 p-4"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Referensi</div><div class="mt-2 font-semibold text-slate-950">{{ $showing->external_reference ?? $showing->id }}</div></div>
                    <div class="rounded-[22px] bg-slate-50 p-4"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Status</div><div class="mt-2 font-semibold text-slate-950">{{ strtoupper($showing->status) }}</div></div>
                    <div class="rounded-[22px] bg-slate-50 p-4"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Provider</div><div class="mt-2 font-semibold text-slate-950">{{ $showing->provider?->name ?? 'Provider' }}</div></div>
                    <div class="rounded-[22px] bg-slate-50 p-4"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Cabang</div><div class="mt-2 font-semibold text-slate-950">{{ $showing->branch?->name ?? 'Cabang' }}</div></div>
                    <div class="rounded-[22px] bg-slate-50 p-4 sm:col-span-2"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Nominal</div><div class="mt-2 font-semibold text-slate-950">Rp {{ number_format($showing->amount, 0, ',', '.') }}</div></div>
                </div>

                <div class="pos-modal-actions">
                    <a href="{{ route('ppob.transactions.index') }}" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Tutup</a>
                </div>
            </div>
        </div>
    @endif
</x-layouts.app>
