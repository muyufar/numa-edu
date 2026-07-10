@props(['title', 'columns' => [], 'rows' => []])

<section class="rounded-2xl border border-gray-100/80 bg-white shadow-sm ring-1 ring-black/5">
    <div class="border-b border-gray-100 bg-gray-50 px-5 py-3">
        <h3 class="text-sm font-bold uppercase tracking-wide text-gray-800">{{ $title }}</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-white text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-5 py-3">{{ __('No') }}</th>
                    @foreach ($columns as $column)
                        <th class="px-5 py-3">{{ $column }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($rows as $index => $row)
                    <tr class="hover:bg-gray-50/80">
                        <td class="px-5 py-3 text-gray-500">{{ $index + 1 }}</td>
                        @foreach ($row as $cell)
                            <td class="px-5 py-3 text-gray-800">{{ $cell }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + 1 }}" class="px-5 py-8 text-center text-sm text-gray-500">
                            {{ __('Data tidak ada') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
