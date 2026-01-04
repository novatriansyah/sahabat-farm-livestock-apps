<x-app-layout>
    <div class="max-w-5xl mx-auto">
        <div class="mb-4 flex items-center justify-between no-print">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Detail Invoice: {{ $invoice->invoice_number }}
            </h2>
            <div class="flex gap-2">
                @if($invoice->status == 'ISSUED' || $invoice->status == 'DRAFT')
                    @if($invoice->type == 'PROFORMA')
                        <!-- Convert Action -->
                        <form action="{{ route('invoices.convert', $invoice->id) }}" method="POST" onsubmit="return confirm('Konversi ke Commercial Invoice? Ini akan mengubah nomor invoice dan status menjadi Issued.');">
                            @csrf
                            <button type="submit" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800">
                                Konversi ke Commercial
                            </button>
                        </form>
                    @elseif($invoice->type == 'COMMERCIAL' && $invoice->status == 'ISSUED')
                        <!-- Payment Action -->
                        <form action="{{ route('invoices.paid', $invoice->id) }}" method="POST" onsubmit="return confirm('Tandai sebagai LUNAS?');">
                            @csrf
                            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                Tandai LUNAS
                            </button>
                        </form>
                    @endif
                @endif
                
                <!-- Print Button -->
                <button onclick="window.print()" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700 no-print">
                    <svg class="w-4 h-4 inline-block mr-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M8 3a2 2 0 0 0-2 2v3h12V5a2 2 0 0 0-2-2H8Zm-2 7a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h1v2a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-2h1a2 2 0 0 0 2-2v-5a2 2 0 0 0-2-2H6Zm9 7a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2v-2h6v2Z" clip-rule="evenodd"/>
                    </svg>
                    Cetak
                </button>
                
                <a href="{{ route('invoices.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700 no-print">Kembali</a>
            </div>
        </div>

        <div class="bg-white border text-black border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 p-8 print-container">
            <!-- Logo for Print/View -->
            <div class="mb-8 text-center hidden print:block">
                <img src="{{ asset('img/logo.png') }}" class="h-24 mx-auto mb-2" alt="Sahabat Farm Logo">
                <h1 class="text-2xl font-bold uppercase">Sahabat Farm Indonesia</h1>
                <p class="text-sm">Jalan Raya Peternakan No. 1, Bogor, Jawa Barat</p>
                <p class="text-sm">Email: contact@sahabatfarm.id | HP: 0812-3456-7890</p>
            </div>

            <!-- Header Status - RESTORED ORIGINAL STYLE -->
            <div class="flex justify-between border-b pb-4 mb-4">
                <div>
                     @if($invoice->type == 'PROFORMA')
                        <span class="bg-purple-100 text-purple-800 text-sm font-medium px-3 py-1 rounded dark:bg-purple-900 dark:text-purple-300">PROFORMA INVOICE</span>
                    @else
                        <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded dark:bg-blue-900 dark:text-blue-300">COMMERCIAL INVOICE</span>
                    @endif
                </div>
                <div>
                    Status: 
                     @if($invoice->status == 'DRAFT')
                        <span class="font-bold text-gray-500">DRAFT</span>
                    @elseif($invoice->status == 'ISSUED')
                        <span class="font-bold text-yellow-600">TERBIT / UNPAID</span>
                    @elseif($invoice->status == 'PAID')
                        <span class="font-bold text-green-600">LUNAS</span>
                    @else
                        <span class="font-bold text-red-600">BATAL</span>
                    @endif
                </div>
            </div>

            <!-- Addresses - RESTORED ORIGINAL STYLE + NEW ADDRESS FIELD -->
            <div class="grid grid-cols-2 gap-8 mb-8">
                <div>
                    <h3 class="text-gray-500 dark:text-gray-400 text-sm uppercase">Kepada:</h3>
                    <p class="font-bold text-lg dark:text-white">{{ $invoice->customer_name }}</p>
                    <p class="text-gray-600 dark:text-gray-300">{{ $invoice->customer_contact ?? '-' }}</p>
                    @if($invoice->customer_address)
                        <p class="text-gray-600 dark:text-gray-300 mt-1 whitespace-pre-line">{{ $invoice->customer_address }}</p>
                    @endif
                </div>
                <div class="text-right">
                    <div class="mb-2">
                        <span class="text-gray-500 dark:text-gray-400 text-sm block">Tanggal:</span>
                        <span class="font-medium dark:text-white">{{ $invoice->issued_date->format('d M Y') }}</span>
                    </div>
                     <div>
                        <span class="text-gray-500 dark:text-gray-400 text-sm block">Invoice #:</span>
                        <span class="font-medium dark:text-white">{{ $invoice->invoice_number }}</span>
                    </div>
                    @if($invoice->due_date)
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 text-sm block">Jatuh Tempo:</span>
                        <span class="font-medium dark:text-white">{{ $invoice->due_date->format('d M Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Items Table - NEW DATA COLUMNS (Eartag, Weight, etc) with ORIGINAL STYLING -->
            <div class="relative overflow-x-auto mb-8">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">No</th>
                            <th scope="col" class="px-6 py-3">Tag/Item</th>
                            <th scope="col" class="px-6 py-3 text-center">Berat (Kg)</th>
                            <th scope="col" class="px-6 py-3">Jenis/Deskripsi</th>
                            <th scope="col" class="px-6 py-3 text-right">Harga/Kg</th>
                            <th scope="col" class="px-6 py-3 text-right">Harga/Ekor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $index => $item)
                        @php
                            $animal = $item->relatedAnimal;
                            $weight = $animal?->latestWeightLog?->weight_kg ?? 0;
                            $pricePerKg = ($weight > 0 && $item->unit_price > 0) ? ($item->unit_price / $weight) : 0;
                        @endphp
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-6 py-4">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                {{ $animal ? $animal->tag_id : '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                {{ $weight > 0 ? number_format($weight, 2, ',', '.') : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($animal)
                                    {{ $animal->breed?->name ?? 'Unknown' }} - {{ $animal->gender }}
                                @else
                                    {{ $item->description }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                {{ $pricePerKg > 0 ? 'Rp ' . number_format($pricePerKg, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-gray-900 dark:text-white">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <!-- Total Weight -->
                        @php
                            $totalWeight = $invoice->items->sum(fn($item) => $item->relatedAnimal?->latestWeightLog?->weight_kg ?? 0);
                        @endphp
                        @if($totalWeight > 0)
                        <tr class="font-bold text-gray-900 dark:text-white border-b border-gray-200">
                            <td colspan="2" class="px-6 py-2 text-right">Total Berat</td>
                            <td class="px-2 py-2 text-center">{{ number_format($totalWeight, 2, ',', '.') }} kg</td>
                            <td colspan="4"></td>
                        </tr>
                        @endif

                        <!-- Subtotal -->
                        <tr class="font-bold text-gray-900 dark:text-white">
                            <td colspan="5" class="px-6 py-4 text-right">Subtotal</td>
                            <td class="px-6 py-4 text-right">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        <!-- Tax -->
                        @if($invoice->tax_rate > 0)
                        <tr class="text-sm text-gray-600 dark:text-gray-300">
                            <td colspan="5" class="px-6 py-1 text-right">PPN ({{ $invoice->tax_rate }}%)</td>
                            <td class="px-6 py-1 text-right">Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                         @if($invoice->additional_tax_rate > 0)
                        <tr class="text-sm text-gray-600 dark:text-gray-300">
                            <td colspan="5" class="px-6 py-1 text-right">Pajak Lain ({{ $invoice->additional_tax_rate }}%)</td>
                            <td class="px-6 py-1 text-right">Rp {{ number_format($invoice->total_amount - $invoice->subtotal - $invoice->tax_amount, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <!-- Grand Total -->
                        <!-- Grand Total -->
                        <tr class="font-bold text-lg text-gray-900 dark:text-white border-t border-gray-300 dark:border-gray-600">
                            <td colspan="5" class="px-6 py-4 text-right">Total</td>
                            <td class="px-6 py-4 text-right">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                        </tr>
                        @if($invoice->down_payment > 0)
                        <tr class="text-gray-900 dark:text-white">
                            <td colspan="5" class="px-6 py-2 text-right">Uang Muka (DP)</td>
                            <td class="px-6 py-2 text-right">- Rp {{ number_format($invoice->down_payment, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="font-bold text-lg text-gray-900 dark:text-white border-t border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
                            <td colspan="5" class="px-6 py-4 text-right">Sisa Tagihan</td>
                            <td class="px-6 py-4 text-right">Rp {{ number_format($invoice->total_amount - $invoice->down_payment, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                    </tfoot>
                </table>
            </div>

            <!-- Footer Notes & Signature -->
            <div class="mt-12 break-inside-avoid">
                <div class="flex justify-between items-start mb-12">
                     <div class="text-sm text-gray-500 max-w-md">
                        <p class="font-bold mb-1">Catatan :</p>
                        <p>Pembayaran dapat ditransfer ke:</p>
                        <p class="font-bold">BCA : 515-015-7171 a.n Sahabat Farm</p>
                        <p class="mt-2 text-xs">Terima kasih atas kepercayaan Anda.</p>
                    </div>
                </div>

                <!-- Signatures -->
                <div class="flex justify-between items-end px-8 mb-4">
                    <div class="text-center">
                        <p class="mb-16 font-medium text-sm">Penerima</p>
                        <p class="font-bold border-b border-gray-400 pb-1 min-w-[150px]">{{ $invoice->customer_name ?? '......................' }}</p>
                    </div>
                    <div class="text-center">
                        <p class="mb-16 font-medium text-sm">Hormat Kami,</p>
                        <p class="font-bold border-b border-gray-400 pb-1 min-w-[150px]">Rizki Dwianda</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            @page {
                size: A4;
                margin: 1cm; /* Set reasonable margins at page level */
            }
            
            /* Hide everything by default */
            body {
                visibility: hidden;
                overflow: hidden;
                background: white;
            }

            /* Unhide the print container */
            .print-container, .print-container * {
                visibility: visible;
            }

            /* Position and Sizing */
            .print-container {
                position: absolute;
                top: 0;
                left: 0;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
                background: white !important;
            }

            /* Prevent Table Overflow */
            .overflow-x-auto {
                overflow: visible !important;
            }
            
            /* Compact Columns */
            table {
                width: 100% !important;
                table-layout: fixed; /* Inspect this if columns get too squished */
            }
            th, td {
                padding: 4px 2px !important; /* Reduce padding */
                font-size: 11px !important;  /* Reduce font size */
                word-wrap: break-word;
            }
            th.w-10 { width: 5% !important; }
            th.w-16 { width: 10% !important; }
            th.w-32 { width: 15% !important; }

            /* Ensure text contrast */
            .text-gray-500, .text-gray-600 { color: #374151 !important; }
            .text-gray-900, .dark\:text-white { color: #000 !important; }
            
            /* Hide UI elements */
            nav, header, footer, .no-print {
                display: none !important;
            }
        }
    </style>
</x-app-layout>
