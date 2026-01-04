<x-app-layout>

    <!-- Partner Filter (Owner Only) -->
    @if(Auth::user()->role === 'OWNER')
    <div class="mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 p-4 dark:bg-gray-800">
        <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-4">
            <label for="partner_id" class="text-sm font-medium text-gray-700 dark:text-gray-300">Filter Dashboard per Mitra:</label>
            <select name="partner_id" id="partner_id" onchange="updateDashboard(this.value)" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-64 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="">-- Semua Mitra (Global) --</option>
                @foreach($partners as $partner)
                    <option value="{{ $partner->id }}" {{ isset($filterPartnerId) && $filterPartnerId == $partner->id ? 'selected' : '' }}>
                        {{ $partner->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
    @endif

    <!-- Smart Alerts Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <!-- Vaccine Alerts -->
        @if(count($vaccineAlerts) > 0)
        <div id="alert-vaccine" class="p-4 text-sm text-cyan-800 rounded-lg bg-cyan-50 dark:bg-gray-800 dark:text-cyan-300" role="alert">
            <span class="font-medium">💉 Jadwal Vaksinasi/Obat!</span> Berikut yang perlu ditangani (7 Hari Kedepan):
            <ul id="list-vaccine" class="mt-1.5 list-disc list-inside max-h-64 overflow-y-auto">
                @foreach($vaccineAlerts as $log)
                    <li>
                        {{ $log->animal->tag_id }} - {{ $log->notes }} 
                        <span class="font-bold">({{ $log->next_due_date->format('d M') }})</span>
                    </li>
                @endforeach
            </ul>
        </div>
        @else
        <div id="alert-vaccine" class="hidden p-4 text-sm text-cyan-800 rounded-lg bg-cyan-50 dark:bg-gray-800 dark:text-cyan-300" role="alert">
             <span class="font-medium">💉 Jadwal Vaksinasi/Obat!</span> Berikut yang perlu ditangani (7 Hari Kedepan):
             <ul id="list-vaccine" class="mt-1.5 list-disc list-inside max-h-64 overflow-y-auto"></ul>
        </div>
        @endif

        <!-- Weaning Alerts (Ready to Wean) -->
        @if(count($weaningAlerts) > 0)
        <div id="alert-weaming" class="p-4 text-sm text-indigo-800 rounded-lg bg-indigo-50 dark:bg-gray-800 dark:text-indigo-300" role="alert">
            <span class="font-medium">🍼 Siap Sapih (35+ Hari)!</span> Cempe ini sudah mendekati usia sapih:
            <ul id="list-weaning" class="mt-1.5 list-disc list-inside max-h-64 overflow-y-auto">
                @foreach($weaningAlerts as $animal)
                    <li>
                         {{ $animal->tag_id }} (Usia: {{ number_format($animal->birth_date->diffInDays(now()), 0) }} hari)
                         - Lokasi: {{ $animal->location->name ?? '-' }}
                    </li>
                @endforeach
            </ul>
        </div>
        @else
        <div id="alert-weaming" class="hidden p-4 text-sm text-indigo-800 rounded-lg bg-indigo-50 dark:bg-gray-800 dark:text-indigo-300" role="alert">
            <span class="font-medium">🍼 Siap Sapih (35+ Hari)!</span> Cempe ini sudah mendekati usia sapih:
            <ul id="list-weaning" class="mt-1.5 list-disc list-inside max-h-64 overflow-y-auto"></ul>
        </div>
        @endif
    </div>

    <!-- Separation Alert (Pisah Koloni / Sapih > 60 Hari) -->
    @if(count($separationCandidates) > 0)
    <div id="alert-separation" class="mb-4 p-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-300" role="alert">
        <span class="font-medium">⚠️ Waktunya Sapih (Pisah Induk)!</span> Cempe berikut sudah berusia > 2 bulan:
        <ul id="list-separation" class="mt-1.5 list-disc list-inside max-h-64 overflow-y-auto">
            @foreach($separationCandidates as $animal)
                <li>ID: {{ $animal->tag_id }} (Usia: {{ number_format($animal->birth_date->floatDiffInMonths(now()), 1) }} bulan)</li>
            @endforeach
        </ul>
    </div>
    @else
    <div id="alert-separation" class="hidden mb-4 p-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-300" role="alert">
        <span class="font-medium">⚠️ Waktunya Sapih (Pisah Induk)!</span> Cempe berikut sudah berusia > 2 bulan:
        <ul id="list-separation" class="mt-1.5 list-disc list-inside max-h-64 overflow-y-auto"></ul>
    </div>
    @endif

    <!-- Mating Separation Alert (Pisah Pejantan) -->
    @if(count($matingSeparationCandidates) > 0)
    <div id="alert-mating" class="mb-4 p-4 text-sm text-orange-800 rounded-lg bg-orange-50 dark:bg-gray-800 dark:text-orange-300" role="alert">
        <span class="font-medium">⚠️ Waktunya Pisah Pejantan!</span> Pasangan berikut sudah disatukan > 2 bulan:
        <ul id="list-mating" class="mt-1.5 list-disc list-inside max-h-64 overflow-y-auto">
            @foreach($matingSeparationCandidates as $event)
                <li>Dam: {{ $event->dam->tag_id }} + Sire: {{ $event->sire->tag_id }} (Mulai: {{ $event->mating_date->format('d M Y') }})</li>
            @endforeach
        </ul>
    </div>
    @else
    <div id="alert-mating" class="hidden mb-4 p-4 text-sm text-orange-800 rounded-lg bg-orange-50 dark:bg-gray-800 dark:text-orange-300" role="alert">
         <span class="font-medium">⚠️ Waktunya Pisah Pejantan!</span> Pasangan berikut sudah disatukan > 2 bulan:
         <ul id="list-mating" class="mt-1.5 list-disc list-inside max-h-64 overflow-y-auto"></ul>
    </div>
    @endif

    <!-- Low Stock Alert -->
    @if(count($lowStockItems) > 0)
    <div id="alert-stock" class="mb-4 p-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300" role="alert">
        <span class="font-medium">⚠️ Peringatan Stok!</span> Item berikut hampir habis (< 10 unit):
        <ul id="list-stock" class="mt-1.5 list-disc list-inside max-h-64 overflow-y-auto">
            @foreach($lowStockItems as $item)
                <li>{{ $item->name }} (Sisa: {{ $item->current_stock }} {{ $item->unit }})</li>
            @endforeach
        </ul>
    </div>
    @else
    <div id="alert-stock" class="hidden mb-4 p-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300" role="alert">
        <span class="font-medium">⚠️ Peringatan Stok!</span> Item berikut hampir habis (< 10 unit):
         <ul id="list-stock" class="mt-1.5 list-disc list-inside max-h-64 overflow-y-auto"></ul>
    </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        <!-- Metric 1: Active Animals -->
        <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="w-full">
                <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Total Populasi Hidup</h3>
                <span id="stat-active-animals" class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">{{ $activeAnimals }} Ekor</span>
                <p class="text-xs text-gray-500 mt-1">
                    Jantan: <span id="stat-live-male">{{ $liveMale }}</span> | 
                    Betina: <span id="stat-live-female">{{ $liveFemale }}</span>
                </p>
            </div>
        </div>

        <!-- Metric 2: Avg ADG -->
        <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="w-full">
                <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Pertumbuhan (Rata-rata ADG)</h3>
                <div class="flex items-center">
                     <span id="stat-avg-adg" class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">{{ number_format($avgAdg, 3) }} kg/hari</span>
                     @if($avgAdg >= 0.15)
                        <span class="bg-green-100 text-green-800 text-xs font-medium ms-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Bagus</span>
                     @else
                        <span class="bg-red-100 text-red-800 text-xs font-medium ms-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Rendah</span>
                     @endif
                </div>
            </div>
        </div>

        <!-- Metric 3: Sales This Month -->
        <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="w-full">
                <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Penjualan (Bulan Ini)</h3>
                <span id="stat-sales-month" class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">Rp {{ number_format($salesThisMonth, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Metric 4: Net Profit -->
        <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="w-full">
                <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Net Profit (Bulan Ini)</h3>
                <span id="stat-net-profit" class="text-2xl font-bold leading-none {{ $netProfit >= 0 ? 'text-green-500' : 'text-red-500' }} sm:text-3xl">
                    Rp {{ number_format($netProfit, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        <!-- Feed Usage -->
        <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="w-full">
                <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Penggunaan Pakan (Kg)</h3>
                <span id="stat-feed-usage" class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">{{ number_format($feedUsage, 1) }}</span>
            </div>
        </div>
        <!-- Medicine Cost -->
        <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="w-full">
                <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Biaya Obat (Est)</h3>
                <span id="stat-medicine-cost" class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">Rp {{ number_format($medicineCost, 0, ',', '.') }}</span>
            </div>
        </div>
        <!-- Mortality -->
        <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="w-full">
                <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Kematian (Bulan Ini)</h3>
                <span id="stat-death-count" class="text-2xl font-bold leading-none text-red-600 sm:text-3xl dark:text-red-500">{{ $deathCount }} Ekor</span>
                <p class="text-xs text-gray-500 mt-1">
                    Jantan: <span id="stat-dead-male">{{ $deadMale }}</span> | 
                    Betina: <span id="stat-dead-female">{{ $deadFemale }}</span>
                </p>
            </div>
        </div>
        <!-- Mortality Value -->
        <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="w-full">
                <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Kerugian Aset (Kematian)</h3>
                <span id="stat-death-value" class="text-2xl font-bold leading-none text-red-600 sm:text-3xl dark:text-red-500">Rp {{ number_format($deathValue, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Charts & Tables -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mb-4">

        <!-- 1. Population Demographics & Cage (Mixed Layout) -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 p-4 dark:bg-gray-800">
            <h3 class="text-xl font-bold mb-4 dark:text-white">Demografi & Kandang</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Cage List -->
                <div>
                    <h4 class="text-sm font-semibold mb-2 text-gray-500 dark:text-gray-400">Populasi per Kandang</h4>
                    <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400 text-sm max-h-64 overflow-y-auto">
                        @foreach($populationByCage as $cage)
                            <li class="flex justify-between items-center py-1 border-b border-gray-100 dark:border-gray-700">
                                <span>{{ $cage->name }}</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $cage->animals_count }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Demographics Pie Chart -->
                <div class="h-48 relative">
                    <canvas id="demographicsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 2. Financial Summary (Bar Chart) -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 p-4 dark:bg-gray-800">
            <h3 class="text-xl font-bold mb-4 dark:text-white">Ringkasan Keuangan (6 Bulan)</h3>
            <div class="relative h-64 w-full">
                <canvas id="financialChart"></canvas>
            </div>
        </div>

        <!-- 3. Mortality Trend (Line Chart) -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 p-4 dark:bg-gray-800">
            <h3 class="text-xl font-bold mb-4 dark:text-white">Tren Kematian (6 Bulan)</h3>
            <div class="relative h-64 w-full">
                <canvas id="mortalityChart"></canvas>
            </div>
        </div>

        <!-- 4. Expense Breakdown & Conception Rate -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 p-4 dark:bg-gray-800">
            <h3 class="text-xl font-bold mb-4 dark:text-white">Performa & Biaya</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Conception Rate Text -->
                <div class="flex flex-col justify-center items-center">
                    <span class="text-gray-500 dark:text-gray-400 mb-1">Conception Rate</span>
                    <span class="text-4xl font-extrabold text-blue-600 dark:text-blue-500">{{ number_format($conceptionRate, 1) }}%</span>
                    <p class="text-xs text-gray-400 mt-2 text-center">Persentase keberhasilan kebuntingan (Excl. Pending)</p>
                </div>

                <!-- Expense Pie Chart -->
                <div class="h-48 relative">
                    <canvas id="expenseChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Biomass Trend (Aggregate Weight) -->
    <div class="mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 p-4 dark:bg-gray-800">
        <h3 class="text-xl font-bold mb-4 dark:text-white">Pertumbuhan Biomassa (Total Bobot Kg)</h3>
        <p class="text-sm text-gray-500 mb-2">Tren total berat hidup berdasarkan kategori usia & gender.</p>
        <div class="relative h-72 w-full">
            <canvas id="biomassChart"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Store chart instances globally for AJAX updates
        window.dashboardCharts = {};

        // 1. Demographics Chart (Pie)
        window.dashboardCharts.demographics = new Chart(document.getElementById('demographicsChart'), {
            type: 'pie',
            data: {
                labels: @json($demographicsLabels),
                datasets: [{
                    data: @json($demographicsData),
                    backgroundColor: ['#1C64F2', '#16BDCA', '#FDBA8C', '#E74694', '#9061F9', '#FACA15', '#31C48D'],
                    borderWidth: 0
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right', labels: { boxWidth: 10 } } } }
        });

        // 2. Financial Chart (Bar)
        window.dashboardCharts.financial = new Chart(document.getElementById('financialChart'), {
            type: 'bar',
            data: {
                labels: @json($financialLabels),
                datasets: [
                    { label: 'Pendapatan (Jual)', data: @json($financialRevenue), backgroundColor: '#31C48D' },
                    { label: 'Kerugian (Mati)', data: @json($financialLoss), backgroundColor: '#F05252' }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
        });

        // 3. Mortality Chart (Line)
        window.dashboardCharts.mortality = new Chart(document.getElementById('mortalityChart'), {
            type: 'line',
            data: {
                labels: @json($mortalityTrendLabels),
                datasets: [{
                    label: 'Jumlah Kematian',
                    data: @json($mortalityTrendData),
                    borderColor: '#F05252',
                    backgroundColor: 'rgba(240, 82, 82, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
        });

        // 4. Expense Chart (Pie)
        window.dashboardCharts.expense = new Chart(document.getElementById('expenseChart'), {
            type: 'pie',
            data: {
                labels: @json($expenseLabels),
                datasets: [{
                    data: @json($expenseData),
                    backgroundColor: ['#E3A008', '#7E3AF2', '#1C64F2'], // Yellow (Feed), Purple (Meds), Blue (Ops)
                    borderWidth: 0
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });

        // 5. Biomass Chart (Aggregate Line)
        window.dashboardCharts.biomass = new Chart(document.getElementById('biomassChart'), {
            type: 'line',
            data: {
                labels: @json($biomassLabels),
                datasets: [
                    {
                        label: 'Jantan Dewasa',
                        data: @json($biomassDataMale),
                        borderColor: '#1C64F2', // Blue
                        backgroundColor: '#1C64F2',
                        fill: false,
                        tension: 0.1
                    },
                    {
                        label: 'Betina Dewasa',
                        data: @json($biomassDataFemale),
                        borderColor: '#E74694', // Pink
                        backgroundColor: '#E74694',
                        fill: false,
                        tension: 0.1
                    },
                    {
                        label: 'Anakan (<1 th)',
                        data: @json($biomassDataKids),
                        borderColor: '#31C48D', // Green
                        backgroundColor: '#31C48D',
                        fill: false,
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Total Berat (Kg)' }
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += Math.round(context.parsed.y) + ' Kg';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // Global controller for aborting fetch
        let currentController = null;

        // --- AJAX Live Update Logic ---
        function updateDashboard(partnerId) {
            // 1. Abort previous request if exists
            if (currentController) {
                currentController.abort();
            }
            currentController = new AbortController();

            const url = `{{ route('dashboard') }}?partner_id=${partnerId}`;
            
            fetch(url, {
                signal: currentController.signal,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // 1. Update Text Stats
                updateText('stat-active-animals', data.activeAnimals + ' Ekor');
                updateText('stat-live-male', data.liveMale);
                updateText('stat-live-female', data.liveFemale);
                updateText('stat-avg-adg', formatNumber(data.avgAdg, 3) + ' kg/hari');
                updateText('stat-sales-month', formatCurrency(data.salesThisMonth));
                
                // Net Profit Color & Value
                const profitEl = document.getElementById('stat-net-profit');
                if(profitEl) {
                    profitEl.innerText = formatCurrency(data.netProfit);
                    profitEl.className = `text-2xl font-bold leading-none sm:text-3xl ${data.netProfit >= 0 ? 'text-green-500' : 'text-red-500'}`;
                }

                updateText('stat-feed-usage', formatNumber(data.feedUsage, 1));
                updateText('stat-medicine-cost', formatCurrency(data.medicineCost));
                updateText('stat-death-count', data.deathCount + ' Ekor');
                updateText('stat-dead-male', data.deadMale);
                updateText('stat-dead-female', data.deadFemale);
                updateText('stat-death-value', formatCurrency(data.deathValue));

                // 2. Update Charts
                // Demographics
                window.dashboardCharts.demographics.data.labels = data.demographicsLabels;
                window.dashboardCharts.demographics.data.datasets[0].data = data.demographicsData;
                window.dashboardCharts.demographics.update();

                // Financial
                window.dashboardCharts.financial.data.labels = data.financialLabels;
                window.dashboardCharts.financial.data.datasets[0].data = data.financialRevenue;
                window.dashboardCharts.financial.data.datasets[1].data = data.financialLoss;
                window.dashboardCharts.financial.update();

                // Mortality
                window.dashboardCharts.mortality.data.labels = data.mortalityTrendLabels;
                window.dashboardCharts.mortality.data.datasets[0].data = data.mortalityTrendData;
                window.dashboardCharts.mortality.update();

                // Expense
                window.dashboardCharts.expense.data.labels = data.expenseLabels;
                window.dashboardCharts.expense.data.datasets[0].data = data.expenseData;
                window.dashboardCharts.expense.update();

                // Biomass
                window.dashboardCharts.biomass.data.labels = data.biomassLabels;
                window.dashboardCharts.biomass.data.datasets[0].data = data.biomassDataMale;
                window.dashboardCharts.biomass.data.datasets[1].data = data.biomassDataFemale;
                window.dashboardCharts.biomass.data.datasets[2].data = data.biomassDataKids;
                window.dashboardCharts.biomass.update();

                // 3. Update Alerts (Lists)
                updateList('alert-vaccine', 'list-vaccine', data.vaccineAlerts, item => `${item.tag_id} - ${item.notes} <span class="font-bold">(${item.date})</span>`);
                updateList('alert-weaming', 'list-weaning', data.weaningAlerts, item => `${item.tag_id} (Usia: ${item.age_days} hari) - Lokasi: ${item.location}`);
                updateList('alert-separation', 'list-separation', data.separationCandidates, item => `ID: ${item.tag_id} (Usia: ${item.age_months} bulan)`);
                updateList('alert-mating', 'list-mating', data.matingSeparationCandidates, item => `Dam: ${item.dam_tag} + Sire: ${item.sire_tag} (Mulai: ${item.date})`);
                updateList('alert-stock', 'list-stock', data.lowStockItems, item => `${item.name} (Sisa: ${item.stock} ${item.unit})`);

            })
            .catch(error => console.error('Error fetching dashboard data:', error));
        }

        // Helpers
        function updateList(containerId, listId, items, formatCallback) {
            const container = document.getElementById(containerId);
            const list = document.getElementById(listId);
            
            if(!container || !list) return;

            list.innerHTML = ''; // Clear current
            if (items && items.length > 0) {
                container.classList.remove('hidden');
                items.forEach(item => {
                    const li = document.createElement('li');
                    li.innerHTML = formatCallback(item); // Using innerHTML to support bold tags
                    list.appendChild(li);
                });
            } else {
                container.classList.add('hidden');
            }
        }

        function updateText(id, value) {
            const el = document.getElementById(id);
            if (el) el.innerText = value;
        }

        function formatNumber(num, decimals = 0) {
            return parseFloat(num || 0).toLocaleString('id-ID', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
        }

        function formatCurrency(num) {
            return 'Rp ' + formatNumber(num);
        }
    </script>
</x-app-layout>
