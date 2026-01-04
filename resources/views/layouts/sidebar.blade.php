<aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidebar">
   <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
      <ul class="space-y-2 font-medium">

         <!-- Dashboard Logic -->
         @if(Auth::user()->role == 'PARTNER')
         <li>
            <a href="{{ route('partner.dashboard') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
               <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                  <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"/>
                  <path d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z"/>
               </svg>
               <span class="ms-3">Beranda (Partner)</span>
            </a>
         </li>
         @elseif(Auth::user()->role == 'OWNER' || Auth::user()->role == 'BREEDER')
         <li>
            <a href="{{ route('dashboard') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
               <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                  <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"/>
                  <path d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z"/>
               </svg>
               <span class="ms-3">Beranda (Dashboard)</span>
            </a>
         </li>
         @endif

         <!-- Data Ternak (Single) -->
         @if(in_array(Auth::user()->role, ['OWNER', 'BREEDER', 'PARTNER']))
         <li>
            <a href="{{ route('animals.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
               <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                  <path d="M14 2a3.963 3.963 0 0 0-1.4.267 6.439 6.439 0 0 1-1.331 6.638A4 4 0 1 0 14 2Zm1 9h-1.264A6.957 6.957 0 0 1 15 15v2a2.97 2.97 0 0 1-.184 1H19a1 1 0 0 0 1-1v-1a5.006 5.006 0 0 0-5-5ZM6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z"/>
               </svg>
               <span class="flex-1 ms-3 whitespace-nowrap">Data Ternak</span>
            </a>
         </li>
         @endif

        <!-- Reports Group (Dropdown) -->
        @if(in_array(Auth::user()->role, ['OWNER', 'BREEDER', 'PARTNER']))
        <li x-data="{ open: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700" aria-controls="dropdown-reports" :aria-expanded="open">
                <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M5 2a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-5H2a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h3V2Z"/>
                </svg>
                <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Laporan (Reports)</span>
                <svg :class="{'rotate-180': open}" class="w-3 h-3 transition-transform" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                </svg>
            </button>
            <ul x-show="open" class="py-2 space-y-2" id="dropdown-reports">
                <li>
                    <a href="{{ route('reports.index') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Kelahiran & Kematian</a>
                </li>
                <li>
                    <a href="{{ route('reports.sales') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Penjualan</a>
                </li>
                <li>
                    <a href="{{ route('reports.stock') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Stok & Populasi</a>
                </li>
                <li>
                    <a href="{{ route('reports.partners') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Laporan Mitra</a>
                </li>
                <li>
                    <a href="{{ route('reports.operational') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Laporan Operasional</a>
                </li>
                <li>
                    <a href="{{ route('reports.performance') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Performa (ADG)</a>
                </li>
                <li>
                    <a href="{{ route('reports.reproduction') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Reproduksi</a>
                </li>
                <li>
                    <a href="{{ route('reports.audit') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Audit (Mortalitas)</a>
                </li>
            </ul>
        </li>
        @endif

        <!-- Finance & Inventory Group (Dropdown) -->
        @if(in_array(Auth::user()->role, ['OWNER', 'BREEDER']))
        <li x-data="{ open: {{ (request()->routeIs('inventory.*') || request()->routeIs('invoices.*')) ? 'true' : 'false' }} }">
             <button @click="open = !open" type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                   <path d="M17 5.923A1 1 0 0 0 16 5h-3V4a4 4 0 1 0-8 0v1H2a1 1 0 0 0-1 .923L.166 16.003V16a1 1 0 0 0 .935.997h15.798a1 1 0 0 0 .935-.997V16l-.834-10.077ZM7 4a2 2 0 1 1 4 0v1H7V4Z"/>
                </svg>
                <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Gudang & Keuangan</span>
                <svg :class="{'rotate-180': open}" class="w-3 h-3 transition-transform" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                   <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                </svg>
             </button>
             <ul x-show="open" class="py-2 space-y-2">
                 <li>
                     <a href="{{ route('inventory.index') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Gudang & Pakan</a>
                 </li>
                 <li>
                     <a href="{{ route('invoices.index') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Invoices</a>
                 </li>
             </ul>
         </li>
         @endif

         <!-- Admin Group (Dropdown - Owner Only) -->
         @if(Auth::user()->role == 'OWNER')
         <li x-data="{ open: {{ (request()->routeIs('users.*') || request()->routeIs('partners.*') || request()->routeIs('masters.*')) ? 'true' : 'false' }} }">
             <button @click="open = !open" type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                   <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm0 5a3 3 0 1 1-3 3 3 3 0 0 1 3-3Zm0 13a7 7 0 0 1-5-6.623c.277-.37.587-.714.923-1.018a7.032 7.032 0 0 1 8.154 0c.336.304.646.648.923 1.018A7 7 0 0 1 10 18Z"/>
                </svg>
                <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Admin Area</span>
                <svg :class="{'rotate-180': open}" class="w-3 h-3 transition-transform" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                   <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                </svg>
             </button>
             <ul x-show="open" class="py-2 space-y-2">
                 <li>
                     <a href="{{ route('masters.index') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Pengaturan Farm</a>
                 </li>
                 <li>
                     <a href="{{ route('partners.index') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Mitra (Partners)</a>
                 </li>
                 <li>
                     <a href="{{ route('users.index') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Manajemen User</a>
                 </li>
             </ul>
         </li>
         @endif

         <!-- Operator Routes -->
         @if(Auth::user()->role == 'STAFF' || Auth::user()->role == 'BREEDER' || Auth::user()->role == 'OWNER')
         <li>
            <a href="{{ route('scan.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
               <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h6v6H4V4Zm10 10h6v6h-6v-6Zm0-10h6v6h-6V4ZM4 14h6v6H4v-6Z"/>
               </svg>
               <span class="ms-3">Scan QR</span>
            </a>
         </li>
         <!-- Only STAFF (Operator) needs pure feeding mode, but others can too if needed. Let's keep it inclusive or strict?
              Requirement: "breeder and staff cannot manage user... breeder can go to dashboard..."
              It implies Breeder is Managerial.
              Let's allow everyone to Scan.
         -->
         @if(Auth::user()->role == 'STAFF')
         <li>
            <a href="{{ route('operator.inventory.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
               <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                  <path d="M17 5.923A1 1 0 0 0 16 5h-3V4a4 4 0 1 0-8 0v1H2a1 1 0 0 0-1 .923L.166 16.003V16a1 1 0 0 0 .935.997h15.798a1 1 0 0 0 .935-.997V16l-.834-10.077ZM7 4a2 2 0 1 1 4 0v1H7V4Z"/>
               </svg>
               <span class="ms-3">Feeding / Usage</span>
            </a>
         </li>
         @endif
         @endif
      </ul>
   </div>
</aside>
