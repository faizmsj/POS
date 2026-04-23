<x-layouts.app>
    <div class="space-y-4 sm:space-y-6">
        <div class="flex flex-col gap-1">
            <h1 class="text-xl sm:text-2xl font-semibold">Manajemen Supplier</h1>
            <p class="text-xs sm:text-sm text-slate-500">Kelola sumber pembelian dan informasi vendor.</p>
        </div>

        <div class="grid gap-4 sm:gap-6 grid-cols-1 lg:grid-cols-3">
            <section class="lg:col-span-2 bg-white rounded-2xl sm:rounded-3xl shadow-sm p-4 sm:p-6">
                <h2 class="text-base sm:text-lg font-semibold mb-4">Supplier</h2>

                <!-- Desktop Table -->
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
                                    <td class="py-3 px-2">{{ $supplier->name }}</td>
                                    <td class="py-3 px-2 text-xs">{{ $supplier->email ?? '-' }}</td>
                                    <td class="py-3 px-2 text-xs">{{ $supplier->phone ?? '-' }}</td>
                                    <td class="py-3 px-2 text-right">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('suppliers.edit', $supplier) }}" class="text-xs text-blue-600 hover:underline">Edit</a>
                                            <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST"
                                                onsubmit="return confirm('Hapus?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-xs text-rose-600 hover:underline">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="space-y-3 lg:hidden">
                    @foreach ($suppliers as $supplier)
                        <div class="bg-slate-50 rounded-2xl p-3 border border-slate-200">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-semibold text-sm">{{ $supplier->name }}</h3>
                                <div class="flex gap-2">
                                    <a href="{{ route('suppliers.edit', $supplier) }}" class="text-xs text-blue-600">Edit</a>
                                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST"
                                        onsubmit="return confirm('Hapus?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-rose-600">Hapus</button>
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

            <section class="bg-white rounded-2xl sm:rounded-3xl shadow-sm p-4 sm:p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-base sm:text-lg font-semibold">{{ $editing ? 'Edit Supplier' : 'Tambah Supplier' }}</h2>
                    @if ($editing)
                        <a href="{{ route('suppliers.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Batal</a>
                    @endif
                </div>
                <form action="{{ $editing ? route('suppliers.update', $editing) : route('suppliers.store') }}" method="POST" class="space-y-3 sm:space-y-4">
                    @csrf
                    @if ($editing)
                        @method('PATCH')
                    @endif
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1">Nama</label>
                        <input type="text" name="name" value="{{ old('name', $editing?->name) }}"
                            class="w-full rounded-2xl border border-slate-200 px-3 sm:px-4 py-2 sm:py-3 text-sm"
                            required>
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $editing?->email) }}"
                            class="w-full rounded-2xl border border-slate-200 px-3 sm:px-4 py-2 sm:py-3 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1">Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone', $editing?->phone) }}"
                            class="w-full rounded-2xl border border-slate-200 px-3 sm:px-4 py-2 sm:py-3 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1">Alamat</label>
                        <input type="text" name="address" value="{{ old('address', $editing?->address) }}"
                            class="w-full rounded-2xl border border-slate-200 px-3 sm:px-4 py-2 sm:py-3 text-sm">
                    </div>
                    <button type="submit"
                        class="w-full rounded-2xl bg-[#111827] px-4 sm:px-5 py-2.5 sm:py-3 text-xs sm:text-sm font-semibold text-white hover:bg-[#0f172a] transition">{{ $editing ? 'Perbarui' : 'Simpan' }}
                        Supplier</button>
                </form>
            </section>
        </div>
    </div>
</x-layouts.app>
