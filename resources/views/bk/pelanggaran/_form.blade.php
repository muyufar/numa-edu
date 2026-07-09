@php
    $p = $pelanggaran ?? null;
    $tanggalDefault = $p?->tanggal?->format('Y-m-d') ?? now()->toDateString();
    $jenisData = ($jenisPelanggaranOptions ?? collect())->mapWithKeys(fn ($j) => [
        $j->id => ['poin' => $j->poin, 'tingkat' => $j->tingkat],
    ]);
@endphp

<div class="space-y-5">
    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Siswa') }}</label>
        <select name="siswa_id" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required>
            <option value="">{{ __('— Pilih siswa —') }}</option>
            @foreach ($siswas as $s)
                <option value="{{ $s->id }}" {{ (string) old('siswa_id', $p?->siswa_id) === (string) $s->id ? 'selected' : '' }}>
                    {{ $s->nis }} — {{ $s->nama }}
                </option>
            @endforeach
        </select>
        @error('siswa_id')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Tanggal') }}</label>
            <input type="date" name="tanggal" value="{{ old('tanggal', $tanggalDefault) }}" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required />
            @error('tanggal')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Jenis pelanggaran') }}</label>
            <select id="bk_jenis_pelanggaran_id" name="bk_jenis_pelanggaran_id" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required>
                <option value="">{{ __('— Pilih jenis —') }}</option>
                @foreach ($jenisPelanggaranOptions ?? [] as $j)
                    <option value="{{ $j->id }}" {{ (string) old('bk_jenis_pelanggaran_id', $p?->bk_jenis_pelanggaran_id) === (string) $j->id ? 'selected' : '' }}>
                        {{ $j->nama }} ({{ $j->poin }} {{ __('poin') }})
                    </option>
                @endforeach
            </select>
            @error('bk_jenis_pelanggaran_id')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid gap-5 sm:grid-cols-3">
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Poin') }}</label>
            <input type="number" id="poin" name="poin" value="{{ old('poin', $p?->poin) }}" min="0" max="999" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" />
            @error('poin')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Tingkat') }}</label>
            <select id="tingkat" name="tingkat" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20">
                <option value="">{{ __('— Otomatis —') }}</option>
                @foreach (\App\Support\BkTingkat::OPTIONS as $t)
                    <option value="{{ $t }}" {{ old('tingkat', $p?->tingkat) === $t ? 'selected' : '' }}>{{ \App\Support\BkTingkat::label($t) }}</option>
                @endforeach
            </select>
            @error('tingkat')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Sanksi') }}</label>
            <select name="bk_sanksi_id" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20">
                <option value="">{{ __('— Opsional —') }}</option>
                @foreach ($sanksiOptions ?? [] as $s)
                    <option value="{{ $s->id }}" {{ (string) old('bk_sanksi_id', $p?->bk_sanksi_id) === (string) $s->id ? 'selected' : '' }}>
                        {{ $s->nama }}
                    </option>
                @endforeach
            </select>
            @error('bk_sanksi_id')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Deskripsi kejadian') }}</label>
        <textarea name="deskripsi" rows="3" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" placeholder="{{ __('Opsional') }}">{{ old('deskripsi', $p?->deskripsi) }}</textarea>
        @error('deskripsi')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Tindakan / pembinaan') }}</label>
        <textarea name="tindakan" rows="2" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" placeholder="{{ __('Opsional, mis. pembinaan lisan, surat peringatan, dsb.') }}">{{ old('tindakan', $p?->tindakan) }}</textarea>
        @error('tindakan')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<script>
(() => {
    const jenisMap = @json($jenisData);
    const jenisSelect = document.getElementById('bk_jenis_pelanggaran_id');
    const poinInput = document.getElementById('poin');
    const tingkatSelect = document.getElementById('tingkat');

    function applyJenis() {
        const data = jenisMap[jenisSelect?.value];
        if (!data) return;
        if (poinInput) poinInput.value = data.poin ?? '';
        if (tingkatSelect) tingkatSelect.value = data.tingkat ?? '';
    }

    jenisSelect?.addEventListener('change', applyJenis);
})();
</script>
