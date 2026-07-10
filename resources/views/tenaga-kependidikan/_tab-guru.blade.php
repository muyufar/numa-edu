<div class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
    <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
        <div class="text-sm font-semibold text-gray-900">{{ __('Daftar guru') }}</div>
        <div class="text-xs font-semibold text-gray-500">{{ __('Total') }}: {{ $gurus->total() }}</div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3">{{ __('Foto') }}</th>
                            <th class="px-5 py-3">{{ __('NIP') }}</th>
                    <th class="px-5 py-3">{{ __('Nama') }}</th>
                    <th class="px-5 py-3">{{ __('Email') }}</th>
                    <th class="px-5 py-3">{{ __('Telepon') }}</th>
                    <th class="px-5 py-3 text-right">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($gurus as $g)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-5 py-3">
                                    <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-xl border border-gray-200 bg-gray-50">
                                        @if ($g->fotoUrl())
                                            <img src="{{ $g->fotoUrl() }}" alt="{{ $g->nama }}" class="h-full w-full object-cover" />
                                        @else
                                            <span class="text-xs font-bold text-nu-primary">{{ mb_strtoupper(mb_substr($g->nama, 0, 1)) }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-3 font-mono text-gray-900">{{ $g->nip ?: '—' }}</td>
                        <td class="px-5 py-3 font-semibold text-gray-900">
                            <a href="{{ route('guru.show', $g) }}" class="hover:text-nu-primary hover:underline">{{ $g->nama }}</a>
                        </td>
                        <td class="px-5 py-3 text-gray-700">{{ $g->user?->email ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ $g->phone ?: '—' }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-2">
                                @can('view', $g)
                                    <a href="{{ route('guru.show', $g) }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                        {{ __('Detail') }}
                                    </a>
                                @endcan
                                @can('update', $g)
                                    <a href="{{ route('guru.edit', $g) }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                        {{ __('Edit') }}
                                    </a>
                                @endcan
                                @can('delete', $g)
                                    <form method="POST" action="{{ route('guru.destroy', $g) }}" onsubmit="return confirm('{{ __('Hapus guru dan akun masuknya?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100">
                                            {{ __('Hapus') }}
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-sm text-gray-500">
                            {{ __('Belum ada data guru.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($gurus->hasPages())
        <div class="border-t border-gray-100 px-5 py-4">
            {{ $gurus->links() }}
        </div>
    @endif
</div>
