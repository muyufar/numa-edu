@props(['entity', 'type' => 'guru'])

@php
    $isGuru = $type === 'guru';
    $wilayahInitial = \App\Support\GtkProfilePayload::wilayahInitial($entity);
    $inputClass = 'mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20';
    $labelClass = 'block text-sm font-semibold text-gray-700';
@endphp

<div class="space-y-8">
    <section class="space-y-4">
        <h3 class="border-b border-gray-100 pb-2 text-sm font-bold uppercase tracking-wide text-gray-800">{{ __('Foto') }}</h3>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
            <div class="flex h-28 w-28 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-gray-200 bg-gray-50">
                @if ($entity->fotoUrl())
                    <img src="{{ $entity->fotoUrl() }}" alt="{{ $entity->nama }}" class="h-full w-full object-cover" />
                @else
                    <span class="text-3xl font-bold text-nu-primary/60">{{ mb_strtoupper(mb_substr($entity->nama, 0, 1)) }}</span>
                @endif
            </div>
            <div class="flex-1 space-y-3">
                <div>
                    <label class="{{ $labelClass }}">{{ __('Unggah foto') }}</label>
                    <input type="file" name="foto" accept="image/jpeg,image/png,image/webp" class="{{ $inputClass }}" />
                    <p class="mt-1 text-xs text-gray-500">{{ __('JPG, PNG, atau WebP. Maks. 5 MB.') }}</p>
                    @error('foto')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                @if ($entity->fotoUrl())
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="hapus_foto" value="1" class="h-4 w-4 rounded border-gray-300 text-nu-primary focus:ring-nu-primary" />
                        {{ __('Hapus foto saat ini') }}
                    </label>
                @endif
            </div>
        </div>
    </section>

    @if ($isGuru)
        <section class="space-y-4">
            <h3 class="border-b border-gray-100 pb-2 text-sm font-bold uppercase tracking-wide text-gray-800">{{ __('Akun Masuk') }}</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="{{ $labelClass }}">{{ __('Email masuk') }}</label>
                    <input type="email" name="email" value="{{ old('email', $entity->user?->email) }}" class="{{ $inputClass }}" required />
                    @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="{{ $labelClass }}">{{ __('Kata sandi baru') }}</label>
                    <input type="password" name="password" class="{{ $inputClass }}" autocomplete="new-password" />
                    <p class="mt-1 text-xs text-gray-500">{{ __('Kosongkan jika tidak diubah.') }}</p>
                    @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="{{ $labelClass }}">{{ __('Ulangi kata sandi') }}</label>
                    <input type="password" name="password_confirmation" class="{{ $inputClass }}" autocomplete="new-password" />
                </div>
            </div>
        </section>
    @endif

    <section class="space-y-4">
        <h3 class="border-b border-gray-100 pb-2 text-sm font-bold uppercase tracking-wide text-gray-800">{{ __('Data Diri') }}</h3>
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="{{ $labelClass }}">{{ __('Nama lengkap') }}</label>
                <input type="text" name="nama" value="{{ old('nama', $entity->nama) }}" class="{{ $inputClass }}" required />
                @error('nama')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $labelClass }}">{{ __('NIK') }}</label>
                <input type="text" name="nik" value="{{ old('nik', $entity->nik) }}" maxlength="32" class="{{ $inputClass }} font-mono" />
                @error('nik')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $labelClass }}">{{ __('NUPTK') }}</label>
                <input type="text" name="nuptk" value="{{ old('nuptk', $entity->nuptk) }}" maxlength="32" class="{{ $inputClass }} font-mono" />
                @error('nuptk')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $labelClass }}">{{ __('Jenis kelamin') }}</label>
                <select name="jenis_kelamin" class="{{ $inputClass }}">
                    <option value="">{{ __('—') }}</option>
                    <option value="L" @selected(old('jenis_kelamin', $entity->jenis_kelamin) === 'L')>{{ __('Laki-laki') }}</option>
                    <option value="P" @selected(old('jenis_kelamin', $entity->jenis_kelamin) === 'P')>{{ __('Perempuan') }}</option>
                </select>
                @error('jenis_kelamin')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $labelClass }}">{{ __('Tempat lahir') }}</label>
                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $entity->tempat_lahir) }}" class="{{ $inputClass }}" />
                @error('tempat_lahir')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $labelClass }}">{{ __('Tanggal lahir') }}</label>
                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $entity->tanggal_lahir?->format('Y-m-d')) }}" class="{{ $inputClass }}" />
                @error('tanggal_lahir')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $labelClass }}">{{ __('Agama') }}</label>
                <input type="text" name="agama" value="{{ old('agama', $entity->agama) }}" class="{{ $inputClass }}" />
                @error('agama')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $labelClass }}">{{ __('Status perkawinan') }}</label>
                <input type="text" name="status_perkawinan" value="{{ old('status_perkawinan', $entity->status_perkawinan) }}" class="{{ $inputClass }}" />
                @error('status_perkawinan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <label class="{{ $labelClass }}">{{ __('Nama ibu kandung') }}</label>
                <input type="text" name="nama_ibu_kandung" value="{{ old('nama_ibu_kandung', $entity->nama_ibu_kandung) }}" class="{{ $inputClass }}" />
                @error('nama_ibu_kandung')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $labelClass }}">{{ __('Email pribadi') }}</label>
                <input type="email" name="email_pribadi" value="{{ old('email_pribadi', $entity->email_pribadi) }}" class="{{ $inputClass }}" />
                @error('email_pribadi')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $labelClass }}">{{ __('Kewarganegaraan') }}</label>
                <input type="text" name="kewarganegaraan" value="{{ old('kewarganegaraan', $entity->kewarganegaraan) }}" class="{{ $inputClass }}" placeholder="Indonesia" />
                @error('kewarganegaraan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </section>

    <section class="space-y-4">
        <h3 class="border-b border-gray-100 pb-2 text-sm font-bold uppercase tracking-wide text-gray-800">{{ __('Kepegawaian') }}</h3>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="{{ $labelClass }}">{{ __('Status kepegawaian') }}</label>
                <input type="text" name="status_kepegawaian" value="{{ old('status_kepegawaian', $entity->status_kepegawaian) }}" class="{{ $inputClass }}" placeholder="PNS / Non PNS" />
                @error('status_kepegawaian')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $labelClass }}">{{ __('NIP') }}</label>
                <input type="text" name="nip" value="{{ old('nip', $entity->nip) }}" maxlength="32" class="{{ $inputClass }} font-mono" />
                @error('nip')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $labelClass }}">{{ __('Jenis PTK') }}</label>
                <input type="text" name="jenis_ptk" value="{{ old('jenis_ptk', $entity->jenis_ptk) }}" class="{{ $inputClass }}" />
                @error('jenis_ptk')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $labelClass }}">{{ $isGuru ? __('Tugas / Jabatan') : __('Jabatan') }}</label>
                <input type="text" name="{{ $isGuru ? 'tugas' : 'jabatan' }}" value="{{ old($isGuru ? 'tugas' : 'jabatan', $isGuru ? $entity->tugas : $entity->jabatan) }}" maxlength="128" class="{{ $inputClass }}" />
                @error($isGuru ? 'tugas' : 'jabatan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <label class="{{ $labelClass }}">{{ __('SK Pengangkatan') }}</label>
                <input type="text" name="sk_pengangkatan" value="{{ old('sk_pengangkatan', $entity->sk_pengangkatan) }}" class="{{ $inputClass }}" />
                @error('sk_pengangkatan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $labelClass }}">{{ __('TMT CPNS') }}</label>
                <input type="date" name="tmt_cpns" value="{{ old('tmt_cpns', $entity->tmt_cpns?->format('Y-m-d')) }}" class="{{ $inputClass }}" />
                @error('tmt_cpns')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $labelClass }}">{{ __('TMT PNS') }}</label>
                <input type="date" name="tmt_pns" value="{{ old('tmt_pns', $entity->tmt_pns?->format('Y-m-d')) }}" class="{{ $inputClass }}" />
                @error('tmt_pns')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $labelClass }}">{{ __('TMT Jabatan') }}</label>
                <input type="date" name="tmt_jabatan" value="{{ old('tmt_jabatan', $entity->tmt_jabatan?->format('Y-m-d')) }}" class="{{ $inputClass }}" />
                @error('tmt_jabatan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            @if ($isGuru)
                <div>
                    <label class="{{ $labelClass }}">{{ __('Mata pelajaran') }}</label>
                    <input type="text" name="mata_pelajaran" value="{{ old('mata_pelajaran', $entity->mata_pelajaran) }}" class="{{ $inputClass }}" />
                    @error('mata_pelajaran')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="{{ $labelClass }}">{{ __('Penempatan') }}</label>
                    <input type="text" name="penempatan" value="{{ old('penempatan', $entity->penempatan) }}" class="{{ $inputClass }}" />
                    @error('penempatan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="{{ $labelClass }}">{{ __('Total JTM') }}</label>
                    <input type="text" name="total_jtm" value="{{ old('total_jtm', $entity->total_jtm) }}" maxlength="16" class="{{ $inputClass }}" />
                    @error('total_jtm')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            @else
                <div class="sm:col-span-2 flex items-center gap-3 rounded-xl border border-gray-100 bg-gray-50/80 px-4 py-3">
                    <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-gray-300 text-nu-primary focus:ring-nu-primary" {{ old('is_active', $entity->is_active ?? true) ? 'checked' : '' }} />
                    <label class="text-sm font-medium text-gray-800">{{ __('Aktif (ikut presensi)') }}</label>
                </div>
            @endif
        </div>
    </section>

    <section class="space-y-4">
        <h3 class="border-b border-gray-100 pb-2 text-sm font-bold uppercase tracking-wide text-gray-800">{{ __('Data Alamat Rumah') }}</h3>
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <x-wilayah-alamat-fields :initial="$wilayahInitial" />
            </div>
            <div class="sm:col-span-2">
                <label class="{{ $labelClass }}">{{ __('Alamat jalan / nomor rumah') }}</label>
                <textarea name="alamat_jalan" rows="2" class="{{ $inputClass }}">{{ old('alamat_jalan', $entity->alamat_jalan) }}</textarea>
                @error('alamat_jalan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $labelClass }}">{{ __('RT/RW') }}</label>
                <input type="text" name="rt_rw" value="{{ old('rt_rw', $entity->rt_rw) }}" maxlength="16" class="{{ $inputClass }}" />
                @error('rt_rw')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $labelClass }}">{{ __('Kode pos') }}</label>
                <input type="text" name="kode_pos" value="{{ old('kode_pos', $entity->kode_pos) }}" maxlength="10" class="{{ $inputClass }}" />
                @error('kode_pos')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </section>

    <section class="space-y-4">
        <h3 class="border-b border-gray-100 pb-2 text-sm font-bold uppercase tracking-wide text-gray-800">{{ __('Kontak') }}</h3>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="{{ $labelClass }}">{{ __('No. telepon rumah') }}</label>
                <input type="text" name="telepon_rumah" value="{{ old('telepon_rumah', $entity->telepon_rumah) }}" maxlength="20" class="{{ $inputClass }}" />
                @error('telepon_rumah')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $labelClass }}">{{ __('No. HP') }}</label>
                <input type="text" name="phone" value="{{ old('phone', $entity->phone) }}" maxlength="20" class="{{ $inputClass }} font-mono" />
                @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </section>
</div>
