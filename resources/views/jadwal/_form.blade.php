@props(['jadwal' => null, 'kelasOptions', 'mapelOptions', 'guruOptions', 'tahunAjaranOptions'])

@php
    $jamOld = static function (?string $field, $model) {
        $o = old($field);
        if ($o !== null) {
            return is_string($o) ? substr($o, 0, 5) : $o;
        }
        if (! $model?->{$field}) {
            return '';
        }
        $v = $model->{$field};

        return is_string($v) ? substr($v, 0, 5) : $v->format('H:i');
    };
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Tahun ajaran') }}</label>
        @if ($tahunAjaranOptions->isNotEmpty())
            <select
                name="tahun_ajaran"
                class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
                required
            >
                @foreach ($tahunAjaranOptions as $t)
                    <option value="{{ $t }}" {{ (string) old('tahun_ajaran', $jadwal?->tahun_ajaran) === (string) $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        @else
            <input
                name="tahun_ajaran"
                type="text"
                maxlength="16"
                value="{{ old('tahun_ajaran', $jadwal?->tahun_ajaran) }}"
                class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
                placeholder="2025/2026"
                required
            />
            <p class="mt-1 text-xs text-gray-500">{{ __('Belum ada kelas di master. Isi tahun ajaran manual, lalu buat kelas agar muncul di daftar pilihan.') }}</p>
        @endif
        @error('tahun_ajaran')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Hari') }}</label>
        <select
            name="hari"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            required
        >
            @foreach (\App\Models\Jadwal::HARI_OPTIONS as $h)
                <option value="{{ $h }}" {{ old('hari', $jadwal?->hari) === $h ? 'selected' : '' }}>{{ $h }}</option>
            @endforeach
        </select>
        @error('hari')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Kelas') }}</label>
        <select
            name="kelas_id"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            required
        >
            <option value="">{{ __('— Pilih kelas —') }}</option>
            @foreach ($kelasOptions as $k)
                <option value="{{ $k->id }}" {{ (string) old('kelas_id', $jadwal?->kelas_id) === (string) $k->id ? 'selected' : '' }}>
                    {{ $k->tingkat }} {{ $k->nama }} · {{ $k->tahun_ajaran }}{{ $k->is_active ? '' : ' (nonaktif)' }}
                </option>
            @endforeach
        </select>
        @error('kelas_id')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Mata pelajaran') }}</label>
        <select
            name="mata_pelajaran_id"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            required
        >
            <option value="">{{ __('— Pilih mapel —') }}</option>
            @foreach ($mapelOptions as $m)
                <option value="{{ $m->id }}" {{ (string) old('mata_pelajaran_id', $jadwal?->mata_pelajaran_id) === (string) $m->id ? 'selected' : '' }}>
                    {{ $m->nama }} ({{ $m->kode }})
                </option>
            @endforeach
        </select>
        @error('mata_pelajaran_id')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">{{ __('Guru') }}</label>
        <select
            name="guru_id"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            required
        >
            <option value="">{{ __('— Pilih guru —') }}</option>
            @foreach ($guruOptions as $g)
                <option value="{{ $g->id }}" {{ (string) old('guru_id', $jadwal?->guru_id) === (string) $g->id ? 'selected' : '' }}>
                    {{ $g->nama }}{{ $g->nip ? ' · NIP '.$g->nip : '' }}
                </option>
            @endforeach
        </select>
        @error('guru_id')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Jam mulai') }}</label>
        <input
            name="jam_mulai"
            type="time"
            value="{{ $jamOld('jam_mulai', $jadwal) }}"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            required
        />
        @error('jam_mulai')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Jam selesai') }}</label>
        <input
            name="jam_selesai"
            type="time"
            value="{{ $jamOld('jam_selesai', $jadwal) }}"
            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
            required
        />
        @error('jam_selesai')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
