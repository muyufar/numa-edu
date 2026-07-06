<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Akun siswa') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Pantau siswa yang sudah/belum punya akun login.') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-5">
        <div class="rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-6">
            <form method="GET" action="{{ route('siswa-akun-admin.index') }}" class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-12 sm:items-end">
                    @if (!empty($sekolahOptions))
                        <div class="sm:col-span-4">
                            <x-input-label for="sekolah_id" :value="__('Sekolah')" />
                            <select id="sekolah_id" name="sekolah_id" class="mt-2 w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-nu-primary focus:ring-nu-primary/25">
                                <option value="">{{ __('Semua sekolah') }}</option>
                                @foreach ($sekolahOptions as $s)
                                    <option value="{{ $s->id }}" @selected((string) request('sekolah_id') === (string) $s->id)>{{ $s->nama }} ({{ $s->npsn }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="{{ !empty($sekolahOptions) ? 'sm:col-span-6' : 'sm:col-span-8' }}">
                        <x-input-label for="q" :value="__('Cari siswa (nama/NIS/NISN)')" />
                        <x-text-input id="q" name="q" class="mt-2 block w-full" type="search" :value="$q" placeholder="mis. Abdul / 12345" />
                    </div>

                    <div class="sm:col-span-4">
                        <label class="mt-6 inline-flex w-full items-center justify-between gap-3 rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm font-semibold text-gray-700">
                            <span class="leading-snug">{{ __('Hanya yang belum punya akun') }}</span>
                            <input type="checkbox" name="only_missing" value="1" class="h-5 w-5 rounded border-gray-300 text-nu-primary shadow-sm focus:ring-nu-primary/25" @checked($onlyMissing)>
                        </label>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-2 border-t border-gray-100 pt-4">
                    <a href="{{ route('siswa-akun-admin.index') }}" class="btn-nu">{{ __('Reset') }}</a>
                    <x-primary-button type="submit">{{ __('Terapkan') }}</x-primary-button>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                            <th class="px-5 py-3">{{ __('Nama') }}</th>
                            <th class="px-5 py-3">{{ __('NIS') }}</th>
                            <th class="px-5 py-3">{{ __('NISN') }}</th>
                            <th class="px-5 py-3">{{ __('Kelas') }}</th>
                            <th class="px-5 py-3">{{ __('Akun') }}</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($siswas as $s)
                            <tr class="text-sm text-gray-700">
                                <td class="px-5 py-3 font-semibold text-gray-900">{{ $s->nama }}</td>
                                <td class="px-5 py-3 font-mono text-gray-600">{{ $s->nis }}</td>
                                <td class="px-5 py-3 font-mono text-gray-600">{{ $s->nisn ?: '—' }}</td>
                                <td class="px-5 py-3 text-gray-600">
                                    @if ($s->kelas)
                                        {{ $s->kelas->tingkat }} {{ $s->kelas->nama }} · {{ $s->kelas->tahun_ajaran }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    @if ($s->user)
                                        <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800 ring-1 ring-emerald-200">
                                            {{ __('Aktif') }}
                                            <span class="font-normal text-emerald-700">{{ $s->user->email }}</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">
                                            {{ __('Belum ada') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('siswa.edit', $s) }}" class="text-sm font-bold text-nu-primary hover:underline">
                                        {{ $s->user ? __('Kelola akun') : __('Buat akun') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-sm text-gray-500">
                                    {{ __('Tidak ada data.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-100 px-5 py-4">
                {{ $siswas->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

