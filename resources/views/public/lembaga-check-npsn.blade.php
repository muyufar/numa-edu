<x-public-wide-layout :title="__('Cek status pendaftaran (NPSN)')">
    <div class="nu-surface rounded-2xl p-6 shadow-lg ring-1 ring-black/5 sm:p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">{{ __('Cek status pendaftaran lembaga') }}</h1>
                <p class="mt-1 max-w-xl text-sm text-gray-600">
                    {{ __('Masukkan NPSN sekolah/madrasah untuk melihat ringkasan status permohonan terbaru di sistem. Tautan pribadi (token) tidak ditampilkan di sini demi keamanan; gunakan tautan yang Anda simpan atau terima setelah mengirim formulir untuk membuka MoU, mengunduh PDF, atau memperbaiki data.') }}
                </p>
            </div>
            <a href="{{ route('public.lembaga-registrations.create') }}" class="shrink-0 text-sm font-semibold text-nu-primary hover:underline">
                {{ __('Formulir pendaftaran') }}
            </a>
        </div>

        <form method="POST" action="{{ route('public.lembaga-registrations.check-status.submit') }}" class="mt-8 space-y-4">
            @csrf
            <div>
                <x-input-label for="npsn" :value="__('NPSN (8 digit)')" />
                <x-text-input
                    id="npsn"
                    name="npsn"
                    type="text"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    maxlength="8"
                    class="mt-1 block w-full max-w-xs font-mono tracking-widest"
                    :value="old('npsn', $submittedNpsn)"
                    required
                    autofocus
                    autocomplete="off"
                />
                <x-input-error :messages="$errors->get('npsn')" class="mt-1" />
            </div>
            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light focus:outline-none focus:ring-2 focus:ring-nu-gold focus:ring-offset-2">
                {{ __('Cek status') }}
            </button>
        </form>

        @if ($submittedNpsn !== null)
            <div class="mt-10 border-t border-gray-100 pt-8">
                @if ($registration === null)
                    <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800">
                        <p class="font-semibold">{{ __('Tidak ada permohonan untuk NPSN ini') }}</p>
                        <p class="mt-2 text-gray-700">
                            {{ __('Belum ada data pendaftaran lembaga dengan NPSN tersebut, atau NPSN salah ketik. Periksa kembali angka Anda, atau mulai pendaftaran lewat formulir pendaftaran lembaga.') }}
                        </p>
                    </div>
                @else
                    <h2 class="text-sm font-bold uppercase tracking-wide text-gray-500">{{ __('Hasil pengecekan') }}</h2>
                    <p class="mt-2 text-sm text-gray-600">
                        <span class="font-semibold text-gray-900">{{ $registration->nama_lembaga }}</span>
                        <span class="text-gray-400">·</span>
                        {{ __('NPSN') }} <span class="font-mono font-semibold text-gray-900">{{ $registration->npsn }}</span>
                    </p>

                    @php
                        $st = $registration->status;
                    @endphp

                    <div class="mt-4 rounded-xl border px-4 py-3 text-sm
                        @if ($st === \App\Models\LembagaRegistration::STATUS_AWAITING_MOU) border-amber-200 bg-amber-50 text-amber-950
                        @elseif ($st === \App\Models\LembagaRegistration::STATUS_PENDING_REVIEW) border-sky-200 bg-sky-50 text-sky-950
                        @elseif ($st === \App\Models\LembagaRegistration::STATUS_APPROVED) border-emerald-200 bg-emerald-50 text-emerald-950
                        @else border-red-200 bg-red-50 text-red-950
                        @endif">
                        @if ($st === \App\Models\LembagaRegistration::STATUS_AWAITING_MOU)
                            <p class="font-semibold">{{ __('Status: menunggu penyelesaian MoU') }}</p>
                            <p class="mt-2 leading-relaxed">
                                {{ __('Permohonan sudah tercatat; Anda perlu menyelesaikan langkah MoU melalui tautan pribadi yang didapat setelah mengirim formulir (buka dari perangkat yang sama atau cek riwayat unduhan/email).') }}
                            </p>
                        @elseif ($st === \App\Models\LembagaRegistration::STATUS_PENDING_REVIEW)
                            <p class="font-semibold">{{ __('Status: menunggu verifikasi LP Ma’arif') }}</p>
                            <p class="mt-2 leading-relaxed">
                                {{ __('MoU telah diajukan melalui sistem. Untuk mengunduh draft MoU dan e-sertifikat, atau melihat petunjuk lengkap, buka halaman status menggunakan tautan pribadi Anda.') }}
                            </p>
                        @elseif ($st === \App\Models\LembagaRegistration::STATUS_APPROVED)
                            <p class="font-semibold">{{ __('Status: disetujui') }}</p>
                            <p class="mt-2 leading-relaxed">
                                {{ __('Permohonan telah disetujui. Login admin sekolah memakai email operator yang didaftarkan. Jika lupa tautan bantuan, hubungi LP Ma’arif setempat.') }}
                            </p>
                        @else
                            <p class="font-semibold">{{ __('Status: tidak dilanjutkan / ditolak') }}</p>
                            <p class="mt-2 leading-relaxed">
                                {{ __('Permohonan tidak dapat dilanjutkan. Alasan detail dan opsi perbaikan data (jika tersedia) hanya ditampilkan di halaman status pribadi Anda. Hubungi LP Ma’arif jika Anda kehilangan tautan tersebut.') }}
                            </p>
                        @endif
                    </div>

                    <p class="mt-6 text-xs text-gray-500">
                        {{ __('Terakhir diperbarui di sistem:') }}
                        <span class="font-mono text-gray-700">{{ $registration->updated_at->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</span>
                    </p>
                @endif
            </div>
        @endif
    </div>
</x-public-wide-layout>
