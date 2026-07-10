@php
    /** @var \App\Support\GtkDetail $gtk */
    $editUrl = $gtk->type === 'guru' ? route('guru.edit', $gtk->model) : route('pegawai.edit', $gtk->model);
    $kartuType = $gtk->type === 'guru' ? 'guru' : 'pegawai';
    $canKartu = $gtk->type === 'guru'
        ? auth()->user()->can('viewAny', \App\Models\PresensiGuru::class)
        : auth()->user()->can('viewAny', \App\Models\PresensiPegawai::class);
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Detail Guru dan Tenaga Kependidikan') }}</p>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ $gtk->nama }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ $gtk->typeLabel() }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if ($canKartu)
                    <a href="{{ route('presensi.kartu', [$kartuType, $gtk->model]) }}" target="_blank" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                        {{ __('Cetak Kartu GTK') }}
                    </a>
                @endif
                @can('update', $gtk->model)
                    <a href="{{ $editUrl }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                        {{ __('Edit') }}
                    </a>
                @endcan
                <a href="{{ route('tenaga-kependidikan.index', ['tab' => $gtk->tab()]) }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                    {{ __('Kembali') }}
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

        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-gray-200 bg-nu-primary/10">
                        @if ($gtk->fotoUrl)
                            <img src="{{ $gtk->fotoUrl }}" alt="{{ $gtk->nama }}" class="h-full w-full object-cover" />
                        @else
                            <span class="text-2xl font-bold text-nu-primary">{{ mb_strtoupper(mb_substr($gtk->nama, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div>
                        <div class="text-lg font-bold text-gray-900">{{ $gtk->nama }}</div>
                        <div class="mt-1 text-sm text-gray-600">{{ __('NIP') }}: <span class="font-mono">{{ $gtk->display($gtk->nip) }}</span></div>
                        <div class="text-sm text-gray-600">{{ __('NUPTK') }}: <span class="font-mono">{{ $gtk->display($gtk->nuptk) }}</span></div>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if ($gtk->statusKepegawaian)
                        <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">
                            {{ $gtk->statusKepegawaian }}
                        </span>
                    @endif
                    @if ($gtk->type === 'pegawai')
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $gtk->isActive ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-gray-50 text-gray-500 ring-gray-200' }}">
                            {{ $gtk->isActive ? __('Aktif') : __('Nonaktif') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <section class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="border-b border-gray-100 bg-gray-50 px-5 py-3">
                <h3 class="text-sm font-bold uppercase tracking-wide text-gray-800">{{ __('Data Diri') }}</h3>
            </div>
            <dl class="px-5">
                <x-gtk-detail-field :label="__('NIK')" :value="$gtk->display($gtk->nik)" />
                <x-gtk-detail-field :label="__('Nama')" :value="$gtk->display($gtk->nama)" />
                <x-gtk-detail-field :label="__('Jenis Kelamin')" :value="$gtk->display($gtk->jenisKelamin)" />
                <x-gtk-detail-field :label="__('Tempat Lahir')" :value="$gtk->display($gtk->tempatLahir)" />
                <x-gtk-detail-field :label="__('Tanggal Lahir')" :value="$gtk->display($gtk->tanggalLahir)" />
                <x-gtk-detail-field :label="__('Agama')" :value="$gtk->display($gtk->agama)" />
                <x-gtk-detail-field :label="__('Nama Ibu Kandung')" :value="$gtk->display($gtk->namaIbuKandung)" />
                <x-gtk-detail-field :label="__('Status Perkawinan')" :value="$gtk->display($gtk->statusPerkawinan)" />
                <x-gtk-detail-field :label="__('Email')" :value="$gtk->display($gtk->email)" />
                <x-gtk-detail-field :label="__('Kewarganegaraan')" :value="$gtk->display($gtk->kewarganegaraan)" />
                <x-gtk-detail-field :label="__('Alamat Jalan')" :value="$gtk->display($gtk->alamatJalan)" />
                <x-gtk-detail-field :label="__('RT/RW')" :value="$gtk->display($gtk->rtRw)" />
                <x-gtk-detail-field :label="__('Kode Pos')" :value="$gtk->display($gtk->kodePos)" />
            </dl>
        </section>

        <section class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="border-b border-gray-100 bg-gray-50 px-5 py-3">
                <h3 class="text-sm font-bold uppercase tracking-wide text-gray-800">{{ __('Kepegawaian') }}</h3>
            </div>
            <dl class="px-5">
                <x-gtk-detail-field :label="__('Status Kepegawaian')" :value="$gtk->display($gtk->statusKepegawaian)" />
                <x-gtk-detail-field :label="__('NIP')" :value="$gtk->display($gtk->nip)" />
                <x-gtk-detail-field :label="__('NUPTK')" :value="$gtk->display($gtk->nuptk)" />
                <x-gtk-detail-field :label="__('Jabatan')" :value="$gtk->display($gtk->jabatan)" />
                <x-gtk-detail-field :label="__('Jenis PTK')" :value="$gtk->display($gtk->jenisPtk)" />
                <x-gtk-detail-field :label="__('SK Pengangkatan')" :value="$gtk->display($gtk->skPengangkatan)" />
                <x-gtk-detail-field :label="__('TMT CPNS')" :value="$gtk->display($gtk->tmtCpns)" />
                <x-gtk-detail-field :label="__('TMT PNS')" :value="$gtk->display($gtk->tmtPns)" />
                <x-gtk-detail-field :label="__('TMT Jabatan')" :value="$gtk->display($gtk->tmtJabatan)" />
            </dl>
        </section>

        <section class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="border-b border-gray-100 bg-gray-50 px-5 py-3">
                <h3 class="text-sm font-bold uppercase tracking-wide text-gray-800">{{ __('Data Alamat Rumah') }}</h3>
            </div>
            <dl class="px-5">
                <x-gtk-detail-field :label="__('Dusun')" :value="$gtk->display($gtk->dusun)" />
                <x-gtk-detail-field :label="__('Jalan')" :value="$gtk->display($gtk->alamatJalan)" />
                <x-gtk-detail-field :label="__('RT/RW')" :value="$gtk->display($gtk->rtRw)" />
                <x-gtk-detail-field :label="__('Desa/Kelurahan')" :value="$gtk->display($gtk->desaKelurahan)" />
                <x-gtk-detail-field :label="__('Kecamatan')" :value="$gtk->display($gtk->kecamatan)" />
                <x-gtk-detail-field :label="__('Kabupaten/Kota')" :value="$gtk->display($gtk->kabupatenKota)" />
                <x-gtk-detail-field :label="__('Provinsi')" :value="$gtk->display($gtk->provinsi)" />
            </dl>
        </section>

        <section class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="border-b border-gray-100 bg-gray-50 px-5 py-3">
                <h3 class="text-sm font-bold uppercase tracking-wide text-gray-800">{{ __('Kontak') }}</h3>
            </div>
            <dl class="px-5">
                <x-gtk-detail-field :label="__('No. Telepon Rumah')" :value="$gtk->display($gtk->teleponRumah)" />
                <x-gtk-detail-field :label="__('No. HP')" :value="$gtk->display($gtk->noHp)" />
                <x-gtk-detail-field :label="__('Email')" :value="$gtk->display($gtk->email)" />
                @if ($gtk->emailLogin)
                    <x-gtk-detail-field :label="__('Email Akun Masuk')" :value="$gtk->display($gtk->emailLogin)" />
                @endif
                <x-gtk-detail-field :label="__('Kode Presensi')" :value="$gtk->display($gtk->presensiKode)" />
            </dl>
        </section>

        <x-gtk-detail-table
            :title="__('Kependidikan')"
            :columns="[__('Jenjang'), __('Gelar'), __('Jurusan'), __('Tahun Lulus'), __('NIM'), __('Sekolah/PT')]"
            :rows="[]"
        />

        <x-gtk-detail-table
            :title="__('Riwayat Sertifikasi')"
            :columns="[__('Nomor Sertifikat'), __('Nama Sertifikat'), __('Bidang Studi'), __('Tahun Lulus'), __('Status')]"
            :rows="[]"
        />

        <x-gtk-detail-table
            :title="__('Kejuaraan')"
            :columns="[__('Jenjang'), __('Nama Kejuaraan'), __('Peringkat'), __('Lembaga'), __('Tahun'), __('Tingkat')]"
            :rows="[]"
        />

        <x-gtk-detail-table
            :title="__('Tugas Pengalaman')"
            :columns="[__('Jabatan'), __('Nama Instansi'), __('Hubungan Kerja'), __('Tanggal Mulai'), __('Tanggal Selesai'), __('No. SK')]"
            :rows="[]"
        />

        <x-gtk-detail-table
            :title="__('Tugas Kependidikan')"
            :columns="[__('Tugas Utama'), __('Mata Pelajaran'), __('Jenis GTK'), __('Jam')]"
            :rows="$gtk->tugasKependidikanRows()"
        />

        <x-gtk-detail-table
            :title="__('Riwayat Gaji')"
            :columns="[__('Golongan'), __('Nomor SK'), __('Tanggal SK'), __('TMT'), __('Gaji Pokok')]"
            :rows="[]"
        />

        <x-gtk-detail-table
            :title="__('Riwayat Penghargaan')"
            :columns="[__('Nama Penghargaan'), __('Instansi'), __('Tahun')]"
            :rows="[]"
        />
    </div>
</x-app-layout>
