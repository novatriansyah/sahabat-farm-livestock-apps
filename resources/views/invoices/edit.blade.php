<x-app-layout>
    <div class="max-w-5xl mx-auto">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Edit Invoice: {{ $invoice->invoice_number }}</h2>

        <form action="{{ route('invoices.update', $invoice->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- 1. Customer Info -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-6 border-l-4 border-blue-500">
                <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Informasi Pelanggan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Pelanggan / Perusahaan</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name', $invoice->customer_name) }}" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kontak / HP</label>
                        <input type="text" name="customer_contact" value="{{ old('customer_contact', $invoice->customer_contact) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat Lengkap</label>
                        <textarea name="customer_address" rows="2" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">{{ old('customer_address', $invoice->customer_address) }}</textarea>
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
                            <option value="PROFORMA" {{ old('type', $invoice->type) == 'PROFORMA' ? 'selected' : '' }}>Proforma (Penawaran)</option>
                            <option value="KOMERSIAL" {{ old('type', $invoice->type) == 'KOMERSIAL' ? 'selected' : '' }}>Commercial (Penjualan)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Terbit</label>
                        <input type="date" name="issued_date" value="{{ old('issued_date', $invoice->issued_date->format('Y-m-d')) }}" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jatuh Tempo (Opsional)</label>
                        <input type="date" name="due_date" value="{{ old('due_date', $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">PPN (%) (Opsional)</label>
                        <input type="number" step="0.01" min="0" name="tax_rate" value="{{ old('tax_rate', $invoice->tax_rate) }}" placeholder="Contoh: 11" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pajak Tambahan (%) (Opsional)</label>
                        <input type="number" step="0.01" min="0" name="additional_tax_rate" value="{{ old('additional_tax_rate', $invoice->additional_tax_rate) }}" placeholder="Contoh: 1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Uang Muka / DP (Rp) (Opsional)</label>
                    <input type="number" name="down_payment" min="0" value="{{ old('down_payment', $invoice->down_payment) }}" placeholder="0" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                </div>
            </div>

            <!-- 3. Items -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-6">
                <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Daftar Barang / Hewan</h3>
                
                <div id="items-container" class="space-y-4">
                    <!-- Loaded via JS -->
                </div>
                
                <button type="button" id="add-item-btn" class="mt-4 text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Baris
                </button>
            </div>

            <div class="flex justify-end gap-3 pb-10">
                <a href="{{ route('invoices.show', $invoice->id) }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">Batal</a>
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Simpan Perubahan</button>
            </div>
        </form>
    </div>
    
    <style>
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>

    <template id="animal-options">
        <option value="">-- Pilih Hewan (Opsional) --</option>
        @foreach($animals as $animal)
        @php
            $genderLabel = $animal->gender === 'JANTAN' ? 'Jantan' : 'Betina';
            $description = $animal->breed->name . ' ' . $genderLabel;
        @endphp
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

            function addRow(data = null) {
                const index = rowCount++;
                const row = document.createElement('div');
                row.className = 'grid grid-cols-12 gap-2 item-row border-b border-gray-100 pb-4 mb-2 items-end';
                
                const selectedAnimalId = data ? data.related_animal_id : '';
                const description = data ? data.description : '';
                const quantity = data ? data.quantity : 1;
                const unitPrice = data ? data.unit_price : 0;
                
                row.innerHTML = `
                    <div class="col-span-3">
                        <label class="block mb-1 text-xs font-medium text-gray-700">Item/Hewan</label>
                        <select name="items[${index}][related_animal_id]" class="animal-select w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2 dark:bg-gray-700 dark:text-white">
                            ${animalOptions}
                        </select>
                    </div>
                    <div class="col-span-3">
                        <label class="block mb-1 text-xs font-medium text-gray-700">Deskripsi</label>
                        <input type="text" id="desc-${index}" name="items[${index}][description]" value="${description}" required class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2">
                    </div>
                    <div class="col-span-1">
                        <label class="block mb-1 text-xs font-medium text-gray-700">Qty</label>
                        <input type="number" id="qty-${index}" name="items[${index}][quantity]" value="${quantity}" min="1" required class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2">
                    </div>
                    <div class="col-span-3">
                        <label class="block mb-1 text-xs font-medium text-gray-700">Harga Satuan (Rp)</label>
                        <input type="number" name="items[${index}][unit_price]" value="${unitPrice}" min="0" required class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2">
                    </div>
                    <div class="col-span-1 text-right">
                        <button type="button" class="remove-row-btn text-red-600 hover:text-red-800 transition-colors p-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                `;
                container.appendChild(row);

                if (selectedAnimalId) {
                    row.querySelector('.animal-select').value = selectedAnimalId;
                }

                row.querySelector('.animal-select').addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (this.value) {
                         document.getElementById(`desc-${index}`).value = selectedOption.getAttribute('data-desc');
                         document.getElementById(`qty-${index}`).value = 1;
                    }
                });

                row.querySelector('.remove-row-btn').addEventListener('click', () => {
                    row.remove();
                });
            }

            // Load Existing Items
            @foreach($invoice->items as $item)
                addRow({
                    related_animal_id: '{{ $item->related_animal_id }}',
                    description: '{{ $item->description }}',
                    quantity: {{ $item->quantity }},
                    unit_price: {{ $item->unit_price }}
                });
            @endforeach

            if (rowCount === 0) addRow();

            addItemBtn.addEventListener('click', () => addRow());
        });
    </script>
    @endpush
</x-app-layout>
