@php
    $p = $row ?? null;
    $inputClass = 'mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20';
    $tanggalDefault = $p?->tanggal_jadwal?->format('Y-m-d') ?? now()->toDateString();
    $waktuVal = old('waktu', $p?->waktu ? substr((string) $p->waktu, 0, 5) : '');
    $targetVal = old('target', $p?->target ?? 'siswa');
    $urutanVal = (int) old('urutan', $p?->urutan ?? 1);
    $statusLabels = [
        'terjadwal' => __('Terjadwal'),
        'hadir' => __('Hadir'),
        'tidak_hadir' => __('Tidak hadir'),
        'dijadwal_ulang' => __('Dijadwal ulang'),
    ];
@endphp

<div class="space-y-5">
    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Siswa') }}</label>
        <select name="siswa_id" class="{{ $inputClass }}" required>
            <option value="">{{ __('— Pilih siswa —') }}</option>
            @foreach ($siswas as $s)
                <option value="{{ $s->id }}" {{ (string) old('siswa_id', $p?->siswa_id) === (string) $s->id ? 'selected' : '' }}>
                    {{ $s->nis }} — {{ $s->nama }}
                </option>
            @endforeach
        </select>
        @error('siswa_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Target pemanggilan') }}</label>
            <select id="bk_pemanggilan_target" name="target" class="{{ $inputClass }}" required>
                @foreach (\App\Models\BkPemanggilan::TARGET_OPTIONS as $t)
                    <option value="{{ $t }}" {{ $targetVal === $t ? 'selected' : '' }}>{{ \App\Models\BkPemanggilan::targetLabel($t) }}</option>
                @endforeach
            </select>
            @error('target')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Urutan pemanggilan') }}</label>
            <select id="bk_pemanggilan_urutan" name="urutan" class="{{ $inputClass }}" required>
                @for ($i = 1; $i <= 3; $i++)
                    <option value="{{ $i }}" data-target-siswa="1" data-target-wali="{{ $i <= 2 ? '1' : '0' }}" {{ $urutanVal === $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
            <p id="bk_pemanggilan_urutan_hint" class="mt-1 text-xs text-gray-500"></p>
            @error('urutan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Tanggal jadwal') }}</label>
            <input type="date" name="tanggal_jadwal" value="{{ old('tanggal_jadwal', $tanggalDefault) }}" class="{{ $inputClass }} font-mono" required />
            @error('tanggal_jadwal')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Waktu') }}</label>
            <input type="time" name="waktu" value="{{ $waktuVal }}" class="{{ $inputClass }} font-mono" />
            @error('waktu')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Tempat') }}</label>
        <input type="text" name="tempat" value="{{ old('tempat', $p?->tempat) }}" maxlength="160" class="{{ $inputClass }}" placeholder="{{ __('Opsional, mis. ruang BK') }}" />
        @error('tempat')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Alasan') }}</label>
        <textarea name="alasan" rows="3" class="{{ $inputClass }}" required>{{ old('alasan', $p?->alasan) }}</textarea>
        @error('alasan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Status') }}</label>
        <select name="status" class="{{ $inputClass }}" required>
            @foreach (\App\Models\BkPemanggilan::STATUS_OPTIONS as $st)
                <option value="{{ $st }}" {{ old('status', $p?->status ?? 'terjadwal') === $st ? 'selected' : '' }}>{{ $statusLabels[$st] ?? $st }}</option>
            @endforeach
        </select>
        @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<script>
(() => {
    const targetSelect = document.getElementById('bk_pemanggilan_target');
    const urutanSelect = document.getElementById('bk_pemanggilan_urutan');
    const hint = document.getElementById('bk_pemanggilan_urutan_hint');
    if (!targetSelect || !urutanSelect) return;

    const hints = {
        siswa: @json(__('Maksimal 3 kali pemanggilan siswa.')),
        wali: @json(__('Maksimal 2 kali pemanggilan wali murid.')),
    };

    function applyTarget() {
        const isWali = targetSelect.value === 'wali';
        const max = isWali ? 2 : 3;
        if (hint) hint.textContent = isWali ? hints.wali : hints.siswa;

        Array.from(urutanSelect.options).forEach((opt) => {
            const n = parseInt(opt.value, 10);
            const allowed = n <= max;
            opt.hidden = !allowed;
            opt.disabled = !allowed;
        });

        if (parseInt(urutanSelect.value, 10) > max) {
            urutanSelect.value = String(max);
        }
    }

    targetSelect.addEventListener('change', applyTarget);
    applyTarget();
})();
</script>
