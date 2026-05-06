@props(['siswa' => null, 'kelasOptions'])

<div class="grid gap-4 sm:grid-cols-3">
    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('NISN') }}</label>
        <input
            name="nis"
            type="text"
            maxlength="32"
            value="{{ old('nis', $siswa?->nis) }}"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            placeholder="Contoh: 3118590419"
            required
        />
        @error('nis')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Nama Lengkap') }}</label>
        <input
            name="nama"
            type="text"
            value="{{ old('nama', $siswa?->nama) }}"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            placeholder="Nama lengkap"
            required
        />
        @error('nama')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-1">
        <label class="block text-sm font-semibold text-gray-700">{{ __('NIK') }}</label>
        <input
            name="nik"
            type="text"
            value="{{ old('nik', $siswa?->nik) }}"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            placeholder="NIK (opsional)"
        />
        @error('nik')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Tempat lahir') }}</label>
        <input
            name="tempat_lahir"
            type="text"
            value="{{ old('tempat_lahir', $siswa?->tempat_lahir) }}"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            placeholder="Tempat lahir (opsional)"
        />
        @error('tempat_lahir')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Kelas') }}</label>
        <select
            name="kelas_id"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
        >
            <option value="">{{ __('— Pilih kelas (opsional) —') }}</option>
            @foreach($kelasOptions as $k)
                <option value="{{ $k->id }}" {{ (string) old('kelas_id', $siswa?->kelas_id) === (string) $k->id ? 'selected' : '' }}>
                    {{ $k->tingkat }} {{ $k->nama }} · {{ $k->tahun_ajaran }}{{ $k->is_active ? '' : ' (nonaktif)' }}
                </option>
            @endforeach
        </select>
        @error('kelas_id')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Tingkat - Rombel') }}</label>
        <input
            name="tingkat_rombel"
            type="text"
            value="{{ old('tingkat_rombel', $siswa?->tingkat_rombel) }}"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            placeholder="Contoh: Kelas 9 - KELAS 9A"
        />
        @error('tingkat_rombel')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-xs text-gray-500">{{ __('Boleh dikosongkan bila sudah memilih Kelas.') }}</p>
    </div>

    <div class="sm:col-span-1">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Umur') }}</label>
        <input
            name="umur"
            type="text"
            value="{{ old('umur', $siswa?->umur) }}"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            placeholder="Contoh: 15 th, 1 bln"
        />
        @error('umur')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-1">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Status') }}</label>
        <input
            name="status"
            type="text"
            value="{{ old('status', $siswa?->status) }}"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            placeholder="Contoh: Aktif"
        />
        @error('status')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Jenis kelamin') }}</label>
        <select
            name="jenis_kelamin"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
        >
            <option value="">{{ __('—') }}</option>
            <option value="L" {{ old('jenis_kelamin', $siswa?->jenis_kelamin) === 'L' ? 'selected' : '' }}>{{ __('Laki-laki') }}</option>
            <option value="P" {{ old('jenis_kelamin', $siswa?->jenis_kelamin) === 'P' ? 'selected' : '' }}>{{ __('Perempuan') }}</option>
        </select>
        @error('jenis_kelamin')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-1">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Tanggal lahir') }}</label>
        <input
            name="tanggal_lahir"
            type="date"
            value="{{ old('tanggal_lahir', $siswa?->tanggal_lahir?->format('Y-m-d')) }}"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
        />
        @error('tanggal_lahir')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Alamat') }}</label>
        <textarea
            name="alamat"
            rows="3"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            placeholder="Alamat (opsional)"
        >{{ old('alamat', $siswa?->alamat) }}</textarea>
        @error('alamat')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-1">
        <label class="block text-sm font-semibold text-gray-700">{{ __('No Telepon') }}</label>
        <input
            name="no_telepon"
            type="text"
            value="{{ old('no_telepon', $siswa?->no_telepon) }}"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            placeholder="No telepon (opsional)"
        />
        @error('no_telepon')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-1">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Kebutuhan khusus') }}</label>
        <input
            name="kebutuhan_khusus"
            type="text"
            value="{{ old('kebutuhan_khusus', $siswa?->kebutuhan_khusus) }}"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            placeholder="Contoh: Tidak Ada"
        />
        @error('kebutuhan_khusus')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-1">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Disabilitas') }}</label>
        <input
            name="disabilitas"
            type="text"
            value="{{ old('disabilitas', $siswa?->disabilitas) }}"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            placeholder="Contoh: Tidak Ada"
        />
        @error('disabilitas')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-1">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Nomor KIP/PIP') }}</label>
        <input
            name="nomor_kip_pip"
            type="text"
            value="{{ old('nomor_kip_pip', $siswa?->nomor_kip_pip) }}"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            placeholder="Nomor (opsional)"
        />
        @error('nomor_kip_pip')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-1">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Nama Ayah Kandung') }}</label>
        <input
            name="nama_ayah_kandung"
            type="text"
            value="{{ old('nama_ayah_kandung', $siswa?->nama_ayah_kandung) }}"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            placeholder="Nama ayah (opsional)"
        />
        @error('nama_ayah_kandung')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-1">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Nama Ibu Kandung') }}</label>
        <input
            name="nama_ibu_kandung"
            type="text"
            value="{{ old('nama_ibu_kandung', $siswa?->nama_ibu_kandung) }}"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            placeholder="Nama ibu (opsional)"
        />
        @error('nama_ibu_kandung')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-1">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Nama Wali') }}</label>
        <input
            name="nama_wali"
            type="text"
            value="{{ old('nama_wali', $siswa?->nama_wali) }}"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            placeholder="Nama wali (opsional)"
        />
        @error('nama_wali')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

