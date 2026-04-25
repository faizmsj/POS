<x-layouts.app>
    @php
        $showing = $suppliers->firstWhere('id', (int) request('show'));
        $formAutoOpen = $editing || old('_modal') === 'supplier-form-modal';
    @endphp

    <div class="space-y-4 sm:space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex flex-col gap-1">
                <h1 class="text-xl sm:text-2xl font-semibold">Manajemen Supplier</h1>
                <p class="text-xs sm:text-sm text-slate-500">Kelola sumber pembelian dan informasi vendor.</p>
            </div>
            <button type="button" data-modal-open="#supplier-form-modal" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                Tambah Supplier
            </button>
        </div>

        <section class="bg-white rounded-2xl sm:rounded-3xl shadow-sm p-4 sm:p-6">
            <h2 class="text-base sm:text-lg font-semibold mb-4">Supplier</h2>
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-y-2 text-sm">
                    <thead class="text-xs text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="pb-3 px-2">Nama</th>
                            <th class="pb-3 px-2">Email</th>
                            <th class="pb-3 px-2">Telepon</th>
                            <th class="pb-3 px-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($suppliers as $supplier)
                            <tr class="bg-slate-50 rounded-2xl">
                                <td class="py-3 px-2 font-semibold text-slate-950">{{ $supplier->name }}</td>
                                <td class="py-3 px-2 text-xs">{{ $supplier->email ?? '-' }}</td>
                                <td class="py-3 px-2 text-xs">{{ $supplier->phone ?? '-' }}</td>
                                <td class="py-3 px-2 text-right">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('suppliers.index', ['show' => $supplier->id]) }}" class="text-blue-600 hover:underline">Lihat</a>
                                        <a href="{{ route('suppliers.edit', $supplier) }}" class="text-slate-700 hover:underline">Edit</a>
                                        <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('Hapus?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:underline">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="space-y-3 lg:hidden">
                @foreach ($suppliers as $supplier)
                    <div class="bg-slate-50 rounded-2xl p-3 border border-slate-200">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-semibold text-sm">{{ $supplier->name }}</h3>
                            <div class="flex gap-2 text-xs font-semibold">
                                <a href="{{ route('suppliers.index', ['show' => $supplier->id]) }}" class="text-blue-600">Lihat</a>
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="text-slate-700">Edit</a>
                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('Hapus?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600">Hapus</button>
                                </form>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500">Email: {{ $supplier->email ?? '-' }}</p>
                        <p class="text-xs text-slate-500">Telepon: {{ $supplier->phone ?? '-' }}</p>
                        <p class="text-xs text-slate-500">Alamat: {{ $supplier->address ?? '-' }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <div id="supplier-form-modal" data-modal data-modal-auto-open="{{ $formAutoOpen ? 'true' : 'false' }}" class="pos-modal-backdrop">
        <div class="pos-modal-panel">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-slate-950">{{ $editing ? 'Edit Supplier' : 'Tambah Supplier' }}</h2>
                    <p class="mt-1 text-sm text-slate-500">Form supplier sekarang tampil di modal agar lebih cepat dipakai.</p>
                </div>
                <button type="button" data-modal-close class="pos-modal-close">×</button>
            </div>
            <form action="{{ $editing ? route('suppliers.update', $editing) : route('suppliers.store') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="_modal" value="supplier-form-modal">
                @if ($editing)
                    @method('PATCH')
                @endif
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $editing?->name) }}" class="pos-form-input" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $editing?->email) }}" class="pos-form-input" inputmode="email" data-validate-email="#supplier-email-feedback">
                    <div id="supplier-email-feedback" class="mt-2 text-xs text-slate-500">Gunakan email vendor yang aktif untuk kontak pembelian.</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Telepon</label>
                    <input type="tel" name="phone" value="{{ old('phone', $editing?->phone) }}" class="pos-form-input" inputmode="tel" pattern="[0-9+\-\s()]{8,20}" data-validate-phone="#supplier-phone-feedback">
                    <div id="supplier-phone-feedback" class="mt-2 text-xs text-slate-500">Gunakan nomor aktif 8-20 digit, boleh memakai +, spasi, atau tanda minus.</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
                    <input type="text" name="address" value="{{ old('address', $editing?->address) }}" class="pos-form-input">
                </div>
                <div class="pos-modal-actions">
                    <button type="button" data-modal-close class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Tutup</button>
                    <button type="submit" class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">{{ $editing ? 'Perbarui Supplier' : 'Simpan Supplier' }}</button>
                </div>
            </form>
        </div>
    </div>

    @if ($showing)
        <div data-modal data-modal-auto-open="true" class="pos-modal-backdrop">
            <div class="pos-modal-panel">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-950">Detail Supplier</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ $showing->name }}</p>
                    </div>
                    <a href="{{ route('suppliers.index') }}" class="pos-modal-close">×</a>
                </div>
                <div class="mt-6 grid gap-3">
                    <div class="rounded-[22px] bg-slate-50 p-4"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Email</div><div class="mt-2 text-slate-700">{{ $showing->email ?: '-' }}</div></div>
                    <div class="rounded-[22px] bg-slate-50 p-4"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Telepon</div><div class="mt-2 text-slate-700">{{ $showing->phone ?: '-' }}</div></div>
                    <div class="rounded-[22px] bg-slate-50 p-4"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Alamat</div><div class="mt-2 text-slate-700">{{ $showing->address ?: '-' }}</div></div>
                </div>
                <div class="pos-modal-actions">
                    <a href="{{ route('suppliers.index') }}" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Tutup</a>
                    <a href="{{ route('suppliers.edit', $showing) }}" class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Edit Supplier</a>
                </div>
            </div>
        </div>
    @endif
</x-layouts.app>
