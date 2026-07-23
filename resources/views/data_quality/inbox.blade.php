<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Data Quality Inbox & Issue Correction Queue</h2>
            <div class="flex gap-2">
                <a href="{{ route('data-quality-inbox.index', ['status' => 'OPEN']) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg {{ request('status', 'OPEN') === 'OPEN' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">OPEN</a>
                <a href="{{ route('data-quality-inbox.index', ['status' => 'RESOLVED']) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg {{ request('status') === 'RESOLVED' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">RESOLVED</a>
                <a href="{{ route('data-quality-inbox.index', ['status' => 'ALL']) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg {{ request('status') === 'ALL' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">SEMUA</a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">Tag ID / Animal</th>
                                <th class="px-4 py-3">Missing Field</th>
                                <th class="px-4 py-3">Severity</th>
                                <th class="px-4 py-3">Status Isu</th>
                                <th class="px-4 py-3">Dibuat</th>
                                <th class="px-4 py-3 text-right">Aksi Koreksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($issues as $issue)
                                <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">
                                        {{ $issue->animal_id ?? 'System Baseline' }}
                                    </td>
                                    <td class="px-4 py-3 font-mono text-xs text-amber-600 dark:text-amber-400">
                                        {{ $issue->field_name }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-0.5 text-xs font-bold rounded-full {{ $issue->severity === 'CRITICAL' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $issue->severity ?? 'MEDIUM' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $issue->status === 'RESOLVED' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $issue->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-xs">
                                        {{ $issue->created_at ? $issue->created_at->format('d M Y H:i') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if($issue->status !== 'RESOLVED' && $issue->animal_id)
                                            <a href="{{ route('animals.edit', $issue->animal_id) }}" class="inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                                                Lengkapi / Lengkapi Data
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400">Selesai</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">Tidak ada isu kualitas data pada kategori ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $issues->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
