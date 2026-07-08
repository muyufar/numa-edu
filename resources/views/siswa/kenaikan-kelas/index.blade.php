<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Kenaikan kelas & kelulusan') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Naikkan siswa ke kelas berikutnya atau luluskan ke daftar alumni secara massal.') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('siswa.index') }}" class="btn-nu">{{ __('Daftar siswa') }}</a>
                <a href="{{ route('siswa.alumni.index') }}" class="btn-nu">{{ __('Daftar alumni') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
            <h3 class="text-sm font-bold text-gray-900">{{ __('1. Pilih kelas asal') }}</h3>
            <p class="mt-1 text-xs text-gray-500">{{ __('Tampilkan siswa aktif di kelas yang dipilih.') }}</p>

            <form method="GET" action="{{ route('siswa.kenaikan-kelas.index') }}" class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 sm:items-end">
                <div class="sm:col-span-2 lg:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700">{{ __('Kelas asal') }}</label>
                    <select name="kelas_asal_id" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required>
                        <option value="">{{ __('— Pilih kelas —') }}</option>
                        @foreach ($kelasOptions as $k)
                            <option value="{{ $k->id }}" {{ (string) $kelasAsalId === (string) $k->id ? 'selected' : '' }}>
                                {{ $k->tingkat }} {{ $k->nama }} · {{ $k->tahun_ajaran }}
                                @if ($k->is_active) ({{ __('aktif') }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn-nu-primary w-full sm:w-auto">{{ __('Tampilkan siswa') }}</button>
                </div>
            </form>
        </div>

        @if ($kelasAsal)
            <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-8">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">{{ __('2. Pilih siswa') }}</h3>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ __('Kelas') }}: <span class="font-semibold text-gray-800">{{ $kelasAsal->tingkat }} {{ $kelasAsal->nama }} · {{ $kelasAsal->tahun_ajaran }}</span>
                            · {{ __('Siswa aktif') }}: <span class="font-semibold text-gray-800">{{ $siswas->count() }}</span>
                        </p>
                    </div>
                    @if ($siswas->isNotEmpty())
                        <button type="button" id="toggle-all-siswa" class="text-sm font-semibold text-nu-primary hover:underline">
                            {{ __('Pilih semua / hapus semua') }}
                        </button>
                    @endif
                </div>

                @if ($siswas->isEmpty())
                    <p class="mt-6 text-sm text-gray-500">{{ __('Tidak ada siswa aktif di kelas ini. Semua mungkin sudah lulus atau belum ada penempatan.') }}</p>
                @else
                    <div class="mt-4 overflow-x-auto rounded-xl border border-gray-100">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                <tr>
                                    <th class="px-4 py-3 w-10"></th>
                                    <th class="px-4 py-3">{{ __('NIS') }}</th>
                                    <th class="px-4 py-3">{{ __('NISN') }}</th>
                                    <th class="px-4 py-3">{{ __('Nama') }}</th>
                                    <th class="px-4 py-3">{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach ($siswas as $s)
                                    <tr class="hover:bg-gray-50/80">
                                        <td class="px-4 py-3">
                                            <input
                                                type="checkbox"
                                                value="{{ $s->id }}"
                                                class="siswa-checkbox rounded border-gray-300 text-nu-primary focus:ring-nu-primary/30"
                                                checked
                                            />
                                        </td>
                                        <td class="px-4 py-3 font-mono font-semibold text-gray-900">{{ $s->nis }}</td>
                                        <td class="px-4 py-3 font-mono text-gray-700">{{ $s->nisn ?: '—' }}</td>
                                        <td class="px-4 py-3 text-gray-900">{{ $s->nama }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $s->status ?: __('Aktif') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 grid gap-4 lg:grid-cols-2">
                        <div class="rounded-2xl border border-sky-100 bg-sky-50/50 p-5">
                            <h4 class="text-sm font-bold text-sky-900">{{ __('Naik kelas') }}</h4>
                            <p class="mt-1 text-xs text-sky-800/80">{{ __('Pindahkan siswa terpilih ke kelas tujuan. Kelas terakhir akan diperbarui.') }}</p>

                            <form id="form-naik-kelas" method="POST" action="{{ route('siswa.kenaikan-kelas.naik') }}" class="mt-4 space-y-4" onsubmit="return submitKenaikanForm(this, '{{ __('Naikkan siswa terpilih ke kelas tujuan?') }}')">
                                @csrf
                                <input type="hidden" name="kelas_asal_id" value="{{ $kelasAsal->id }}" />

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700">{{ __('Kelas tujuan') }}</label>
                                    <select name="kelas_tujuan_id" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required>
                                        <option value="">{{ __('— Pilih kelas tujuan —') }}</option>
                                        @foreach ($kelasTujuanOptions as $k)
                                            <option value="{{ $k->id }}" {{ (string) old('kelas_tujuan_id') === (string) $k->id ? 'selected' : '' }}>
                                                {{ $k->tingkat }} {{ $k->nama }} · {{ $k->tahun_ajaran }}
                                                @if ($k->is_active) ({{ __('aktif') }}) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kelas_tujuan_id')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                @error('siswa_ids')
                                    <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror
                                @foreach ($errors->get('siswa_ids.*') as $messages)
                                    @foreach ($messages as $message)
                                        <p class="text-xs text-red-600">{{ $message }}</p>
                                    @endforeach
                                @endforeach

                                <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-sky-700">
                                    {{ __('Naikkan siswa terpilih') }}
                                </button>
                            </form>
                        </div>

                        <div class="rounded-2xl border border-emerald-100 bg-emerald-50/50 p-5">
                            <h4 class="text-sm font-bold text-emerald-900">{{ __('Kelulusan / alumni') }}</h4>
                            <p class="mt-1 text-xs text-emerald-800/80">{{ __('Tandai siswa terpilih sebagai lulus. Kelas terakhir tetap tersimpan untuk arsip.') }}</p>

                            <form id="form-luluskan" method="POST" action="{{ route('siswa.kenaikan-kelas.luluskan') }}" class="mt-4 space-y-4" onsubmit="return submitKenaikanForm(this, '{{ __('Luluskan siswa terpilih ke daftar alumni?') }}')">
                                @csrf
                                <input type="hidden" name="kelas_id" value="{{ $kelasAsal->id }}" />

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700">{{ __('Status kelulusan') }}</label>
                                    <select name="status" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" required>
                                        @foreach (\App\Http\Requests\GraduateSiswaKelasRequest::STATUS_OPTIONS as $opt)
                                            <option value="{{ $opt }}" {{ old('status', 'Lulus') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">
                                    {{ __('Luluskan siswa terpilih') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>

    @if ($siswas->isNotEmpty())
        <script>
            function submitKenaikanForm(form, message) {
                form.querySelectorAll('input[name="siswa_ids[]"]').forEach((el) => el.remove());

                const checked = document.querySelectorAll('.siswa-checkbox:checked');
                if (checked.length === 0) {
                    alert('{{ __('Pilih minimal satu siswa.') }}');
                    return false;
                }

                checked.forEach((cb) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'siswa_ids[]';
                    input.value = cb.value;
                    form.appendChild(input);
                });

                return confirm(message);
            }

            document.getElementById('toggle-all-siswa')?.addEventListener('click', function () {
                const boxes = document.querySelectorAll('.siswa-checkbox');
                const allChecked = Array.from(boxes).every((b) => b.checked);
                boxes.forEach((b) => { b.checked = !allChecked; });
            });
        </script>
    @endif
</x-app-layout>
