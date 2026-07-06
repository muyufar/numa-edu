<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-nu-primary">{{ __('Master Siswa') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Kelola data siswa dan penempatan kelas.') }}</p>
            </div>
            @can('create', \App\Models\Siswa::class)
                <a href="{{ route('siswa.create') }}" class="inline-flex items-center justify-center rounded-xl bg-nu-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-nu-primary-light focus:outline-none focus:ring-2 focus:ring-nu-gold focus:ring-offset-2">
                    <span class="me-2 inline-flex h-6 w-6 items-center justify-center rounded-lg bg-white/10">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </span>
                    {{ __('Tambah siswa') }}
                </a>
            @endcan

            @if (auth()->user()->hasAnyRole(['super_admin', 'admin', 'guru', 'pengurus_cabang']))
                <a href="{{ route('siswa.index', ['import' => 1]) }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                    {{ __('Import/Export') }}
                </a>
            @endif
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        @if (auth()->user()->hasAnyRole(['super_admin', 'admin', 'pengurus_cabang']))
            <div class="grid gap-3 sm:grid-cols-2">
                <div class="rounded-2xl border border-nu-primary/15 bg-white p-5 shadow-sm ring-1 ring-black/5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-sm font-extrabold text-gray-900">{{ __('Siswa') }}</div>
                            <div class="mt-1 text-xs text-gray-500">{{ __('Kelola data siswa & penempatan kelas') }}</div>
                        </div>
                        <span class="inline-flex items-center rounded-full bg-nu-primary/10 px-2.5 py-1 text-xs font-semibold text-nu-primary ring-1 ring-nu-primary/15">
                            {{ __('Master') }}
                        </span>
                    </div>
                    <div class="mt-4">
                        <span class="inline-flex items-center rounded-xl bg-gray-50 px-3 py-2 text-xs font-semibold text-gray-700 ring-1 ring-gray-200">
                            {{ __('Halaman ini') }}
                        </span>
                    </div>
                </div>

                <a href="{{ route('siswa-akun-admin.index') }}" class="group rounded-2xl border border-gray-100/80 bg-white p-5 shadow-sm ring-1 ring-black/5 hover:border-nu-primary/25 hover:ring-nu-primary/10">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-sm font-extrabold text-gray-900 group-hover:text-nu-primary">{{ __('Akun siswa') }}</div>
                            <div class="mt-1 text-xs text-gray-500">{{ __('Pantau yang sudah/belum punya akun login') }}</div>
                        </div>
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700 ring-1 ring-gray-200">
                            {{ __('Akun') }}
                        </span>
                    </div>
                    <div class="mt-4 inline-flex items-center gap-2 text-sm font-bold text-nu-primary">
                        {{ __('Buka') }}
                        <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>
            </div>
        @endif

        <div class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
            <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
                <div class="text-sm font-semibold text-gray-900">{{ __('Daftar siswa') }}</div>
                <div class="text-xs font-semibold text-gray-500">{{ __('Total') }}: {{ $siswas->total() }}</div>
            </div>

            @can('deleteAny', \App\Models\Siswa::class)
                <div class="border-b border-gray-100 bg-gray-50/40 px-5 py-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <div class="text-sm font-semibold text-gray-900">{{ __('Hapus siswa terpilih') }}</div>
                            <div class="mt-1 text-xs text-gray-600">
                                {{ __('Centang siswa yang ingin dihapus, lalu konfirmasi dengan mengetik HAPUS.') }}
                            </div>
                        </div>
                        <div class="text-xs font-semibold text-gray-500">
                            {{ __('Terpilih:') }} <span id="bulkSelectedCount">0</span>
                        </div>
                    </div>

                    <form id="bulkDeleteForm" method="POST" action="{{ route('siswa.destroy-bulk') }}" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
                        @csrf
                        @method('DELETE')
                        <div class="flex-1">
                            <label class="block text-xs font-semibold text-gray-700">{{ __('Ketik HAPUS untuk konfirmasi') }}</label>
                            <input
                                name="confirm"
                                type="text"
                                required
                                class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nu-primary focus:outline-none focus:ring-2 focus:ring-nu-primary/20"
                                placeholder="HAPUS"
                            />
                            @error('confirm')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <button
                            id="bulkDeleteBtn"
                            type="submit"
                            disabled
                            onclick="return confirm('{{ __('Yakin hapus siswa yang dicentang?') }}')"
                            class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            {{ __('Hapus terpilih') }}
                        </button>
                    </form>
                </div>
            @endcan

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            @can('deleteAny', \App\Models\Siswa::class)
                                <th class="px-5 py-3 w-10">
                                    <input id="bulkSelectAll" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-nu-primary focus:ring-nu-primary/30" />
                                </th>
                            @endcan
                            <th class="px-5 py-3">{{ __('NIS') }}</th>
                            <th class="px-5 py-3">{{ __('NISN') }}</th>
                            <th class="px-5 py-3">{{ __('Nama') }}</th>
                            <th class="px-5 py-3">{{ __('Kelas') }}</th>
                            <th class="px-5 py-3">{{ __('JK') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($siswas as $s)
                            <tr class="hover:bg-gray-50/80">
                                @can('deleteAny', \App\Models\Siswa::class)
                                    <td class="px-5 py-3">
                                        <input
                                            form="bulkDeleteForm"
                                            type="checkbox"
                                            name="ids[]"
                                            value="{{ $s->id }}"
                                            class="bulkRowCheckbox h-4 w-4 rounded border-gray-300 text-nu-primary focus:ring-nu-primary/30"
                                        />
                                    </td>
                                @endcan
                                <td class="px-5 py-3 font-mono font-semibold text-gray-900">{{ $s->nis }}</td>
                                <td class="px-5 py-3 font-mono text-gray-700">{{ $s->nisn ?: '—' }}</td>
                                <td class="px-5 py-3 text-gray-900">{{ $s->nama }}</td>
                                <td class="px-5 py-3 text-gray-700">
                                    @if($s->kelas)
                                        <span class="inline-flex items-center rounded-full bg-nu-primary/10 px-2.5 py-1 text-xs font-semibold text-nu-primary ring-1 ring-nu-primary/15">
                                            {{ $s->kelas->tingkat }} {{ $s->kelas->nama }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">{{ __('Belum ditetapkan') }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-gray-700">
                                    @if($s->jenis_kelamin === 'L')
                                        {{ __('L') }}
                                    @elseif($s->jenis_kelamin === 'P')
                                        {{ __('P') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        @can('update', $s)
                                            <a href="{{ route('siswa.edit', $s) }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                                {{ __('Edit') }}
                                            </a>
                                        @endcan
                                        @can('delete', $s)
                                            <form method="POST" action="{{ route('siswa.destroy', $s) }}" onsubmit="return confirm('{{ __('Hapus siswa ini?') }}')">
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
                                <td colspan="{{ auth()->user()->can('deleteAny', \App\Models\Siswa::class) ? 7 : 6 }}" class="px-5 py-10 text-center text-sm text-gray-500">
                                    {{ __('Belum ada data siswa.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($siswas->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">
                    {{ $siswas->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

@can('deleteAny', \App\Models\Siswa::class)
    <script>
        (function () {
            const selectAll = document.getElementById('bulkSelectAll');
            const checkboxes = () => Array.from(document.querySelectorAll('.bulkRowCheckbox'));
            const countEl = document.getElementById('bulkSelectedCount');
            const btn = document.getElementById('bulkDeleteBtn');

            function update() {
                const boxes = checkboxes();
                const selected = boxes.filter(b => b.checked).length;
                countEl.textContent = String(selected);
                btn.disabled = selected === 0;
                if (selectAll) {
                    selectAll.checked = selected > 0 && selected === boxes.length;
                    selectAll.indeterminate = selected > 0 && selected < boxes.length;
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', () => {
                    checkboxes().forEach(b => b.checked = selectAll.checked);
                    update();
                });
            }

            document.addEventListener('change', (e) => {
                if (e.target && e.target.classList && e.target.classList.contains('bulkRowCheckbox')) {
                    update();
                }
            });

            update();
        })();
    </script>
@endcan

