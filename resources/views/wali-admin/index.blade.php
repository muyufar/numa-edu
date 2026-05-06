<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Wali murid') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Kelola akun wali secara global.') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-5">
        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <form method="GET" action="{{ route('wali-admin.index') }}" class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <x-input-label for="q" :value="__('Cari wali (nama/email)')" />
                    <x-text-input id="q" name="q" class="mt-2 block w-full" type="search" :value="$q" placeholder="mis. Siti / wali@contoh.com" />
                </div>
                <div class="flex gap-2">
                    <x-primary-button type="submit">{{ __('Cari') }}</x-primary-button>
                    @if ($q !== '')
                        <a href="{{ route('wali-admin.index') }}" class="btn-nu">{{ __('Reset') }}</a>
                    @endif
                </div>
                </form>

                <div class="sm:pl-3">
                    <a href="{{ route('wali-admin.create') }}" class="btn-nu-primary">
                        {{ __('Daftar wali') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                            <th class="px-5 py-3">{{ __('Nama') }}</th>
                            <th class="px-5 py-3">{{ __('Email') }}</th>
                            <th class="px-5 py-3 text-center">{{ __('Jumlah anak') }}</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($users as $u)
                            <tr class="text-sm text-gray-700">
                                <td class="px-5 py-3 font-semibold text-gray-900">{{ $u->name }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $u->email }}</td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-800">
                                        {{ (int) $u->wali_siswas_count }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('wali-admin.show', $u) }}" class="text-sm font-bold text-nu-primary hover:underline">
                                        {{ __('Detail') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-10 text-center text-sm text-gray-500">
                                    {{ __('Belum ada data wali.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-100 px-5 py-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

