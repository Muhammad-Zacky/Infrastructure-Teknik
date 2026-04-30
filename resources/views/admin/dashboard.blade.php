<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        /* Menggunakan font Inter yang merupakan standar de-facto UI Enterprise */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            color: #334155;
        }

        [x-cloak] { display: none !important; }

        /* Animasi standar korporat (cepat dan tegas) */
        .animate-fade {
            animation: fadeIn 0.4s ease-out forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Card Enterprise Style: Sudut lebih tajam, shadow tipis, border tegas */
        .ent-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }

        .ent-card-header {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ent-card-title {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ent-table th {
            background-color: #f8fafc;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 12px 16px;
            border-bottom: 2px solid #e2e8f0;
        }

        .ent-table td {
            padding: 12px 16px;
            font-size: 13px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }

        .ent-table tr:hover {
            background-color: #f8fafc;
        }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>

    @php
        // LOGIKA & RUMUS BARU UNTUK ENTERPRISE DASHBOARD
        $equipment = isset($allInfrastructures) ? $allInfrastructures->where('category', 'equipment') : collect();
        $facility = isset($allInfrastructures) ? $allInfrastructures->where('category', 'facility') : collect();
        $utility = isset($allInfrastructures) ? $allInfrastructures->where('category', 'utility') : collect();

        // 1. Rumus Availability Rate (SLA KPI)
        $totalAssets = $stats['total'] ?? 0;
        $availableAssets = $stats['available'] ?? 0;
        $availabilityRate = $totalAssets > 0 ? round(($availableAssets / $totalAssets) * 100, 1) : 0;

        // Penentuan Warna SLA
        $slaColor = $availabilityRate >= 90 ? 'bg-emerald-500' : ($availabilityRate >= 75 ? 'bg-amber-500' : 'bg-red-500');

        // 2. Data Top 5 Entitas dengan Kerusakan Terbanyak (Untuk Horizontal Bar Chart)
        $entityBreakdowns = isset($allInfrastructures) ? $allInfrastructures->where('status', 'breakdown')->groupBy(fn($i) => $i->entity->name ?? 'Unknown') : collect();
        $topBrokenEntities = $entityBreakdowns->map->count()->sortByDesc(fn($c) => $c)->take(5);
    @endphp

    <div id="main-ui" class="max-w-[1600px] mx-auto w-full space-y-6 pb-16 px-4 sm:px-6">

        <!-- HEADER KORPORAT -->
        <div class="bg-white border-b border-slate-200 px-6 py-4 flex flex-col md:flex-row items-center justify-between gap-4 rounded-b-lg shadow-sm">
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-4 border-r border-slate-200 pr-6">
                    <img src="{{ asset('danantara.png') }}" alt="Danantara" class="h-8 object-contain">
                    <div class="w-px h-6 bg-slate-300"></div>
                    <img src="{{ asset('pelindo.png') }}" alt="Pelindo" class="h-8 object-contain">
                </div>
                <div>
                    <h1 class="text-lg font-bold text-[#003366]">Command Center</h1>
                    <p class="text-[11px] font-medium text-slate-500">Infrastructure Monitoring System</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.away="open = false" class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 px-4 py-2 rounded text-xs font-semibold transition-colors flex items-center gap-2">
                        <i class="fas fa-print text-slate-400"></i> Generate Report <i class="fas fa-caret-down ml-1"></i>
                    </button>
                    <div x-show="open" x-transition class="absolute right-0 mt-1 w-40 bg-white rounded shadow-lg border border-slate-200 z-50 py-1">
                        <button onclick="openExportModal('pdf')" class="w-full text-left px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-100 flex items-center gap-2">
                            <i class="fas fa-file-pdf text-red-500 w-4"></i> PDF Document
                        </button>
                        <button onclick="openExportModal('excel')" class="w-full text-left px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-100 flex items-center gap-2">
                            <i class="fas fa-file-excel text-emerald-500 w-4"></i> Excel Spreadsheet
                        </button>
                    </div>
                </div>

                <a href="{{ route('admin.breakdowns.create') }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-xs font-semibold transition-colors flex items-center gap-2 shadow-sm">
                    <i class="fas fa-triangle-exclamation"></i> Lapor Insiden
                </a>
                <a href="{{ route('admin.infrastructures.create') }}" class="bg-[#0055a4] hover:bg-[#004380] text-white px-4 py-2 rounded text-xs font-semibold transition-colors flex items-center gap-2 shadow-sm">
                    <i class="fas fa-plus"></i> Register Aset
                </a>
            </div>
        </div>

        <!-- 4 KPI CARDS (Standard Enterprise) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 animate-fade">
            <!-- Total Asset -->
            <div class="ent-card p-5 border-t-4 border-t-[#003366]">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wide">Total Infrastruktur</p>
                <div class="mt-2 flex justify-between items-end">
                    <h2 class="text-3xl font-black text-slate-800">{{ $stats['total'] ?? 0 }}</h2>
                    <i class="fas fa-boxes-stacked text-3xl text-slate-200"></i>
                </div>
            </div>

            <!-- Ready -->
            <div class="ent-card p-5 border-t-4 border-t-emerald-500">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wide">Status Operasional (Ready)</p>
                <div class="mt-2 flex justify-between items-end">
                    <h2 class="text-3xl font-black text-emerald-600">{{ $stats['available'] ?? 0 }}</h2>
                    <i class="fas fa-check-circle text-3xl text-emerald-100"></i>
                </div>
            </div>

            <!-- Breakdown -->
            <div class="ent-card p-5 border-t-4 border-t-red-500">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wide">Dalam Perbaikan (Down)</p>
                <div class="mt-2 flex justify-between items-end">
                    <h2 class="text-3xl font-black text-red-600">{{ $stats['breakdown'] ?? 0 }}</h2>
                    <i class="fas fa-wrench text-3xl text-red-100"></i>
                </div>
            </div>

            <!-- RUMUS BARU: Availability Rate -->
            <div class="ent-card p-5 border-t-4 border-t-blue-400 flex flex-col justify-between">
                <div class="flex justify-between items-start mb-2">
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wide">SLA Availability Rate</p>
                    <span class="text-sm font-black text-slate-800">{{ $availabilityRate }}%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2.5 mb-1 overflow-hidden">
                    <div class="{{ $slaColor }} h-2.5 rounded-full" style="width: {{ $availabilityRate }}%"></div>
                </div>
                <p class="text-[10px] text-slate-400 font-medium mt-1">Target Perusahaan: > 90%</p>
            </div>
        </div>

        <!-- MAIN ANALYTICS GRID -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade" style="animation-delay: 100ms;">

            <!-- Tren Laporan (Line Chart) -->
            <div class="lg:col-span-2 ent-card flex flex-col">
                <div class="ent-card-header">
                    <h3 class="ent-card-title"><i class="fas fa-chart-area text-[#0055a4]"></i> Tren Laporan Insiden (30 Hari)</h3>
                </div>
                <div class="p-5 flex-1 relative h-64 w-full">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <!-- Readiness Ratio (Doughnut) -->
            <div class="ent-card flex flex-col">
                <div class="ent-card-header">
                    <h3 class="ent-card-title"><i class="fas fa-chart-pie text-emerald-500"></i> Rasio Kesiapan Alat</h3>
                </div>
                <div class="p-5 flex-1 relative h-64 w-full flex items-center justify-center">
                    <canvas id="healthChart"></canvas>
                </div>
            </div>

        </div>

        <!-- SECONDARY ANALYTICS & WIDGETS -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade" style="animation-delay: 150ms;">

            <!-- CHART BARU: Top 5 Worst Entities -->
            <div class="ent-card flex flex-col">
                <div class="ent-card-header">
                    <h3 class="ent-card-title"><i class="fas fa-building-flag text-red-500"></i> Top 5 Entitas (Issue Terbanyak)</h3>
                </div>
                <div class="p-5 flex-1 relative h-64 w-full">
                    <canvas id="topEntityChart"></canvas>
                </div>
            </div>

            <!-- Distribusi Kategori (Bar Chart) -->
            <div class="ent-card flex flex-col">
                <div class="ent-card-header">
                    <h3 class="ent-card-title"><i class="fas fa-layer-group text-blue-500"></i> Distribusi Kategori Aset</h3>
                </div>
                <div class="p-5 flex-1 relative h-64 w-full">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>

            <!-- Widget: Aset Kritis (List) -->
            <div class="ent-card flex flex-col">
                <div class="ent-card-header">
                    <h3 class="ent-card-title text-red-600"><i class="fas fa-engine-warning animate-pulse"></i> Critical Assets (Down)</h3>
                </div>
                <div class="flex-1 overflow-y-auto p-0 m-0" style="max-height: 280px;">
                    @php
                        $brokenAssets = isset($allInfrastructures) ? $allInfrastructures->where('status', 'breakdown')->sortByDesc('updated_at')->take(5) : collect();
                    @endphp

                    <ul class="divide-y divide-slate-100">
                        @forelse($brokenAssets as $broken)
                            <li class="px-5 py-3 hover:bg-slate-50 transition-colors flex justify-between items-center">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 bg-red-100 text-red-600 rounded flex items-center justify-center text-xs shrink-0">
                                        <i class="fas {{ $broken->category == 'equipment' ? 'fa-truck-monster' : ($broken->category == 'facility' ? 'fa-warehouse' : 'fa-bolt') }}"></i>
                                    </div>
                                    <div class="truncate">
                                        <p class="text-xs font-bold text-slate-800 truncate" title="{{ $broken->code_name }}">{{ $broken->code_name }}</p>
                                        <p class="text-[10px] text-slate-500 truncate mt-0.5">{{ $broken->entity->name ?? 'Unknown' }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('admin.infrastructures.index') }}" class="text-[10px] font-bold text-blue-600 hover:underline shrink-0">Detail</a>
                            </li>
                        @empty
                            <li class="px-5 py-8 text-center">
                                <i class="fas fa-check-circle text-2xl text-emerald-400 mb-2"></i>
                                <p class="text-xs font-bold text-slate-600">Sistem Normal</p>
                                <p class="text-[10px] text-slate-400 mt-1">Tidak ada aset berstatus kritis.</p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>

        <!-- TABLE: Log Insiden Aktif -->
        <div class="ent-card animate-fade" style="animation-delay: 200ms;">
            <div class="ent-card-header bg-[#00152b] border-none rounded-t-lg">
                <h3 class="ent-card-title text-white"><i class="fas fa-list-check text-blue-400"></i> Ongoing Incident Log</h3>
                <a href="{{ route('admin.breakdowns.index') }}" class="text-xs text-blue-300 hover:text-white font-medium flex items-center gap-1">
                    Manage Logs <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse ent-table min-w-[900px]">
                    <thead>
                        <tr>
                            <th class="w-12 text-center">No</th>
                            <th>Reference ID</th>
                            <th>Location / Entity</th>
                            <th>Asset Code</th>
                            <th>Issue Description</th>
                            <th>Current Status</th>
                            <th>PIC/Vendor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLogs ?? [] as $index => $log)
                        <tr>
                            <td class="text-center text-slate-400">{{ $index + 1 }}</td>
                            <td class="font-mono text-xs text-slate-500">#INC-{{ str_pad($log->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="font-semibold">{{ $log->infrastructure->entity->name ?? '-' }}</td>
                            <td><span class="bg-slate-100 text-[#003366] px-2 py-1 rounded text-xs font-bold font-mono border border-slate-200">{{ $log->infrastructure->code_name ?? '-' }}</span></td>
                            <td class="max-w-[250px] truncate" title="{{ $log->issue_detail }}">{{ $log->issue_detail }}</td>
                            <td>
                                @if($log->repair_status == 'order_part')
                                    <span class="bg-purple-100 text-purple-700 text-[10px] font-bold px-2 py-1 rounded uppercase border border-purple-200">Menunggu Part</span>
                                @elseif($log->repair_status == 'on_progress')
                                    <span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-1 rounded uppercase border border-amber-200">On Progress</span>
                                @elseif($log->repair_status == 'reported')
                                    <span class="bg-red-100 text-red-700 text-[10px] font-bold px-2 py-1 rounded uppercase border border-red-200">Dilaporkan</span>
                                @else
                                    <span class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-1 rounded uppercase border border-emerald-200">Resolved</span>
                                @endif
                            </td>
                            <td class="text-xs font-medium">{{ $log->vendor_pic ?? 'Internal' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500 text-sm">
                                <i class="fas fa-folder-open text-3xl mb-3 text-slate-300 block"></i>
                                Tidak ada log insiden yang sedang aktif.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Hidden Export Logic (assuming you have this component or logic) -->
    <x-export-report :infrastructures="$allInfrastructures ?? collect()" :recentBreakdowns="$allActiveBreakdowns ?? collect()" />
    <x-export-filter-modal />

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enterprise Chart Configurations
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.color = '#64748b';
            Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(15, 23, 42, 0.9)';
            Chart.defaults.plugins.tooltip.padding = 10;
            Chart.defaults.plugins.tooltip.cornerRadius = 4;

            // 1. Health Doughnut Chart
            new Chart(document.getElementById('healthChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Ready', 'Down'],
                    datasets: [{
                        data: [{{ $stats['available'] ?? 0 }}, {{ $stats['breakdown'] ?? 0 }}],
                        backgroundColor: ['#10b981', '#ef4444'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 4
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: {size: 11, weight: '600'} } } } }
            });

            // 2. Trend Line Chart
            new Chart(document.getElementById('trendChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartData['trendLabels'] ?? []) !!},
                    datasets: [{
                        label: 'Insiden Tercatat',
                        data: {!! json_encode($chartData['trendCounts'] ?? []) !!},
                        borderColor: '#0055a4',
                        backgroundColor: 'rgba(0, 85, 164, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#0055a4',
                        pointRadius: 3
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, border: { display: false }, grid: { color: '#f1f5f9' }, ticks: { stepSize: 1, font: {size: 10} } },
                        x: { grid: { display: false }, ticks: { font: {size: 10} } }
                    }
                }
            });

            // 3. Category Bar Chart
            new Chart(document.getElementById('categoryChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['Peralatan', 'Fasilitas', 'Utilitas'],
                    datasets: [
                        {
                            label: 'Ready',
                            data: [
                                {{ $equipment->where('status', 'available')->count() }},
                                {{ $facility->where('status', 'available')->count() }},
                                {{ $utility->where('status', 'available')->count() }}
                            ],
                            backgroundColor: '#10b981',
                            borderRadius: 2,
                            barPercentage: 0.5
                        },
                        {
                            label: 'Down',
                            data: [
                                {{ $equipment->where('status', 'breakdown')->count() }},
                                {{ $facility->where('status', 'breakdown')->count() }},
                                {{ $utility->where('status', 'breakdown')->count() }}
                            ],
                            backgroundColor: '#ef4444',
                            borderRadius: 2,
                            barPercentage: 0.5
                        }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: {
                        x: { stacked: true, grid: { display: false }, ticks: { font: { size: 11, weight: '500' } } },
                        y: { stacked: true, beginAtZero: true, border: { display: false }, grid: { color: '#f1f5f9' } }
                    },
                    plugins: { legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8, font: {size: 11} } } }
                }
            });

            // 4. CHART BARU: Horizontal Bar untuk Top 5 Entitas Bermasalah
            new Chart(document.getElementById('topEntityChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! $topBrokenEntities->keys()->toJson() !!},
                    datasets: [{
                        label: 'Total Aset Rusak',
                        data: {!! $topBrokenEntities->values()->toJson() !!},
                        backgroundColor: '#ef4444',
                        borderRadius: 2,
                        barThickness: 15
                    }]
                },
                options: {
                    indexAxis: 'y', // Membuatnya menjadi Horizontal Bar Chart
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { beginAtZero: true, grid: { color: '#f1f5f9' }, border: { display: false }, ticks: { stepSize: 1, font: {size: 10} } },
                        y: { grid: { display: false }, ticks: { font: { size: 10, weight: '500' } } }
                    }
                }
            });
        });
    </script>
</x-app-layout>
