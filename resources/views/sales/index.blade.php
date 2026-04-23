<x-layouts.app>
    <div class="space-y-4 sm:space-y-6">
        <div class="flex flex-col gap-1">
            <h1 class="text-xl sm:text-2xl font-semibold">Manajemen Penjualan</h1>
            <p class="text-xs sm:text-sm text-slate-500">Kelola transaksi penjualan multi-cabang dan riwayat kasir.</p>
        </div>

        <section class="bg-white rounded-2xl sm:rounded-3xl shadow-sm p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <h2 class="text-base sm:text-lg font-semibold">Riwayat Penjualan</h2>
                <a href="{{ route('sales.create') }}"
                    class="w-full sm:w-auto text-center rounded-2xl bg-[#111827] px-4 sm:px-5 py-2.5 sm:py-3 text-xs sm:text-sm font-semibold text-white hover:bg-[#0f172a] transition">
                    Buat Penjualan Baru
                </a>
            </div>

            <!-- Desktop Table -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-y-2 text-sm">
                    <thead class="text-xs text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="pb-3 px-2">No. Faktur</th>
                            <th class="pb-3 px-2">Cabang</th>
                            <th class="pb-3 px-2">Pelanggan</th>
                            <th class="pb-3 px-2">Total</th>
                            <th class="pb-3 px-2">Tanggal</th>
                            <th class="pb-3 px-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sales as $sale)
                            <tr class="bg-slate-50 rounded-2xl">
                                <td class="py-3 px-2">{{ $sale->invoice }}</td>
                                <td class="py-3 px-2">{{ $sale->branch->name }}</td>
                                <td class="py-3 px-2">{{ $sale->customer?->name ?? 'Umum' }}</td>
                                <td class="py-3 px-2">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                <td class="py-3 px-2">{{ optional($sale->created_at)->format('d M Y') }}</td>
                                <td class="py-3 px-2 text-right">
                                    <a href="{{ route('sales.receipt', $sale) }}" target="_blank" class="text-sm font-semibold text-blue-600 hover:underline">Cetak Nota</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="space-y-3 lg:hidden">
                @foreach ($sales as $sale)
                    <div class="pos-stack-card">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-semibold text-sm text-slate-950">{{ $sale->invoice }}</h3>
                                <p class="mt-1 text-xs text-slate-500">{{ optional($sale->created_at)->format('d M Y H:i') }}</p>
                            </div>
                            <div class="text-sm font-semibold text-slate-950">Rp {{ number_format($sale->total, 0, ',', '.') }}</div>
                        </div>
                        <div class="mt-3 grid gap-2 text-xs text-slate-500 sm:grid-cols-2">
                            <p>Cabang: {{ $sale->branch->name }}</p>
                            <p>Pelanggan: {{ $sale->customer?->name ?? 'Umum' }}</p>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('sales.receipt', $sale) }}" target="_blank" class="text-sm font-semibold text-blue-600">Cetak Nota</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</x-layouts.app>
