<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Inline override: avoids stale Vite/CSS cache */
        html, body {
            margin: 0;
            padding: 0;
        }

        .bg-nu-landing {
            background-color: #22c55e;
            background-image:
                radial-gradient(circle at 18% 28%, rgba(255, 255, 255, 0.22) 0 22%, transparent 23%),
                radial-gradient(circle at 76% 36%, rgba(255, 255, 255, 0.14) 0 26%, transparent 27%),
                radial-gradient(circle at 70% 78%, rgba(13, 74, 44, 0.12) 0 22%, transparent 23%),
                linear-gradient(90deg, #16a34a 0%, #34d399 55%, #22c55e 100%);
        }

        .nu-section-soft {
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
        }

        .nu-cta-gradient {
            background-image: linear-gradient(135deg, rgba(16, 185, 129, 1) 0%, rgba(59, 130, 246, 1) 100%);
        }
    </style>
</head>
<body class="min-h-screen bg-nu-landing font-sans text-gray-900 antialiased overflow-x-hidden">
    <div class="w-full">
        <div class="mx-auto max-w-6xl px-6 pt-10 pb-16 sm:pt-14 sm:pb-20">
            <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <x-application-logo class="h-10 w-10 fill-current text-nu-gold" />
                    <div class="leading-tight">
                        <div class="badge-nu">Lingkungan Pendidikan Ma'arif</div>
                        <div class="mt-2 text-2xl font-bold tracking-tight text-white sm:text-3xl">{{ config('app.name') }}</div>
                    </div>
                </div>

                <nav class="flex flex-wrap items-center gap-2 rounded-2xl bg-white/10 p-2 ring-1 ring-white/15 backdrop-blur sm:gap-3">
                    <a href="{{ route('informasi.index') }}" class="rounded-xl px-4 py-2 text-sm font-semibold text-white/90 hover:bg-white/10 hover:text-white">{{ __('Informasi') }}</a>
                    <a href="{{ route('ppdb.daftar') }}" class="rounded-xl px-4 py-2 text-sm font-semibold text-white/90 hover:bg-white/10 hover:text-white">{{ __('Formulir PPDB') }}</a>
                    <span class="mx-1 hidden h-6 w-px bg-white/15 sm:block" aria-hidden="true"></span>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-nu-primary !px-4 !py-2.5">{{ __('Dashboard') }}</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-nu-primary !px-4 !py-2.5">{{ __('Log in') }}</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="rounded-xl border border-white/25 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white hover:bg-white/15">{{ __('Daftar') }}</a>
                        @endif
                    @endauth
                </nav>
            </header>

            <main class="mt-10 lg:mt-12">
                <section class="grid gap-10 lg:grid-cols-12 lg:items-start">
                    <div class="lg:col-span-7">
                        <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
                            Sistem sekolah terpadu yang rapi, cepat, dan mudah dipahami.
                        </h1>
                        <p class="mt-5 max-w-xl text-base leading-relaxed text-white/80 sm:text-lg">
                            Satu dashboard untuk akademik, keuangan, absensi, BK, PPDB, dan inventaris — dibangun modular agar mudah dirawat dan dikembangkan.
                        </p>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn-nu-primary w-full sm:w-auto !py-2.5">{{ __('Masuk dashboard') }}</a>
                            @else
                                <a href="{{ route('login') }}" class="btn-nu-primary w-full sm:w-auto !py-2.5">{{ __('Masuk') }}</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn-nu w-full border border-white/30 bg-white/10 text-white hover:bg-white/15 sm:w-auto !py-2.5">{{ __('Daftar') }}</a>
                                @endif
                            @endauth
                            <a href="{{ route('informasi.index') }}" class="btn-nu w-full border border-white/30 bg-white/10 text-white hover:bg-white/15 sm:w-auto !py-2.5">{{ __('Informasi') }}</a>
                        </div>

                        @if (!empty($highlights))
                            <div class="mt-6 flex flex-wrap items-center gap-2 text-white/85">
                                <span class="inline-flex items-center gap-2 rounded-full bg-black/10 px-3 py-1 text-xs font-semibold ring-1 ring-white/15">
                                    <span class="h-1.5 w-1.5 rounded-full bg-nu-gold"></span>
                                    <span>{{ __('PPDB menunggu') }}</span>
                                    <span class="font-bold text-white">{{ number_format((int) ($highlights['ppdb_pending'] ?? 0)) }}</span>
                                </span>
                                <span class="inline-flex items-center gap-2 rounded-full bg-black/10 px-3 py-1 text-xs font-semibold ring-1 ring-white/15">
                                    <span class="h-1.5 w-1.5 rounded-full bg-nu-gold"></span>
                                    <span>{{ __('Perizinan pending') }}</span>
                                    <span class="font-bold text-white">{{ number_format((int) ($highlights['perizinan_pending'] ?? 0)) }}</span>
                                </span>
                                <span class="inline-flex items-center gap-2 rounded-full bg-black/10 px-3 py-1 text-xs font-semibold ring-1 ring-white/15">
                                    <span class="h-1.5 w-1.5 rounded-full bg-nu-gold"></span>
                                    <span>{{ __('Stok menipis') }}</span>
                                    <span class="font-bold text-white">{{ number_format((int) ($highlights['stok_minimum'] ?? 0)) }}</span>
                                </span>
                            </div>
                        @endif
                    </div>

                    <aside class="lg:col-span-5">
                        <div class="rounded-3xl bg-white/95 p-6 shadow-xl ring-1 ring-black/5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Statistik') }}</div>
                                    <div class="mt-1 text-lg font-extrabold text-gray-900">{{ __('Kekuatan sekolah dalam angka') }}</div>
                                </div>
                                <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-nu-primary hover:underline">{{ __('Dashboard') }} →</a>
                            </div>
                            <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                @foreach (($stats ?? []) as $st)
                                    <div class="rounded-2xl bg-gray-50 p-4 ring-1 ring-gray-100">
                                        <div class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">{{ $st['label'] }}</div>
                                        <div class="mt-2 text-2xl font-extrabold text-nu-primary">{{ number_format((int) $st['value']) }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </aside>
                </section>
            </main>
        </div>
    </div>

    <div class="bg-white">
        <div class="mx-auto max-w-6xl px-6 py-14">
            <section>
                <div class="text-center">
                    <h2 class="text-2xl font-extrabold tracking-tight text-gray-900 sm:text-3xl">{{ __('Kekuatan sekolah dalam angka') }}</h2>
                    <p class="mx-auto mt-2 max-w-2xl text-sm text-gray-600">{{ __('Ringkasan data real dari database untuk membantu pengambilan keputusan.') }}</p>
                </div>

                <div class="mt-8 grid gap-4 lg:grid-cols-12">
                    <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm ring-1 ring-black/5 lg:col-span-5">
                        <div class="flex items-start gap-4">
                            <div class="rounded-2xl bg-nu-primary/10 p-3 text-nu-primary">
                                <x-application-logo class="h-8 w-8 fill-current" />
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Sekolah') }}</div>
                                <div class="mt-1 truncate text-lg font-extrabold text-gray-900">{{ config('app.name') }}</div>
                                <p class="mt-2 text-sm text-gray-600">
                                    {{ __('Dashboard terintegrasi untuk operasional harian, pelaporan, dan layanan sekolah.') }}
                                </p>
                            </div>
                        </div>

                        @if (!empty($highlights))
                            <div class="mt-5 flex flex-wrap gap-2">
                                <span class="inline-flex items-center gap-2 rounded-full bg-gray-50 px-3 py-1 text-xs font-semibold text-gray-700 ring-1 ring-gray-100">
                                    <span class="h-1.5 w-1.5 rounded-full bg-nu-primary/70"></span>
                                    {{ __('PPDB menunggu') }}: <span class="font-extrabold text-nu-primary">{{ number_format((int) ($highlights['ppdb_pending'] ?? 0)) }}</span>
                                </span>
                                <span class="inline-flex items-center gap-2 rounded-full bg-gray-50 px-3 py-1 text-xs font-semibold text-gray-700 ring-1 ring-gray-100">
                                    <span class="h-1.5 w-1.5 rounded-full bg-nu-primary/70"></span>
                                    {{ __('Perizinan pending') }}: <span class="font-extrabold text-nu-primary">{{ number_format((int) ($highlights['perizinan_pending'] ?? 0)) }}</span>
                                </span>
                                <span class="inline-flex items-center gap-2 rounded-full bg-gray-50 px-3 py-1 text-xs font-semibold text-gray-700 ring-1 ring-gray-100">
                                    <span class="h-1.5 w-1.5 rounded-full bg-nu-primary/70"></span>
                                    {{ __('Stok menipis') }}: <span class="font-extrabold text-nu-primary">{{ number_format((int) ($highlights['stok_minimum'] ?? 0)) }}</span>
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 lg:col-span-7 lg:grid-cols-3">
                        @foreach (($stats ?? []) as $st)
                            <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm ring-1 ring-black/5">
                                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $st['label'] }}</div>
                                <div class="mt-2 text-3xl font-extrabold tracking-tight text-nu-primary">{{ number_format((int) $st['value']) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div class="nu-section-soft">
        <div class="mx-auto max-w-6xl px-6 py-14">
            <section class="grid gap-6 lg:grid-cols-12">
                @php
                    $maxKelas = 0;
                    foreach (($kelasTerpadat ?? []) as $k) { $maxKelas = max($maxKelas, (int) ($k->siswas_count ?? 0)); }
                    $maxKelas = $maxKelas ?: 1;
                    $palette = [
                        ['bg' => 'bg-emerald-50', 'dot' => 'bg-emerald-400', 'text' => 'text-emerald-700'],
                        ['bg' => 'bg-sky-50', 'dot' => 'bg-sky-400', 'text' => 'text-sky-700'],
                        ['bg' => 'bg-amber-50', 'dot' => 'bg-amber-400', 'text' => 'text-amber-700'],
                        ['bg' => 'bg-fuchsia-50', 'dot' => 'bg-fuchsia-400', 'text' => 'text-fuchsia-700'],
                    ];
                @endphp

                <div class="rounded-3xl border border-gray-100 bg-white p-7 shadow-sm ring-1 ring-black/5 lg:col-span-7">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Regional insights') }}</div>
                            <h3 class="mt-1 text-lg font-extrabold text-gray-900">{{ __('Kelas terpadat') }}</h3>
                        </div>
                        <span class="text-xs font-semibold text-gray-500">{{ __('Top 3') }}</span>
                    </div>
                    <div class="mt-6 space-y-4">
                        @forelse ($kelasTerpadat ?? [] as $k)
                            @php
                                $v = (int) $k->siswas_count;
                                $pct = (int) round(($v / $maxKelas) * 100);
                            @endphp
                            <div>
                                <div class="flex items-center justify-between text-sm font-semibold text-gray-700">
                                    <span>{{ $k->tingkat }} {{ $k->nama }} <span class="text-xs font-semibold text-gray-500">· {{ $k->tahun_ajaran }}</span></span>
                                    <span class="text-nu-primary">{{ number_format($v) }}</span>
                                </div>
                                <div class="mt-2 h-2.5 w-full rounded-full bg-gray-100">
                                    <div class="h-2.5 rounded-full bg-gradient-to-r from-nu-primary to-emerald-400" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl bg-gray-50 p-6 text-center text-sm text-gray-500 ring-1 ring-gray-100">{{ __('Belum ada data kelas.') }}</div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-3xl border border-gray-100 bg-white p-7 shadow-sm ring-1 ring-black/5 lg:col-span-5">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Education levels') }}</div>
                            <h3 class="mt-1 text-lg font-extrabold text-gray-900">{{ __('Distribusi tingkat') }}</h3>
                        </div>
                        <span class="text-xs font-semibold text-gray-500">{{ __('% siswa') }}</span>
                    </div>
                    <div class="mt-6 grid grid-cols-2 gap-4">
                        @forelse (($distribusiJenjang ?? []) as $i => $row)
                            @php $c = $palette[$i % count($palette)]; @endphp
                            <div class="rounded-3xl {{ $c['bg'] }} p-5 ring-1 ring-black/5">
                                <div class="flex items-center justify-between">
                                    <div class="inline-flex items-center gap-2 text-sm font-semibold text-gray-700">
                                        <span class="h-2.5 w-2.5 rounded-full {{ $c['dot'] }}"></span>
                                        {{ __('Tingkat') }} {{ $row['tingkat'] }}
                                    </div>
                                    <div class="text-xs font-semibold text-gray-500">{{ number_format((int) $row['jumlah_siswa']) }}</div>
                                </div>
                                <div class="mt-4 text-3xl font-extrabold {{ $c['text'] }}">{{ (int) $row['pct'] }}%</div>
                            </div>
                        @empty
                            <div class="col-span-2 rounded-2xl bg-gray-50 p-6 text-center text-sm text-gray-500 ring-1 ring-gray-100">{{ __('Belum ada data distribusi.') }}</div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div class="bg-white">
        <div class="mx-auto max-w-6xl px-6 py-16">
            <section>
                <div class="nu-cta-gradient rounded-[2.75rem] px-7 py-14 text-center shadow-xl ring-1 ring-black/10 sm:px-12">
                    <div class="mx-auto max-w-3xl">
                        <div class="inline-flex items-center rounded-full bg-white/20 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white ring-1 ring-white/25">
                            {{ __('Numa-Edu') }}
                        </div>
                        <h2 class="mt-6 text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                            {{ __('Digitalisasi data sekolah yang terintegrasi') }}
                        </h2>
                        <p class="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-white/90 sm:text-base">
                            {{ __('Kelola data, operasional, dan laporan dengan alur yang rapi. Mulai dari master data, lalu presensi, perizinan, inventaris, hingga pelaporan.') }}
                        </p>

                        <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center rounded-2xl bg-white px-6 py-3 text-sm font-bold text-nu-primary shadow-sm hover:bg-white/90">
                                    {{ __('Masuk Dashboard') }}
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-2xl bg-white px-6 py-3 text-sm font-bold text-nu-primary shadow-sm hover:bg-white/90">
                                    {{ __('Masuk') }}
                                </a>
                            @endauth
                            <a href="{{ route('ppdb.daftar') }}" class="inline-flex items-center justify-center rounded-2xl bg-black/20 px-6 py-3 text-sm font-bold text-white ring-1 ring-white/25 hover:bg-black/25">
                                {{ __('Formulir PPDB') }}
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <footer class="mt-12 border-t border-gray-200/80 pt-8 text-sm text-gray-600">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="font-semibold text-gray-900">© {{ date('Y') }} {{ config('app.name') }}</div>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('informasi.index') }}" class="font-semibold text-nu-primary hover:underline">{{ __('Informasi') }}</a>
                        <a href="{{ route('ppdb.daftar') }}" class="font-semibold text-nu-primary hover:underline">{{ __('PPDB') }}</a>
                        @auth
                            <a href="{{ url('/dashboard') }}" class="font-semibold text-nu-primary hover:underline">{{ __('Dashboard') }}</a>
                        @else
                            <a href="{{ route('login') }}" class="font-semibold text-nu-primary hover:underline">{{ __('Login') }}</a>
                        @endauth
                    </div>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>
