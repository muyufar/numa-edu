<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ $typeLabel }} — {{ __('Scan') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Presensi otomatis via barcode/QR atau pengenalan wajah.') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ $indexRoute }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                    {{ __('Riwayat presensi') }}
                </a>
                <a href="{{ route('presensi.index') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                    {{ __('Ringkasan absensi') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div
        id="presensi-scan-root"
        class="space-y-4"
        data-type="{{ $type }}"
        data-barcode-url="{{ route('presensi.scan.barcode', $type) }}"
        data-face-url="{{ route('presensi.scan.face', $type) }}"
        data-enroll-url-template="{{ route('presensi.scan.face-enroll', ['type' => $type, 'person' => '__ID__']) }}"
        data-csrf="{{ csrf_token() }}"
        data-today="{{ now()->toDateString() }}"
        data-people='@json($peopleOptions->map(fn ($p) => ["id" => $p->id, "nama" => $p->nama, "has_face" => ! empty($p->face_descriptor)]))'
    >
        <div class="rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5 sm:p-5">
            <div class="flex flex-wrap gap-2">
                <button type="button" data-tab="barcode" class="presensi-scan-tab inline-flex items-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm">
                    {{ __('Barcode / QR') }}
                </button>
                <button type="button" data-tab="face" class="presensi-scan-tab inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                    {{ __('Face recognition') }}
                </button>
            </div>

            @if ($type === 'siswa')
                <div class="mt-4 max-w-md">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Filter kelas (opsional, untuk wajah)') }}</label>
                    <select id="presensi-scan-kelas" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20">
                        <option value="">{{ __('— Semua siswa sekolah —') }}</option>
                        @foreach ($kelasOptions as $k)
                            <option value="{{ $k->id }}">{{ $k->tingkat }} {{ $k->nama }} · {{ $k->tahun_ajaran }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <div id="presensi-scan-panel-barcode" class="presensi-scan-panel space-y-4">
            <div class="rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5 sm:p-5">
                <p class="text-sm text-gray-600">{{ __('Arahkan kamera ke barcode/QR kartu presensi. Status otomatis: hadir.') }}</p>
                <div id="presensi-barcode-reader" class="mt-4 overflow-hidden rounded-xl bg-black/90"></div>
                <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
                    <div class="flex-1">
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Atau ketik kode manual') }}</label>
                        <input id="presensi-barcode-manual" type="text" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 font-mono text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20" placeholder="NUMA-SIS-XXXXXXXXXXXX" />
                    </div>
                    <button type="button" id="presensi-barcode-submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                        {{ __('Catat presensi') }}
                    </button>
                </div>
            </div>
        </div>

        <div id="presensi-scan-panel-face" class="presensi-scan-panel hidden space-y-4">
            <div class="rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5 sm:p-5">
                <p class="text-sm text-gray-600">{{ __('Posisikan wajah di depan kamera. Wajah harus sudah didaftarkan terlebih dahulu.') }}</p>
                <div class="relative mt-4 overflow-hidden rounded-xl bg-black">
                    <video id="presensi-face-video" class="mx-auto block max-h-[420px] w-full" autoplay muted playsinline></video>
                    <canvas id="presensi-face-overlay" class="pointer-events-none absolute inset-0 h-full w-full"></canvas>
                </div>
                <div class="mt-4 flex flex-wrap gap-2">
                    <button type="button" id="presensi-face-start" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                        {{ __('Mulai kamera') }}
                    </button>
                    <button type="button" id="presensi-face-capture" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50" disabled>
                        {{ __('Scan wajah sekarang') }}
                    </button>
                </div>
            </div>

            <div class="rounded-2xl border border-amber-200/80 bg-amber-50/80 p-4 shadow-sm ring-1 ring-amber-200/60 sm:p-5">
                <h3 class="text-sm font-bold text-amber-950">{{ __('Daftarkan wajah') }}</h3>
                <p class="mt-1 text-sm text-amber-900">{{ __('Untuk pendaftaran wajah baru, pilih orang lalu ambil foto wajah sekali.') }}</p>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-amber-900/80">{{ __('Pilih orang') }}</label>
                        <select id="presensi-face-enroll-select" class="mt-1 w-full rounded-xl border border-amber-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20">
                            <option value="">{{ __('— Pilih —') }}</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="button" id="presensi-face-enroll-btn" class="inline-flex w-full items-center justify-center rounded-xl border border-amber-300 bg-white px-4 py-2.5 text-sm font-semibold text-amber-950 shadow-sm hover:bg-amber-50" disabled>
                            {{ __('Simpan data wajah') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="presensi-scan-log" class="rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5 sm:p-5">
            <div class="text-sm font-semibold text-gray-900">{{ __('Log presensi') }}</div>
            <ul id="presensi-scan-log-list" class="mt-3 space-y-2 text-sm text-gray-700"></ul>
        </div>
    </div>

    @vite(['resources/js/presensi-scan.js'])
</x-app-layout>
