<x-layouts.app :title="'PPOB Provider'">
    <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <section class="pos-panel">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-950">Daftar provider PPOB</h2>
                    <p class="mt-1 text-sm text-slate-500">Provider dipakai untuk sinkron katalog dan transaksi digital realtime.</p>
                </div>
            </div>

            <div class="mt-5 space-y-4">
                @forelse ($providers as $provider)
                    <div class="rounded-[24px] border border-slate-200 bg-white p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="text-lg font-semibold text-slate-950">{{ $provider->name }}</div>
                                <div class="mt-1 text-sm text-slate-500">{{ $provider->code }}{{ $provider->api_endpoint ? ' | ' . $provider->api_endpoint : '' }}</div>
                            </div>
                            <div class="text-right">
                                <div class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">{{ $provider->products_count }} produk</div>
                                <form action="{{ route('ppob.providers.destroy', $provider) }}" method="POST" class="mt-3" onsubmit="return confirm('Hapus provider ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-semibold text-rose-500 hover:text-rose-600">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-[24px] border border-dashed border-slate-300 bg-slate-50 px-5 py-10 text-center text-sm text-slate-500">
                        Belum ada provider PPOB.
                    </div>
                @endforelse
            </div>
        </section>

        <section class="pos-panel">
            <h2 class="text-lg font-semibold text-slate-950">Tambah provider</h2>
            <form action="{{ route('ppob.providers.store') }}" method="POST" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Nama Provider</label>
                    <input type="text" name="name" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Kode Provider</label>
                    <input type="text" name="code" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">API Endpoint</label>
                    <input type="url" name="api_endpoint" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" placeholder="https://api.provider.com">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">API Key</label>
                    <input type="text" name="api_key" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">
                </div>
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                    Simpan Provider
                </button>
            </form>
        </section>
    </div>
</x-layouts.app>
