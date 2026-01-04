<x-app-layout>
    <div class="max-w-5xl mx-auto">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Buat Invoice Baru</h2>

        <form action="{{ route('invoices.store') }}" method="POST">
            @csrf

            <!-- Alerts -->
            @if ($errors->any())
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <span class="font-medium">Gagal menyimpan!</span> Periksa input Anda:
                    <ul class="mt-1.5 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <span class="font-medium">Error!</span> {{ session('error') }}
                </div>
            @endif

            <!-- 1. Customer Info -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-6 border-l-4 border-blue-500">
                <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Informasi Pelanggan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Pelanggan / Perusahaan</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kontak / HP</label>
                        <input type="text" name="customer_contact" value="{{ old('customer_contact') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat Lengkap</label>
                        <textarea name="customer_address" rows="2" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">{{ old('customer_address') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- 2. Invoice Details -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-6 border-l-4 border-green-500">
                <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Detail Dokumen</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipe Invoice</label>
                        <select name="type" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                            <option value="PROFORMA" {{ old('type') == 'PROFORMA' ? 'selected' : '' }}>Proforma (Penawaran)</option>
                            <option value="COMMERCIAL" {{ old('type') == 'COMMERCIAL' ? 'selected' : '' }}>Commercial (Penjualan)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Terbit</label>
                        <input type="date" name="issued_date" value="{{ old('issued_date', date('Y-m-d')) }}" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jatuh Tempo (Opsional)</label>
                        <input type="date" name="due_date" value="{{ old('due_date') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">PPN (%) (Opsional)</label>
                        <input type="number" step="0.01" min="0" name="tax_rate" value="{{ old('tax_rate') }}" placeholder="Contoh: 11" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pajak Tambahan (%) (Opsional)</label>
                        <input type="number" step="0.01" min="0" name="additional_tax_rate" value="{{ old('additional_tax_rate') }}" placeholder="Contoh: 1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Uang Muka / DP (Rp) (Opsional)</label>
                    <input type="number" name="down_payment" min="0" value="{{ old('down_payment') }}" placeholder="0" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                </div>
            </div>

            <!-- 3. Items -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-6">
                <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Daftar Barang / Hewan</h3>
                
                <!-- <div id="items-header" class="grid grid-cols-12 gap-3 mb-2 font-medium text-sm text-gray-500 dark:text-gray-400">
                    <div class="col-span-4">Pilih Hewan (Disarankan)</div>
                    <div class="col-span-4">Deskripsi</div>
                    <div class="col-span-1">Qty</div>
                    <div class="col-span-3">Harga Satuan (Rp)</div>
                </div> -->

                <div id="items-container" class="space-y-4">
                    <!-- Default Row -> handled by JS init -->
                </div>
                
                <button type="button" id="add-item-btn" class="mt-4 text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Baris
                </button>
            </div>

            <div class="flex justify-end gap-3 pb-10">
                <a href="{{ route('invoices.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">Batal</a>
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Simpan Invoice</button>
            </div>
        </form>
    </div>
    
    <style>
        /* Remove arrow/spinners from number inputs */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>

    <!-- Hidden Template for JS -->
    <template id="animal-options">
        <option value="" disabled selected>-- Pilih Hewan --</option>
        @foreach($animals as $animal)
        @php
            $genderLabel = $animal->gender === 'MALE' ? 'Jantan' : 'Betina';
            $description = $animal->breed->name . ' ' . $genderLabel;
        @endphp
        <!-- Store data attrs for auto-fill -->
        <option value="{{ $animal->id }}" 
            data-desc="{{ $description }}"
            data-weight="{{ $animal->latestWeightLog ? $animal->latestWeightLog->weight_kg : 0 }}"
            >Tag: {{ $animal->tag_id }} | {{ $animal->breed->name }} | {{ $animal->gender }}</option>
        @endforeach
    </template>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let rowCount = 0;
            const container = document.getElementById('items-container');
            const animalOptionsTemplate = document.getElementById('animal-options');
            const animalOptions = animalOptionsTemplate ? animalOptionsTemplate.innerHTML : '';
            const addItemBtn = document.getElementById('add-item-btn');

            if (!container || !addItemBtn) {
                console.error('Invoice items container or Add Button not found!');
                return;
            }

            function addRow() {
                const index = rowCount++;
                const row = document.createElement('div');
                row.className = 'grid grid-cols-12 gap-2 item-row border-b border-gray-100 pb-4 mb-2 items-end';
                row.innerHTML = `
                    <div class="col-span-3">
                        <label class="block mb-1 text-xs font-medium text-gray-700">Item/Hewan</label>
                        <select name="items[${index}][related_animal_id]" required class="animal-select w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2 dark:bg-gray-700 dark:text-white">
                            ${animalOptions}
                        </select>
                    </div>
                    <div class="col-span-3">
                        <label class="block mb-1 text-xs font-medium text-gray-700">Deskripsi</label>
                        <input type="text" id="desc-${index}" name="items[${index}][description]" placeholder="Auto-fill" required readonly class="w-full bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg p-2 cursor-not-allowed">
                    </div>
                    <div class="col-span-1">
                        <label class="block mb-1 text-xs font-medium text-gray-700">Qty</label>
                        <input type="number" id="qty-${index}" name="items[${index}][quantity]" value="1" min="1" required readonly class="w-full bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg p-2 cursor-not-allowed">
                    </div>
                    
                    <!-- NEW: Weight & Price/Kg Fields -->
                    <div class="col-span-1">
                        <label class="block mb-1 text-xs font-medium text-gray-700">Berat (Kg)</label>
                        <input type="number" step="0.01" id="weight-${index}" placeholder="0" class="weight-input w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2" readonly>
                    </div>
                    <div class="col-span-2">
                         <label class="block mb-1 text-xs font-medium text-gray-700">Harga/Kg</label>
                         <input type="number" id="price-kg-${index}" min="0" placeholder="0" class="price-kg-input w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2 dark:bg-gray-700 dark:text-white">
                    </div>

                    <div class="col-span-2">
                        <label class="block mb-1 text-xs font-medium text-gray-700">Total (Rp)</label>
                        <!-- Actual field sent to backend as unit_price -->
                        <input type="hidden" id="raw-total-${index}" name="items[${index}][unit_price]">
                        <!-- Display field formatted as Rupiah -->
                        <input type="text" id="display-total-${index}" placeholder="Rp 0" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2 font-medium" readonly>
                    </div>
                `;
                container.appendChild(row);

                // Attach Listeners
                const select = row.querySelector('.animal-select');
                const priceKgInput = row.querySelector('.price-kg-input');
                const rawTotalInput = row.querySelector(`#raw-total-${index}`); // Hidden
                const displayTotalInput = row.querySelector(`#display-total-${index}`); // Shown

                // Helper to format currency
                const formatRupiah = (number) => {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
                };

                // 1. Animal Select Change
                select.addEventListener('change', function() {
                    handleAnimalChange(this, index);
                });

                // 2. Price/Kg Input Change
                priceKgInput.addEventListener('input', function() {
                    const weight = parseFloat(document.getElementById(`weight-${index}`).value) || 0;
                    const priceKg = parseFloat(this.value) || 0;
                    
                    if (select.value) {
                        const total = weight * priceKg;
                        rawTotalInput.value = total;
                        displayTotalInput.value = formatRupiah(total);
                    }
                });
                
                // Allow Total editing for manual items (if needed in future, currently locked logic for visual consistency)
                // For manual items, we might want to un-readonly displayTotalInput and parse it back? 
                // For now, let's assume manual items also use unit price logic or maybe we enable typing in Total for manual items later.
                // Given the request is specific to "Total format rupiah", usually best to keep it auto-calc for now.
                
                // Logic for Manual Items to edit Total directly requires parsing "Rp 50.000" back to "50000".
                // Let's implement simple manual override if needed:
                displayTotalInput.addEventListener('input', function(e) {
                     if (!select.value) { // Only if manual
                        // Strip currency symbols to get raw number
                        const raw = this.value.replace(/[^0-9]/g, '');
                        rawTotalInput.value = raw;
                        // Optional: Auto-format as you type (complex), or just let them type raw numbers?
                        // Let's just let them type raw and format on blur
                     }
                });
                
                displayTotalInput.addEventListener('blur', function() {
                     if (!select.value && rawTotalInput.value) {
                         this.value = formatRupiah(rawTotalInput.value);
                     }
                });
            }

            function handleAnimalChange(select, index) {
                const descInput = document.getElementById(`desc-${index}`);
                const weightInput = document.getElementById(`weight-${index}`);
                const priceKgInput = document.getElementById(`price-kg-${index}`);
                const rawTotalInput = document.getElementById(`raw-total-${index}`);
                const displayTotalInput = document.getElementById(`display-total-${index}`);
                
                const selectedOption = select.options[select.selectedIndex];
                
                if (select.value) {
                    // --- ANIMAL SELECTED ---
                    const desc = selectedOption.getAttribute('data-desc');
                    const weight = parseFloat(selectedOption.getAttribute('data-weight')) || 0;

                    // Auto-fill
                    descInput.value = desc;
                    weightInput.value = weight;

                    // Reset Price/Kg to allow new input
                    priceKgInput.value = ''; 
                    rawTotalInput.value = '';
                    displayTotalInput.value = '';

                    // Focus on Price/Kg for convenience
                    priceKgInput.focus();
                }
            };



            // Initialize
            addRow();

            // Button Event
            addItemBtn.addEventListener('click', (e) => {
                e.preventDefault(); 
                addRow();
            });
        });
    </script>
    @endpush
</x-app-layout>
