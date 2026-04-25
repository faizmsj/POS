<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register POS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(37,99,235,0.14),_transparent_28%),radial-gradient(circle_at_top_right,_rgba(251,146,60,0.14),_transparent_24%),linear-gradient(180deg,_#eef4ff_0%,_#f8fafc_42%,_#fdfdfd_100%)] text-slate-900 antialiased">
    <div class="mx-auto flex min-h-screen max-w-7xl items-center px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid w-full gap-6 lg:grid-cols-[1.02fr_0.98fr]">
            <section class="hidden rounded-[36px] border border-white/70 bg-white/75 p-8 shadow-[0_18px_55px_rgba(15,23,42,0.08)] backdrop-blur lg:flex lg:flex-col lg:justify-between">
                <div>
                    <div class="inline-flex h-14 w-14 items-center justify-center rounded-3xl bg-gradient-to-br from-blue-600 via-sky-500 to-cyan-400 text-xl font-bold text-white shadow-lg shadow-blue-500/25">
                        P
                    </div>
                    <p class="mt-8 text-sm font-medium text-blue-600">Registrasi User POS</p>
                    <h1 class="mt-2 text-4xl font-semibold tracking-tight text-slate-950">Buat akun kasir baru dengan cepat.</h1>
                    <p class="mt-4 max-w-xl text-base leading-7 text-slate-500">Registrasi ini membuat akun kasir yang langsung terhubung ke cabang aktif. Hak akses lanjutan tetap bisa diatur oleh admin dari modul Akses Pengguna.</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-[24px] bg-slate-950 px-4 py-5 text-white">
                        <div class="text-xs uppercase tracking-[0.18em] text-slate-300">Role Default</div>
                        <div class="mt-2 text-lg font-semibold">Kasir</div>
                    </div>
                    <div class="rounded-[24px] bg-white px-4 py-5 shadow-sm">
                        <div class="text-xs uppercase tracking-[0.18em] text-slate-400">Branch</div>
                        <div class="mt-2 text-lg font-semibold text-slate-950">Wajib pilih cabang</div>
                    </div>
                    <div class="rounded-[24px] bg-white px-4 py-5 shadow-sm">
                        <div class="text-xs uppercase tracking-[0.18em] text-slate-400">Aman</div>
                        <div class="mt-2 text-lg font-semibold text-slate-950">Password tersembunyi</div>
                    </div>
                </div>
            </section>

            <section class="rounded-[36px] border border-white/70 bg-white/88 p-6 shadow-[0_18px_55px_rgba(15,23,42,0.08)] backdrop-blur sm:p-8">
                <div class="mx-auto max-w-md">
                    <p class="text-sm font-medium text-blue-600">Register User</p>
                    <h2 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Buat akun baru</h2>
                    <p class="mt-2 text-sm text-slate-500">Akun yang dibuat dari sini otomatis terdaftar sebagai kasir.</p>

                    @if ($errors->any())
                        <div class="mt-6 rounded-[22px] border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-700">
                            <div class="font-semibold">Ada data yang perlu diperbaiki:</div>
                            <ul class="mt-2 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>- {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('register.store') }}" method="POST" class="mt-6 space-y-5">
                        @csrf

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="pos-form-input" placeholder="Nama kasir" required autofocus>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="pos-form-input" placeholder="kasir@example.com" required>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Cabang</label>
                            <select name="branch_id" class="pos-form-input" required>
                                <option value="">Pilih cabang</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" @selected((string) old('branch_id') === (string) $branch->id)>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Password</label>
                            <div class="relative">
                                <input id="register-password" type="password" name="password" class="pos-form-input pr-24" placeholder="Masukkan password" required>
                                <button type="button" data-password-toggle="#register-password" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-xl px-3 py-1.5 text-xs font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-slate-900">
                                    Lihat
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Konfirmasi Password</label>
                            <div class="relative">
                                <input id="register-password-confirmation" type="password" name="password_confirmation" class="pos-form-input pr-24" placeholder="Ulangi password" required>
                                <button type="button" data-password-toggle="#register-password-confirmation" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-xl px-3 py-1.5 text-xs font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-slate-900">
                                    Lihat
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-[24px] bg-blue-600 px-5 py-4 text-sm font-semibold text-white shadow-lg shadow-blue-500/25 transition hover:bg-blue-700">
                            Daftarkan User
                        </button>
                    </form>

                    <div class="mt-4 text-center text-sm text-slate-500">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700">Masuk sekarang</a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</body>

</html>
