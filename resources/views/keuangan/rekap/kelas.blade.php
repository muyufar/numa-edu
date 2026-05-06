<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Detail piutang kelas') }}</h2>
                <p class="mt-1 text-sm text-gray-600">
                    <span class="font-semibold text-gray-900">{{ $kelas->tingkat }} {{ $kelas->nama }}</span>
                    <span class="text-gray-500">· {{ $kelas->tahun_ajaran }}</span>
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('keuangan.rekap.index', ['periode_from' => $periodeFrom, 'periode_to' => $periodeTo]) }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                    {{ __('Kembali ke rekap') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5">
            <div class="text-sm font-bold text-gray-900">{{ __('Filter periode') }}</div>
            <form method="GET" action="{{ route('keuangan.rekap.kelas', $kelas) }}" class="mt-4 grid gap-4 sm:grid-cols-12">
                <div class="sm:col-span-6 lg:col-span-2 space-y-2">
                    <x-input-label for="periode_from" :value="__('Periode dari')" />
                    <x-text-input id="periode_from" name="periode_from" class="block w-full font-mono" type="text" maxlength="7" :value="$periodeFrom" />
                </div>
                <div class="sm:col-span-6 lg:col-span-2 space-y-2">
                    <x-input-label for="periode_to" :value="__('Periode sampai')" />
                    <x-text-input id="periode_to" name="periode_to" class="block w-full font-mono" type="text" maxlength="7" :value="$periodeTo" />
                </div>
                <div class="sm:col-span-12 lg:col-span-2 flex items-end">
                    <x-primary-button class="w-full justify-center">{{ __('Terapkan') }}</x-primary-button>
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5">
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Total sisa piutang') }}</div>
            <div class="mt-1 font-mono text-2xl font-extrabold text-nu-primary">@include('keuangan.partials.rupiah', ['value' => $totalSisa])</div>
        </div>

        <div class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="border-b border-gray-100 px-5 py-4 text-sm font-bold text-gray-900">{{ __('Piutang per siswa') }}</div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3">{{ __('Siswa') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Sisa') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($bySiswa as $row)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-5 py-3 font-medium text-gray-900">
                                    {{ $row['siswa']?->nama ?? '—' }}
                                    <div class="mt-0.5 font-mono text-xs text-gray-500">{{ $row['siswa']?->nis ?? '' }}</div>
                                </td>
                                <td class="px-5 py-3 text-right font-mono font-bold text-nu-primary">@include('keuangan.partials.rupiah', ['value' => $row['sisa']])</td>
                                <td class="px-5 py-3 text-right">
                                    @if($row['siswa'])
                                        <a href="{{ route('keuangan.rekap.siswa', ['siswa' => $row['siswa']->id, 'periode_from' => $periodeFrom, 'periode_to' => $periodeTo]) }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                            {{ __('Detail') }}
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-10 text-center text-sm text-gray-500">{{ __('Tidak ada data.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

