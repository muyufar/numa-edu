<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('PPDB') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Daftar pendaftaran calon siswa.') }}</p>
            </div>
            @can('create', \App\Models\PpdbRegistration::class)
                <a href="{{ route('ppdb.create') }}" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                    {{ __('Tambah manual') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white p-4 shadow-sm ring-1 ring-black/5 sm:p-5">
            <form method="GET" action="{{ route('ppdb.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <div class="min-w-0 flex-1 sm:max-w-xs">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Status') }}</label>
                    <select name="status" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20">
                        <option value="">{{ __('— Semua —') }}</option>
                        @foreach (\App\Models\PpdbRegistration::STATUS_OPTIONS as $st)
                            <option value="{{ $st }}" {{ (string) $status === (string) $st ? 'selected' : '' }}>
                                {{ match ($st) {
                                    'submitted' => __('Dikirim'),
                                    'verified' => __('Diverifikasi'),
                                    'accepted' => __('Diterima'),
                                    'rejected' => __('Ditolak'),
                                    default => $st,
                                } }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light">
                        {{ __('Terapkan') }}
                    </button>
                    <a href="{{ route('ppdb.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
                <div class="text-sm font-semibold text-gray-900">{{ __('Pendaftar') }}</div>
                <div class="text-xs font-semibold text-gray-500">{{ __('Total') }}: {{ $registrations->total() }}</div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3">{{ __('Nama') }}</th>
                            <th class="px-5 py-3">{{ __('Kontak') }}</th>
                            <th class="px-5 py-3">{{ __('Status') }}</th>
                            <th class="px-5 py-3">{{ __('Siswa') }}</th>
                            <th class="px-5 py-3">{{ __('Tanggal') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($registrations as $r)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-5 py-3 font-medium text-gray-900">{{ $r->nama }}</td>
                                <td class="px-5 py-3 text-gray-700">
                                    <div class="text-xs">{{ $r->email ?: '—' }}</div>
                                    <div class="text-xs text-gray-500">{{ $r->no_hp_ortu ?: '' }}</div>
                                </td>
                                <td class="px-5 py-3">@include('ppdb.partials.status-badge', ['status' => $r->status])</td>
                                <td class="px-5 py-3">
                                    @if ($r->siswa_exists)
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-800 ring-1 ring-emerald-200/80">{{ __('Ya') }}</span>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ $r->created_at?->format('Y-m-d') }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        @can('view', $r)
                                            <a href="{{ route('ppdb.show', $r) }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">{{ __('Detail') }}</a>
                                        @endcan
                                        @can('update', $r)
                                            <a href="{{ route('ppdb.edit', $r) }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">{{ __('Edit') }}</a>
                                        @endcan
                                        @can('delete', $r)
                                            <form method="POST" action="{{ route('ppdb.destroy', $r) }}" onsubmit="return confirm('{{ __('Hapus pendaftaran ini?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100">{{ __('Hapus') }}</button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-sm text-gray-500">{{ __('Belum ada pendaftaran.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($registrations->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">
                    {{ $registrations->links() }}
                </div>
            @endif
        </div>

        <p class="text-center text-xs text-gray-500">
            {{ __('Formulir publik:') }}
            <a href="{{ route('ppdb.daftar') }}" class="font-semibold text-nu-primary hover:underline">{{ url('/ppdb/daftar') }}</a>
        </p>
    </div>
</x-app-layout>
