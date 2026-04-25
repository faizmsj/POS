<x-layouts.app :title="'Shift Kasir'">
    @php
        $formAutoOpen = old('_modal') === 'shift-create-modal';
    @endphp

    <div class="flex flex-col gap-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex flex-col gap-1">
                <h1 class="text-2xl font-semibold">Shift Kasir</h1>
                <p class="text-sm text-slate-500">Buka dan tutup shift kasir serta pantau saldo awal, penjualan, dan rekonsiliasi kas.</p>
            </div>
            <button type="button" data-modal-open="#shift-create-modal" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                Buka Shift Baru
            </button>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-[28px] bg-white p-5 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Shift Aktif</div>
                <div class="mt-3 text-3xl font-semibold text-slate-950">{{ $openCount }}</div>
            </div>
            <div class="rounded-[28px] bg-white p-5 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Tutup Hari Ini</div>
                <div class="mt-3 text-3xl font-semibold text-slate-950">{{ $closedTodayCount }}</div>
            </div>
            <div class="rounded-[28px] bg-white p-5 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Saldo Awal Aktif</div>
                <div class="mt-3 text-3xl font-semibold text-slate-950">Rp {{ number_format($openBalanceTotal, 0, ',', '.') }}</div>
            </div>
        </div>

        <section class="rounded-3xl bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold">Monitoring Shift</h2>
                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">Realtime operasional</span>
            </div>

            <div class="space-y-4">
                @forelse ($shifts as $shift)
                    <div class="rounded-[28px] border border-slate-200 bg-slate-50 p-5">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-lg font-semibold text-slate-950">{{ $shift->user?->name ?? 'Kasir' }}</h3>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $shift->status === 'open' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">
                                        {{ strtoupper($shift->status) }}
                                    </span>
                                </div>
                                <div class="mt-2 text-sm text-slate-500">{{ $shift->branch?->name ?? '-' }} | Mulai {{ optional($shift->started_at)->format('d M Y H:i') }}</div>
                                <div class="mt-4 grid gap-3 md:grid-cols-4">
                                    <div class="rounded-2xl bg-white px-4 py-3">
                                        <div class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Saldo Awal</div>
                                        <div class="mt-1 font-semibold text-slate-950">Rp {{ number_format((float) $shift->opening_balance, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="rounded-2xl bg-white px-4 py-3">
                                        <div class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Cash In</div>
                                        <div class="mt-1 font-semibold text-slate-950">Rp {{ number_format((float) $shift->cash_in, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="rounded-2xl bg-white px-4 py-3">
                                        <div class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Cash Out</div>
                                        <div class="mt-1 font-semibold text-slate-950">Rp {{ number_format((float) $shift->cash_out, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="rounded-2xl bg-white px-4 py-3">
                                        <div class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Penjualan</div>
                                        <div class="mt-1 font-semibold text-slate-950">Rp {{ number_format((float) ($shift->sales_sum_total ?? 0), 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col items-stretch gap-3 lg:w-[240px]">
                                <button type="button" data-modal-open="#shift-detail-{{ $shift->id }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300">
                                    Lihat Detail
                                </button>
                                @if ($shift->status === 'open')
                                    <button type="button" data-modal-open="#shift-close-{{ $shift->id }}" class="rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                        Tutup Shift
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-[28px] border border-dashed border-slate-300 bg-slate-50 px-5 py-10 text-center text-sm text-slate-500">
                        Belum ada shift kasir yang tercatat.
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <div id="shift-create-modal" data-modal data-modal-auto-open="{{ $formAutoOpen ? 'true' : 'false' }}" class="pos-modal-backdrop">
        <div class="pos-modal-panel">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-slate-950">Buka Shift Baru</h2>
                    <p class="mt-1 text-sm text-slate-500">Gunakan form ini untuk memulai shift kasir baru dengan saldo awal yang terukur.</p>
                </div>
                <button type="button" data-modal-close class="pos-modal-close">×</button>
            </div>

            <form action="{{ route('cashier-shifts.store') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="_modal" value="shift-create-modal">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kasir</label>
                    <select name="user_id" class="pos-form-input" required>
                        <option value="">Pilih kasir</option>
                        @foreach ($cashiers as $cashier)
                            <option value="{{ $cashier->id }}" @selected((string) old('user_id') === (string) $cashier->id)>{{ $cashier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Cabang</label>
                    <select name="branch_id" class="pos-form-input" required>
                        <option value="">Pilih cabang</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" @selected((string) old('branch_id') === (string) $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Saldo Awal</label>
                    <input type="number" step="0.01" min="0" name="opening_balance" value="{{ old('opening_balance', 0) }}" class="pos-form-input" required>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Cash In</label>
                        <input type="number" step="0.01" min="0" name="cash_in" value="{{ old('cash_in', 0) }}" class="pos-form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Cash Out</label>
                        <input type="number" step="0.01" min="0" name="cash_out" value="{{ old('cash_out', 0) }}" class="pos-form-input">
                    </div>
                </div>

                <div class="pos-modal-actions">
                    <button type="button" data-modal-close class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Tutup</button>
                    <button type="submit" class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Buka Shift</button>
                </div>
            </form>
        </div>
    </div>

    @foreach ($shifts as $shift)
        <div id="shift-detail-{{ $shift->id }}" data-modal class="pos-modal-backdrop">
            <div class="pos-modal-panel">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-950">Detail Shift</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ $shift->user?->name ?? 'Kasir' }} - {{ $shift->branch?->name ?? '-' }}</p>
                    </div>
                    <button type="button" data-modal-close class="pos-modal-close">×</button>
                </div>

                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[22px] bg-slate-50 p-4"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Status</div><div class="mt-2 font-semibold text-slate-950">{{ strtoupper($shift->status) }}</div></div>
                    <div class="rounded-[22px] bg-slate-50 p-4"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Mulai</div><div class="mt-2 font-semibold text-slate-950">{{ optional($shift->started_at)->format('d M Y H:i') }}</div></div>
                    <div class="rounded-[22px] bg-slate-50 p-4"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Saldo Awal</div><div class="mt-2 font-semibold text-slate-950">Rp {{ number_format((float) $shift->opening_balance, 0, ',', '.') }}</div></div>
                    <div class="rounded-[22px] bg-slate-50 p-4"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Penjualan</div><div class="mt-2 font-semibold text-slate-950">Rp {{ number_format((float) ($shift->sales_sum_total ?? 0), 0, ',', '.') }}</div></div>
                    <div class="rounded-[22px] bg-slate-50 p-4"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Cash In</div><div class="mt-2 font-semibold text-slate-950">Rp {{ number_format((float) $shift->cash_in, 0, ',', '.') }}</div></div>
                    <div class="rounded-[22px] bg-slate-50 p-4"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Cash Out</div><div class="mt-2 font-semibold text-slate-950">Rp {{ number_format((float) $shift->cash_out, 0, ',', '.') }}</div></div>
                    <div class="rounded-[22px] bg-slate-50 p-4 sm:col-span-2"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Saldo Akhir</div><div class="mt-2 font-semibold text-slate-950">{{ $shift->closing_balance !== null ? 'Rp '.number_format((float) $shift->closing_balance, 0, ',', '.') : '-' }}</div></div>
                </div>

                <div class="pos-modal-actions">
                    <button type="button" data-modal-close class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Tutup</button>
                    @if ($shift->status === 'open')
                        <button type="button" data-modal-open="#shift-close-{{ $shift->id }}" class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Tutup Shift</button>
                    @endif
                </div>
            </div>
        </div>

        @if ($shift->status === 'open')
            <div id="shift-close-{{ $shift->id }}" data-modal data-modal-auto-open="{{ old('_modal') === 'shift-close-'.$shift->id ? 'true' : 'false' }}" class="pos-modal-backdrop">
                <div class="pos-modal-panel">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-950">Tutup Shift</h2>
                            <p class="mt-1 text-sm text-slate-500">{{ $shift->user?->name ?? 'Kasir' }} - {{ $shift->branch?->name ?? '-' }}</p>
                        </div>
                        <button type="button" data-modal-close class="pos-modal-close">×</button>
                    </div>

                    <form action="{{ route('cashier-shifts.update', $shift) }}" method="POST" class="mt-6 space-y-4">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="_modal" value="shift-close-{{ $shift->id }}">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Saldo Akhir</label>
                            <input type="number" step="0.01" min="0" name="closing_balance" class="pos-form-input" required>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Cash In</label>
                                <input type="number" step="0.01" min="0" name="cash_in" value="{{ (float) $shift->cash_in }}" class="pos-form-input">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Cash Out</label>
                                <input type="number" step="0.01" min="0" name="cash_out" value="{{ (float) $shift->cash_out }}" class="pos-form-input">
                            </div>
                        </div>
                        <div class="pos-modal-actions">
                            <button type="button" data-modal-close class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Batal</button>
                            <button type="submit" class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Simpan Penutupan</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endforeach
</x-layouts.app>
