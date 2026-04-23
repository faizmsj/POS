<x-layouts.app :title="'Transaksi PPOB'">
    <div class="space-y-6">
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

        <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
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
                        </div>
                    @empty
                        <div class="rounded-[24px] border border-dashed border-slate-300 bg-slate-50 px-5 py-10 text-center text-sm text-slate-500">
                            Belum ada transaksi PPOB yang tercatat.
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="pos-panel">
                <h2 class="text-lg font-semibold text-slate-950">Buat transaksi PPOB</h2>
                <p class="mt-1 text-sm text-slate-500">Pilih cabang dan produk digital yang ingin diproses.</p>

                <form action="{{ route('ppob.transactions.store') }}" method="POST" class="mt-5 space-y-4">
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
                        <label class="mb-2 block text-sm font-medium text-slate-700">Produk PPOB</label>
                        <select name="product_id" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" required>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->provider?->name }} - {{ $product->name }} (Rp {{ number_format($product->price, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Proses Transaksi
                    </button>
                </form>
            </section>
        </div>
    </div>
</x-layouts.app>
