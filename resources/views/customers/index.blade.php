<x-layouts.app>
    @php
        $showing = $customers->firstWhere('id', (int) request('show'));
        $formAutoOpen = $editing || old('_modal') === 'customer-form-modal';
    @endphp

    <div class="space-y-4 sm:space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex flex-col gap-1">
                <h1 class="text-xl sm:text-2xl font-semibold">Manajemen Pelanggan</h1>
                <p class="text-xs sm:text-sm text-slate-500">Kelola profil pelanggan dan saldo poin loyalitas.</p>
            </div>
            <button type="button" data-modal-open="#customer-form-modal" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                Tambah Pelanggan
            </button>
        </div>

        <section class="bg-white rounded-2xl sm:rounded-3xl shadow-sm p-4 sm:p-6">
            <h2 class="text-base sm:text-lg font-semibold mb-4">Daftar Pelanggan</h2>
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-y-2 text-sm">
                    <thead class="text-xs text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="pb-3 px-2">Nama</th>
                            <th class="pb-3 px-2">Kode</th>
                            <th class="pb-3 px-2">Telepon</th>
                            <th class="pb-3 px-2">Poin</th>
                            <th class="pb-3 px-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr class="bg-slate-50 rounded-2xl">
                                <td class="py-3 px-2 font-semibold text-slate-950">{{ $customer->name }}</td>
                                <td class="py-3 px-2">{{ $customer->code }}</td>
                                <td class="py-3 px-2 text-xs">{{ $customer->phone ?? '-' }}</td>
                                <td class="py-3 px-2">{{ number_format($customer->points_balance, 2, ',', '.') }}</td>
                                <td class="py-3 px-2 text-right">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('customers.index', ['show' => $customer->id]) }}" class="text-blue-600 hover:underline">Lihat</a>
                                        <a href="{{ route('customers.edit', $customer) }}" class="text-slate-700 hover:underline">Edit</a>
                                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Hapus?');">
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
                @foreach ($customers as $customer)
                    <div class="bg-slate-50 rounded-2xl p-3 border border-slate-200">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-semibold text-sm">{{ $customer->name }}</h3>
                                <p class="text-xs text-slate-500">{{ $customer->code }}</p>
                            </div>
                            <div class="flex gap-2 text-xs font-semibold">
                                <a href="{{ route('customers.index', ['show' => $customer->id]) }}" class="text-blue-600">Lihat</a>
                                <a href="{{ route('customers.edit', $customer) }}" class="text-slate-700">Edit</a>
                                <form action="{{ route('customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Hapus?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600">Hapus</button>
                                </form>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500">Telepon: {{ $customer->phone ?? '-' }}</p>
                        <p class="text-xs text-slate-500">Poin: {{ number_format($customer->points_balance, 2, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <div id="customer-form-modal" data-modal data-modal-auto-open="{{ $formAutoOpen ? 'true' : 'false' }}" class="pos-modal-backdrop">
        <div class="pos-modal-panel">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-slate-950">{{ $editing ? 'Edit Pelanggan' : 'Tambah Pelanggan' }}</h2>
                    <p class="mt-1 text-sm text-slate-500">Form pelanggan sekarang tampil sebagai modal agar kerja kasir lebih cepat.</p>
                </div>
                <button type="button" data-modal-close class="pos-modal-close">×</button>
            </div>
            <form action="{{ $editing ? route('customers.update', $editing) : route('customers.store') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="_modal" value="customer-form-modal">
                @if ($editing)
                    @method('PATCH')
                @endif
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $editing?->name) }}" class="pos-form-input" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kode Pelanggan</label>
                    <input type="text" name="code" value="{{ old('code', $editing?->code) }}" class="pos-form-input" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $editing?->email) }}" class="pos-form-input" inputmode="email" data-validate-email="#customer-email-feedback">
                    <div id="customer-email-feedback" class="mt-2 text-xs text-slate-500">Gunakan email aktif jika pelanggan ingin menerima komunikasi digital.</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Telepon</label>
                    <input type="tel" name="phone" value="{{ old('phone', $editing?->phone) }}" class="pos-form-input" inputmode="tel" pattern="[0-9+\-\s()]{8,20}" data-validate-phone="#customer-phone-feedback">
                    <div id="customer-phone-feedback" class="mt-2 text-xs text-slate-500">Gunakan nomor aktif 8-20 digit, boleh memakai +, spasi, atau tanda minus.</div>
                </div>
                <div class="pos-modal-actions">
                    <button type="button" data-modal-close class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Tutup</button>
                    <button type="submit" class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">{{ $editing ? 'Perbarui Pelanggan' : 'Simpan Pelanggan' }}</button>
                </div>
            </form>
        </div>
    </div>

    @if ($showing)
        <div data-modal data-modal-auto-open="true" class="pos-modal-backdrop">
            <div class="pos-modal-panel">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-950">Detail Pelanggan</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ $showing->name }}</p>
                    </div>
                    <a href="{{ route('customers.index') }}" class="pos-modal-close">×</a>
                </div>
                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[22px] bg-slate-50 p-4"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Kode</div><div class="mt-2 font-semibold text-slate-950">{{ $showing->code }}</div></div>
                    <div class="rounded-[22px] bg-slate-50 p-4"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Poin</div><div class="mt-2 font-semibold text-slate-950">{{ number_format($showing->points_balance, 2, ',', '.') }}</div></div>
                    <div class="rounded-[22px] bg-slate-50 p-4"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Email</div><div class="mt-2 text-slate-700">{{ $showing->email ?: '-' }}</div></div>
                    <div class="rounded-[22px] bg-slate-50 p-4"><div class="text-xs uppercase tracking-[0.18em] text-slate-400">Telepon</div><div class="mt-2 text-slate-700">{{ $showing->phone ?: '-' }}</div></div>
                </div>
                <div class="pos-modal-actions">
                    <a href="{{ route('customers.index') }}" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Tutup</a>
                    <a href="{{ route('customers.edit', $showing) }}" class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Edit Pelanggan</a>
                </div>
            </div>
        </div>
    @endif
</x-layouts.app>
