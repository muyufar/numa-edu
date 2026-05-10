@php
    use Illuminate\Support\Carbon;
    Carbon::setLocale('id');
@endphp
<x-public-wide-layout :title="__('Nota kesepahaman (MoU)')">
    <div class="nu-surface rounded-2xl p-6 shadow-lg ring-1 ring-black/5 sm:p-8">
        <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">{{ __('Nota kesepahaman (MoU)') }}</h1>
                <p class="mt-1 text-sm text-gray-600">{{ __('Data berikut disinkronkan dari pendaftaran lembaga Anda. Isi nomor surat dari sekolah sesuai format internal Anda. Nomor surat LP Ma’arif akan diberikan otomatis oleh sistem. Baca isi MoU, lalu kirim; meterai dan tanda tangan basah kepala lembaga dilakukan pada salinan cetak, sedangkan cap LP dan tanda tangan basah Ketua LP dilakukan di kantor LP Ma’arif.') }}</p>
            </div>
            <a href="{{ route('public.lembaga-registrations.status', ['token' => $reg->public_token]) }}" class="text-sm font-semibold text-nu-primary hover:underline">{{ __('Status permohonan') }}</a>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                {{ __('Periksa kembali isian formulir.') }}
            </div>
        @endif

        <div class="lembaga-mou-doc-root max-w-none rounded-xl border border-gray-100 bg-white p-6 text-gray-900 shadow-inner">
            <style>
                .lembaga-mou-doc-root { font-size: 14px; line-height: 1.5; }
                .lembaga-mou-doc-root p { margin: 0 0 8px; text-align: justify; }
                .lembaga-mou-doc-root .mou-r26-kop-title { text-align: center; font-weight: 700; font-size: 1.125rem; text-transform: uppercase; margin: 0 0 6px; line-height: 1.35; color: #111827; }
                .lembaga-mou-doc-root .mou-r26-kop-sub { font-size: 0.8125rem; font-weight: 500; text-transform: none; color: #4b5563; display: block; margin-top: 2px; }
                .lembaga-mou-doc-root .mou-r26-center { text-align: center; }
                .lembaga-mou-doc-root .mou-r26-small { color: #4b5563; font-size: 0.8125rem; margin: 8px 0 4px; }
                .lembaga-mou-doc-root .mou-r26-school { font-weight: 700; font-size: 1rem; text-transform: uppercase; margin: 6px 0; text-align: center; color: #111827; }
                .lembaga-mou-doc-root .mou-r26-lp-block { font-weight: 700; font-size: 0.875rem; text-transform: uppercase; line-height: 1.45; margin: 8px 0; text-align: center; color: #111827; }
                .lembaga-mou-doc-root .mou-r26-nomor { font-size: 0.875rem; margin: 4px 0; text-align: center; }
                .lembaga-mou-doc-root .mou-r26-pasal-h { text-transform: uppercase; font-weight: 700; text-align: center; margin: 14px 0 6px; font-size: 0.875rem; color: #111827; }
                .lembaga-mou-doc-root .mou-r26-ol { margin: 0 0 10px 1.5rem; padding-left: 0.25rem; list-style-type: decimal; }
                .lembaga-mou-doc-root .mou-r26-ol li { margin-bottom: 6px; text-align: justify; }
            </style>
            @include('partials.lembaga-mou-revisi-mar-body', [
                'reg' => $reg,
                'cabang' => $reg->cabang,
                'nomorLpHtml' => '<span class="font-mono text-sm text-gray-800">'.e(__('Diberikan otomatis saat Anda mengirim formulir MoU.')).'</span>',
                'nomorSekolahHtml' => '<span class="font-mono text-sm text-gray-800">'.e(__('Diisi pada formulir di bawah sesuai format sekolah Anda.')).'</span>',
                'mouCarbon' => now(),
            ])
            <p class="mt-2 border-t border-gray-100 pt-3 text-sm leading-snug text-gray-600">
                {{ __('Catatan sistem: meterai dan tanda tangan basah PIHAK KESATU dilakukan pada salinan cetak; cap organisasi dan tanda tangan basah PIHAK KEDUA dilakukan pada salinan cetak di kantor LP Ma’arif.') }}
            </p>
        </div>

        <form id="mou-form" method="POST" action="{{ route('public.lembaga-registrations.mou.sign', ['token' => $reg->public_token]) }}" class="mt-8 space-y-6">
            @csrf

            <div class="space-y-2">
                <x-input-label for="mou_nomor_sekolah" :value="__('Nomor surat dari sekolah/madrasah (wajib)')" />
                <p class="text-xs text-gray-500">{{ __('Isi sesuai penomoran surat internal sekolah Anda (contoh: 042/SK/III/2026).') }}</p>
                <x-text-input id="mou_nomor_sekolah" name="mou_nomor_sekolah" type="text" class="block w-full font-mono" :value="old('mou_nomor_sekolah', $reg->mou_nomor_sekolah)" required />
                <x-input-error :messages="$errors->get('mou_nomor_sekolah')" class="mt-1" />
            </div>
            <x-input-error :messages="$errors->get('mou_settings')" class="mt-1" />

            <div class="rounded-xl border border-amber-100 bg-amber-50/80 p-4 text-sm text-amber-950">
                <label class="flex cursor-pointer items-start gap-3">
                    <input type="checkbox" name="mou_accepted" value="1" class="mt-1 rounded border-gray-300 text-nu-primary focus:ring-nu-primary" {{ old('mou_accepted') ? 'checked' : '' }} required />
                    <span>{{ __('Saya telah membaca dan memahami isi Nota Kesepahaman di atas, dan bersedia melanjutkan pengajuan. Saya memahami bahwa meterai dan tanda tangan basah saya sebagai kepala lembaga dilakukan pada salinan cetak bermeterai, serta cap LP dan tanda tangan basah Ketua LP Ma’arif dilakukan di kantor LP Ma’arif pada salinan yang sama.') }}</span>
                </label>
                <x-input-error :messages="$errors->get('mou_accepted')" class="mt-2" />
            </div>

            <div class="flex flex-col gap-3 border-t border-gray-100 pt-4 sm:flex-row sm:justify-end">
                <a href="{{ url('/') }}" class="text-center text-sm font-semibold text-gray-600 hover:text-gray-900 sm:mr-auto">{{ __('Kembali ke beranda') }}</a>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light focus:outline-none focus:ring-2 focus:ring-nu-gold focus:ring-offset-2">
                    {{ __('Kirim & buat draft MoU') }}
                </button>
            </div>
        </form>
    </div>

</x-public-wide-layout>
