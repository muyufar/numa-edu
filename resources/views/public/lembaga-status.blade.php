<x-public-wide-layout :title="__('Status pendaftaran lembaga')">
    <div class="nu-surface rounded-2xl p-6 shadow-lg ring-1 ring-black/5 sm:p-8">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">{{ __('Status pendaftaran lembaga') }}</h1>
        <p class="mt-1 text-sm text-gray-600">{{ $reg->nama_lembaga }} — NPSN {{ $reg->npsn }}</p>

        @if (session('status'))
            <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        @if (session('info'))
            <div class="mt-4 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-900">
                {{ session('info') }}
            </div>
        @endif

        <x-input-error :messages="$errors->get('pdf')" class="mt-4" />

        @php
            $canRegeneratePdf = $reg->cabang
                && filled($reg->mou_nomor_lp)
                && filled($reg->mou_nomor_sekolah)
                && in_array($reg->status, [\App\Models\LembagaRegistration::STATUS_PENDING_REVIEW, \App\Models\LembagaRegistration::STATUS_APPROVED], true);
        @endphp

        <div class="mt-6 space-y-4 text-sm text-gray-700">
            @if ($reg->status === \App\Models\LembagaRegistration::STATUS_AWAITING_MOU)
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
                    {{ __('Anda belum menyelesaikan penandatanganan MoU. Silakan lanjut ke halaman MoU.') }}
                    <div class="mt-3">
                        <a href="{{ route('public.lembaga-registrations.mou', ['token' => $reg->public_token]) }}" class="inline-flex rounded-xl bg-nu-primary px-4 py-2 text-sm font-semibold text-white hover:bg-nu-primary-light">{{ __('Buka halaman MoU') }}</a>
                    </div>
                </div>
            @elseif ($reg->status === \App\Models\LembagaRegistration::STATUS_PENDING_REVIEW)
                <div class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sky-950">
                    <p class="font-semibold">{{ __('Langkah berikutnya: verifikasi di kantor LP Ma’arif') }}</p>
                    <ol class="mt-3 list-decimal space-y-2 pl-5 text-sm leading-relaxed">
                        <li>{{ __('Unduh draft Nota Kesepahaman (MoU) dan e-sertifikat di bawah ini.') }}</li>
                        <li>{{ __('Cetak draft MoU, lalu bawa ke kantor LP Ma’arif NU setempat.') }}</li>
                        <li>{{ __('Di kantor LP: lengkapi meterai sesuai ketentuan pada salinan cetak, tandatangani secara basah, dan minta tanda tangan Ketua LP Ma’arif NU.') }}</li>
                        <li>{{ __('Setelah verifikasi administratif selesai, admin akan menyetujui permohonan di sistem; akun sekolah dapat diaktifkan sesuai prosedur LP.') }}</li>
                    </ol>
                    <p class="mt-3 text-sm font-semibold text-sky-900">{{ __('Unduhan dokumen') }}</p>
                    <p class="mt-1 text-xs text-sky-800/90">{{ __('File PDF disimpan saat Anda mengirim MoU. Jika sistem memperbarui tampilan dokumen, gunakan tombol di bawah lalu unduh ulang.') }}</p>
                    <div class="mt-2 flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center">
                        @if ($reg->mou_draft_pdf_path)
                            <a href="{{ asset('storage/'.$reg->mou_draft_pdf_path) }}?v={{ (int) $reg->updated_at->timestamp }}" target="_blank" rel="noopener" class="inline-flex rounded-xl bg-nu-primary px-4 py-2 text-sm font-semibold text-white hover:bg-nu-primary-light">{{ __('Unduh draft MoU (PDF)') }}</a>
                        @endif
                        @if ($reg->e_sertifikat_pdf_path)
                            <a href="{{ asset('storage/'.$reg->e_sertifikat_pdf_path) }}?v={{ (int) $reg->updated_at->timestamp }}" target="_blank" rel="noopener" class="inline-flex rounded-xl border border-sky-300 bg-white px-4 py-2 text-sm font-semibold text-sky-900 hover:bg-sky-100">{{ __('Unduh e-sertifikat (PDF)') }}</a>
                        @endif
                        @if ($canRegeneratePdf)
                            <form method="POST" action="{{ route('public.lembaga-registrations.pdf-regenerate', ['token' => $reg->public_token]) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex rounded-xl border border-sky-400 bg-white px-4 py-2 text-sm font-semibold text-sky-950 hover:bg-sky-50">
                                    {{ __('Perbarui PDF (tampilan terbaru)') }}
                                </button>
                            </form>
                        @endif
                    </div>
                    @if (! $reg->mou_draft_pdf_path && ! $reg->e_sertifikat_pdf_path)
                        <p class="mt-2 text-xs text-amber-800">{{ __('Berkas PDF belum tersedia. Hubungi admin jika Anda sudah menandatangani tetapi tautan unduhan tidak muncul.') }}</p>
                    @endif
                </div>
            @elseif ($reg->status === \App\Models\LembagaRegistration::STATUS_APPROVED)
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-950">
                    <p class="font-semibold">{{ __('Permohonan disetujui') }}</p>
                    <p class="mt-2">{{ __('Akun admin sekolah telah dibuat. Silakan login menggunakan email operator yang Anda daftarkan. Jika belum menerima sandi dari verifikator, hubungi LP Ma’arif setempat.') }}</p>
                    @if ($reg->mou_draft_pdf_path || $reg->e_sertifikat_pdf_path)
                        <p class="mt-3 text-sm font-semibold">{{ __('Salinan PDF') }}</p>
                        <p class="mt-1 text-xs text-emerald-900/80">{{ __('Jika tampilan PDF lama, perbarui lalu unduh ulang.') }}</p>
                        <div class="mt-2 flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center">
                            @if ($reg->mou_draft_pdf_path)
                                <a href="{{ asset('storage/'.$reg->mou_draft_pdf_path) }}?v={{ (int) $reg->updated_at->timestamp }}" target="_blank" rel="noopener" class="inline-flex rounded-xl border border-emerald-300 bg-white px-4 py-2 text-sm font-semibold text-emerald-900 hover:bg-emerald-100">{{ __('Draft MoU (PDF)') }}</a>
                            @endif
                            @if ($reg->e_sertifikat_pdf_path)
                                <a href="{{ asset('storage/'.$reg->e_sertifikat_pdf_path) }}?v={{ (int) $reg->updated_at->timestamp }}" target="_blank" rel="noopener" class="inline-flex rounded-xl border border-emerald-300 bg-white px-4 py-2 text-sm font-semibold text-emerald-900 hover:bg-emerald-100">{{ __('E-sertifikat (PDF)') }}</a>
                            @endif
                            @if ($canRegeneratePdf)
                                <form method="POST" action="{{ route('public.lembaga-registrations.pdf-regenerate', ['token' => $reg->public_token]) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex rounded-xl border border-emerald-500 bg-white px-4 py-2 text-sm font-semibold text-emerald-950 hover:bg-emerald-50">
                                        {{ __('Perbarui PDF (tampilan terbaru)') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                    <div class="mt-3">
                        <a href="{{ route('login') }}" class="inline-flex rounded-xl bg-nu-primary px-4 py-2 text-sm font-semibold text-white hover:bg-nu-primary-light">{{ __('Log in') }}</a>
                    </div>
                </div>
            @elseif ($reg->status === \App\Models\LembagaRegistration::STATUS_REJECTED)
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-900">
                    <p class="font-semibold">{{ __('Permohonan tidak dapat dilanjutkan') }}</p>
                    @if ($reg->admin_notes)
                        <p class="mt-2 whitespace-pre-line">{{ $reg->admin_notes }}</p>
                    @endif
                    <div class="mt-4">
                        <a href="{{ route('public.lembaga-registrations.edit', ['token' => $reg->public_token]) }}" class="inline-flex rounded-xl bg-nu-primary px-4 py-2 text-sm font-semibold text-white hover:bg-nu-primary-light">{{ __('Perbaiki / ubah data permohonan') }}</a>
                    </div>
                </div>
            @endif
        </div>

        <p class="mt-8 text-xs text-gray-500">{{ __('Simpan tautan status ini untuk pengecekan mandiri:') }}</p>
        <p class="mt-1 break-all font-mono text-xs text-gray-700">{{ url()->current() }}</p>
    </div>
</x-public-wide-layout>
