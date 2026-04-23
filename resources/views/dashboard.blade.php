<x-layouts.app :title="'Dashboard POS 2026'">
    <div class="space-y-6">
        <section class="rounded-[32px] border border-white/70 bg-white/82 p-5 shadow-[0_18px_55px_rgba(15,23,42,0.08)] backdrop-blur sm:p-6">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-600">Selamat datang, Administrator Pusat</p>
                    <h2 class="mt-1 text-3xl font-semibold tracking-tight text-slate-950">Ringkasan operasional hari ini</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Pantau penjualan, profit FIFO, aktivitas PPOB, loyalty pelanggan, dan status stok multi-cabang dalam satu tampilan.</p>
                </div>
                <div class="rounded-[26px] border border-slate-200 bg-slate-50 px-5 py-4 text-sm text-slate-600">
                    <div class="font-semibold text-slate-900">{{ $todayLabel }}</div>
                    <div class="mt-1">Periode analitik: {{ $periodLabel }}</div>
                </div>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <article class="pos-kpi-card bg-gradient-to-br from-blue-600 via-indigo-500 to-cyan-400 text-white">
                    <div class="pos-kpi-label text-white/75">Penjualan Kotor</div>
                    <div class="mt-3 text-3xl font-semibold">Rp {{ number_format($grossSalesToday, 0, ',', '.') }}</div>
                    <div class="mt-2 text-sm text-white/80">{{ $transactionCountToday }} transaksi hari ini</div>
                </article>

                <article class="pos-kpi-card bg-gradient-to-br from-cyan-500 via-teal-400 to-emerald-400 text-slate-950">
                    <div class="pos-kpi-label text-slate-800/70">Transaksi POS</div>
                    <div class="mt-3 text-3xl font-semibold">{{ $transactionCountToday }}</div>
                    <div class="mt-2 text-sm text-slate-800/80">Rata-rata Rp {{ number_format($averageTransaction, 0, ',', '.') }}</div>
                </article>

                <article class="pos-kpi-card bg-gradient-to-br from-emerald-500 via-green-400 to-lime-300 text-slate-950">
                    <div class="pos-kpi-label text-slate-800/70">PPOB Sales</div>
                    <div class="mt-3 text-3xl font-semibold">Rp {{ number_format($ppobSalesToday, 0, ',', '.') }}</div>
                    <div class="mt-2 text-sm text-slate-800/80">{{ $pendingPpobToday }} transaksi perlu tindak lanjut</div>
                </article>

                <article class="pos-kpi-card bg-gradient-to-br from-amber-400 via-orange-400 to-rose-400 text-slate-950">
                    <div class="pos-kpi-label text-slate-800/70">Pending PPOB</div>
                    <div class="mt-3 text-3xl font-semibold">{{ $pendingPpobToday }}</div>
                    <div class="mt-2 text-sm text-slate-800/80">Monitoring status provider realtime</div>
                </article>
            </div>
        </section>

        <section class="grid gap-4 xl:grid-cols-4">
            <article class="pos-panel xl:col-span-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-950">Analisis keuntungan hari ini</h3>
                        <p class="mt-1 text-sm text-slate-500">Simulasi margin dari transaksi yang sudah tercatat dengan referensi batch FIFO.</p>
                    </div>
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">Live snapshot</span>
                </div>

                <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-[24px] bg-emerald-50 p-5 ring-1 ring-emerald-100">
                        <div class="text-xs font-semibold uppercase tracking-[0.22em] text-emerald-700">Total Profit</div>
                        <div class="mt-3 text-2xl font-semibold text-emerald-950">Rp {{ number_format($profitToday, 0, ',', '.') }}</div>
                    </div>
                    <div class="rounded-[24px] bg-blue-50 p-5 ring-1 ring-blue-100">
                        <div class="text-xs font-semibold uppercase tracking-[0.22em] text-blue-700">HPP FIFO</div>
                        <div class="mt-3 text-2xl font-semibold text-blue-950">Rp {{ number_format($cogsToday, 0, ',', '.') }}</div>
                    </div>
                    <div class="rounded-[24px] bg-amber-50 p-5 ring-1 ring-amber-100">
                        <div class="text-xs font-semibold uppercase tracking-[0.22em] text-amber-700">Margin</div>
                        <div class="mt-3 text-2xl font-semibold text-amber-950">{{ number_format($marginToday, 1) }}%</div>
                    </div>
                    <div class="rounded-[24px] bg-fuchsia-50 p-5 ring-1 ring-fuchsia-100">
                        <div class="text-xs font-semibold uppercase tracking-[0.22em] text-fuchsia-700">Loyalty Earned</div>
                        <div class="mt-3 text-2xl font-semibold text-fuchsia-950">{{ number_format($loyaltyPointsIssued, 0, ',', '.') }} poin</div>
                    </div>
                </div>
            </article>

            <article class="pos-panel">
                <h3 class="text-lg font-semibold text-slate-950">Status operasional</h3>
                <div class="mt-5 space-y-3">
                    <div class="flex items-center justify-between rounded-[22px] bg-slate-50 px-4 py-4">
                        <span class="text-sm text-slate-500">Cabang aktif</span>
                        <span class="text-lg font-semibold text-slate-950">{{ $activeBranches }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-[22px] bg-slate-50 px-4 py-4">
                        <span class="text-sm text-slate-500">Produk aktif</span>
                        <span class="text-lg font-semibold text-slate-950">{{ $activeProducts }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-[22px] bg-slate-50 px-4 py-4">
                        <span class="text-sm text-slate-500">Shift terbuka</span>
                        <span class="text-lg font-semibold text-slate-950">{{ $openShifts }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-[22px] bg-rose-50 px-4 py-4 ring-1 ring-rose-100">
                        <span class="text-sm text-rose-700">Stok menipis</span>
                        <span class="text-lg font-semibold text-rose-950">{{ $lowStockItems }}</span>
                    </div>
                </div>
            </article>
        </section>

        <section class="grid gap-4 xl:grid-cols-[1.6fr_1fr]">
            <article class="pos-panel">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-950">Performa cabang periode ini</h3>
                        <p class="mt-1 text-sm text-slate-500">Peringkat cabang berdasarkan total penjualan yang tercatat di sistem.</p>
                    </div>
                    <a href="{{ route('branches.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Lihat cabang</a>
                </div>

                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    @forelse ($branchPerformance as $index => $branch)
                        <div class="rounded-[26px] border border-slate-200 bg-slate-50 p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-sm font-semibold text-slate-950">{{ $branch->name }}</div>
                                    <div class="mt-1 text-sm text-slate-500">{{ $branch->code }} | {{ $branch->period_transactions }} transaksi</div>
                                </div>
                                <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-500">#{{ $index + 1 }}</span>
                            </div>
                            <div class="mt-4 text-2xl font-semibold text-slate-950">Rp {{ number_format((float) $branch->period_total, 0, ',', '.') }}</div>
                            <div class="mt-4 h-2 rounded-full bg-slate-200">
                                @php
                                    $maxTotal = max(1, (float) $branchPerformance->max('period_total'));
                                    $width = min(100, (((float) $branch->period_total) / $maxTotal) * 100);
                                @endphp
                                <div class="h-2 rounded-full bg-gradient-to-r from-blue-600 to-cyan-400" style="width: {{ $width }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="md:col-span-2 rounded-[26px] border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm text-slate-500">
                            Belum ada data penjualan per cabang untuk periode ini.
                        </div>
                    @endforelse
                </div>
            </article>

            <article class="pos-panel">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-950">Stok menipis</h3>
                        <p class="mt-1 text-sm text-slate-500">Prioritas restock dari data product branch.</p>
                    </div>
                    <div class="text-sm font-semibold text-slate-400">FIFO ready</div>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse ($stockAlerts as $alert)
                        <div class="rounded-[22px] border border-slate-200 bg-white px-4 py-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="font-semibold text-slate-950">{{ $alert->product?->name ?? 'Produk' }}</div>
                                    <div class="mt-1 text-sm text-slate-500">{{ $alert->branch?->name ?? 'Cabang' }}</div>
                                </div>
                                <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700">{{ number_format((float) $alert->stock, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[22px] border border-dashed border-emerald-200 bg-emerald-50 px-4 py-6 text-sm text-emerald-800">
                            Semua stok dalam kondisi aman.
                        </div>
                    @endforelse
                </div>
            </article>
        </section>

        <section class="grid gap-4 xl:grid-cols-[1.4fr_1fr]">
            <article class="pos-panel">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-950">Transaksi terbaru</h3>
                        <p class="mt-1 text-sm text-slate-500">Aktivitas penjualan terbaru dari seluruh cabang.</p>
                    </div>
                    <a href="{{ route('sales.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Lihat semua</a>
                </div>

                <div class="mt-5 overflow-hidden rounded-[26px] border border-slate-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.18em] text-slate-400">
                                <tr>
                                    <th class="px-4 py-4">Invoice</th>
                                    <th class="px-4 py-4">Cabang</th>
                                    <th class="px-4 py-4">Pelanggan</th>
                                    <th class="px-4 py-4">Total</th>
                                    <th class="px-4 py-4">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse ($recentSales as $sale)
                                    <tr>
                                        <td class="px-4 py-4 font-semibold text-blue-600">{{ $sale->invoice }}</td>
                                        <td class="px-4 py-4 text-slate-600">{{ $sale->branch?->name ?? '-' }}</td>
                                        <td class="px-4 py-4 text-slate-600">{{ $sale->customer?->name ?? 'Umum' }}</td>
                                        <td class="px-4 py-4 font-semibold text-slate-950">Rp {{ number_format((float) $sale->total, 0, ',', '.') }}</td>
                                        <td class="px-4 py-4">
                                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">{{ ucfirst($sale->status) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada transaksi yang tercatat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </article>

            <div class="space-y-4">
                <article class="pos-panel">
                    <h3 class="text-lg font-semibold text-slate-950">Produk terlaris</h3>
                    <div class="mt-5 space-y-4">
                        @forelse ($topProducts as $item)
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="font-semibold text-slate-950">{{ $item->product?->name ?? 'Produk tanpa nama' }}</div>
                                    <div class="mt-1 text-sm text-slate-500">{{ number_format((float) $item->quantity_sold, 0, ',', '.') }} terjual</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold text-emerald-700">Rp {{ number_format((float) $item->revenue, 0, ',', '.') }}</div>
                                    <div class="mt-1 text-sm text-slate-400">Profit Rp {{ number_format(((float) $item->revenue) - ((float) $item->cogs), 0, ',', '.') }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-slate-500">Belum ada data produk terjual.</div>
                        @endforelse
                    </div>
                </article>

                <article class="pos-panel bg-slate-950 text-white">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold">Asset inventaris</h3>
                        <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-white/80">FIFO batches</span>
                    </div>
                    <div class="mt-5 text-3xl font-semibold">Rp {{ number_format($stockValue, 0, ',', '.') }}</div>
                    <div class="mt-3 text-sm text-slate-300">Estimasi nilai stok berdasarkan total batch pembelian yang masuk ke sistem.</div>
                </article>
            </div>
        </section>

        <section class="grid gap-4 xl:grid-cols-[1fr_1.1fr]">
            <article class="pos-panel">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-950">Aktivitas PPOB terbaru</h3>
                        <p class="mt-1 text-sm text-slate-500">Pantau provider, produk, dan status transaksi digital.</p>
                    </div>
                    <a href="{{ route('ppob.transactions.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Buka modul</a>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse ($recentPpobTransactions as $transaction)
                        <div class="rounded-[22px] border border-slate-200 bg-white px-4 py-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="font-semibold text-slate-950">{{ $transaction->product?->name ?? 'Produk PPOB' }}</div>
                                    <div class="mt-1 text-sm text-slate-500">{{ $transaction->provider?->name ?? 'Provider' }} | {{ $transaction->branch?->name ?? 'Cabang' }}</div>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $transaction->status === 'success' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ strtoupper($transaction->status) }}
                                </span>
                            </div>
                            <div class="mt-3 text-sm font-semibold text-slate-950">Rp {{ number_format((float) $transaction->amount, 0, ',', '.') }}</div>
                        </div>
                    @empty
                        <div class="rounded-[22px] border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-sm text-slate-500">
                            Belum ada transaksi PPOB yang tercatat.
                        </div>
                    @endforelse
                </div>
            </article>

            <article class="pos-panel">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-950">Roadmap modul inti</h3>
                        <p class="mt-1 text-sm text-slate-500">Checklist fitur yang sudah terwakili oleh fondasi aplikasi.</p>
                    </div>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Modular</span>
                </div>

                <div class="mt-5 grid gap-3">
                    @foreach ([
                        'Manajemen cabang dengan isolasi stok dan harga per branch',
                        'FIFO purchase batches untuk perhitungan HPP dan profit',
                        'POS engine untuk penjualan, transaksi, dan histori',
                        'PPOB provider, katalog produk, dan transaksi',
                        'CRM pelanggan serta histori poin loyalty',
                        'Dynamic settings untuk preferensi operasional toko',
                    ] as $feature)
                        <div class="flex items-start gap-3 rounded-[22px] bg-slate-50 px-4 py-4">
                            <span class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-xs font-bold text-white">OK</span>
                            <span class="text-sm leading-6 text-slate-700">{{ $feature }}</span>
                        </div>
                    @endforeach
                </div>
            </article>
        </section>
    </div>
</x-layouts.app>
