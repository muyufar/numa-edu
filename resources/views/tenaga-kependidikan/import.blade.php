<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">
                    @if ($tab === 'pegawai')
                        {{ __('Import Tenaga Kependidikan (GTK)') }}
                    @else
                        {{ __('Import Guru (GTK)') }}
                    @endif
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    @if ($tab === 'pegawai')
                        {{ __('Total tenaga kependidikan saat ini: :total', ['total' => $total]) }}
                    @else
                        {{ __('Total guru saat ini: :total', ['total' => $total]) }}
                    @endif
                </p>
            </div>
            <a href="{{ route('tenaga-kependidikan.index', ['tab' => $tab]) }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                {{ __('Kembali ke daftar') }}
            </a>
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
                <div class="text-sm font-semibold text-gray-900">{{ __('Import dari Daftar GTK (Excel)') }}</div>
                <div class="mt-1 text-xs text-gray-500">
                    @if ($tab === 'pegawai')
                        {{ __('Unggah file export Daftar GTK / Madrasah Digital (.xlsx). Sistem membaca sheet "Tenaga Kependidikan" (sheet kedua) dan membuat atau memperbarui data berdasarkan NIP atau nama.') }}
                    @else
                        {{ __('Unggah file export Daftar GTK / Madrasah Digital (.xlsx). Sistem membaca sheet "Guru" (sheet pertama) dan membuat atau memperbarui akun guru berdasarkan email atau NIP.') }}
                    @endif
                </div>
            </div>

            <div class="p-5">
                <form method="POST" action="{{ route('tenaga-kependidikan.import') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="tab" value="{{ $tab }}" />

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
                        <div class="font-semibold text-gray-900">{{ __('Kolom yang dikenali:') }}</div>
                        @if ($tab === 'pegawai')
                            <div class="mt-1 text-xs text-gray-600">
                                <span class="font-mono">Nama Lengkap</span>,
                                <span class="font-mono">NIP</span>,
                                <span class="font-mono">Tugas</span>
                            </div>
                            <div class="mt-3 text-xs text-gray-500">
                                {{ __('File yang sama dengan import guru dapat digunakan. Data diambil otomatis dari sheet kedua "Tenaga Kependidikan".') }}
                            </div>
                        @else
                            <div class="mt-1 text-xs text-gray-600">
                                <span class="font-mono">Nama Lengkap</span>,
                                <span class="font-mono">NIP</span>,
                                <span class="font-mono">NUPTK</span>,
                                <span class="font-mono">Jenis Kelamin</span>,
                                <span class="font-mono">Nomor Handphone</span>,
                                <span class="font-mono">Email</span>,
                                <span class="font-mono">Email Akun Madrasah Digital</span>,
                                <span class="font-mono">Password Awal</span>
                            </div>
                            <div class="mt-3 text-xs text-gray-500">
                                {{ __('Data diambil otomatis dari sheet pertama "Guru".') }}
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
