<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'POS Kasir' }}</title>
    @vite(['resources/css/app.css'])
</head>

<body class="pos-body min-h-screen text-slate-900 antialiased">
    @php
        $navigation = [
            [
                'section' => 'Overview',
                'items' => [
                    ['label' => 'Dashboard', 'route' => 'dashboard', 'match' => 'dashboard'],
                    ['label' => 'POS Kasir', 'route' => 'sales.create', 'match' => 'sales.create'],
                    ['label' => 'Transaksi', 'route' => 'sales.index', 'match' => 'sales.index'],
                    ['label' => 'PPOB', 'route' => 'ppob.transactions.index', 'match' => 'ppob.*'],
                ],
            ],
            [
                'section' => 'Inventory',
                'items' => [
                    ['label' => 'Cabang', 'route' => 'branches.index', 'match' => 'branches.*'],
                    ['label' => 'Produk', 'route' => 'products.index', 'match' => 'products.*'],
                    ['label' => 'Cetak Label', 'route' => 'labels.index', 'match' => 'labels.*'],
                    ['label' => 'Kategori', 'route' => 'categories.index', 'match' => 'categories.*'],
                    ['label' => 'Supplier', 'route' => 'suppliers.index', 'match' => 'suppliers.*'],
                    ['label' => 'Pembelian', 'route' => 'purchases.index', 'match' => 'purchases.*'],
                ],
            ],
            [
                'section' => 'Pelanggan',
                'items' => [
                    ['label' => 'Pelanggan', 'route' => 'customers.index', 'match' => 'customers.*'],
                    ['label' => 'Pengaturan', 'route' => 'settings.index', 'match' => 'settings.*'],
                ],
            ],
        ];
    @endphp

    <div class="relative min-h-screen overflow-hidden">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(37,99,235,0.14),_transparent_28%),radial-gradient(circle_at_top_right,_rgba(251,146,60,0.14),_transparent_24%),linear-gradient(180deg,_#eef4ff_0%,_#f8fafc_42%,_#fdfdfd_100%)]"></div>

        <div class="relative mx-auto flex min-h-screen max-w-[1680px] gap-6 px-3 py-3 sm:px-4 lg:px-6">
            <aside class="hidden w-[290px] shrink-0 xl:flex xl:flex-col">
                <div class="rounded-[30px] border border-white/70 bg-white/86 p-5 shadow-[0_18px_55px_rgba(15,23,42,0.08)] backdrop-blur">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 via-sky-500 to-cyan-400 text-lg font-bold text-white shadow-lg shadow-blue-500/25">
                            P
                        </div>
                        <div>
                            <div class="text-base font-semibold tracking-tight text-slate-900">Kasir Pusat</div>
                            <div class="text-sm text-slate-500">POS Multicabang 2026</div>
                        </div>
                    </a>

                    <div class="mt-6 space-y-5">
                        @foreach ($navigation as $group)
                            <div>
                                <p class="px-3 text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">{{ $group['section'] }}</p>
                                <nav class="mt-2 space-y-1.5">
                                    @foreach ($group['items'] as $item)
                                        @php($active = request()->routeIs($item['match']))
                                        <a href="{{ route($item['route']) }}" class="pos-nav-link {{ $active ? 'is-active' : '' }}">
                                            <span>{{ $item['label'] }}</span>
                                        </a>
                                    @endforeach
                                </nav>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4 rounded-[28px] border border-white/70 bg-white/86 p-5 shadow-[0_16px_40px_rgba(15,23,42,0.08)] backdrop-blur">
                    <p class="text-sm font-semibold text-slate-900">Administrator Pusat</p>
                    <p class="mt-1 text-sm text-slate-500">Mode operasional aktif untuk dashboard, inventaris, PPOB, dan loyalty.</p>
                    <div class="mt-4 rounded-2xl bg-slate-950 px-4 py-3 text-sm text-white">
                        <div class="font-medium">Shift monitoring</div>
                        <div class="mt-1 text-slate-300">Pantau transaksi, stok, dan performa cabang dari satu panel.</div>
                    </div>
                </div>
            </aside>

            <div class="flex min-w-0 flex-1 flex-col">
                <header class="rounded-[26px] border border-white/70 bg-white/78 px-4 py-4 shadow-[0_16px_45px_rgba(15,23,42,0.08)] backdrop-blur sm:rounded-[30px] sm:px-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <div class="text-sm font-medium text-blue-600">Aplikasi Point of Sale</div>
                            <h1 class="mt-1 text-xl font-semibold tracking-tight text-slate-950 sm:text-2xl">{{ $title ?? 'POS Multicabang - Multicashier' }}</h1>
                            <p class="mt-1 text-xs text-slate-500 sm:text-sm">Panel operasional untuk cabang, kasir, FIFO inventory, PPOB, dan loyalty.</p>
                        </div>

                        <div class="grid w-full gap-2 sm:flex sm:w-auto sm:flex-wrap sm:items-center sm:gap-3">
                            <a href="{{ route('sales.create') }}"
                                class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                                Buat Transaksi
                            </a>
                            <a href="{{ route('products.index') }}"
                                class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:text-slate-950">
                                Kelola Produk
                            </a>
                        </div>
                    </div>
                </header>

                <main class="flex-1 px-1 py-5 sm:px-2 sm:py-6 pb-24 xl:pb-6">
                    @include('partials.flash')
                    {{ $slot }}
                </main>

                <nav class="sticky bottom-3 z-10 mt-auto grid grid-cols-4 gap-2 rounded-[22px] border border-white/70 bg-white/92 p-2 shadow-[0_16px_45px_rgba(15,23,42,0.1)] backdrop-blur xl:hidden">
                    <a href="{{ route('dashboard') }}" class="pos-mobile-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">Dashboard</a>
                    <a href="{{ route('sales.create') }}" class="pos-mobile-link {{ request()->routeIs('sales.create') ? 'is-active' : '' }}">Kasir</a>
                    <a href="{{ route('products.index') }}" class="pos-mobile-link {{ request()->routeIs('products.*') ? 'is-active' : '' }}">Produk</a>
                    <a href="{{ route('labels.index') }}" class="pos-mobile-link {{ request()->routeIs('labels.*') ? 'is-active' : '' }}">Label</a>
                </nav>
            </div>
        </div>
    </div>
</body>

</html>
