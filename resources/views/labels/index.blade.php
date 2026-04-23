<x-layouts.app :title="'Cetak Label'">
    <div class="grid gap-6 xl:grid-cols-[0.86fr_1.14fr]">
        <section class="pos-panel xl:sticky xl:top-6 xl:self-start">
            <h2 class="text-lg font-semibold text-slate-950">Pengaturan cetak label</h2>
            <p class="mt-1 text-sm text-slate-500">Pilih cabang, produk, dan jumlah salinan label. Hasil preview siap dicetak langsung dari browser.</p>

            <form action="{{ route('labels.index') }}" method="GET" class="mt-5 space-y-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Cabang</label>
                    <select name="branch_id" class="pos-form-input">
                        <option value="">Pilih cabang</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" @selected($selectedBranchId == $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Jumlah salinan</label>
                    <input type="number" name="copies" min="1" max="12" value="{{ $copies }}" class="pos-form-input">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Pilih produk</label>
                    <div class="max-h-[320px] overflow-y-auto rounded-2xl border border-slate-200 bg-slate-50 p-3">
                        @foreach ($products as $product)
                            <label class="mb-2 flex items-start gap-3 rounded-2xl bg-white px-3 py-3 text-sm last:mb-0">
                                <input type="checkbox" name="products[]" value="{{ $product->id }}" class="mt-1" @checked($selectedProducts->contains('id', $product->id))>
                                <span>
                                    <span class="block font-semibold text-slate-900">{{ $product->name }}</span>
                                    <span class="block text-xs text-slate-500">{{ $product->sku }} | {{ $product->barcode ?: 'Tanpa barcode' }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Preview Label
                    </button>
                    <button type="button" onclick="window.print()" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300">
                        Cetak Label
                    </button>
                </div>
            </form>
        </section>

        <section class="pos-panel">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-950">Preview label</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ $printerSettings['paper_size'] }} | {{ $printerSettings['columns'] }} kolom</p>
                </div>
            </div>

            <div class="mt-5 rounded-[24px] bg-slate-50 p-4 print:bg-white print:p-0">
                @if ($selectedProducts->isEmpty() || !$selectedBranchId)
                    <div class="rounded-[20px] border border-dashed border-slate-300 bg-white px-5 py-10 text-center text-sm text-slate-500">
                        Pilih cabang dan minimal satu produk untuk menampilkan label.
                    </div>
                @else
                    <div class="mb-4 rounded-[20px] border border-slate-200 bg-white px-4 py-4">
                        <div class="grid gap-3 md:grid-cols-3">
                            <div>
                                <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Cabang</div>
                                <div class="mt-1 text-sm font-semibold text-slate-950">{{ $selectedBranch?->name ?? 'Cabang terpilih' }}</div>
                            </div>
                            <div>
                                <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Produk</div>
                                <div class="mt-1 text-sm font-semibold text-slate-950">{{ $selectedProducts->count() }} item</div>
                            </div>
                            <div>
                                <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Salinan</div>
                                <div class="mt-1 text-sm font-semibold text-slate-950">{{ $copies }} label per produk</div>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 print:gap-2" style="grid-template-columns: repeat({{ max(1, $printerSettings['columns']) }}, minmax(0, 1fr));">
                        @foreach ($labelPreview as $item)
                            @for ($i = 0; $i < $copies; $i++)
                                <div class="rounded-[18px] border border-slate-200 bg-white p-4 text-center shadow-sm print:break-inside-avoid">
                                    @if ($printerSettings['show_store_name'])
                                        <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">{{ $item['store_name'] }}</div>
                                    @endif
                                    <div class="mt-2 line-clamp-2 min-h-[2.5rem] text-sm font-semibold text-slate-950">{{ $item['product']->name }}</div>
                                    <div class="mt-1 text-lg font-bold text-slate-950">Rp {{ number_format($item['branch_product']?->selling_price ?? $item['product']->base_price, 0, ',', '.') }}</div>
                                    <div class="mt-3 overflow-hidden rounded-lg border border-slate-200 bg-white px-2 py-2">
                                        {!! $item['barcode_svg'] !!}
                                    </div>
                                    <div class="mt-2 text-[11px] tracking-[0.2em] text-slate-500">{{ $item['barcode_value'] }}</div>
                                </div>
                            @endfor
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    </div>
</x-layouts.app>
