<x-layouts.app :title="'Shift Kasir'">
    <div class="flex flex-col gap-6">
        <div class="flex flex-col gap-1">
            <h1 class="text-2xl font-semibold">Shift Kasir</h1>
            <p class="text-sm text-slate-500">Buka dan tutup shift kasir serta pantau saldo awal, penjualan, dan rekonsiliasi kas.</p>
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

        <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
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

                                @if ($shift->status === 'open')
                                    <form action="{{ route('cashier-shifts.update', $shift) }}" method="POST" class="w-full rounded-[24px] border border-slate-200 bg-white p-4 lg:w-[320px]">
                                        @csrf
                                        @method('PATCH')
                                        <div class="text-sm font-semibold text-slate-950">Tutup Shift</div>
                                        <div class="mt-3 space-y-3">
                                            <div>
                                                <label class="block text-sm font-medium text-slate-700">Saldo Akhir</label>
                                                <input type="number" step="0.01" min="0" name="closing_balance" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" required>
                                            </div>
                                            <div class="grid gap-3 sm:grid-cols-2">
                                                <div>
                                                    <label class="block text-sm font-medium text-slate-700">Cash In</label>
                                                    <input type="number" step="0.01" min="0" name="cash_in" value="{{ (float) $shift->cash_in }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-slate-700">Cash Out</label>
                                                    <input type="number" step="0.01" min="0" name="cash_out" value="{{ (float) $shift->cash_out }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3">
                                                </div>
                                            </div>
                                            <button type="submit" class="w-full rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white hover:bg-slate-800">Simpan Penutupan</button>
                                        </div>
                                    </form>
                                @else
                                    <div class="rounded-[24px] border border-slate-200 bg-white px-4 py-4 text-sm text-slate-500 lg:w-[320px]">
                                        Shift ditutup pada {{ optional($shift->ended_at)->format('d M Y H:i') ?? '-' }}.
                                        <div class="mt-2 font-semibold text-slate-950">Saldo akhir Rp {{ number_format((float) $shift->closing_balance, 0, ',', '.') }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[28px] border border-dashed border-slate-300 bg-slate-50 px-5 py-10 text-center text-sm text-slate-500">
                            Belum ada shift kasir yang tercatat.
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-3xl bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold">Buka Shift Baru</h2>
                <p class="mt-1 text-sm text-slate-500">Gunakan form ini untuk memulai shift kasir baru dengan saldo awal yang terukur.</p>

                <form action="{{ route('cashier-shifts.store') }}" method="POST" class="mt-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Kasir</label>
                        <select name="user_id" class="mt-2 w-full rounded-3xl border border-slate-200 px-4 py-3" required>
                            <option value="">Pilih kasir</option>
                            @foreach ($cashiers as $cashier)
                                <option value="{{ $cashier->id }}" @selected((string) old('user_id') === (string) $cashier->id)>{{ $cashier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Cabang</label>
                        <select name="branch_id" class="mt-2 w-full rounded-3xl border border-slate-200 px-4 py-3" required>
                            <option value="">Pilih cabang</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @selected((string) old('branch_id') === (string) $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Saldo Awal</label>
                        <input type="number" step="0.01" min="0" name="opening_balance" value="{{ old('opening_balance', 0) }}" class="mt-2 w-full rounded-3xl border border-slate-200 px-4 py-3" required>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Cash In</label>
                            <input type="number" step="0.01" min="0" name="cash_in" value="{{ old('cash_in', 0) }}" class="mt-2 w-full rounded-3xl border border-slate-200 px-4 py-3">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Cash Out</label>
                            <input type="number" step="0.01" min="0" name="cash_out" value="{{ old('cash_out', 0) }}" class="mt-2 w-full rounded-3xl border border-slate-200 px-4 py-3">
                        </div>
                    </div>

                    <button type="submit" class="w-full rounded-3xl bg-[#111827] px-5 py-3 text-sm font-semibold text-white hover:bg-[#0f172a]">
                        Buka Shift
                    </button>
                </form>
            </section>
        </div>
    </div>
</x-layouts.app>
