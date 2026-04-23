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
        $user = auth()->user();
        $navigation = [
            [
                'section' => 'Overview',
                'items' => [
                    ['label' => 'Dashboard', 'route' => 'dashboard', 'match' => 'dashboard', 'roles' => ['owner', 'admin', 'manager', 'cashier']],
                    ['label' => 'POS Kasir', 'route' => 'sales.create', 'match' => 'sales.create', 'roles' => ['owner', 'admin', 'manager', 'cashier']],
                    ['label' => 'Transaksi', 'route' => 'sales.index', 'match' => 'sales.index', 'roles' => ['owner', 'admin', 'manager', 'cashier']],
                    ['label' => 'Shift Kasir', 'route' => 'cashier-shifts.index', 'match' => 'cashier-shifts.*', 'roles' => ['owner', 'admin', 'manager', 'cashier']],
                    ['label' => 'PPOB', 'route' => 'ppob.transactions.index', 'match' => 'ppob.transactions.*', 'roles' => ['owner', 'admin', 'manager', 'cashier']],
                ],
            ],
            [
                'section' => 'Inventory',
                'items' => [
                    ['label' => 'Cabang', 'route' => 'branches.index', 'match' => 'branches.*', 'roles' => ['owner', 'admin', 'manager']],
                    ['label' => 'Produk', 'route' => 'products.index', 'match' => 'products.*', 'roles' => ['owner', 'admin', 'manager']],
                    ['label' => 'Cetak Label', 'route' => 'labels.index', 'match' => 'labels.*', 'roles' => ['owner', 'admin', 'manager']],
                    ['label' => 'Kategori', 'route' => 'categories.index', 'match' => 'categories.*', 'roles' => ['owner', 'admin', 'manager']],
                    ['label' => 'Supplier', 'route' => 'suppliers.index', 'match' => 'suppliers.*', 'roles' => ['owner', 'admin', 'manager']],
                    ['label' => 'Pembelian', 'route' => 'purchases.index', 'match' => 'purchases.*', 'roles' => ['owner', 'admin', 'manager']],
                ],
            ],
            [
                'section' => 'Layanan',
                'items' => [
                    ['label' => 'Pelanggan', 'route' => 'customers.index', 'match' => 'customers.*', 'roles' => ['owner', 'admin', 'manager', 'cashier']],
                    ['label' => 'FAQ Program', 'route' => 'help.faq', 'match' => 'help.faq', 'roles' => ['owner', 'admin', 'manager', 'cashier']],
                    ['label' => 'SOP Penggunaan', 'route' => 'help.sop', 'match' => 'help.sop', 'roles' => ['owner', 'admin', 'manager', 'cashier']],
                    ['label' => 'Produk PPOB', 'route' => 'ppob.products.index', 'match' => 'ppob.products.*', 'roles' => ['owner', 'admin']],
                    ['label' => 'Provider PPOB', 'route' => 'ppob.providers.index', 'match' => 'ppob.providers.*', 'roles' => ['owner', 'admin']],
                    ['label' => 'Akses Pengguna', 'route' => 'users.index', 'match' => 'users.*', 'roles' => ['owner', 'admin']],
                    ['label' => 'Pengaturan', 'route' => 'settings.index', 'match' => 'settings.*', 'roles' => ['owner', 'admin']],
                ],
            ],
        ];

        $navigation = collect($navigation)->map(function (array $group) use ($user) {
            $group['items'] = collect($group['items'])
                ->filter(fn (array $item) => $user && $user->hasAnyRole($item['roles']))
                ->values()
                ->all();

            return $group;
        })->filter(fn (array $group) => count($group['items']) > 0)->values();

        $quickLinks = collect([
            ['label' => 'Dashboard', 'route' => 'dashboard', 'match' => 'dashboard', 'roles' => ['owner', 'admin', 'manager', 'cashier']],
            ['label' => 'Kasir', 'route' => 'sales.create', 'match' => 'sales.create', 'roles' => ['owner', 'admin', 'manager', 'cashier']],
            ['label' => 'Shift', 'route' => 'cashier-shifts.index', 'match' => 'cashier-shifts.*', 'roles' => ['owner', 'admin', 'manager', 'cashier']],
            ['label' => 'User', 'route' => 'users.index', 'match' => 'users.*', 'roles' => ['owner', 'admin']],
            ['label' => 'Produk', 'route' => 'products.index', 'match' => 'products.*', 'roles' => ['owner', 'admin', 'manager']],
            ['label' => 'Setting', 'route' => 'settings.index', 'match' => 'settings.*', 'roles' => ['owner', 'admin']],
        ])->filter(fn (array $item) => $user && $user->hasAnyRole($item['roles']))->take(4)->values();
    @endphp

    <div class="relative min-h-screen overflow-hidden" data-shell>
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
                                            <span class="pos-nav-text">{{ $item['label'] }}</span>
                                        </a>
                                    @endforeach
                                </nav>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4 rounded-[28px] border border-white/70 bg-white/86 p-5 shadow-[0_16px_40px_rgba(15,23,42,0.08)] backdrop-blur">
                    <p class="text-sm font-semibold text-slate-900">{{ $user?->name ?? 'Administrator' }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $user?->email ?? 'Mode operasional aktif' }}</p>
                    <div class="mt-4 rounded-2xl bg-slate-950 px-4 py-3 text-sm text-white">
                        <div class="font-medium">{{ ucfirst($user?->role ?? 'admin') }}</div>
                        <div class="mt-1 text-slate-300">{{ $user?->branch?->name ?? 'Akses seluruh cabang' }}</div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:text-slate-950">
                            Logout
                        </button>
                    </form>
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
                            <div class="hidden rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-600 lg:block">
                                {{ $user?->name ?? 'User' }}
                            </div>
                            @if ($user && $user->hasAnyRole(['owner', 'admin', 'manager', 'cashier']))
                                <a href="{{ route('sales.create') }}"
                                    class="pos-header-action bg-slate-950 text-white hover:bg-slate-800">
                                    Buat Transaksi
                                </a>
                            @endif
                            @if ($user && $user->hasAnyRole(['owner', 'admin', 'manager']))
                                <a href="{{ route('products.index') }}"
                                    class="pos-header-action border border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:text-slate-950">
                                    Kelola Produk
                                </a>
                            @endif
                        </div>
                    </div>
                </header>

                <main class="flex-1 px-1 py-5 pb-48 sm:px-2 sm:py-6 sm:pb-52 xl:pb-6">
                    @include('partials.flash')
                    {{ $slot }}
                </main>

                @if ($quickLinks->isNotEmpty())
                    <nav class="pos-mobile-dock">
                        <div class="grid gap-2 rounded-[22px] border border-white/70 bg-white/92 p-2 shadow-[0_16px_45px_rgba(15,23,42,0.1)] backdrop-blur" style="grid-template-columns: repeat({{ max(1, $quickLinks->count()) }}, minmax(0, 1fr));">
                        @foreach ($quickLinks as $item)
                            <a href="{{ route($item['route']) }}" class="pos-mobile-link {{ request()->routeIs($item['match']) ? 'is-active' : '' }}">
                                <span class="pos-mobile-link-text">{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                        </div>
                    </nav>
                @endif
            </div>
        </div>
    </div>
</body>

</html>
