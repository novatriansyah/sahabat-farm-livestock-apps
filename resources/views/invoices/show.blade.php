<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-4 flex items-center justify-between">
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

        <div class="bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 p-8 print-container">
            <!-- Logo for Print/View -->
            <div class="mb-8 text-center hidden print:block">
                <img src="{{ asset('img/logo.png') }}" class="h-24 mx-auto mb-2" alt="Sahabat Farm Logo">
                <h1 class="text-2xl font-bold uppercase text-gray-900">Sahabat Farm Indonesia</h1>
                <p class="text-sm text-gray-600">Jalan Raya Peternakan No. 1, Bogor, Jawa Barat</p>
                <p class="text-sm text-gray-600">Email: contact@sahabatfarm.id | HP: 0812-3456-7890</p>
            </div>

            <!-- Header Status -->
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

            <!-- Addresses -->
            <div class="grid grid-cols-2 gap-8 mb-8">
                <div>
                    <h3 class="text-gray-500 dark:text-gray-400 text-sm uppercase">Kepada:</h3>
                    <p class="font-bold text-lg dark:text-white">{{ $invoice->customer_name }}</p>
                    <p class="text-gray-600 dark:text-gray-300">{{ $invoice->customer_contact ?? '-' }}</p>
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

            <!-- Items Table -->
            <div class="relative overflow-x-auto mb-8">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Deskripsi</th>
                            <th scope="col" class="px-6 py-3 text-center">Qty</th>
                            <th scope="col" class="px-6 py-3 text-right">Harga Satuan</th>
                            <th scope="col" class="px-6 py-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-6 py-4">
                                {{ $item->description }}
                                @if($item->related_animal_id)
                                    <br><span class="text-xs text-gray-400">Tag: {{ $item->relatedAnimal->tag_id }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-bold text-gray-900 dark:text-white">
                            <td colspan="3" class="px-6 py-4 text-right">Total</td>
                            <td class="px-6 py-4 text-right text-lg">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Footer Notes & Signature -->
            <div class="flex justify-between items-end mt-12 mb-4 break-inside-avoid">
                 <div class="text-sm text-gray-500 max-w-md">
                    <p class="font-bold mb-1">Catatan :</p>
                    <p>Pembayaran dapat ditransfer ke:</p>
                    <p>BCA 1234567890 a.n Sahabat Farm</p>
                    <p class="mt-2 text-xs">Terima kasih atas bisnis Anda.</p>
                </div>
                 <div class="text-center">
                    <p class="text-sm mb-16">Hormat Kami,</p>
                    <p class="font-bold underline">Rizki Dwianda</p>
                    <p class="text-xs text-gray-500">Owner, Sahabat Farm</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Styles -->
    <style>
        @media print {
            @page {
                size: A4;
                margin: 0; /* Remove default browser margins if desired, or keep small */
            }
            body {
                margin: 0; /* Let print container handle padding */
                background-color: white !important;
                color: black !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                font-family: 'Times New Roman', Times, serif; /* Optional: More formal for print, or keep sans */
            }

            /* HIDE EVERYTHING ELSE */
            body * {
                visibility: hidden;
            }
            
            /* RESET MAIN CONTAINER */
            /* We specifically target the print container and its children to be visible */
            .print-container, .print-container * {
                visibility: visible;
            }

            /* POSITION THE PRINT CONTAINER */
            .print-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 2.5cm; /* Standard professional margin */
                border: none !important;
                box-shadow: none !important;
                background-color: white !important;
                font-size: 12pt; /* Standard readable size */
                line-height: 1.5;
            }

            /* Ensure text colors are black */
            .text-gray-500, .text-gray-600, .text-gray-700, .text-gray-900, .dark\:text-white {
                color: black !important;
            }
            
            /* Logo Sizing in Print */
            .print\:block img {
                height: 80px !important; 
                width: auto !important;
            }
        }
    </style>
</x-app-layout>
