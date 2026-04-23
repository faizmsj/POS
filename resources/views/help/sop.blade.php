<x-layouts.app :title="'SOP Penggunaan'">
    <div class="space-y-6">
        <section class="pos-panel">
            <p class="text-sm font-medium text-blue-600">Pusat Bantuan</p>
            <h2 class="mt-1 text-3xl font-semibold tracking-tight text-slate-950">SOP Penggunaan Program</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">Panduan operasional standar untuk penggunaan aplikasi POS oleh kasir maupun administrator.</p>
        </section>

        <div class="grid gap-6 xl:grid-cols-3">
            @foreach ($sections as $section)
                <section class="pos-panel">
                    <h3 class="text-lg font-semibold text-slate-950">{{ $section['title'] }}</h3>
                    <ol class="mt-5 space-y-3">
                        @foreach ($section['steps'] as $index => $step)
                            <li class="flex items-start gap-3 rounded-[22px] bg-slate-50 px-4 py-4">
                                <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-950 text-xs font-semibold text-white">{{ $index + 1 }}</span>
                                <span class="text-sm leading-6 text-slate-700">{{ $step }}</span>
                            </li>
                        @endforeach
                    </ol>
                </section>
            @endforeach
        </div>
    </div>
</x-layouts.app>
