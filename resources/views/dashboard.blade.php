<x-app-layout>

    <!-- Partner Filter (Owner Only) -->
    @if(Auth::user()->role === 'PEMILIK')
        <div class="mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 p-4 dark:bg-gray-800">
            <form method="GET" action="{{ route('dashboard') }}"
                class="flex flex-col md:flex-row items-start md:items-center gap-4">
                <label for="partner_id" class="text-sm font-medium text-gray-700 dark:text-gray-300">Filter Dashboard per
                    Mitra:</label>
                <select name="partner_id" id="partner_id" onchange="updateDashboard(this.value)"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-64 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
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

    <!-- Alerts have been moved to the notification bell system -->

    @if($dashboardSettings['metrics']->is_visible ?? true)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <!-- Metric 1: Active Animals -->
            <div
                class="flex items-center justify-between p-4 bg-white border border-gray-100 rounded-2xl shadow-soft dark:border-gray-700 sm:p-6 dark:bg-gray-800 animate-slide-up">
                <div class="w-full">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Total
                            Populasi Hidup</h3>
                        <div class="p-2 bg-emerald-50 rounded-lg dark:bg-emerald-900/30">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <span id="stat-active-animals"
                        class="text-2xl font-extrabold leading-none text-gray-900 sm:text-3xl dark:text-white">{{ $activeAnimals }}
                        Ekor</span>
                    <p class="text-xs text-gray-500 mt-2 font-medium">
                        Jantan: <span id="stat-live-male"
                            class="text-gray-900 dark:text-gray-300 font-bold">{{ $liveMale }}</span> |
                        Betina: <span id="stat-live-female"
                            class="text-gray-900 dark:text-gray-300 font-bold">{{ $liveFemale }}</span>
                    </p>
                </div>
            </div>

            <!-- Metric 2: Avg ADG -->
            <div
                class="flex items-center justify-between p-4 bg-white border border-gray-100 rounded-2xl shadow-soft dark:border-gray-700 sm:p-6 dark:bg-gray-800 animate-slide-up delay-100">
                <div class="w-full">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Pertumbuhan
                            (Avg ADG)</h3>
                        <div class="p-2 bg-blue-50 rounded-lg dark:bg-blue-900/30">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span id="stat-avg-adg"
                            class="text-2xl font-extrabold leading-none text-gray-900 sm:text-3xl dark:text-white">{{ number_format($avgAdg, 3) }}
                            kg/hari</span>
                        @if($avgAdg >= 0.15)
                            <span
                                class="bg-emerald-100 text-emerald-800 text-[10px] uppercase font-bold ms-2 px-2 py-0.5 rounded-full dark:bg-emerald-900 dark:text-emerald-300">Bagus</span>
                        @else
                            <span
                                class="bg-red-100 text-red-800 text-[10px] uppercase font-bold ms-2 px-2 py-0.5 rounded-full dark:bg-red-900 dark:text-red-300">Rendah</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Metric 3: Sales This Month -->
            <div
                class="flex items-center justify-between p-4 bg-white border border-gray-100 rounded-2xl shadow-soft dark:border-gray-700 sm:p-6 dark:bg-gray-800 animate-slide-up delay-200">
                <div class="w-full">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Penjualan
                            Bulan Ini</h3>
                        <div class="p-2 bg-purple-50 rounded-lg dark:bg-purple-900/30">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <span id="stat-sales-month"
                        class="text-2xl font-extrabold leading-none text-gray-900 sm:text-3xl dark:text-white">Rp
                        {{ number_format($salesThisMonth, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Metric 4: Laba Bersih -->
            <div
                class="flex items-center justify-between p-4 bg-white border border-gray-100 rounded-2xl shadow-soft dark:border-gray-700 sm:p-6 dark:bg-gray-800 animate-slide-up delay-300">
                <div class="w-full">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Laba Bersih
                            Bulan Ini</h3>
                        <div
                            class="p-2 {{ $netProfit >= 0 ? 'bg-emerald-50' : 'bg-red-50' }} rounded-lg dark:bg-opacity-20">
                            <svg class="w-5 h-5 {{ $netProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }}" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <span id="stat-net-profit"
                        class="text-2xl font-extrabold leading-none {{ $netProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }} sm:text-3xl">
                        Rp {{ number_format($netProfit, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    @endif

    <!-- Additional Stats -->
    @if($dashboardSettings['additional_stats']->is_visible ?? true)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-4 animate-slide-up delay-400">
            <!-- Rata-rata HPP -->
            <div
                class="p-4 bg-white/60 backdrop-blur-sm border border-gray-100 rounded-2xl shadow-soft dark:bg-gray-800/60 dark:border-gray-700">
                <h3 class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Avg HPP
                </h3>
                <span id="stat-avg-hpp" class="text-lg font-extrabold text-gray-900 dark:text-white">Rp
                    {{ number_format($avgHpp, 0, ',', '.') }}</span>
            </div>
            <!-- Feed Usage -->
            <div
                class="p-4 bg-white/60 backdrop-blur-sm border border-gray-100 rounded-2xl shadow-soft dark:bg-gray-800/60 dark:border-gray-700">
                <h3 class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Pakan (Kg)
                </h3>
                <span id="stat-feed-usage"
                    class="text-lg font-extrabold text-gray-900 dark:text-white">{{ number_format($feedUsage, 1) }}</span>
            </div>
            <!-- Medicine Cost -->
            <div
                class="p-4 bg-white/60 backdrop-blur-sm border border-gray-100 rounded-2xl shadow-soft dark:bg-gray-800/60 dark:border-gray-700">
                <h3 class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Biaya Obat
                </h3>
                <span id="stat-medicine-cost" class="text-lg font-extrabold text-gray-900 dark:text-white">Rp
                    {{ number_format($medicineCost, 0, ',', '.') }}</span>
            </div>
            <!-- Mortality -->
            <div
                class="p-4 bg-white/60 backdrop-blur-sm border border-gray-100 rounded-2xl shadow-soft dark:bg-gray-800/60 dark:border-gray-700">
                <h3 class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Kematian
                </h3>
                <span id="stat-death-count" class="text-lg font-extrabold text-red-600 truncate block">{{ $deathCount }}
                    Ekor</span>
            </div>
            <!-- Mortality Value -->
            <div
                class="p-4 bg-white/60 backdrop-blur-sm border border-gray-100 rounded-2xl shadow-soft dark:bg-gray-800/60 dark:border-gray-700">
                <h3 class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Estimasi
                    Rugi</h3>
                <span id="stat-death-value" class="text-lg font-extrabold text-red-600 truncate block">Rp
                    {{ number_format($deathValue, 0, ',', '.') }}</span>
            </div>
        </div>
    @endif

    <!-- Charts & Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- 1. Population Demographics & Cage (Mixed Layout) -->
        @if($dashboardSettings['charts_demographics']->is_visible ?? true)
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 p-4 dark:bg-gray-800">
                <h3 class="text-xl font-bold mb-4 dark:text-white">Demografi & Kandang</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Cage List -->
                    <div>
                        <h4 class="text-sm font-semibold mb-2 text-gray-500 dark:text-gray-400">Populasi per Kandang</h4>
                        <ul
                            class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400 text-sm max-h-64 overflow-y-auto">
                            @foreach($populationByCage as $cage)
                                <li
                                    class="flex justify-between items-center py-1 border-b border-gray-100 dark:border-gray-700">
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
        @endif

        <!-- 2. Financial Summary (Bar Chart) -->
        @if($dashboardSettings['charts_financial']->is_visible ?? true)
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 p-4 dark:bg-gray-800">
                <h3 class="text-xl font-bold mb-4 dark:text-white">Ringkasan Keuangan (6 Bulan)</h3>
                <div class="relative h-64 w-full">
                    <canvas id="financialChart"></canvas>
                </div>
            </div>
        @endif

        <!-- 3. Mortality Trend (Line Chart) -->
        @if($dashboardSettings['charts_mortality']->is_visible ?? true)
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 p-4 dark:bg-gray-800">
                <h3 class="text-xl font-bold mb-4 dark:text-white">Tren Kematian (6 Bulan)</h3>
                <div class="relative h-64 w-full">
                    <canvas id="mortalityChart"></canvas>
                </div>
            </div>
        @endif

        <!-- 4. Expense Breakdown & Conception Rate -->
        @if($dashboardSettings['charts_performance']->is_visible ?? true)
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 p-4 dark:bg-gray-800">
                <h3 class="text-xl font-bold mb-4 dark:text-white">Performa & Biaya</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Tingkat Kebuntingan Text -->
                    <div class="flex flex-col justify-center items-center">
                        <span class="text-gray-500 dark:text-gray-400 mb-1">Tingkat Kebuntingan</span>
                        <span
                            class="text-4xl font-extrabold text-blue-600 dark:text-blue-500">{{ number_format($conceptionRate, 1) }}%</span>
                        <p class="text-xs text-gray-400 mt-2 text-center">Persentase keberhasilan kebuntingan (Excl.
                            Pending)</p>
                    </div>

                    <!-- Expense Pie Chart -->
                    <div class="h-48 relative">
                        <canvas id="expenseChart"></canvas>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- 5. Biomass Trend (Aggregate Weight) -->
    @if($dashboardSettings['charts_biomass']->is_visible ?? true)
        <div
            class="mt-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 p-4 dark:bg-gray-800">
            <h3 class="text-xl font-bold mb-4 dark:text-white">Pertumbuhan Biomassa (Total Bobot Kg)</h3>
            <p class="text-sm text-gray-500 mb-2">Tren total berat hidup berdasarkan kategori usia & gender.</p>
            <div class="relative h-72 w-full">
                <canvas id="biomassChart"></canvas>
            </div>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Store chart instances globally for AJAX updates
        window.dashboardCharts = {};

        // 1. Demographics Chart (Pie)
        if (document.getElementById('demographicsChart')) {
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
        }

        // 2. Financial Chart (Bar)
        if (document.getElementById('financialChart')) {
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
        }

        // 3. Mortality Chart (Line)
        if (document.getElementById('mortalityChart')) {
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
        }

        // 4. Expense Chart (Pie)
        if (document.getElementById('expenseChart')) {
            window.dashboardCharts.expense = new Chart(document.getElementById('expenseChart'), {
                type: 'pie',
                data: {
                    labels: @json($expenseLabels),
                    datasets: [{
                        data: @json($expenseData),
                        backgroundColor: ['#E3A008', '#7E3AF2', '#1C64F2', '#16BDCA', '#FDBA8C', '#E74694', '#9061F9', '#FACA15', '#31C48D'],
                        borderWidth: 0
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
            });
        }

        // 5. Biomass Chart (Aggregate Line)
        if (document.getElementById('biomassChart')) {
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
                                label: function (context) {
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
        }

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
                    updateText('stat-avg-hpp', formatCurrency(data.avgHpp));
                    updateText('stat-sales-month', formatCurrency(data.salesThisMonth));

                    // Net Profit Color & Value
                    const profitEl = document.getElementById('stat-net-profit');
                    if (profitEl) {
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
                    if (window.dashboardCharts.demographics) {
                        window.dashboardCharts.demographics.data.labels = data.demographicsLabels;
                        window.dashboardCharts.demographics.data.datasets[0].data = data.demographicsData;
                        window.dashboardCharts.demographics.update();
                    }

                    // Financial
                    if (window.dashboardCharts.financial) {
                        window.dashboardCharts.financial.data.labels = data.financialLabels;
                        window.dashboardCharts.financial.data.datasets[0].data = data.financialRevenue;
                        window.dashboardCharts.financial.data.datasets[1].data = data.financialLoss;
                        window.dashboardCharts.financial.update();
                    }

                    // Mortality
                    if (window.dashboardCharts.mortality) {
                        window.dashboardCharts.mortality.data.labels = data.mortalityTrendLabels;
                        window.dashboardCharts.mortality.data.datasets[0].data = data.mortalityTrendData;
                        window.dashboardCharts.mortality.update();
                    }

                    // Expense
                    if (window.dashboardCharts.expense) {
                        window.dashboardCharts.expense.data.labels = data.expenseLabels;
                        window.dashboardCharts.expense.data.datasets[0].data = data.expenseData;
                        window.dashboardCharts.expense.update();
                    }

                    // Biomass
                    if (window.dashboardCharts.biomass) {
                        window.dashboardCharts.biomass.data.labels = data.biomassLabels;
                        window.dashboardCharts.biomass.data.datasets[0].data = data.biomassDataMale;
                        window.dashboardCharts.biomass.data.datasets[1].data = data.biomassDataFemale;
                        window.dashboardCharts.biomass.data.datasets[2].data = data.biomassDataKids;
                        window.dashboardCharts.biomass.update();
                    }

                    // 3. Update Alerts (Lists)
                    updateList('alert-vaccine', 'list-vaccine', data.vaccineAlerts, item => `${item.tag_id} - ${item.notes} <span class="font-bold">(${item.date})</span>`);
                    updateList('alert-weaming', 'list-weaning', data.weaningAlerts, item => `${item.tag_id} (Usia: ${item.age_days} hari) - Lokasi: ${item.location}`);
                    updateList('alert-separation', 'list-separation', data.separationCandidates, item => `ID: ${item.tag_id} (Usia: ${item.age_months} bulan)`);
                    updateList('alert-mating', 'list-mating', data.matingSeparationCandidates, item => `Induk: ${item.dam_tag} + Pejantan: ${item.sire_tag} (Mulai: ${item.date})`);
                    updateList('alert-stock', 'list-stock', data.lowStockItems, item => `${item.name} (Sisa: ${item.stock} ${item.unit})`);

                })
                .catch(error => console.error('Error fetching dashboard data:', error));
        }

        // Helpers
        function updateList(containerId, listId, items, formatCallback) {
            const container = document.getElementById(containerId);
            const list = document.getElementById(listId);

            if (!container || !list) return;

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