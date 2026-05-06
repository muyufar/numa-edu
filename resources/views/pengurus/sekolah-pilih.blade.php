<x-app-layout>
    <div class="mx-auto max-w-2xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">{{ __('Sekolah & konteks') }}</h1>
                <p class="mt-1 text-sm text-gray-600">
                    @if (! empty($isSuperAdmin))
                        {{ __('Daftar semua sekolah aktif. Pilih sekolah untuk bertindak sebagai pengawas (konteks data), atau daftarkan sekolah baru.') }}
                    @else
                        {{ __('Pilih sekolah di bawah cabang Anda untuk melihat dan mengelola data. Anda dapat mengganti sekolah kapan saja dari menu ini.') }}
                    @endif
                </p>
            </div>
            @can('create', \App\Models\Sekolah::class)
                <a href="{{ route('pengurus.sekolah.create') }}" class="inline-flex shrink-0 items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                    {{ __('+ Sekolah baru') }}
                </a>
            @endcan
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        @php($op = session('operator_setup'))
        @if (is_array($op))
            <div class="rounded-xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-950 shadow-sm">
                <p class="font-semibold">{{ __('Akun operator sekolah (simpan sekarang)') }}</p>
                <p class="mt-2 text-amber-900/90">{{ __('Lembaga: :nama', ['nama' => $op['sekolah'] ?? '']) }}</p>
                <dl class="mt-3 space-y-1 font-mono text-xs sm:text-sm">
                    <div><span class="text-amber-800/80">email</span> {{ $op['email'] ?? '' }}</div>
                    <div><span class="text-amber-800/80">password</span> {{ $op['password'] ?? '' }}</div>
                </dl>
                <p class="mt-3 text-xs text-amber-900/80">{{ __('Pesan ini hanya ditampilkan sekali. Sarankan operator mengganti password setelah login pertama.') }}</p>
            </div>
        @endif

        @if (! empty($missingCabang))
            <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                {{ __('Akun Anda belum dihubungkan ke cabang. Hubungi super admin untuk mengatur cabang_id.') }}
            </div>
        @elseif ($sekolahs->isEmpty())
            <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                {{ __('Belum ada sekolah aktif di cabang ini. Tambahkan sekolah baru dengan tombol di atas.') }}
            </div>
        @else
            <ul class="divide-y divide-gray-200 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                @foreach ($sekolahs as $sekolah)
                    <li class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            @if (! empty($isSuperAdmin) && $sekolah->cabang)
                                <div class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">{{ $sekolah->cabang->nama }}</div>
                            @endif
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-semibold text-gray-900">{{ $sekolah->nama }}</span>
                                @if (! $sekolah->is_active)
                                    <span class="rounded-full bg-gray-200 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-gray-700">{{ __('Nonaktif') }}</span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500">
                                NPSN {{ $sekolah->npsn }}
                                <span class="text-gray-400">·</span> {{ \App\Models\Sekolah::jenjangLabel($sekolah->jenjang) }}
                            </div>
                            @if ($sekolah->alamatWilayahRingkas() || filled($sekolah->alamat_dusun))
                                <p class="mt-1 max-w-xl text-xs leading-relaxed text-gray-600">
                                    @if ($sekolah->alamatWilayahRingkas())
                                        <span class="text-gray-700">{{ $sekolah->alamatWilayahRingkas() }}</span>
                                    @endif
                                    @if (filled($sekolah->alamat_dusun))
                                        @if ($sekolah->alamatWilayahRingkas())<span class="text-gray-400"> · </span>@endif
                                        <span>{{ \Illuminate\Support\Str::limit((string) $sekolah->alamat_dusun, 120) }}</span>
                                    @endif
                                </p>
                            @endif
                        </div>
                        <div class="flex shrink-0 flex-wrap items-center gap-2">
                            @can('update', $sekolah)
                                <a href="{{ route('pengurus.sekolah.edit', $sekolah) }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                                    {{ __('Profil') }}
                                </a>
                            @endcan
                            @role('pengurus_cabang')
                                @if ($sekolah->is_active)
                                    <form method="post" action="{{ route('pengurus.sekolah.pilih') }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="sekolah_id" value="{{ $sekolah->id }}" />
                                        <x-primary-button type="submit">{{ __('Gunakan') }}</x-primary-button>
                                    </form>
                                @endif
                            @endrole
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        @role('pengurus_cabang')
            @if (session('pengurus_sekolah_id'))
                <form method="post" action="{{ route('pengurus.sekolah.reset') }}">
                    @csrf
                    <x-secondary-button type="submit">{{ __('Hapus konteks sekolah') }}</x-secondary-button>
                </form>
            @endif
        @endrole
    </div>
</x-app-layout>
