<x-layouts.app :title="'FAQ Program'">
    <div class="space-y-6">
        <section class="pos-panel">
            <p class="text-sm font-medium text-blue-600">Pusat Bantuan</p>
            <h2 class="mt-1 text-3xl font-semibold tracking-tight text-slate-950">FAQ Program</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">Pertanyaan umum untuk kasir, supervisor, manager, dan administrator agar operasional harian lebih cepat dan minim kebingungan.</p>
        </section>

        <div class="grid gap-6 xl:grid-cols-2">
            @foreach ($faqGroups as $group)
                <section class="pos-panel">
                    <h3 class="text-lg font-semibold text-slate-950">{{ $group['title'] }}</h3>
                    <div class="mt-5 space-y-4">
                        @foreach ($group['items'] as $item)
                            <article class="rounded-[24px] border border-slate-200 bg-white p-5">
                                <h4 class="text-sm font-semibold text-slate-950">{{ $item['q'] }}</h4>
                                <p class="mt-2 text-sm leading-6 text-slate-500">{{ $item['a'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    </div>
</x-layouts.app>
