<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Master Guru') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Kelola data guru dan akun masuk.') }}</p>
            </div>
            @can('create', \App\Models\Guru::class)
                <a href="{{ route('guru.create') }}" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light focus:outline-none focus:ring-2 focus:ring-nu-gold focus:ring-offset-2">
                    <span class="me-2 inline-flex h-6 w-6 items-center justify-center rounded-lg bg-white/10">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </span>
                    {{ __('Tambah guru') }}
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

        @if (session('error'))
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">
                {{ session('error') }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
                <div class="text-sm font-semibold text-gray-900">{{ __('Daftar guru') }}</div>
                <div class="text-xs font-semibold text-gray-500">{{ __('Total') }}: {{ $gurus->total() }}</div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
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
                                <td class="px-5 py-3 font-mono text-gray-900">{{ $g->nip ?: '—' }}</td>
                                <td class="px-5 py-3 font-semibold text-gray-900">{{ $g->nama }}</td>
                                <td class="px-5 py-3 text-gray-700">{{ $g->user?->email ?? '—' }}</td>
                                <td class="px-5 py-3 text-gray-700">{{ $g->phone ?: '—' }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-2">
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
                                <td colspan="5" class="px-5 py-10 text-center text-sm text-gray-500">
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
    </div>
</x-app-layout>
