<x-layouts.app :title="'Pengaturan Aplikasi'">
    @php
        $groupMeta = [
            'Umum' => 'Identitas toko dan informasi dasar aplikasi.',
            'POS' => 'Konfigurasi invoice, pajak, dan perilaku kasir.',
            'Printer' => 'Atur printer nota, label, dan format cetak default.',
            'Loyalty' => 'Atur akumulasi dan penukaran poin pelanggan.',
            'PPOB' => 'Atur margin dan preferensi layanan digital.',
        ];

        $fieldMeta = [
            'loyalty_enabled' => ['type' => 'toggle'],
            'printer_show_logo' => ['type' => 'toggle'],
            'label_show_store_name' => ['type' => 'toggle'],
            'printer_paper_width' => ['type' => 'select', 'options' => ['58mm', '80mm']],
            'label_paper_size' => ['type' => 'select', 'options' => ['A4 12 Label', 'A4 24 Label', 'A5 8 Label']],
            'label_columns' => ['type' => 'select', 'options' => ['2', '3', '4']],
            'printer_copies' => ['type' => 'select', 'options' => ['1', '2', '3']],
            'default_tax_percent' => ['type' => 'number'],
            'points_per_amount' => ['type' => 'number'],
            'points_redeem_minimum' => ['type' => 'number'],
            'points_redeem_value' => ['type' => 'number'],
            'ppob_markup_percent' => ['type' => 'number'],
        ];
    @endphp

    <div class="space-y-6">
        <section class="pos-panel">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-600">Dynamic Configuration</p>
                    <h2 class="mt-1 text-3xl font-semibold tracking-tight text-slate-950">Pengaturan aplikasi</h2>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">Kelola identitas toko, POS, loyalty, PPOB, dan printer dari satu halaman. Semua perubahan disimpan sekaligus dengan satu tombol.</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('settings.receipt-preview') }}" target="_blank" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Preview Nota
                    </a>
                    <a href="{{ route('labels.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-slate-300">
                        Preview Label
                    </a>
                    <div class="rounded-[24px] border border-slate-200 bg-slate-50 px-5 py-4 text-sm text-slate-500">
                        {{ $settings->count() }} setting tersimpan
                    </div>
                </div>
            </div>
        </section>

        <form action="{{ route('settings.store') }}" method="POST" class="space-y-6" id="settings-form">
            @csrf

            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                @foreach ($settingGroups as $group => $groupSettings)
                    @php
                        $slug = \Illuminate\Support\Str::slug($group);
                    @endphp
                    <button type="button" data-settings-tab="{{ $slug }}" class="pos-settings-tab rounded-[24px] border border-slate-200 bg-white px-4 py-4 text-left shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md">
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $group }}</div>
                        <div class="mt-2 text-2xl font-semibold text-slate-950">{{ $groupSettings->count() }}</div>
                        <div class="mt-1 text-sm text-slate-500">{{ $groupMeta[$group] ?? 'Kelompok pengaturan aplikasi.' }}</div>
                    </button>
                @endforeach
            </section>

            <div class="space-y-6">
                @foreach ($settingGroups as $group => $groupSettings)
                    @php
                        $slug = \Illuminate\Support\Str::slug($group);
                    @endphp
                    <section id="group-{{ $slug }}" data-settings-panel="{{ $slug }}" class="pos-panel hidden">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-950">{{ $group }}</h3>
                                <p class="mt-1 text-sm text-slate-500">{{ $groupMeta[$group] ?? 'Kelompok pengaturan aplikasi.' }}</p>
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">{{ $groupSettings->count() }} item</span>
                        </div>

                        <div class="mt-5 space-y-4">
                            @foreach ($groupSettings as $setting)
                                @php
                                    $config = $fieldMeta[$setting['key']] ?? ['type' => 'text'];
                                    $value = old('settings.'.$setting['key'], is_array($setting['value']) ? json_encode($setting['value'], JSON_UNESCAPED_UNICODE) : $setting['value']);
                                @endphp
                                <div class="rounded-[24px] border border-slate-200 bg-white p-4">
                                    <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            <label for="setting-{{ $setting['key'] }}" class="block text-sm font-semibold text-slate-900">{{ $setting['label'] }}</label>
                                            <div class="mt-1 text-xs uppercase tracking-[0.18em] text-slate-400">{{ $setting['key'] }}</div>
                                        </div>
                                        <span class="rounded-full bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-500">{{ $group }}</span>
                                    </div>

                                    <div class="mt-4">
                                        @if ($config['type'] === 'toggle')
                                            <label class="flex items-center justify-between rounded-[20px] bg-slate-50 px-4 py-4">
                                                <span class="text-sm font-medium text-slate-700">Aktifkan opsi ini</span>
                                                <input
                                                    type="hidden"
                                                    name="settings[{{ $setting['key'] }}]"
                                                    value="0"
                                                >
                                                <input
                                                    id="setting-{{ $setting['key'] }}"
                                                    type="checkbox"
                                                    name="settings[{{ $setting['key'] }}]"
                                                    value="1"
                                                    class="h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                                    @checked((string) $value === '1')
                                                >
                                            </label>
                                        @elseif ($config['type'] === 'select')
                                            <select id="setting-{{ $setting['key'] }}" name="settings[{{ $setting['key'] }}]" class="pos-form-input">
                                                @foreach ($config['options'] as $option)
                                                    <option value="{{ $option }}" @selected((string) $value === (string) $option)>{{ $option }}</option>
                                                @endforeach
                                            </select>
                                        @elseif ($config['type'] === 'number')
                                            <input
                                                id="setting-{{ $setting['key'] }}"
                                                type="number"
                                                step="0.01"
                                                name="settings[{{ $setting['key'] }}]"
                                                value="{{ $value }}"
                                                class="pos-form-input"
                                            >
                                        @else
                                            <input
                                                id="setting-{{ $setting['key'] }}"
                                                type="text"
                                                name="settings[{{ $setting['key'] }}]"
                                                value="{{ $value }}"
                                                class="pos-form-input"
                                            >
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>

            <div class="pos-safe-sticky-mobile sticky z-10 xl:bottom-4">
                <div class="rounded-[28px] border border-white/70 bg-white/92 p-4 shadow-[0_16px_45px_rgba(15,23,42,0.12)] backdrop-blur">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="text-sm font-semibold text-slate-950">Simpan semua pengaturan</div>
                            <div class="mt-1 text-sm text-slate-500">Perubahan di seluruh section akan disimpan sekaligus.</div>
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center rounded-[22px] bg-blue-600 px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-blue-500/25 transition hover:bg-blue-700">
                            Simpan Semua Pengaturan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        (function () {
            const tabButtons = Array.from(document.querySelectorAll('[data-settings-tab]'));
            const panels = Array.from(document.querySelectorAll('[data-settings-panel]'));

            if (!tabButtons.length || !panels.length) {
                return;
            }

            function activateTab(key) {
                tabButtons.forEach((button) => {
                    const active = button.dataset.settingsTab === key;
                    button.classList.toggle('is-active', active);
                });

                panels.forEach((panel) => {
                    panel.classList.toggle('hidden', panel.dataset.settingsPanel !== key);
                });
            }

            tabButtons.forEach((button) => {
                button.addEventListener('click', () => activateTab(button.dataset.settingsTab));
            });

            activateTab(tabButtons[0].dataset.settingsTab);
        })();
    </script>
</x-layouts.app>
