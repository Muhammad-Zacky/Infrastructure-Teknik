<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');
        
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f4f7fa; }
        
        /* Pelindo Blue Theme */
        .bg-pelindo { background-color: #0055a4; }
        .text-pelindo { color: #0055a4; }
        .border-pelindo { border-color: #0055a4; }
        
        .animate-fade { animation: fadeIn 0.8s ease-out forwards; opacity: 0; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

        /* Professional Cards */
        .card-stats {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 1.5rem;
            transition: all 0.3s ease;
        }
        .card-stats:hover { transform: translateY(-5px); border-color: #0055a4; box-shadow: 0 15px 30px -10px rgba(0,85,164,0.1); }

        #hiddenExportTable { display: none; background: white; }
    </style>

    <div id="main-ui" class="max-w-[1600px] mx-auto w-full space-y-8 pb-16 px-4 pt-8">

        <div class="bg-pelindo rounded-[2rem] p-8 shadow-xl flex flex-col md:flex-row items-center justify-between gap-6 animate-fade relative z-[60]">
            <div class="absolute inset-0 overflow-hidden rounded-[2rem] pointer-events-none">
                <div class="absolute right-0 top-0 opacity-10 -mr-10 -mt-10">
                    <i class="fas fa-chart-line text-[15rem] text-white"></i>
                </div>
            </div>
            
            <div class="flex items-center gap-6 relative z-10">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center text-3xl text-white border border-white/30">
                    <i class="fas fa-analytics"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-white uppercase tracking-tight">Analytics Center</h1>
                    <p class="text-blue-100 text-xs font-bold uppercase tracking-widest mt-1">
                        {{ $user->role === 'superadmin' ? 'Monitoring Performa Seluruh Cabang' : 'Statistik Detail ' . $chartData['entity_name'] }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-4 relative z-10">
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.away="open = false" class="bg-white text-pelindo px-6 py-3.5 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg transition-all hover:bg-blue-50 flex items-center gap-3">
                        <i class="fas fa-file-export"></i> Download Report <i class="fas fa-chevron-down opacity-50"></i>
                    </button>
                    <div x-show="open" x-transition class="absolute right-0 mt-2 w-56 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 overflow-hidden">
                        <button onclick="exportToExcel()" class="w-full text-left px-5 py-4 text-[10px] font-black uppercase text-slate-600 hover:bg-blue-50 transition-colors flex items-center gap-3">
                            <i class="fas fa-file-excel text-emerald-500"></i> Export to Excel
                        </button>
                        <button onclick="exportToPDF()" class="w-full text-left px-5 py-4 text-[10px] font-black uppercase text-slate-600 hover:bg-blue-50 transition-colors flex items-center gap-3 border-t border-slate-50">
                            <i class="fas fa-file-pdf text-red-500"></i> Export to PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-fade" style="animation-delay: 150ms;">
            
            <div class="lg:col-span-2 card-stats p-8">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Tren Insiden Kerusakan</h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">Aktivitas pelaporan dalam 30 hari terakhir</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-pelindo border border-blue-100">
                        <i class="fas fa-wave-square"></i>
                    </div>
                </div>
                <div class="h-80 w-full">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <div class="card-stats p-8">
                <div class="mb-8">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Readiness Ratio</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase mt-1 text-center">Persentase Kesiapan Aset</p>
                </div>
                <div class="h-64 w-full flex items-center justify-center relative">
                    <canvas id="pieChart"></canvas>
                </div>
                <div class="mt-6 grid grid-cols-2 gap-4">
                    <div class="bg-emerald-50 p-3 rounded-xl border border-emerald-100 text-center">
                        <p class="text-[9px] font-black text-emerald-600 uppercase">Ready</p>
                        <p class="text-lg font-black text-emerald-700">{{ array_sum($chartData['ready']) }}</p>
                    </div>
                    <div class="bg-red-50 p-3 rounded-xl border border-red-100 text-center">
                        <p class="text-[9px] font-black text-red-600 uppercase">Down</p>
                        <p class="text-lg font-black text-red-700">{{ array_sum($chartData['breakdown']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-stats p-8 animate-fade" style="animation-delay: 250ms;">
            <div class="mb-8">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">
                    {{ $user->role === 'superadmin' ? 'Perbandingan Performa Antar Terminal' : 'Statistik Kesiapan per Kategori' }}
                </h3>
            </div>
            <div class="h-96 w-full">
                <canvas id="barChart"></canvas>
            </div>
        </div>

        <div class="card-stats overflow-hidden animate-fade shadow-sm" style="animation-delay: 350ms;">
            <div class="px-8 py-6 bg-[#00152b] border-b border-slate-800 flex items-center gap-4">
                <i class="fas fa-book-spells text-blue-400"></i>
                <h3 class="text-white font-black uppercase tracking-widest text-sm leading-none">Logbook Pemeliharaan Unit</h3>
            </div>

            <div class="divide-y divide-slate-100">
                @foreach($infrastructures as $item)
                    <div x-data="{ open: false }" class="group">
                        <button @click="open = !open" class="w-full px-8 py-5 flex items-center justify-between hover:bg-slate-50 transition-all">
                            <div class="flex items-center gap-6">
                                <div class="w-12 h-12 rounded-2xl bg-slate-50 text-pelindo border border-slate-200 flex items-center justify-center text-lg group-hover:bg-pelindo group-hover:text-white group-hover:border-pelindo transition-all">
                                    <i class="fas {{ $item->category == 'equipment' ? 'fa-truck-container' : 'fa-building-columns' }}"></i>
                                </div>
                                <div class="text-left">
                                    <p class="text-[10px] font-black text-pelindo uppercase tracking-widest">{{ $item->code_name }}</p>
                                    <h4 class="text-sm font-bold text-slate-700">{{ $item->type }}</h4>
                                </div>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="hidden md:block text-right">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Total Laporan</p>
                                    <p class="text-xs font-bold text-slate-700">{{ $item->breakdownLogs->count() }} Kali</p>
                                </div>
                                <div class="w-8 h-8 rounded-full border border-slate-200 flex items-center justify-center transition-all" :class="open ? 'bg-pelindo text-white rotate-180' : ''">
                                    <i class="fas fa-chevron-down text-[10px]"></i>
                                </div>
                            </div>
                        </button>
                        
                        <div x-show="open" x-collapse>
                            <div class="px-8 pb-8 bg-slate-50/50">
                                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                                    <table class="w-full text-left text-xs">
                                        <thead class="bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-500 uppercase tracking-widest">
                                            <tr>
                                                <th class="px-6 py-4">Tgl Kejadian</th>
                                                <th class="px-6 py-4">Rincian Kendala</th>
                                                <th class="px-6 py-4">Status Terakhir</th>
                                                <th class="px-6 py-4">Vendor / PIC</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-50">
                                            @forelse($item->breakdownLogs as $log)
                                            <tr>
                                                <td class="px-6 py-4 font-bold text-slate-600">{{ $log->created_at->format('d/m/Y') }}</td>
                                                <td class="px-6 py-4 text-slate-500">{{ $log->issue_detail }}</td>
                                                <td class="px-6 py-4">
                                                    <span class="px-2 py-1 rounded bg-slate-100 text-slate-600 text-[9px] font-black uppercase">{{ str_replace('_', ' ', $log->repair_status) }}</span>
                                                </td>
                                                <td class="px-6 py-4 font-bold text-pelindo uppercase">{{ $log->vendor_pic }}</td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="4" class="px-6 py-8 text-center text-slate-400 italic">Belum ada riwayat perbaikan.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div id="hiddenExportTable">
        <div style="text-align: center; border-bottom: 2px solid #0055a4; padding-bottom: 20px; margin-bottom: 30px;">
            <h1 style="color: #0055a4; margin: 0; font-size: 24px;">PELINDO REGIONAL 2</h1>
            <h2 style="margin: 5px 0; font-size: 16px;">LAPORAN HISTORI PEMELIHARAAN INFRASTRUKTUR</h2>
            <p style="font-size: 10px; color: #666;">Generated on: {{ now()->format('d F Y H:i') }} | By: {{ Auth::user()->name }}</p>
        </div>

        <table id="excelDataTable" border="1" style="width: 100%; border-collapse: collapse; font-size: 10px;">
            <thead style="background-color: #0055a4; color: white;">
                <tr>
                    <th style="padding: 10px;">UNIT</th>
                    <th style="padding: 10px;">CABANG</th>
                    <th style="padding: 10px;">TGL RUSAK</th>
                    <th style="padding: 10px;">DETAIL KENDALA</th>
                    <th style="padding: 10px;">STATUS AKHIR</th>
                    <th style="padding: 10px;">PIC/VENDOR</th>
                </tr>
            </thead>
            <tbody>
                @foreach($infrastructures as $item)
                    @foreach($item->breakdownLogs as $log)
                    <tr>
                        <td style="padding: 8px;">{{ $item->code_name }} ({{ $item->type }})</td>
                        <td style="padding: 8px;">{{ $item->entity->name }}</td>
                        <td style="padding: 8px;">{{ $log->created_at->format('d/m/Y') }}</td>
                        <td style="padding: 8px;">{{ $log->issue_detail }}</td>
                        <td style="padding: 8px;">{{ strtoupper($log->repair_status) }}</td>
                        <td style="padding: 8px;">{{ $log->vendor_pic }}</td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const data = @json($chartData);
            Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";

            // 1. TREN CHART (LINE)
            new Chart(document.getElementById('trendChart'), {
                type: 'line',
                data: {
                    labels: data.trendLabels,
                    datasets: [{
                        label: 'Jumlah Laporan Kerusakan',
                        data: data.trendCounts,
                        borderColor: '#0055a4',
                        backgroundColor: 'rgba(0, 85, 164, 0.05)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#0055a4',
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { stepSize: 1 } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // 2. STACKED BAR CHART
            new Chart(document.getElementById('barChart'), {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [
                        { label: 'Ready', data: data.ready, backgroundColor: '#10b981', barThickness: 40 },
                        { label: 'Down', data: data.breakdown, backgroundColor: '#ef4444', barThickness: 40 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { stacked: true, grid: { color: '#f1f5f9' } },
                        x: { stacked: true, grid: { display: false } }
                    }
                }
            });

            // 3. PIE CHART
            new Chart(document.getElementById('pieChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Ready', 'Down'],
                    datasets: [{
                        data: [data.ready.reduce((a,b)=>a+b,0), data.breakdown.reduce((a,b)=>a+b,0)],
                        backgroundColor: ['#10b981', '#ef4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    cutout: '75%',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        });

        // EXPORT FUNCTIONS
        function exportToExcel() {
            const table = document.getElementById('excelDataTable');
            const wb = XLSX.utils.table_to_book(table);
            XLSX.writeFile(wb, "Analytics_Report_Pelindo_{{ date('dMY') }}.xlsx");
        }

        function exportToPDF() {
            const element = document.getElementById('hiddenExportTable');
            element.style.display = 'block';
            
            const opt = {
                margin: 0.5,
                filename: 'Analytics_Report_Pelindo.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'letter', orientation: 'landscape' }
            };

            html2pdf().set(opt).from(element).save().then(() => {
                element.style.display = 'none';
            });
        }
    </script>
</x-app-layout>
