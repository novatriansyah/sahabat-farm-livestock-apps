<x-app-layout>
    <div class="max-w-4xl mx-auto">
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

            <!-- Header Info -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Customer Info -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Pelanggan</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kontak / HP</label>
                    <input type="text" name="customer_contact" value="{{ old('customer_contact') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                </div>

                <!-- Invoice Details -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipe Invoice</label>
                    <select name="type" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="PROFORMA" {{ old('type') == 'PROFORMA' ? 'selected' : '' }}>Proforma (Penawaran)</option>
                        <option value="COMMERCIAL" {{ old('type') == 'COMMERCIAL' ? 'selected' : '' }}>Commercial (Penjualan Langsung)</option>
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

            <!-- Items -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-6">
                <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Daftar Barang / Hewan</h3>
                
                <div id="items-container" class="space-y-4">
                    <!-- Default Row -->
                    <div class="grid grid-cols-12 gap-3 item-row">
                        <div class="col-span-4">
                             <input type="text" name="items[0][description]" placeholder="Deskripsi Item / Hewan" required class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:text-white">
                        </div>
                         <div class="col-span-2">
                             <input type="number" name="items[0][quantity]" value="1" min="1" placeholder="Qty" required class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div class="col-span-3">
                             <input type="number" name="items[0][unit_price]" placeholder="Harga Satuan (Rp)" required class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div class="col-span-3">
                            <!-- Optional Animal Selector -->
                            <select name="items[0][related_animal_id]" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:text-white">
                                <option value="">-- Pilih Hewan (Opsional) --</option>
                                @foreach($animals as $animal)
                                <option value="{{ $animal->id }}">{{ $animal->tag_id }} - {{ $animal->breed->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                
                <button type="button" id="add-item-btn" class="mt-4 text-sm text-blue-600 dark:text-blue-400 hover:underline">+ Tambah Baris</button>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('invoices.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">Batal</a>
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Simpan Invoice</button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.getElementById('add-item-btn').addEventListener('click', function() {
            const container = document.getElementById('items-container');
            const index = container.children.length;
            
            const row = document.createElement('div');
            row.className = 'grid grid-cols-12 gap-3 item-row border-t pt-4 border-gray-200 dark:border-gray-700 mt-4';
            row.innerHTML = `
                <div class="col-span-4">
                     <input type="text" name="items[${index}][description]" placeholder="Deskripsi Item / Hewan" required class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:text-white">
                </div>
                 <div class="col-span-2">
                     <input type="number" name="items[${index}][quantity]" value="1" min="1" placeholder="Qty" required class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:text-white">
                </div>
                <div class="col-span-3">
                     <input type="number" name="items[${index}][unit_price]" placeholder="Harga Satuan (Rp)" required class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:text-white">
                </div>
                 <div class="col-span-3">
                    <select name="items[${index}][related_animal_id]" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:text-white">
                        <option value="">-- Pilih Hewan (Opsional) --</option>
                        @foreach($animals as $animal)
                        <option value="{{ $animal->id }}">{{ $animal->tag_id }} - {{ $animal->breed->name }}</option>
                        @endforeach
                    </select>
                </div>
            `;
            container.appendChild(row);
        });
    </script>
    @endpush
</x-app-layout>
