<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Pendaftaran lembaga masuk') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Verifikasi profil, lampiran, dan MoU dari calon lembaga sebelum menyetujui pembuatan akun.') }}</p>
            </div>
            <a href="{{ route('pengurus.lembaga-mou-settings.edit') }}" class="inline-flex shrink-0 items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-800 shadow-sm hover:bg-gray-50">{{ __('Pengaturan nomor MoU') }}</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-6xl space-y-4">
        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm ring-1 ring-black/5">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-bold uppercase tracking-wide text-gray-600">
                    <tr>
                        <th class="px-4 py-3">{{ __('Tanggal') }}</th>
                        <th class="px-4 py-3">{{ __('Lembaga') }}</th>
                        <th class="px-4 py-3">{{ __('NPSN') }}</th>
                        <th class="px-4 py-3">{{ __('Status') }}</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($registrations as $r)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-600">{{ $r->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ $r->nama_lembaga }}</td>
                            <td class="px-4 py-3 font-mono text-gray-700">{{ $r->npsn }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $badge = match ($r->status) {
                                        \App\Models\LembagaRegistration::STATUS_AWAITING_MOU => 'bg-amber-50 text-amber-900 ring-amber-200',
                                        \App\Models\LembagaRegistration::STATUS_PENDING_REVIEW => 'bg-sky-50 text-sky-900 ring-sky-200',
                                        \App\Models\LembagaRegistration::STATUS_APPROVED => 'bg-emerald-50 text-emerald-900 ring-emerald-200',
                                        \App\Models\LembagaRegistration::STATUS_REJECTED => 'bg-red-50 text-red-900 ring-red-200',
                                        default => 'bg-gray-50 text-gray-800 ring-gray-200',
                                    };
                                @endphp
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold ring-1 {{ $badge }}">
                                    {{ $r->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('pengurus.lembaga-registrations.show', $r) }}" class="text-sm font-semibold text-nu-primary hover:underline">{{ __('Detail') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">{{ __('Belum ada permohonan.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pb-4">
            {{ $registrations->links() }}
        </div>
    </div>
</x-app-layout>
