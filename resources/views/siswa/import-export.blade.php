<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Import & Export Siswa') }}</h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Total siswa saat ini: :total', ['total' => $totalSiswa]) }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('siswa.index', ['template' => 1]) }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                    {{ __('Download template') }}
                </a>
                <a href="{{ route('siswa.index', ['export' => 1]) }}" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light focus:outline-none focus:ring-2 focus:ring-nu-gold focus:ring-offset-2">
                    {{ __('Export data XLSX') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <div class="font-semibold">{{ __('Ada yang perlu diperiksa.') }}</div>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="border-b border-gray-100 px-5 py-4">
                <div class="text-sm font-semibold text-gray-900">{{ __('Import data dari Excel') }}</div>
                <div class="mt-1 text-xs text-gray-500">
                    {{ __('Gunakan template yang didownload agar kolomnya sesuai. Import akan otomatis menambah atau memperbarui siswa berdasarkan NIS (atau NISN jika NIS kosong).') }}
                </div>
            </div>

            <div class="p-5">
                <form method="POST" action="{{ route('siswa.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="grid gap-4 sm:grid-cols-12 sm:items-end">
                        <div class="sm:col-span-8">
                            <label class="block text-sm font-semibold text-gray-700">{{ __('File Excel (.xlsx/.xls)') }}</label>
                            <input
                                type="file"
                                name="file"
                                accept=".xlsx,.xls"
                                class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
                                required
                            />
                        </div>
                        <div class="sm:col-span-4">
                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light focus:outline-none focus:ring-2 focus:ring-nu-gold focus:ring-offset-2">
                                {{ __('Import') }}
                            </button>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 text-sm text-gray-700">
                        <div class="font-semibold text-gray-900">{{ __('Kolom yang digunakan:') }}</div>
                        <div class="mt-1 text-xs text-gray-600">
                            <span class="font-mono">No</span>, <span class="font-mono">Nama Lengkap</span>, <span class="font-mono">NIS</span>, <span class="font-mono">NISN</span>, <span class="font-mono">NIK</span>, <span class="font-mono">Tempat Lahir</span>, <span class="font-mono">Tanggal Lahir</span>, <span class="font-mono">Tingkat - Rombel</span>, <span class="font-mono">Umur</span>, <span class="font-mono">Status</span>, <span class="font-mono">Jenis Kelamin</span>, <span class="font-mono">Alamat</span>, <span class="font-mono">No Telepon</span>, <span class="font-mono">Kebutuhan Khusus</span>, <span class="font-mono">Disabilitas</span>, <span class="font-mono">Nomor KIP/PIP</span>, <span class="font-mono">Nama Ayah Kandung</span>, <span class="font-mono">Nama Ibu Kandung</span>, <span class="font-mono">Nama Wali</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

