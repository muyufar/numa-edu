@php
    $p = $perizinan ?? null;
    $canReview = auth()->user()->hasAnyRole(['super_admin', 'admin']);
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
            <input type="date" name="tanggal" value="{{ old('tanggal', $p?->tanggal?->format('Y-m-d')) }}" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required />
            @error('tanggal')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ __('Jenis') }}</label>
            <select name="jenis" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required>
                @foreach (\App\Models\Perizinan::JENIS_OPTIONS as $j)
                    <option value="{{ $j }}" {{ old('jenis', $p?->jenis) === $j ? 'selected' : '' }}>
                        {{ \App\Models\Perizinan::jenisLabel($j) }}
                    </option>
                @endforeach
            </select>
            @error('jenis')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Status') }}</label>
        <select name="status" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required>
            @foreach ($canReview ? \App\Models\Perizinan::STATUS_OPTIONS : ['pending'] as $st)
                <option value="{{ $st }}" {{ old('status', $p?->status ?? 'pending') === $st ? 'selected' : '' }}>
                    {{ \App\Models\Perizinan::statusLabel($st) }}
                </option>
            @endforeach
        </select>
        @if (! $canReview)
            <p class="mt-1 text-xs text-gray-500">{{ __('Guru: pengajuan akan menunggu persetujuan admin.') }}</p>
        @endif
        @error('status')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">{{ __('Keterangan') }}</label>
        <textarea name="keterangan" rows="3" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20">{{ old('keterangan', $p?->keterangan) }}</textarea>
        @error('keterangan')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
