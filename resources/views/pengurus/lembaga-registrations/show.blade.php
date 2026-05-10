<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Detail pendaftaran lembaga') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ $reg->nama_lembaga }} — NPSN {{ $reg->npsn }}</p>
            </div>
            <a href="{{ route('pengurus.lembaga-registrations.index') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">{{ __('Daftar permohonan') }}</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-5xl space-y-6">
        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->has('approve'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                {{ $errors->first('approve') }}
            </div>
        @endif

        @if (session('operator_setup'))
            @php $op = session('operator_setup'); @endphp
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-950">
                <p class="font-semibold">{{ __('Akun operator berhasil dibuat') }}</p>
                <p class="mt-2 font-mono text-xs">{{ __('Email') }}: {{ $op['email'] ?? '' }}</p>
                <p class="mt-1 font-mono text-xs">{{ __('Sandi sementara') }}: {{ $op['password'] ?? '' }}</p>
                <p class="mt-2 text-xs">{{ __('Segera bagikan kredensial ini kepada lembaga melalui kanal resmi, lalu minta pengguna mengganti sandi setelah login.') }}</p>
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm ring-1 ring-black/5">
                    <h3 class="text-sm font-bold uppercase tracking-wide text-gray-500">{{ __('Identitas') }}</h3>
                    <dl class="mt-3 grid gap-2 text-sm sm:grid-cols-2">
                        <div><dt class="text-gray-500">{{ __('Kepala') }}</dt><dd class="font-medium text-gray-900">{{ $reg->nama_kepala ?? '—' }}</dd></div>
                        <div><dt class="text-gray-500">{{ __('Jenjang') }}</dt><dd class="font-medium text-gray-900">{{ \App\Models\Sekolah::jenjangLabel($reg->jenjang) }}</dd></div>
                        <div><dt class="text-gray-500">{{ __('NPWP') }}</dt><dd class="font-medium text-gray-900">{{ $reg->npwp ?? '—' }}</dd></div>
                        <div><dt class="text-gray-500">{{ __('Telepon') }}</dt><dd class="font-medium text-gray-900">{{ $reg->telepon ?? '—' }}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-gray-500">{{ __('Alamat web / email / medsos') }}</dt><dd class="font-medium text-gray-900">{{ $reg->website ?? '—' }} · {{ $reg->email ?? '—' }} · {{ $reg->medsos ?? '—' }}</dd></div>
                        <div><dt class="text-gray-500">{{ __('Tahun berdiri') }}</dt><dd class="font-medium text-gray-900">{{ $reg->tahun_berdiri ?? '—' }}</dd></div>
                        <div><dt class="text-gray-500">{{ __('Waktu belajar') }}</dt><dd class="font-medium text-gray-900">{{ $reg->waktu_belajar }}</dd></div>
                        <div><dt class="text-gray-500">{{ __('Status KKM') }}</dt><dd class="font-medium text-gray-900">{{ $reg->status_kkm }}</dd></div>
                        <div><dt class="text-gray-500">{{ __('Komite') }}</dt><dd class="font-medium text-gray-900">{{ $reg->komite }}</dd></div>
                        <div><dt class="text-gray-500">{{ __('Jumlah murid terkini') }}</dt><dd class="font-medium text-gray-900">{{ $reg->jumlah_murid !== null ? number_format((int) $reg->jumlah_murid, 0, ',', '.') : '—' }}</dd></div>
                    </dl>
                </div>

                <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm ring-1 ring-black/5">
                    <h3 class="text-sm font-bold uppercase tracking-wide text-gray-500">{{ __('Alamat') }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-gray-800">{{ $reg->alamatLengkap() }}</p>
                </div>

                <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm ring-1 ring-black/5">
                    <h3 class="text-sm font-bold uppercase tracking-wide text-gray-500">{{ __('Dokumen perijinan') }}</h3>
                    <ul class="mt-3 space-y-2 text-sm">
                        @foreach ($reg->permits as $p)
                            <li class="flex flex-wrap items-center justify-between gap-2 rounded-lg border border-gray-100 px-3 py-2">
                                <span class="font-medium text-gray-900">{{ $p->nama_sk }}</span>
                                <span class="text-gray-600">{{ $p->nomor_sk ?? '—' }} @if($p->tanggal_sk) · {{ $p->tanggal_sk->format('Y-m-d') }} @endif</span>
                                @if ($p->dokumen_path)
                                    <a href="{{ asset('storage/'.$p->dokumen_path) }}" target="_blank" class="text-sm font-semibold text-nu-primary hover:underline">{{ __('Unduh PDF') }}</a>
                                @else
                                    <span class="text-xs text-amber-700">{{ __('Belum diupload') }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm ring-1 ring-black/5">
                    <h3 class="text-sm font-bold uppercase tracking-wide text-gray-500">{{ __('Operator') }}</h3>
                    <p class="mt-2 text-sm text-gray-800">{{ $reg->operator_name }}</p>
                    <p class="mt-1 font-mono text-xs text-gray-600">{{ $reg->operator_email }}</p>
                    <p class="mt-3 text-xs text-gray-500">{{ __('Status') }}: <strong>{{ $reg->status }}</strong></p>
                    @if ($reg->mou_signed_at)
                        <p class="mt-1 text-xs text-gray-500">{{ __('MoU ditandatangani') }}: {{ $reg->mou_signed_at->format('Y-m-d H:i') }}</p>
                    @endif
                </div>

                <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm ring-1 ring-black/5">
                    <h3 class="text-sm font-bold uppercase tracking-wide text-gray-500">{{ __('Tanda tangan basah & meterai (kepala lembaga)') }}</h3>
                    @if ($reg->signature_path)
                        <p class="mt-2 text-xs text-amber-800">{{ __('Arsip tanda tangan elektronik lama (sebelum perubahan alur):') }}</p>
                        <img src="{{ asset('storage/'.$reg->signature_path) }}" alt="TTD" class="mt-2 max-h-40 rounded-lg border border-gray-100 bg-white object-contain" />
                    @else
                        <p class="mt-2 text-xs text-gray-500">{{ __('Pada salinan cetak MoU (tidak diunggah ke sistem).') }}</p>
                    @endif
                    @if ($reg->mou_nomor_lp)
                        <p class="mt-3 text-xs font-semibold text-gray-600">{{ __('Nomor LP (MoU)') }}</p>
                        <p class="font-mono text-xs text-gray-900">{{ $reg->mou_nomor_lp }}</p>
                    @endif
                    @if ($reg->mou_nomor_sekolah)
                        <p class="mt-2 text-xs font-semibold text-gray-600">{{ __('Nomor sekolah (MoU)') }}</p>
                        <p class="font-mono text-xs text-gray-900">{{ $reg->mou_nomor_sekolah }}</p>
                    @endif
                </div>

                @if ($reg->mou_draft_pdf_path || $reg->e_sertifikat_pdf_path)
                    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm ring-1 ring-black/5">
                        <h3 class="text-sm font-bold uppercase tracking-wide text-gray-500">{{ __('Dokumen PDF') }}</h3>
                        <div class="mt-3 flex flex-col gap-2">
                            @if ($reg->mou_draft_pdf_path)
                                <a href="{{ asset('storage/'.$reg->mou_draft_pdf_path) }}" target="_blank" class="text-sm font-semibold text-nu-primary hover:underline">{{ __('Unduh draft MoU') }}</a>
                            @endif
                            @if ($reg->e_sertifikat_pdf_path)
                                <a href="{{ asset('storage/'.$reg->e_sertifikat_pdf_path) }}" target="_blank" class="text-sm font-semibold text-nu-primary hover:underline">{{ __('Unduh e-sertifikat') }}</a>
                            @endif
                        </div>
                        <p class="mt-2 text-xs text-gray-500">{{ __('Setelah persetujuan, cetak e-sertifikat sebagai bukti verifikasi bila diperlukan.') }}</p>
                    </div>
                @endif

                @if ($reg->status === \App\Models\LembagaRegistration::STATUS_PENDING_REVIEW)
                    <form method="POST" action="{{ route('pengurus.lembaga-registrations.approve', $reg) }}" class="rounded-2xl border border-emerald-100 bg-emerald-50/40 p-5">
                        @csrf
                        <p class="text-sm font-semibold text-emerald-900">{{ __('Setujui & buat akun sekolah') }}</p>
                        <p class="mt-1 text-xs text-emerald-800">{{ __('Akan membuat data sekolah dan user admin dengan email operator di atas.') }}</p>
                        <button type="submit" class="mt-3 inline-flex w-full items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">{{ __('Setujui') }}</button>
                    </form>

                    <form method="POST" action="{{ route('pengurus.lembaga-registrations.reject', $reg) }}" class="rounded-2xl border border-red-100 bg-red-50/40 p-5">
                        @csrf
                        <p class="text-sm font-semibold text-red-900">{{ __('Tolak permohonan') }}</p>
                        <label class="mt-2 block text-xs font-semibold text-red-800">{{ __('Catatan untuk lembaga (opsional)') }}</label>
                        <textarea name="admin_notes" rows="3" class="mt-1 block w-full rounded-xl border-red-200 text-sm shadow-sm focus:border-red-400 focus:ring-red-200">{{ old('admin_notes') }}</textarea>
                        <button type="submit" class="mt-3 inline-flex w-full items-center justify-center rounded-xl border border-red-300 bg-white px-4 py-2.5 text-sm font-semibold text-red-800 hover:bg-red-50">{{ __('Tolak') }}</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm ring-1 ring-black/5">
            <h3 class="text-sm font-bold uppercase tracking-wide text-gray-500">{{ __('Galeri foto') }}</h3>
            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    'foto_papan_nama_path' => __('Papan nama'),
                    'foto_gedung_path' => __('Gedung depan'),
                    'foto_kelas_path' => __('Kelas'),
                    'foto_halaman_path' => __('Halaman'),
                ] as $field => $label)
                    @if ($reg->{$field})
                        <div>
                            <p class="text-xs font-semibold text-gray-600">{{ $label }}</p>
                            <a href="{{ asset('storage/'.$reg->{$field}) }}" target="_blank" class="mt-1 block overflow-hidden rounded-xl ring-1 ring-gray-100">
                                <img src="{{ asset('storage/'.$reg->{$field}) }}" alt="{{ $label }}" class="h-36 w-full object-cover hover:opacity-95" />
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
