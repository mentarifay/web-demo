<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pertamina Gas - Dashboard Penyaluran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #a4c5da;
        }
        .pertamina-gradient { 
            background: linear-gradient(135deg, #D71920 0%, #8B0000 100%); 
        }
        .card-hover { 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
        }
        .card-hover:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 20px 40px rgba(215, 25, 32, 0.15); 
        }
        .stat-card { 
            background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
            border-left: 4px solid #D71920;
        }

        .chart-type-btn {
            padding: 0.5rem 1.2rem;
            border-radius: 0.75rem;
            border: 2px solid #e5e7eb;
            background: white;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            color: #6b7280;
        }
        .chart-type-btn:hover { 
            border-color: #D71920; 
            color: #D71920; 
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(215, 25, 32, 0.15);
        }
        .chart-type-btn.active { 
            background: linear-gradient(135deg, #D71920 0%, #8B0000 100%); 
            color: white; 
            border-color: #D71920;
            box-shadow: 0 4px 12px rgba(215, 25, 32, 0.3);
        }

        .trend-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            gap: 0.25rem;
        }
        .trend-up { background: #dcfce7; color: #166534; }
        .trend-down { background: #fee2e2; color: #991b1b; }
        .trend-stable { background: #e0e7ff; color: #3730a3; }
        
        .anomaly-badge {
            animation: pulse-anomaly 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse-anomaly {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #D71920;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .filter-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.9rem;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 500;
            color: #991b1b;
        }

        .section-header {
            border-left: 4px solid #D71920;
            padding-left: 1rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Header -->
    <header class="pertamina-gradient text-white shadow-2xl">
        <div class="container mx-auto px-6 py-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-white rounded-xl p-2.5 flex items-center justify-center shadow-lg">
                        @if(file_exists(public_path('images/logo3.png')))
                            <img src="{{ asset('images/logo3.png') }}" alt="Pertamina Gas" class="h-11 w-auto object-contain">
                        @else
                            <span class="text-red-600 font-bold text-sm px-2">PERTAMINA GAS</span>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight">Pertamina Gas</h1>
                        <p class="text-red-100 text-sm font-medium">Dashboard Penyaluran Gas 2020-2025</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-red-100 font-semibold">{{ date('d F Y') }}</p>
                    <p class="text-xs text-red-200">Real-time Monitoring</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">

        <!-- KPI Cards (3) - TIDAK TERFILTER -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="stat-card rounded-2xl p-6 shadow-lg card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-xs font-bold uppercase tracking-wider mb-1">Total Volume Penyaluran</p>
                        <h3 class="text-3xl font-extrabold text-gray-900">{{ number_format($totalVolume, 2) }}</h3>
                        <p class="text-xs text-gray-500 mt-1 font-medium">MMSCFD</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-red-100 to-red-200 rounded-2xl flex items-center justify-center shadow-md">
                        <i class="fas fa-chart-line text-red-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="stat-card rounded-2xl p-6 shadow-lg card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-xs font-bold uppercase tracking-wider mb-1">Total Records</p>
                        <h3 class="text-3xl font-extrabold text-gray-900">{{ number_format($totalRecords) }}</h3>
                        <p class="text-xs text-gray-500 mt-1 font-medium">Data Points</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-green-100 to-green-200 rounded-2xl flex items-center justify-center shadow-md">
                        <i class="fas fa-database text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="stat-card rounded-2xl p-6 shadow-lg card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-xs font-bold uppercase tracking-wider mb-1">Average Volume</p>
                        <h3 class="text-3xl font-extrabold text-gray-900">{{ number_format($avgVolume, 2) }}</h3>
                        <p class="text-xs text-gray-500 mt-1 font-medium">MMSCFD</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-100 to-purple-200 rounded-2xl flex items-center justify-center shadow-md">
                        <i class="fas fa-chart-bar text-purple-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
            <form id="filterForm" method="GET" action="{{ route('dashboard') }}" class="space-y-5">
                <div class="section-header">
                    <h2 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-filter text-red-600 mr-2"></i>
                        Filter & Pencarian
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Filter data untuk analisis mendalam</p>
                </div>

                <!-- Active Filters Display -->
                <div id="activeFilters" class="flex flex-wrap gap-2 mb-4"></div>

                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <!-- Shipper -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-building text-red-500 mr-1"></i> Shipper
                        </label>
                        <select name="shipper" id="filterShipper" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium">
                            <option value="">Semua Shipper</option>
                            @foreach($shippers as $shipper)
                                <option value="{{ $shipper }}" {{ request('shipper') == $shipper ? 'selected' : '' }}>{{ $shipper }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tahun Dari -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-calendar text-red-500 mr-1"></i> Tahun Dari
                        </label>
                        <select name="tahun_dari" id="filterTahunDari" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium">
                            <option value="">Dari</option>
                            @foreach($tahuns as $tahun)
                                <option value="{{ $tahun }}" {{ request('tahun_dari') == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tahun Sampai -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-calendar text-red-500 mr-1"></i> Tahun Ke
                        </label>
                        <select name="tahun_sampai" id="filterTahunSampai" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium">
                            <option value="">Ke</option>
                            @foreach($tahuns as $tahun)
                                <option value="{{ $tahun }}" {{ request('tahun_sampai') == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Bulan -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-calendar-day text-red-500 mr-1"></i> Bulan
                        </label>
                        <select name="bulan" id="filterBulan" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium">
                            <option value="">Semua Bulan</option>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-end md:col-span-2 gap-3">
                        <button type="submit" class="flex-1 pertamina-gradient text-white px-6 py-3 rounded-xl font-bold hover:opacity-90 transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i class="fas fa-search mr-2"></i> Cari Data
                        </button>
                        <button type="button" onclick="resetForm()" class="px-4 py-3 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition">
                            <i class="fas fa-redo"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

            <!-- Main Chart (2 kolom) -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div class="section-header border-0 p-0 m-0">
                        <h2 class="text-2xl font-bold text-gray-900">
                            <i class="fas fa-chart-area text-red-600 mr-2"></i>
                            Trend Penyaluran Gas
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Visualisasi data historis</p>
                    </div>
                    <!-- Toggle Line / Bar -->
                    <div class="flex gap-2">
                        <button class="chart-type-btn active" id="btnLine" onclick="switchChart('line')">
                            <i class="fas fa-chart-line mr-1"></i> Line
                        </button>
                        <button class="chart-type-btn" id="btnBar" onclick="switchChart('bar')">
                            <i class="fas fa-chart-bar mr-1"></i> Bar
                        </button>
                    </div>
                </div>
                <div id="gasChart" style="min-height: 350px;"></div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Volume Tertinggi & Terendah - FILTERED -->
                <div class="grid grid-cols-1 gap-4">
                    <div class="bg-gradient-to-br from-emerald-500 via-green-500 to-teal-500 rounded-2xl p-6 text-white shadow-lg card-hover">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-bold uppercase tracking-wider opacity-95">Volume Tertinggi</p>
                            <i class="fas fa-arrow-trend-up text-2xl opacity-80"></i>
                        </div>
                        <h3 class="text-4xl font-extrabold mb-2">{{ number_format($volumeTertinggi->daily_average_mmscfd ?? 0, 2) }}</h3>
                        <p class="text-xs opacity-90 font-medium">MMSCFD</p>
                        <div class="mt-3 pt-3 border-t border-white border-opacity-30">
                            <p class="text-xs opacity-85">
                                <i class="fas fa-industry mr-1"></i>{{ $volumeTertinggi->shipper ?? '-' }}
                            </p>
                            <p class="text-xs opacity-85 mt-1">
                                <i class="fas fa-calendar mr-1"></i>{{ !empty($volumeTertinggi->bulan) ? date('M', mktime(0,0,0,$volumeTertinggi->bulan,1)) : '-' }} {{ $volumeTertinggi->tahun ?? '-' }}
                            </p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-rose-500 via-red-500 to-pink-500 rounded-2xl p-6 text-white shadow-lg card-hover">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-bold uppercase tracking-wider opacity-95">Volume Terendah</p>
                            <i class="fas fa-arrow-trend-down text-2xl opacity-80"></i>
                        </div>
                        <h3 class="text-4xl font-extrabold mb-2">{{ number_format($volumeTerendah->daily_average_mmscfd ?? 0, 2) }}</h3>
                        <p class="text-xs opacity-90 font-medium">MMSCFD</p>
                        <div class="mt-3 pt-3 border-t border-white border-opacity-30">
                            <p class="text-xs opacity-85">
                                <i class="fas fa-industry mr-1"></i>{{ $volumeTerendah->shipper ?? '-' }}
                            </p>
                            <p class="text-xs opacity-85 mt-1">
                                <i class="fas fa-calendar mr-1"></i>{{ !empty($volumeTerendah->bulan) ? date('M', mktime(0,0,0,$volumeTerendah->bulan,1)) : '-' }} {{ $volumeTerendah->tahun ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Top 5 Shipper - FILTERED -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                    <div class="section-header border-0 p-0 m-0 mb-4">
                        <h2 class="text-lg font-bold text-gray-900">
                            <i class="fas fa-trophy text-amber-500 mr-2"></i>
                            Top Shipper
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">Berdasarkan filter aktif</p>
                    </div>
                    <div id="topChart" class="min-h-[220px]"></div>
                    <div id="topLoading" class="flex justify-center items-center py-12">
                        <div class="loading-spinner"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trend Analysis Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100" id="trendAnalysisSection" style="display: none;">
            <div class="section-header">
                <h2 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-chart-line text-red-600 mr-2"></i>
                    Analisis Trend Penyaluran
                </h2>
                <p class="text-sm text-gray-500 mt-1">Deteksi perubahan dan anomali volume gas</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-building text-red-500 mr-1"></i> Pilih Shipper untuk Analisis
                </label>
                <select id="trendShipperSelect" class="w-full md:w-1/3 px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition font-medium">
                    <option value="">-- Pilih Shipper --</option>
                    @foreach($shippers as $shipper)
                        <option value="{{ $shipper }}">{{ $shipper }}</option>
                    @endforeach
                </select>
            </div>

            <div id="trendResults" class="mt-6"></div>
            <div id="trendLoading" class="flex justify-center items-center py-12" style="display: none;">
                <div class="loading-spinner"></div>
            </div>
        </div>

        <!-- Data Table - FILTERED -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <div class="section-header border-0 p-0 m-0">
                    <h2 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-table text-red-600 mr-2"></i>
                        Data Penyaluran Gas
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Data berdasarkan filter yang dipilih</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-100 to-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Shipper</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tahun</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Bulan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Periode</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Daily Average (MMSCFD)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($data as $index => $item)
                        <tr class="hover:bg-red-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">{{ $data->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-gray-900">{{ $item->shipper }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-semibold">{{ $item->tahun }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">{{ date('F', mktime(0, 0, 0, $item->bulan, 1)) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1.5 inline-flex text-xs font-bold rounded-full bg-gradient-to-r from-amber-100 to-yellow-100 text-amber-800 shadow-sm">{{ $item->periode }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm font-extrabold text-gray-900 bg-gray-100 px-3 py-1.5 rounded-lg">{{ number_format($item->daily_average_mmscfd, 2) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                                    <p class="text-xl font-bold text-gray-500 mb-2">Tidak ada data ditemukan</p>
                                    <p class="text-sm text-gray-400">Coba ubah filter pencarian Anda</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($data->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                {{ $data->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-gray-800 to-gray-900 text-white mt-16 py-8 shadow-2xl">
        <div class="container mx-auto px-6 text-center">
            <p class="text-sm font-semibold">&copy; 2025 Pertamina Gas</p>
            <p class="text-xs text-gray-400 mt-2">Developed for PKL Program</p>
        </div>
    </footer>

    <script>
        let currentChartType = 'line';
        let currentChart = null;
        let currentLabels = [];
        let currentSeries = [];
        let topChart = null;

        function resetForm() {
            window.location.href = "{{ route('dashboard') }}";
        }

        // Display active filters
        function updateActiveFilters() {
            const filters = [];
            const form = document.getElementById('filterForm');
            
            const shipper = form.querySelector('[name=shipper]').value;
            const tahunDari = form.querySelector('[name=tahun_dari]').value;
            const tahunSampai = form.querySelector('[name=tahun_sampai]').value;
            const bulan = form.querySelector('[name=bulan]').value;
            
            if (shipper) filters.push({label: 'Shipper', value: shipper});
            if (tahunDari) filters.push({label: 'Tahun Dari', value: tahunDari});
            if (tahunSampai) filters.push({label: 'Tahun Ke', value: tahunSampai});
            if (bulan) filters.push({label: 'Bulan', value: new Date(2000, bulan - 1).toLocaleString('id-ID', {month: 'long'})});
            
            const container = document.getElementById('activeFilters');
            if (filters.length === 0) {
                container.innerHTML = '<span class="text-sm text-gray-400 italic">Tidak ada filter aktif</span>';
            } else {
                container.innerHTML = filters.map(f => 
                    `<span class="filter-chip">
                        <i class="fas fa-filter"></i>
                        <span>${f.label}: <strong>${f.value}</strong></span>
                    </span>`
                ).join('');
            }
        }

        // Get filter params
        function getFilterParams() {
            const form = document.getElementById('filterForm');
            const params = new URLSearchParams();
            params.append('shipper', form.querySelector('[name=shipper]').value);
            params.append('tahun_dari', form.querySelector('[name=tahun_dari]').value);
            params.append('tahun_sampai', form.querySelector('[name=tahun_sampai]').value);
            params.append('bulan', form.querySelector('[name=bulan]').value);
            return params.toString();
        }

        // ============ MAIN CHART (Trend) - MULTI SHIPPER ============
        function loadMainChart() {
                    const params = getFilterParams();
                    fetch("{{ route('chart.data') }}?" + params)
                        .then(res => res.json())
                        .then(data => {
                            // Sekarang data udah format { labels: [], series: [] }
                            if (!data || !data.labels || data.labels.length === 0) {
                                document.getElementById('gasChart').innerHTML = '<div class="flex flex-col items-center justify-center py-16"><i class="fas fa-chart-line text-6xl text-gray-300 mb-4"></i><p class="text-center text-gray-500 font-semibold">Tidak ada data untuk ditampilkan</p></div>';
                                return;
                            }
                            
                            currentLabels = data.labels;
                            currentSeries = data.series;
                            console.log('LABELS:', JSON.stringify(currentLabels));
                            console.log('SERIES:', JSON.stringify(currentSeries));
                            renderChart(currentChartType);
                        })
                        .catch(err => {
                            console.error(err);
                            document.getElementById('gasChart').innerHTML = '<p class="text-center text-red-500 py-8 font-semibold">Error loading chart</p>';
                        });
                }

                function renderChart(type) {
                    if (currentChart) {
                        currentChart.destroy();
                        document.getElementById('gasChart').innerHTML = '';
                    }

                    if (!currentSeries || currentSeries.length === 0) {
                        document.getElementById('gasChart').innerHTML = '<div class="flex flex-col items-center justify-center py-16"><i class="fas fa-chart-line text-6xl text-gray-300 mb-4"></i><p class="text-center text-gray-500 font-semibold">Tidak ada data untuk ditampilkan</p></div>';
                        return;
                    }

                    const colors = ['#D71920', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316', '#6366f1', '#84cc16'];

                    const options = {
                        series: currentSeries,
                        chart: {
                            type: type,
                            height: 600,
                            toolbar: { show: true },
                            animations: { enabled: true, easing: 'easeinout', speed: 800 },
                            fontFamily: 'Plus Jakarta Sans, sans-serif'
                        },
                        colors: colors,
                        stroke: type === 'line' ? { curve: 'smooth', width: 2.5 } : { width: 0 },
                        fill: {
                            type: 'solid',
                            opacity: type === 'line' ? 1 : 0.9
                        },
                        markers: type === 'line' ? { 
                            size: 5, 
                            hover: { size: 6, strokeColors: '#fff', strokeWidth: 2 } 
                        } : {},
                        xaxis: {
                            categories: currentLabels,
                            labels: { 
                                style: { fontSize: '11px', fontWeight: 600 }, 
                                rotate: currentLabels.length > 15 ? -35 : 0
                            }
                        },
                        yaxis: { 
                            title: { text: 'Volume (MMSCFD)', style: { fontWeight: 700 } }, 
                            labels: { formatter: v => v.toFixed(1) } 
                        },
                        tooltip: { 
                            theme: 'dark',
                            shared: false,      // cuma 1 shipper yang ditampil
                            intersect: true,
                            followCursor: true,
                            x: {
                                show: true
                            },   // harus tepat di titik
                            y: {
                                formatter: function(val) {
                                    return val.toFixed(2) + " MMSCFD";
                                }
                            }
                        },
                        legend: {
                            position: 'bottom',
                            horizontalAlign: 'center',
                            fontSize: '12px',
                            fontWeight: 600,
                            markers: { width: 12, height: 12, radius: 3 }
                        },
                        grid: { borderColor: '#f1f1f1', strokeDashArray: 3 },
                        plotOptions: type === 'bar' ? {
                            bar: { 
                                borderRadius: 6,
                                columnWidth: currentSeries.length > 5 ? '80%' : '60%'
                            }
                        } : {},
                        dataLabels: { enabled: false }
                    };

                    currentChart = new ApexCharts(document.querySelector("#gasChart"), options);
                    currentChart.render();
                }

        function switchChart(type) {
            currentChartType = type;
            document.getElementById('btnLine').classList.toggle('active', type === 'line');
            document.getElementById('btnBar').classList.toggle('active', type === 'bar');
            renderChart(type);
        }

        // ============ TOP 5 SHIPPER (FILTERED) ============
        function loadTopChart() {
            const params = getFilterParams();
            
            document.getElementById('topLoading').style.display = 'flex';
            document.getElementById('topChart').style.display = 'none';
            
            fetch("{{ route('top.data') }}?" + params)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('topLoading').style.display = 'none';
                    document.getElementById('topChart').style.display = 'block';
                    
                    if (!data || data.length === 0) {
                        document.getElementById('topChart').innerHTML = '<p class="text-center text-gray-400 py-8 text-sm font-semibold">Tidak ada data tersedia</p>';
                        return;
                    }

                    if (topChart) {
                        topChart.destroy();
                    }

                    const options = {
                        series: [{ name: 'Total Volume', data: data.map(d => parseFloat(d.total_volume)) }],
                        chart: { 
                            type: 'bar', 
                            height: 240, 
                            toolbar: { show: false },
                            fontFamily: 'Plus Jakarta Sans, sans-serif'
                        },
                        colors: ['#D71920','#f59e0b','#10b981','#3b82f6','#8b5cf6'],
                        plotOptions: { 
                            bar: { 
                                borderRadius: 8, 
                                distributed: true, 
                                columnWidth: '60%',
                                dataLabels: {
                                    position: 'top'
                                }
                            } 
                        },
                        xaxis: { 
                            categories: data.map(d => d.shipper),
                            labels: { style: { fontSize: '11px', fontWeight: 600 } }
                        },
                        yaxis: { 
                            labels: { formatter: v => v.toFixed(0) },
                            title: { text: 'Volume (MMSCFD)', style: {fontWeight: 700} }
                        },
                        legend: { show: false },
                        dataLabels: { 
                            enabled: true, 
                            formatter: v => v.toFixed(0), 
                            style: { fontSize: '11px', colors: ['#fff'], fontWeight: 'bold' },
                            offsetY: -20
                        },
                        tooltip: {
                            theme: 'dark',
                            y: {
                                formatter: function(val) {
                                    return val.toFixed(2) + " MMSCFD"
                                }
                            }
                        }
                    };

                    topChart = new ApexCharts(document.querySelector("#topChart"), options);
                    topChart.render();
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('topLoading').style.display = 'none';
                    document.getElementById('topChart').innerHTML = '<p class="text-center text-red-500 py-8 text-sm font-semibold">Error loading data</p>';
                });
        }

        // ============ TREND ANALYSIS ============
        document.getElementById('trendShipperSelect').addEventListener('change', function() {
            const shipper = this.value;
            
            if (!shipper) {
                document.getElementById('trendResults').innerHTML = '';
                return;
            }

            document.getElementById('trendAnalysisSection').style.display = 'block';
            document.getElementById('trendLoading').style.display = 'flex';
            document.getElementById('trendResults').innerHTML = '';

            fetch("{{ route('dashboard') }}/trend-analysis?shipper=" + encodeURIComponent(shipper))
                .then(res => res.json())
                .then(data => {
                    document.getElementById('trendLoading').style.display = 'none';
                    
                    if (data.error) {
                        document.getElementById('trendResults').innerHTML = `<p class="text-red-500 font-semibold">${data.error}</p>`;
                        return;
                    }

                    let html = `
                        <div class="mb-6 p-5 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-200">
                            <h3 class="font-bold text-lg text-gray-900 mb-2">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                                Ringkasan Analisis - ${data.shipper}
                            </h3>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Total Periode Analisis:</span>
                                    <span class="font-bold text-gray-900 ml-2">${data.total_periods}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Anomali Terdeteksi:</span>
                                    <span class="font-bold ${data.anomaly_count > 0 ? 'text-red-600' : 'text-green-600'} ml-2">
                                        ${data.anomaly_count} periode
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gradient-to-r from-gray-100 to-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-bold text-gray-700">Periode</th>
                                        <th class="px-4 py-3 text-right font-bold text-gray-700">Volume</th>
                                        <th class="px-4 py-3 text-right font-bold text-gray-700">Volume Sebelum</th>
                                        <th class="px-4 py-3 text-right font-bold text-gray-700">Selisih</th>
                                        <th class="px-4 py-3 text-right font-bold text-gray-700">Perubahan</th>
                                        <th class="px-4 py-3 text-center font-bold text-gray-700">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                    `;

                    data.trends.forEach(trend => {
                        const statusBadge = trend.is_anomaly 
                            ? `<span class="anomaly-badge px-3 py-1.5 text-xs font-bold bg-red-600
                        text-white rounded-full"><i class="fas fa-exclamation-triangle mr-1">
                        </i>ANOMALI</span>`
                            : `<span class="px-3 py-1.5 text-xs font-bold bg-green-100 text-green-800 rounded-full"><i class="fas fa-check-circle mr-1"></i>NORMAL</span>`;

                        html += `
                            <tr class="hover:bg-gray-50 transition ${trend.is_anomaly ? 'bg-red-50' : ''}">
                                <td class="px-4 py-3 font-semibold text-gray-900">${trend.periode}</td>
                                <td class="px-4 py-3 text-right font-bold text-gray-900">${trend.volume.toLocaleString('id-ID', {minimumFractionDigits: 2})}</td>
                                <td class="px-4 py-3 text-right text-gray-600">${trend.previous_volume.toLocaleString('id-ID', {minimumFractionDigits: 2})}</td>
                                <td class="px-4 py-3 text-right font-semibold ${trend.change >= 0 ? 'text-green-600' : 'text-red-600'}">
                                    ${trend.change >= 0 ? '+' : ''}${trend.change.toLocaleString('id-ID', {minimumFractionDigits: 2})}
                                </td>
                                <td class="px-4 py-3 text-right font-bold ${trend.percent_change >= 0 ? 'text-green-600' : 'text-red-600'}">
                                    ${trend.percent_change >= 0 ? '+' : ''}${trend.percent_change.toFixed(2)}%
                                </td>
                                <td class="px-4 py-3 text-center">
                                    ${statusBadge}
                                </td>
                            </tr>
                        `;
                    });

                    html += `
                                </tbody>
                            </table>
                        </div>
                    `;

                    document.getElementById('trendResults').innerHTML = html;
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('trendLoading').style.display = 'none';
                    document.getElementById('trendResults').innerHTML = '<p class="text-red-500 font-semibold">Error loading trend analysis</p>';
                });
        });

        // Listen filter changes for dynamic update
        document.querySelectorAll('#filterForm select').forEach(sel => {
            sel.addEventListener('change', () => {
                loadMainChart();
                loadTopChart();
                updateActiveFilters();
            });
        });

        // Initial load
        document.addEventListener('DOMContentLoaded', function() {
            loadMainChart();
            loadTopChart();
            updateActiveFilters();
            document.getElementById('trendAnalysisSection').style.display = 'block';
        });
    </script>
</body>
</html>