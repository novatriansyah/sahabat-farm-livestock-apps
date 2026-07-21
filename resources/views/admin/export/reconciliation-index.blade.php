<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h1 class="text-2xl font-bold mb-4">Riwayat Rekonsiliasi Data</h1>
        <p class="text-gray-600 text-sm mb-4">
            Berikut adalah daftar semua batch rekonsiliasi yang pernah dilakukan.
            Setiap batch mencatat perbandingan antara data di sistem dengan file Excel yang diunggah.
        </p>
    </div>

    @if($batches->isEmpty())
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
        <p class="text-yellow-700">Belum ada rekonsiliasi yang dilakukan.</p>
    </div>
    @else
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Batch ID</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Tanggal</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-500">Sama</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-500">Konflik</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-500">Hanya Web</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-500">Hanya File</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-500">Tidak Pasti</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-500">Total</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($batches as $batch)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs">
                            <code>{{ $batch->batch_id }}</code>
                        </td>
                        <td class="px-4 py-3">{{ $batch->created_at }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $batch->same_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $batch->conflict_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $batch->web_only_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                {{ $batch->excel_only_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $batch->uncertain_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center font-semibold">{{ $batch->total }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.export.reconciliation.show', $batch->batch_id) }}"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>