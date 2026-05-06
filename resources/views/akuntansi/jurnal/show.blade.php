<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Detail jurnal') }}</h2>
                <p class="mt-1 text-sm text-gray-600">#{{ $jurnal->id }} · {{ \App\Support\ManualJurnalService::sumberLabel($jurnal->sumber_type) }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('akuntansi.jurnal.index') }}" class="btn-nu">{{ __('Daftar jurnal') }}</a>
                @if ($jurnal->sumber_type === null && $jurnal->sumber_id === null)
                    @can('create', \App\Models\Tagihan::class)
                        <form method="POST" action="{{ route('akuntansi.jurnal.destroy', $jurnal) }}" class="inline" onsubmit="return confirm(@json(__('Hapus jurnal manual ini?')))">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-nu text-red-700 ring-red-200 hover:bg-red-50">{{ __('Hapus jurnal') }}</button>
                        </form>
                    @endcan
                @endif
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white p-6 shadow-sm ring-1 ring-black/5">
            <dl class="grid gap-3 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Tanggal') }}</dt>
                    <dd class="mt-0.5 font-mono font-semibold text-gray-900">{{ \App\Support\DateTimeFormat::date($jurnal->tanggal) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('No. bukti') }}</dt>
                    <dd class="mt-0.5 font-mono text-gray-800">{{ $jurnal->no_bukti ?? '—' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Keterangan') }}</dt>
                    <dd class="mt-0.5 text-gray-800">{{ $jurnal->keterangan ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Dicatat oleh') }}</dt>
                    <dd class="mt-0.5 text-gray-800">{{ $jurnal->dibuatOleh?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Sekolah') }}</dt>
                    <dd class="mt-0.5 font-mono text-gray-800">#{{ $jurnal->sekolah_id }}</dd>
                </div>
            </dl>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="border-b border-gray-100 px-5 py-4 text-sm font-bold text-gray-900">{{ __('Baris jurnal') }}</div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3">{{ __('Akun') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Debit') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Kredit') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($jurnal->lines as $line)
                            <tr>
                                <td class="px-5 py-3">
                                    <span class="font-mono font-semibold text-gray-900">{{ $line->akun?->kode }}</span>
                                    <span class="text-gray-700">{{ $line->akun?->nama }}</span>
                                </td>
                                <td class="px-5 py-3 text-right font-mono">{{ number_format((float) $line->debit, 0, ',', '.') }}</td>
                                <td class="px-5 py-3 text-right font-mono">{{ number_format((float) $line->kredit, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t-2 border-gray-200 bg-gray-50/80 text-sm font-semibold">
                        <tr>
                            <td class="px-5 py-3 text-gray-900">{{ __('Total') }}</td>
                            <td class="px-5 py-3 text-right font-mono text-gray-900">Rp {{ number_format((int) round($totalDebit), 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right font-mono text-gray-900">Rp {{ number_format((int) round($totalKredit), 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if (abs($totalDebit - $totalKredit) > 0.01)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                {{ __('Peringatan: total debit dan kredit tidak sama pada data ini.') }}
            </div>
        @endif
    </div>
</x-app-layout>
