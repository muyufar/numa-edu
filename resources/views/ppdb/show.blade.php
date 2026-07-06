<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Detail PPDB') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ $registration->nama }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('ppdb.index') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">{{ __('Daftar') }}</a>
                @can('update', $registration)
                    <a href="{{ route('ppdb.edit', $registration) }}" class="inline-flex items-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">{{ __('Edit') }}</a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('status') }}</div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white p-6 shadow-sm ring-1 ring-black/5">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 pb-4">
                <div class="text-sm font-semibold text-gray-500">{{ __('Status') }}</div>
                @include('ppdb.partials.status-badge', ['status' => $registration->status])
            </div>
            <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Nama') }}</dt>
                    <dd class="mt-1 font-medium text-gray-900">{{ $registration->nama }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('TTL') }}</dt>
                    <dd class="mt-1 text-gray-800">
                        {{ $registration->tempat_lahir ?: '—' }}{{ $registration->tanggal_lahir ? ', '.\App\Support\DateTimeFormat::date($registration->tanggal_lahir, '') : '' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Jenis kelamin') }}</dt>
                    <dd class="mt-1 text-gray-800">{{ $registration->jenis_kelamin === 'L' ? __('Laki-laki') : ($registration->jenis_kelamin === 'P' ? __('Perempuan') : '—') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Asal sekolah') }}</dt>
                    <dd class="mt-1 text-gray-800">{{ $registration->asal_sekolah ?: '—' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Alamat') }}</dt>
                    <dd class="mt-1 text-gray-800">{{ $registration->alamat ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('No. HP orang tua') }}</dt>
                    <dd class="mt-1 font-mono text-gray-800">{{ $registration->no_hp_ortu ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Email') }}</dt>
                    <dd class="mt-1 text-gray-800">{{ $registration->email ?: '—' }}</dd>
                </div>
                <div class="sm:col-span-2 text-xs text-gray-500">
                    {{ __('Terdaftar') }}: {{ \App\Support\DateTimeFormat::datetime($registration->created_at) }}
                    @if ($registration->updated_at && ! $registration->updated_at->equalTo($registration->created_at))
                        · {{ __('Diubah') }}: {{ \App\Support\DateTimeFormat::datetime($registration->updated_at) }}
                    @endif
                </div>
            </dl>
        </div>

        @if ($registration->siswa)
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/80 p-5 text-sm text-emerald-900 shadow-sm ring-1 ring-emerald-200/60">
                <div class="font-semibold">{{ __('Sudah menjadi siswa') }}</div>
                <p class="mt-1 text-emerald-800">
                    {{ __('NIS') }}: <span class="font-mono font-bold">{{ $registration->siswa->nis }}</span>
                    @if ($registration->siswa->nisn)
                        · {{ __('NISN') }}: <span class="font-mono font-bold">{{ $registration->siswa->nisn }}</span>
                    @endif
                    — {{ $registration->siswa->nama }}
                </p>
                @can('update', $registration->siswa)
                    <a href="{{ route('siswa.edit', $registration->siswa) }}" class="mt-3 inline-flex items-center rounded-xl bg-nu-primary px-4 py-2 text-xs font-semibold text-white hover:bg-nu-primary-light">
                        {{ __('Buka data siswa') }}
                    </a>
                @endcan
            </div>
        @elseif ($registration->status === 'accepted')
            @can('create', \App\Models\Siswa::class)
                @can('update', $registration)
                    <div class="rounded-2xl border border-gray-100/80 bg-white p-6 shadow-sm ring-1 ring-black/5">
                        <h3 class="text-sm font-bold text-gray-900">{{ __('Jadikan siswa') }}</h3>
                        <p class="mt-1 text-sm text-gray-600">{{ __('Isi NIS resmi sekolah, NISN (opsional), dan pilih kelas jika sudah ada. Nama, TTL, alamat, dan JK diambil dari PPDB.') }}</p>
                        @if ($kelasOptions->isEmpty())
                            <p class="mt-2 text-xs text-amber-800">{{ __('Belum ada kelas di master — siswa tetap bisa dibuat; kelas bisa ditambahkan lalu diisi lewat edit siswa.') }}</p>
                        @endif
                        <form method="POST" action="{{ route('ppdb.promote-siswa', $registration) }}" class="mt-5 grid gap-4 sm:grid-cols-2">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-gray-600">{{ __('NIS') }}</label>
                                <input type="text" name="nis" maxlength="32" value="{{ old('nis') }}" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2.5 font-mono text-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" placeholder="{{ __('Nomor induk sekolah') }}" required />
                                @error('nis')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600">{{ __('NISN') }}</label>
                                <input type="text" name="nisn" maxlength="32" value="{{ old('nisn') }}" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2.5 font-mono text-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" placeholder="{{ __('Opsional') }}" />
                                @error('nisn')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600">{{ __('Kelas') }}</label>
                                <select name="kelas_id" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" @disabled($kelasOptions->isEmpty())>
                                    <option value="">{{ __('— Belum ditetapkan —') }}</option>
                                    @foreach ($kelasOptions as $k)
                                        <option value="{{ $k->id }}" {{ (string) old('kelas_id') === (string) $k->id ? 'selected' : '' }}>
                                            {{ $k->tingkat }} {{ $k->nama }} · {{ $k->tahun_ajaran }}{{ $k->is_active ? '' : ' (nonaktif)' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kelas_id')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                                    {{ __('Buat data siswa') }}
                                </button>
                            </div>
                        </form>
                    </div>
                @endcan
            @endcan
        @endif
    </div>
</x-app-layout>
