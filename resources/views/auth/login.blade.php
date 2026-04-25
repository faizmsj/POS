<!DOCTYPE html>
<html lang="id">

<head>
    @php
        $storeName = \App\Models\Setting::valueOf('store_name', 'POS Kasir');
        $storeLogo = \App\Models\Setting::assetUrl(\App\Models\Setting::valueOf('store_logo'));
        $storeFavicon = \App\Models\Setting::assetUrl(\App\Models\Setting::valueOf('store_favicon')) ?: $storeLogo;
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login {{ $storeName }}</title>
    @if ($storeFavicon)
        <link rel="icon" type="image/png" href="{{ $storeFavicon }}">
        <link rel="apple-touch-icon" href="{{ $storeFavicon }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(37,99,235,0.14),_transparent_28%),radial-gradient(circle_at_top_right,_rgba(251,146,60,0.14),_transparent_24%),linear-gradient(180deg,_#eef4ff_0%,_#f8fafc_42%,_#fdfdfd_100%)] text-slate-900 antialiased">
    <div class="mx-auto flex min-h-screen max-w-7xl items-center px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid w-full gap-6 lg:grid-cols-[1.1fr_0.9fr]">
            <section class="hidden rounded-[36px] border border-white/70 bg-white/75 p-8 shadow-[0_18px_55px_rgba(15,23,42,0.08)] backdrop-blur lg:flex lg:flex-col lg:justify-between">
                <div>
                    <div class="inline-flex h-14 w-14 items-center justify-center overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 via-sky-500 to-cyan-400 text-xl font-bold text-white shadow-lg shadow-blue-500/25">
                        @if ($storeLogo)
                            <img src="{{ $storeLogo }}" alt="Logo toko" class="h-full w-full object-contain p-2">
                        @else
                            P
                        @endif
                    </div>
                    <p class="mt-8 text-sm font-medium text-blue-600">POS Multicabang 2026</p>
                    <h1 class="mt-2 text-4xl font-semibold tracking-tight text-slate-950">Masuk ke panel operasional bisnis Anda.</h1>
                    <p class="mt-4 max-w-xl text-base leading-7 text-slate-500">Akses dashboard, POS kasir, PPOB, FIFO inventory, loyalty, shift kasir, dan seluruh konfigurasi toko dari satu sistem terpusat.</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-[24px] bg-slate-950 px-4 py-5 text-white">
                        <div class="text-xs uppercase tracking-[0.18em] text-slate-300">Multi Branch</div>
                        <div class="mt-2 text-lg font-semibold">Cabang terpusat</div>
                    </div>
                    <div class="rounded-[24px] bg-white px-4 py-5 shadow-sm">
                        <div class="text-xs uppercase tracking-[0.18em] text-slate-400">FIFO</div>
                        <div class="mt-2 text-lg font-semibold text-slate-950">HPP akurat</div>
                    </div>
                    <div class="rounded-[24px] bg-white px-4 py-5 shadow-sm">
                        <div class="text-xs uppercase tracking-[0.18em] text-slate-400">Realtime</div>
                        <div class="mt-2 text-lg font-semibold text-slate-950">Shift & PPOB</div>
                    </div>
                </div>
            </section>

            <section class="rounded-[36px] border border-white/70 bg-white/88 p-6 shadow-[0_18px_55px_rgba(15,23,42,0.08)] backdrop-blur sm:p-8">
                <div class="mx-auto max-w-md">
                    <p class="text-sm font-medium text-blue-600">Login Sistem</p>
                    <h2 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Masuk ke akun Anda</h2>
                    <p class="mt-2 text-sm text-slate-500">Gunakan akun yang sudah terdaftar untuk mengakses aplikasi POS.</p>

                    @if ($errors->any())
                        <div class="mt-6 rounded-[22px] border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('login.store') }}" method="POST" class="mt-6 space-y-5">
                        @csrf

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="pos-form-input" placeholder="admin@example.com" required autofocus data-validate-email="#login-email-feedback">
                            <div id="login-email-feedback" class="mt-2 text-xs text-slate-500">Gunakan format email aktif yang bisa dipakai untuk login.</div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Password</label>
                            <div class="relative">
                                <input id="login-password" type="password" name="password" class="pos-form-input pr-24" placeholder="Masukkan password" required data-capslock-target="#login-capslock-feedback">
                                <button type="button" data-password-toggle="#login-password" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-xl px-3 py-1.5 text-xs font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-slate-900">
                                    Lihat
                                </button>
                            </div>
                            <div id="login-capslock-feedback" class="mt-2 text-xs text-slate-500"></div>
                            <div class="mt-2 text-xs text-slate-500">Password tetap tersembunyi secara default untuk keamanan.</div>
                        </div>

                        <label class="flex items-center gap-3 rounded-[20px] bg-slate-50 px-4 py-3 text-sm text-slate-600">
                            <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span>Ingat saya di perangkat ini</span>
                        </label>

                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-[24px] bg-blue-600 px-5 py-4 text-sm font-semibold text-white shadow-lg shadow-blue-500/25 transition hover:bg-blue-700">
                            Masuk
                        </button>
                    </form>

                    <div class="mt-4 text-center text-sm text-slate-500">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-700">Daftar user baru</a>
                    </div>

                    <div class="mt-6 rounded-[24px] border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-600">
                        <div class="font-semibold text-slate-900">Akun demo</div>
                        <div class="mt-2">Admin: <span class="font-medium">admin@example.com</span></div>
                        <div>Cabang awal: <span class="font-medium">Cabang Pusat</span></div>
                        <div>Password: <span class="font-medium">password</span></div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</body>

</html>
