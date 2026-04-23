<x-layouts.app :title="'Pengaturan Aplikasi'">
    <div class="space-y-6">
        <section class="pos-panel">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-600">Dynamic Configuration</p>
                    <h2 class="mt-1 text-3xl font-semibold tracking-tight text-slate-950">Pengaturan aplikasi</h2>
                    <p class="mt-2 text-sm text-slate-500">Semua preferensi dasar toko, POS, PPOB, printer, dan loyalty dapat diubah tanpa menyentuh kode.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('settings.receipt-preview') }}" target="_blank" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Preview Nota
                    </a>
                    <a href="{{ route('labels.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-slate-300">
                        Cetak Label
                    </a>
                    <div class="rounded-[24px] border border-slate-200 bg-slate-50 px-5 py-4 text-sm text-slate-500">
                        {{ $settings->count() }} setting tersimpan
                    </div>
                </div>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-2">
            @foreach ($settingGroups as $group => $groupSettings)
                <section class="pos-panel">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-950">{{ $group }}</h3>
                            <p class="mt-1 text-sm text-slate-500">
                                @if ($group === 'Printer')
                                    Atur printer nota, ukuran kertas, jumlah cetak default, dan template label.
                                @elseif ($group === 'Loyalty')
                                    Atur akumulasi dan penukaran poin pelanggan.
                                @elseif ($group === 'POS')
                                    Atur invoice, pajak default, dan perilaku kasir.
                                @else
                                    Kelompok pengaturan {{ strtolower($group) }}.
                                @endif
                            </p>
                        </div>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">{{ $groupSettings->count() }} item</span>
                    </div>

                    <div class="mt-5 space-y-4">
                        @foreach ($groupSettings as $setting)
                            <form action="{{ route('settings.store') }}" method="POST" class="rounded-[22px] border border-slate-200 bg-white p-4">
                                @csrf
                                <input type="hidden" name="key" value="{{ $setting['key'] }}">
                                <input type="hidden" name="group" value="{{ $group }}">
                                <label class="block text-sm font-semibold text-slate-900">{{ $setting['label'] }}</label>
                                <div class="mt-2 text-xs uppercase tracking-[0.18em] text-slate-400">{{ $setting['key'] }}</div>
                                <input
                                    type="text"
                                    name="value"
                                    value="{{ is_array($setting['value']) ? json_encode($setting['value'], JSON_UNESCAPED_UNICODE) : $setting['value'] }}"
                                    class="mt-4 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900"
                                    required
                                >
                                <div class="mt-4 flex justify-end">
                                    <button type="submit" class="rounded-2xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                                        Simpan
                                    </button>
                                </div>
                            </form>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    </div>
</x-layouts.app>
