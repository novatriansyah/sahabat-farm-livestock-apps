<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('admin.export.reconciliation.index') }}" class="text-blue-600 hover:text-blue-800">
            &larr; Kembali ke Daftar Rekonsiliasi
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h1 class="text-2xl font-bold mb-4">Hasil Rekonsiliasi Data</h1>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="font-semibold">Batch ID:</span>
                <code class="bg-gray-100 px-2 py-1 rounded">{{ $batchId }}</code>
            </div>
            <div>
                <span class="font-semibold">Timestamp:</span>
                {{ $timestamp }}
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-5 gap-4 mb-6">
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-green-700">{{ $summary['SAME'] ?? 0 }}</div>
            <div class="text-sm text-green-600">Sama</div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-red-700">{{ $summary['CONFLICT'] ?? 0 }}</div>
            <div class="text-sm text-red-600">Konflik</div>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-blue-700">{{ $summary['WEB_ONLY'] ?? 0 }}</div>
            <div class="text-sm text-blue-600">Hanya di Web</div>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-yellow-700">{{ $summary['EXCEL_ONLY'] ?? 0 }}</div>
            <div class="text-sm text-yellow-600">Hanya di File</div>
        </div>
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-gray-700">{{ $summary['UNCERTAIN'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Tidak Pasti</div>
        </div>
    </div>

    <!-- Read-only notice -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
        <p class="text-blue-700 text-sm">
            <strong>Baca Saja:</strong> Hasil rekonsiliasi ini bersifat informatif.
            Pemilik harus menerapkan perubahan secara manual di sistem.
        </p>
    </div>

    <!-- Diff Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold">Detail Perubahan</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Status</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Tag ID</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Field</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Nilai Lama</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Nilai Baru</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Keyakinan</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($results as $log)
                    <tr class="{{ $log->status === 'CONFLICT' ? 'bg-red-50' : ($log->status === 'EXCEL_ONLY' ? 'bg-yellow-50' : ($log->status === 'WEB_ONLY' ? 'bg-blue-50' : '')) }}">
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $log->status === 'SAME' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $log->status === 'CONFLICT' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $log->status === 'WEB_ONLY' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $log->status === 'EXCEL_ONLY' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $log->status === 'UNCERTAIN' ? 'bg-gray-100 text-gray-800' : '' }}">
                                {{ $log->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-mono">{{ $log->tag_id ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $log->field ?? '-' }}</td>
                        <td class="px-4 py-3 {{ $log->status === 'CONFLICT' ? 'line-through text-red-600' : '' }}">{{ $log->old_value ?? '-' }}</td>
                        <td class="px-4 py-3 {{ $log->status === 'CONFLICT' ? 'font-semibold text-green-600' : '' }}">{{ $log->new_value ?? '-' }}</td>
                        <td class="px-4 py-3">{{ number_format($log->confidence * 100, 0) }}%</td>
                        <td class="px-4 py-3 text-gray-500">{{ $log->notes ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            Tidak ada data rekonsiliasi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>