@php
    use App\Support\PerangkatAjarJenis;
@endphp

<div class="rounded-2xl border border-gray-200 bg-gray-50/80 p-4" x-show="['modul','rpp','modul_pembelajaran'].includes(jenis)" x-cloak>
    <p class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ __('Panduan: perbedaan jenis perangkat') }}</p>
    <p class="mt-2 text-sm text-gray-700" x-show="jenis === 'modul'">{{ PerangkatAjarJenis::deskripsiJenis('modul') }}</p>
    <p class="mt-2 text-sm text-gray-700" x-show="jenis === 'rpp'">{{ PerangkatAjarJenis::deskripsiJenis('rpp') }}</p>
    <p class="mt-2 text-sm text-gray-700" x-show="jenis === 'modul_pembelajaran'">{{ PerangkatAjarJenis::deskripsiJenis('modul_pembelajaran') }}</p>
    <p class="mt-2 text-sm text-gray-700" x-show="jenis === 'lkpd'">{{ PerangkatAjarJenis::deskripsiJenis('lkpd') }}</p>

    <details class="mt-4" x-show="['modul','rpp','modul_pembelajaran'].includes(jenis)">
        <summary class="cursor-pointer text-sm font-semibold text-nu-primary hover:underline">{{ __('Lihat perbandingan Modul Ajar, RPP, & Modul Pembelajaran') }}</summary>
        <div class="mt-3 overflow-x-auto">
            <table class="min-w-full text-left text-xs text-gray-700">
                <thead>
                    <tr class="border-b border-gray-200 text-gray-500">
                        <th class="py-2 pr-3 font-semibold">{{ __('Aspek') }}</th>
                        @foreach (PerangkatAjarJenis::perbandingan() as $row)
                            <th class="py-2 pr-3 font-semibold">{{ $row['label'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td class="py-2 pr-3 font-medium text-gray-500">{{ __('Fokus') }}</td>
                        @foreach (PerangkatAjarJenis::perbandingan() as $row)
                            <td class="py-2 pr-3">{{ $row['fokus'] }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="py-2 pr-3 font-medium text-gray-500">{{ __('Tujuan') }}</td>
                        @foreach (PerangkatAjarJenis::perbandingan() as $row)
                            <td class="py-2 pr-3">{{ $row['tujuan'] }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="py-2 pr-3 font-medium text-gray-500">{{ __('Isi utama') }}</td>
                        @foreach (PerangkatAjarJenis::perbandingan() as $row)
                            <td class="py-2 pr-3">{{ $row['isi'] }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="py-2 pr-3 font-medium text-gray-500">{{ __('Evaluasi') }}</td>
                        @foreach (PerangkatAjarJenis::perbandingan() as $row)
                            <td class="py-2 pr-3">{{ $row['evaluasi'] }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </details>
</div>
