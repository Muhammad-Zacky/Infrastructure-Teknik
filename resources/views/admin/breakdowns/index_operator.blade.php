<x-app-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        [x-cloak] { display: none !important; }

        /* Custom Scrollbar Korporat */
        ::-webkit-scrollbar { height: 8px; width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        .hide-scrollbar::-webkit-scrollbar { height: 8px; }
    </style>

    <!-- State Alpine.js Utama untuk Modals dan Filter -->
    <div class="min-h-screen py-8"
         x-data="{
             showReportModal: false,
             showUpdateModal: false,
             selectedAsset: null,
             selectedLogId: null,
             currentStatus: '',
             logDates: {},
             search: '',
             filterCondition: 'all',
             filterStatus: 'all'
         }">

        <!-- MODAL: LAPOR KERUSAKAN BARU -->
        <template x-teleport="body">
            <div x-show="showReportModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
                <div @click.away="showReportModal = false" x-show="showReportModal" x-transition.scale.origin.bottom.duration.200ms class="bg-white rounded-lg shadow-xl max-w-md w-full overflow-hidden border border-slate-200">
                    <div class="bg-red-50 p-5 border-b border-red-100 flex items-start gap-4">
                        <div class="w-10 h-10 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-lg shrink-0"><i class="fas fa-tools"></i></div>
                        <div>
                            <h2 class="text-sm font-bold text-red-700 uppercase tracking-wide">Lapor Insiden Aset</h2>
                            <p class="text-xs font-semibold text-red-500 mt-0.5" x-text="selectedAsset ? selectedAsset.code : ''"></p>
                        </div>
                    </div>
                    <form action="{{ route('admin.breakdowns.store') }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        <input type="hidden" name="infrastructure_id" :value="selectedAsset ? selectedAsset.id : ''">
                        <input type="hidden" name="repair_status" value="reported">

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-2">Rincian Kendala / Masalah <span class="text-red-500">*</span></label>
                            <textarea name="issue_detail" rows="3" placeholder="Jelaskan kendala secara spesifik..." class="w-full bg-white border border-slate-300 rounded-md text-sm p-2.5 focus:ring-1 focus:ring-red-500 focus:border-red-500 transition-colors" required></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-2">PIC Pelapor / Vendor (Opsional)</label>
                            <input type="text" name="vendor_pic" placeholder="Contoh: Internal / PT Vendor" class="w-full bg-white border border-slate-300 rounded-md text-sm p-2.5 focus:ring-1 focus:ring-red-500 focus:border-red-500 transition-colors uppercase">
                        </div>
                        <div class="pt-2 flex justify-end gap-2">
                            <button type="button" @click="showReportModal = false" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 rounded text-xs font-semibold transition-colors">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-semibold transition-colors shadow-sm flex items-center gap-2"><i class="fas fa-save"></i> Submit Laporan</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        <!-- MODAL: UPDATE PROGRES PERBAIKAN -->
        <template x-teleport="body">
            <div x-show="showUpdateModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
                <div @click.away="showUpdateModal = false" x-show="showUpdateModal" x-transition.scale.origin.bottom.duration.200ms class="bg-white rounded-lg shadow-xl max-w-2xl w-full overflow-hidden border border-slate-200 flex flex-col max-h-[90vh]">

                    <div class="bg-[#00152b] p-5 border-b border-slate-700 flex items-center justify-between shrink-0">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-bars-progress text-blue-400 text-xl"></i>
                            <div>
                                <h2 class="text-sm font-bold text-white uppercase tracking-wide">Pembaruan Progres Kesiapan</h2>
                                <p class="text-xs font-semibold text-blue-300 mt-0.5" x-text="selectedAsset ? selectedAsset.code : ''"></p>
                            </div>
                        </div>
                        <button @click="showUpdateModal = false" type="button" class="text-slate-400 hover:text-red-400 transition-colors"><i class="fas fa-times text-lg"></i></button>
                    </div>

                    <form :action="`/admin/breakdowns/${selectedLogId}`" method="POST" enctype="multipart/form-data" class="p-6 space-y-6 overflow-y-auto">
                        @csrf @method('PUT')

                        <!-- Status Terkini -->
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                            <label class="block text-xs font-bold text-[#003366] uppercase tracking-wide mb-2">Tahap Pekerjaan Terkini <span class="text-red-500">*</span></label>
                            <select name="repair_status" x-model="currentStatus" class="w-full bg-white border border-slate-300 rounded text-sm p-2.5 font-semibold focus:ring-1 focus:ring-[#003366] transition-colors cursor-pointer" required>
                                <option value="reported">1. Dilaporkan (Reported)</option>
                                <option value="troubleshooting">2. Identifikasi / Trouble Shoot</option>
                                <option value="work_order">3. Berita Acara / Work Order</option>
                                <option value="order_part">4. Proses PR / PO / Order Part</option>
                                <option value="on_progress">5. Proses Pekerjaan Berlangsung (On Progress)</option>
                                <option value="testing">6. Commissioning Test</option>
                                <option value="resolved" class="text-emerald-700 font-bold">✔️ 7. SELESAI (ALAT READY)</option>
                            </select>
                        </div>

                        <!-- Tanggal Progres -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block text-[10px] font-semibold text-slate-500 uppercase">Tgl Trouble Shoot</label>
                                <input type="date" name="troubleshoot_date" :value="logDates.troubleshoot_date" class="w-full bg-white border border-slate-300 rounded text-xs p-2 focus:ring-1 focus:ring-[#0055a4]">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] font-semibold text-slate-500 uppercase">Tgl Berita Acara</label>
                                <input type="date" name="ba_date" :value="logDates.ba_date" class="w-full bg-white border border-slate-300 rounded text-xs p-2 focus:ring-1 focus:ring-[#0055a4]">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] font-semibold text-slate-500 uppercase">Tgl Work Order</label>
                                <input type="date" name="work_order_date" :value="logDates.work_order_date" class="w-full bg-white border border-slate-300 rounded text-xs p-2 focus:ring-1 focus:ring-[#0055a4]">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] font-semibold text-slate-500 uppercase">Tgl PR / PO / Order Part</label>
                                <input type="date" name="pr_po_date" :value="logDates.pr_po_date" class="w-full bg-white border border-slate-300 rounded text-xs p-2 focus:ring-1 focus:ring-[#0055a4]">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] font-semibold text-slate-500 uppercase">Tgl Suku Cadang On Site</label>
                                <input type="date" name="sparepart_date" :value="logDates.sparepart_date" class="w-full bg-white border border-slate-300 rounded text-xs p-2 focus:ring-1 focus:ring-[#0055a4]">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] font-semibold text-slate-500 uppercase">Tgl Mulai Pekerjaan</label>
                                <input type="date" name="start_work_date" :value="logDates.start_work_date" class="w-full bg-white border border-slate-300 rounded text-xs p-2 focus:ring-1 focus:ring-[#0055a4]">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] font-semibold text-slate-500 uppercase">Tgl Com Test</label>
                                <input type="date" name="com_test_date" :value="logDates.com_test_date" class="w-full bg-white border border-slate-300 rounded text-xs p-2 focus:ring-1 focus:ring-[#0055a4]">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] font-bold text-emerald-600 uppercase">Tgl Selesai Pekerjaan</label>
                                <input type="date" name="resolved_date" :value="logDates.resolved_date" class="w-full bg-emerald-50 border border-emerald-300 rounded text-xs font-semibold text-emerald-800 p-2 focus:ring-1 focus:ring-emerald-500">
                            </div>
                        </div>

                        <!-- Dokumen Bukti -->
                        <div class="border-t border-slate-100 pt-4">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-2">Lampiran Bukti Pengerjaan <span class="text-[10px] font-normal normal-case text-slate-400">(Opsional - PDF/JPG Maks 5MB)</span></label>
                            <input type="file" name="document_proof" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 transition-colors cursor-pointer border border-slate-200 rounded p-1.5 bg-white">
                        </div>

                        <div class="pt-4 flex justify-end gap-2 border-t border-slate-100 mt-6">
                            <button type="button" @click="showUpdateModal = false" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 rounded text-xs font-semibold transition-colors">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-[#0055a4] hover:bg-[#003366] text-white rounded text-xs font-semibold transition-colors shadow-sm flex items-center gap-2"><i class="fas fa-save"></i> Simpan Progres</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        <div class="max-w-[1600px] mx-auto w-full px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- HEADER KORPORAT -->
            <div class="bg-white p-6 rounded-lg border border-slate-200 shadow-sm flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 relative overflow-hidden animate-fade">
                <div class="absolute left-0 top-0 h-full w-1.5 bg-[#0055a4] rounded-l-lg"></div>

                <div>
                    <h1 class="text-lg font-bold text-[#003366] flex items-center gap-2">
                        <i class="fas fa-clipboard-check text-[#0055a4]"></i> Panel Laporan Kesiapan Alat (PA)
                    </h1>
                    <p class="text-xs font-medium text-slate-500 mt-1">Sistem pelaporan status fisik dan tracking progres perbaikan oleh teknisi / vendor.</p>
                </div>

                <div class="w-full xl:w-auto shrink-0 relative" x-data="{ openExport: false }">
                    <button @click="openExport = !openExport" @click.away="openExport = false" class="w-full justify-center bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded text-xs font-semibold transition-colors flex items-center gap-2 shadow-sm">
                        <i class="fas fa-file-export text-slate-400"></i> Export Data <i class="fas fa-caret-down ml-1"></i>
                    </button>
                    <div x-show="openExport" x-transition class="absolute right-0 mt-1 w-40 bg-white rounded shadow-lg border border-slate-200 z-[100] py-1">
                        <button onclick="openExportModal('pdf')" class="w-full text-left px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-100 flex items-center gap-2">
                            <i class="fas fa-file-pdf text-red-500 w-4"></i> Format PDF
                        </button>
                        <button onclick="openExportModal('excel')" class="w-full text-left px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-100 flex items-center gap-2">
                            <i class="fas fa-file-excel text-emerald-500 w-4"></i> Format Excel
                        </button>
                    </div>
                </div>
            </div>

            <!-- ALERTS -->
            @if(session('success'))
                <div class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-4 py-3 rounded-md text-sm font-medium shadow-sm flex items-center gap-3">
                    <i class="fas fa-check-circle text-emerald-500"></i> {{ session('success') }}
                </div>
            @endif

            <!-- TABLE CONTAINER -->
            <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden flex flex-col animate-fade" style="animation-delay: 100ms;">

                <!-- FILTER BAR (FITUR BARU) -->
                <div class="bg-slate-50 border-b border-slate-200 px-5 py-4 flex flex-col md:flex-row gap-3">
                    <!-- Search Input -->
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" x-model="search" placeholder="Cari Kode Alat, Kendala, atau Nama PIC..."
                               class="w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded text-xs font-medium text-slate-700 focus:ring-[#0055a4] focus:border-[#0055a4] transition-colors shadow-sm">
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 shrink-0">
                        <!-- Kondisi Fisik Filter -->
                        <select x-model="filterCondition" class="w-full sm:w-48 px-3 py-2 bg-white border border-slate-300 rounded text-xs font-medium text-slate-700 focus:ring-[#0055a4] focus:border-[#0055a4] transition-colors shadow-sm cursor-pointer">
                            <option value="all">Semua Kondisi Alat</option>
                            <option value="ready">Fisik Normal (Ready)</option>
                            <option value="breakdown">Fisik Rusak (Breakdown)</option>
                        </select>

                        <!-- Status Progres Filter -->
                        <select x-model="filterStatus" class="w-full sm:w-56 px-3 py-2 bg-white border border-slate-300 rounded text-xs font-medium text-slate-700 focus:ring-[#0055a4] focus:border-[#0055a4] transition-colors shadow-sm cursor-pointer">
                            <option value="all">Semua Tahap Progres</option>
                            <option value="reported">Dilaporkan (Reported)</option>
                            <option value="troubleshooting">Troubleshooting</option>
                            <option value="work_order">Berita Acara / WO</option>
                            <option value="order_part">Menunggu Part (Order Part)</option>
                            <option value="on_progress">Sedang Dikerjakan</option>
                            <option value="testing">Com Test (Testing)</option>
                            <option value="resolved">Selesai (Resolved)</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto w-full hide-scrollbar relative z-0">
                    <table class="w-full text-left border-collapse min-w-[1500px]">
                        <thead>
                            <tr class="bg-white border-b border-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                                <th class="px-4 py-3.5 w-12 text-center sticky left-0 bg-white z-10 shadow-[1px_0_0_0_#e2e8f0]">No</th>
                                <th class="px-4 py-3.5 w-32 sticky left-[3rem] bg-white z-10 shadow-[1px_0_0_0_#e2e8f0]">Identitas Alat</th>
                                <th class="px-4 py-3.5 w-24 text-center">Fisik</th>
                                <th class="px-4 py-3.5 min-w-[200px]">Deskripsi Kendala</th>
                                <th class="px-4 py-3.5 w-24 text-center">Tgl BD</th>
                                <th class="px-4 py-3.5 w-32 text-center">Tahap Progres</th>
                                <th class="px-4 py-3.5 w-24 text-center">T.Shoot</th>
                                <th class="px-4 py-3.5 w-24 text-center">B.Acara</th>
                                <th class="px-4 py-3.5 w-24 text-center">W.Order</th>
                                <th class="px-4 py-3.5 w-24 text-center">PO Part</th>
                                <th class="px-4 py-3.5 w-24 text-center">Part Site</th>
                                <th class="px-4 py-3.5 w-24 text-center">Mulai</th>
                                <th class="px-4 py-3.5 w-24 text-center">Test</th>
                                <th class="px-4 py-3.5 w-24 text-center">Selesai</th>
                                <th class="px-4 py-3.5 w-32">Vendor / PIC</th>
                                <th class="px-4 py-3.5 w-32 text-center export-ignore">Aksi Data</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                            @foreach($infrastructures as $index => $item)
                                @php
                                    $isBroken = $item->status === 'breakdown';
                                    $activeLog = $isBroken ? ($activeBreakdowns[$item->id] ?? null) : null;

                                    // Variables for Alpine Filtering
                                    $rowCondition = $isBroken ? 'breakdown' : 'ready';
                                    $rowStatus = $activeLog ? $activeLog->repair_status : 'none';
                                    $bgColor = $isBroken ? 'bg-red-50/20' : 'bg-white';
                                    $stickyBg = $isBroken ? 'bg-[#fff5f5]' : 'bg-white'; // warna solid fallback dari red-50/20
                                @endphp

                                <tr x-data="{
                                        rowCode: '{{ strtolower(addslashes($item->code_name ?? '')) }}',
                                        rowDetail: '{{ strtolower(addslashes($activeLog->issue_detail ?? '')) }}',
                                        rowPic: '{{ strtolower(addslashes($activeLog->vendor_pic ?? '')) }}',
                                        rowCondition: '{{ $rowCondition }}',
                                        rowStatus: '{{ $rowStatus }}'
                                    }"
                                    x-show="(filterCondition === 'all' || filterCondition === rowCondition) &&
                                            (filterStatus === 'all' || filterStatus === rowStatus || (filterStatus==='resolved' && rowStatus==='none')) &&
                                            (search === '' || rowCode.includes(search.toLowerCase()) || rowDetail.includes(search.toLowerCase()) || rowPic.includes(search.toLowerCase()))"
                                    class="hover:bg-slate-50/80 transition-colors {{ $bgColor }}">

                                    <td class="px-4 py-3 text-center font-medium text-slate-500 sticky left-0 {{ $stickyBg }} z-10 shadow-[1px_0_0_0_#f1f5f9]">
                                        {{ $index + 1 }}
                                    </td>

                                    <td class="px-4 py-3 sticky left-[3rem] {{ $stickyBg }} z-10 shadow-[1px_0_0_0_#f1f5f9]">
                                        <span class="font-bold text-[#003366] text-xs uppercase">{{ $item->code_name }}</span>
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        @if(!$isBroken)
                                            <span class="inline-flex items-center justify-center bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wide border border-emerald-200">Ready</span>
                                        @else
                                            <span class="inline-flex items-center justify-center bg-red-100 text-red-700 px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wide border border-red-200">Down</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3">
                                        <p class="text-[10px] font-medium leading-snug {{ $isBroken ? 'text-red-700' : 'text-slate-400 italic' }} line-clamp-2" title="{{ $activeLog->issue_detail ?? '' }}">
                                            {{ $activeLog ? $activeLog->issue_detail : '-' }}
                                        </p>
                                    </td>

                                    <td class="px-4 py-3 text-center text-[10px] font-medium text-slate-600">{{ $activeLog ? $activeLog->created_at->format('d/m/y') : '-' }}</td>

                                    <td class="px-4 py-3 text-center">
                                        @if($activeLog)
                                            @php
                                                $statusConfig = [
                                                    'reported' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-200', 'label' => 'Dilaporkan'],
                                                    'troubleshooting' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'border' => 'border-orange-200', 'label' => 'T.Shoot'],
                                                    'work_order' => ['bg' => 'bg-yellow-50', 'text' => 'text-yellow-700', 'border' => 'border-yellow-200', 'label' => 'WO / BA'],
                                                    'order_part' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-200', 'label' => 'PO Part'],
                                                    'on_progress' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'label' => 'Progres'],
                                                    'testing' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'label' => 'Com Test'],
                                                    'resolved' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'label' => 'Selesai']
                                                ];
                                                $conf = $statusConfig[$activeLog->repair_status] ?? $statusConfig['reported'];
                                            @endphp
                                            <span class="inline-flex items-center justify-center {{ $conf['bg'] }} {{ $conf['text'] }} border {{ $conf['border'] }} px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wide whitespace-nowrap">
                                                {{ $conf['label'] }}
                                            </span>
                                        @else
                                            <span class="text-slate-300">-</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-center text-[10px] font-medium text-slate-600">{{ $activeLog && $activeLog->troubleshoot_date ? \Carbon\Carbon::parse($activeLog->troubleshoot_date)->format('d/m/y') : '-' }}</td>
                                    <td class="px-4 py-3 text-center text-[10px] font-medium text-slate-600">{{ $activeLog && $activeLog->ba_date ? \Carbon\Carbon::parse($activeLog->ba_date)->format('d/m/y') : '-' }}</td>
                                    <td class="px-4 py-3 text-center text-[10px] font-medium text-slate-600">{{ $activeLog && $activeLog->work_order_date ? \Carbon\Carbon::parse($activeLog->work_order_date)->format('d/m/y') : '-' }}</td>
                                    <td class="px-4 py-3 text-center text-[10px] font-medium text-slate-600">{{ $activeLog && $activeLog->pr_po_date ? \Carbon\Carbon::parse($activeLog->pr_po_date)->format('d/m/y') : '-' }}</td>
                                    <td class="px-4 py-3 text-center text-[10px] font-medium text-slate-600">{{ $activeLog && $activeLog->sparepart_date ? \Carbon\Carbon::parse($activeLog->sparepart_date)->format('d/m/y') : '-' }}</td>
                                    <td class="px-4 py-3 text-center text-[10px] font-medium text-slate-600">{{ $activeLog && $activeLog->start_work_date ? \Carbon\Carbon::parse($activeLog->start_work_date)->format('d/m/y') : '-' }}</td>
                                    <td class="px-4 py-3 text-center text-[10px] font-medium text-slate-600">{{ $activeLog && $activeLog->com_test_date ? \Carbon\Carbon::parse($activeLog->com_test_date)->format('d/m/y') : '-' }}</td>
                                    <td class="px-4 py-3 text-center text-[10px] font-bold text-emerald-600">{{ $activeLog && $activeLog->resolved_date ? \Carbon\Carbon::parse($activeLog->resolved_date)->format('d/m/y') : '-' }}</td>

                                    <td class="px-4 py-3 text-[10px] font-semibold {{ $isBroken ? 'text-[#003366]' : 'text-slate-300 italic' }}">
                                        {{ $activeLog ? $activeLog->vendor_pic : '-' }}
                                    </td>

                                    <td class="px-4 py-3 text-center export-ignore">
                                        @if(!$isBroken)
                                            <!-- Tombol Lapor (Mode Aman) -->
                                            <button @click="selectedAsset = {id: '{{ $item->id }}', code: '{{ $item->code_name }}'}; showReportModal = true;"
                                                    class="inline-flex items-center justify-center bg-white border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 px-3 py-1.5 rounded text-[10px] font-bold transition-colors shadow-sm gap-1.5 w-full">
                                                <i class="fas fa-tools"></i> Lapor Rusak
                                            </button>
                                        @else
                                            <!-- Grup Tombol Update & Dokumen (Mode Breakdown) -->
                                            @if($activeLog)
                                                <div class="flex items-center justify-center gap-1.5">
                                                    <button @click="
                                                            selectedAsset = {id: '{{ $item->id }}', code: '{{ $item->code_name }}'};
                                                            selectedLogId = '{{ $activeLog->id }}';
                                                            currentStatus = '{{ $activeLog->repair_status }}';
                                                            logDates = {
                                                                troubleshoot_date: '{{ $activeLog->troubleshoot_date }}',
                                                                ba_date: '{{ $activeLog->ba_date }}',
                                                                work_order_date: '{{ $activeLog->work_order_date }}',
                                                                pr_po_date: '{{ $activeLog->pr_po_date }}',
                                                                sparepart_date: '{{ $activeLog->sparepart_date }}',
                                                                start_work_date: '{{ $activeLog->start_work_date }}',
                                                                com_test_date: '{{ $activeLog->com_test_date }}',
                                                                resolved_date: '{{ $activeLog->resolved_date }}'
                                                            };
                                                            showUpdateModal = true;
                                                        "
                                                        class="inline-flex items-center justify-center bg-[#0055a4] hover:bg-[#003366] text-white px-2.5 py-1.5 rounded text-[10px] font-bold transition-colors shadow-sm gap-1.5 w-full">
                                                        <i class="fas fa-edit"></i> Update
                                                    </button>

                                                    @if($activeLog->document_proof)
                                                        <a href="{{ asset('storage/'.$activeLog->document_proof) }}" target="_blank" class="w-7 h-7 bg-white text-emerald-600 rounded flex items-center justify-center border border-slate-200 hover:bg-emerald-50 hover:border-emerald-300 transition-colors shrink-0" title="Lampiran Bukti">
                                                            <i class="fas fa-file-pdf text-xs"></i>
                                                        </a>
                                                    @else
                                                        <!-- Placeholder agar lebar sama rata meskipun tak ada file -->
                                                        <div class="w-7 h-7 bg-transparent shrink-0"></div>
                                                    @endif
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(method_exists($infrastructures, 'links') && $infrastructures->hasPages())
                <div class="p-5 border-t border-slate-200 bg-slate-50">
                    {{ $infrastructures->links() }}
                    <p class="text-[10px] text-slate-500 mt-2 font-medium"><i class="fas fa-info-circle text-blue-400"></i> * Fitur pencarian instan di atas hanya menyaring data yang tampil pada halaman aktif ini.</p>
                </div>
                @endif
            </div>

            <div class="flex items-center justify-between text-slate-400 pt-2">
                <p class="text-[10px] font-semibold uppercase tracking-wider">&copy; {{ date('Y') }} Pelindo Command Center</p>
            </div>

        </div>
    </div>

    <!-- Export Logic (Hidden Component) -->
    <x-export-report :infrastructures="$allInfrastructures ?? collect()" :recentBreakdowns="$recentBreakdowns ?? collect()" />
    <x-export-filter-modal />
</x-app-layout>
