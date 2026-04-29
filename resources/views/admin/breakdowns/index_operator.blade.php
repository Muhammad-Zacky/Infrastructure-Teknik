<x-app-layout>
    <div class="min-h-screen bg-slate-50 -mt-8 pt-12 pb-16">
        <div class="max-w-[1600px] mx-auto w-full px-4 space-y-8 animate-fade-up" 
             x-data="{ 
                 showReportModal: false, 
                 showUpdateModal: false, 
                 selectedAsset: null,
                 selectedLogId: null,
                 currentStatus: '',
                 logDates: {}
             }">

            <template x-teleport="body">
                <div x-show="showReportModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" x-transition style="display: none;">
                    <div @click.away="showReportModal = false" class="bg-white rounded-[2rem] shadow-xl max-w-md w-full overflow-hidden border border-slate-200">
                        <div class="bg-red-50 p-6 border-b border-red-100 flex items-center gap-4">
                            <div class="w-12 h-12 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-xl"><i class="fas fa-tools"></i></div>
                            <div>
                                <h2 class="text-lg font-black text-red-700 uppercase tracking-tight">Lapor Kerusakan</h2>
                                <p class="text-[10px] font-bold text-red-500 uppercase" x-text="selectedAsset ? selectedAsset.code : ''"></p>
                            </div>
                        </div>
                        <form action="{{ route('admin.breakdowns.store') }}" method="POST" class="p-6 space-y-4">
                            @csrf
                            <input type="hidden" name="infrastructure_id" :value="selectedAsset ? selectedAsset.id : ''">
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Detail Kerusakan / Kendala</label>
                                <textarea name="issue_detail" rows="3" class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm font-medium focus:ring-red-100 focus:border-red-500 transition-all" required></textarea>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">PIC / Vendor Pekerja</label>
                                <input type="text" name="vendor_pic" class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm font-bold focus:ring-red-100 focus:border-red-500 transition-all" required>
                            </div>
                            <div class="pt-4 flex gap-3">
                                <button type="button" @click="showReportModal = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-black uppercase tracking-widest transition-all">Batal</button>
                                <button type="submit" class="flex-1 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-md">Laporkan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>

            <template x-teleport="body">
                <div x-show="showUpdateModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" x-transition style="display: none;">
                    <div @click.away="showUpdateModal = false" class="bg-white rounded-[2rem] shadow-xl max-w-2xl w-full overflow-hidden border border-slate-200 max-h-[90vh] flex flex-col">
                        <div class="bg-[#003366] p-6 border-b border-[#002244] flex items-center justify-between text-white shrink-0">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center text-xl"><i class="fas fa-calendar-check"></i></div>
                                <div>
                                    <h2 class="text-lg font-black uppercase tracking-tight">Timeline & Progres Kesiapan</h2>
                                    <p class="text-[10px] font-bold text-blue-200 uppercase" x-text="selectedAsset ? selectedAsset.code : ''"></p>
                                </div>
                            </div>
                            <button @click="showUpdateModal = false" type="button" class="w-8 h-8 rounded-full bg-white/10 hover:bg-red-500 flex items-center justify-center transition-colors"><i class="fas fa-times"></i></button>
                        </div>
                        
                        <form :action="`/admin/breakdowns/${selectedLogId}`" method="POST" enctype="multipart/form-data" class="p-6 space-y-6 overflow-y-auto hide-scrollbar">
                            @csrf @method('PUT')
                            
                            <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                                <label class="block text-[10px] font-black text-[#003366] uppercase tracking-widest mb-2">Pilih Tahap Pekerjaan Terkini</label>
                                <select name="repair_status" x-model="currentStatus" class="w-full bg-white border-slate-200 rounded-xl text-sm font-bold focus:ring-[#003366] focus:border-[#003366] transition-all uppercase" required>
                                    <option value="troubleshooting">Identifikasi / Trouble Shoot</option>
                                    <option value="work_order">Berita Acara / Work Order</option>
                                    <option value="order_part">Proses PR / PO / Order Part</option>
                                    <option value="on_progress">Proses Pekerjaan Berlangsung</option>
                                    <option value="testing">Commissioning Test</option>
                                    <option value="resolved" class="text-emerald-600 font-black">✔️ SELESAI (ALAT READY)</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Tgl Trouble Shoot</label>
                                    <input type="date" name="troubleshoot_date" :value="logDates.troubleshoot_date" class="w-full bg-slate-50 border-slate-200 rounded-lg text-xs font-bold focus:ring-[#0055a4]">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Tgl Berita Acara</label>
                                    <input type="date" name="ba_date" :value="logDates.ba_date" class="w-full bg-slate-50 border-slate-200 rounded-lg text-xs font-bold focus:ring-[#0055a4]">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Tgl Work Order</label>
                                    <input type="date" name="work_order_date" :value="logDates.work_order_date" class="w-full bg-slate-50 border-slate-200 rounded-lg text-xs font-bold focus:ring-[#0055a4]">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Tgl PR / PO</label>
                                    <input type="date" name="pr_po_date" :value="logDates.pr_po_date" class="w-full bg-slate-50 border-slate-200 rounded-lg text-xs font-bold focus:ring-[#0055a4]">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Tgl Spare Part On Site</label>
                                    <input type="date" name="sparepart_date" :value="logDates.sparepart_date" class="w-full bg-slate-50 border-slate-200 rounded-lg text-xs font-bold focus:ring-[#0055a4]">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Tgl Mulai Pekerjaan</label>
                                    <input type="date" name="start_work_date" :value="logDates.start_work_date" class="w-full bg-slate-50 border-slate-200 rounded-lg text-xs font-bold focus:ring-[#0055a4]">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Tgl Com Test</label>
                                    <input type="date" name="com_test_date" :value="logDates.com_test_date" class="w-full bg-slate-50 border-slate-200 rounded-lg text-xs font-bold focus:ring-[#0055a4]">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-emerald-600 uppercase tracking-widest mb-1">Tgl Selesai Pekerjaan</label>
                                    <input type="date" name="resolved_date" :value="logDates.resolved_date" class="w-full bg-emerald-50 border-emerald-200 rounded-lg text-xs font-bold text-emerald-700 focus:ring-emerald-500">
                                </div>
                            </div>

                            <div class="border-t border-slate-100 pt-4">
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Lampiran Bukti (Opsional) <span class="text-xs font-normal normal-case text-slate-400">- PDF/JPG maks 5MB</span></label>
                                <input type="file" name="document_proof" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-[#003366] file:text-white hover:file:bg-[#001e3c] transition-all cursor-pointer bg-slate-50 border border-slate-200 rounded-xl p-2">
                            </div>

                            <div class="pt-4 flex gap-3">
                                <button type="submit" class="w-full py-4 bg-[#003366] hover:bg-[#002244] text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-md">Simpan Data Progres</button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>

            <div class="bg-white p-6 md:p-8 rounded-[2rem] border border-slate-200 shadow-sm relative flex flex-col md:flex-row items-center justify-between gap-6 z-[60]">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-[#003366] to-[#0055a4] rounded-t-[2rem]"></div>
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 bg-blue-50 text-[#0055a4] rounded-2xl flex items-center justify-center text-2xl border border-blue-100">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-[#003366] uppercase tracking-tight">Laporan Kesiapan Alat</h1>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Panel Kendali Tindak Lanjut Operator</p>
                    </div>
                </div>

                <div class="relative group z-50">
                    <button class="bg-[#003366] hover:bg-[#002244] text-white px-6 py-3.5 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-blue-900/20 transition-all flex items-center gap-2">
                        <i class="fas fa-file-export"></i> Export <i class="fas fa-chevron-down ml-1 text-[10px]"></i>
                    </button>
                    <div class="absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-xl border border-slate-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all transform origin-top-right scale-95 group-hover:scale-100 flex flex-col overflow-hidden">
                        <button onclick="openExportModal('pdf')" class="flex items-center gap-3 px-4 py-3 text-left hover:bg-slate-50 text-slate-700 text-xs font-black uppercase tracking-widest transition-colors border-b border-slate-50">
                            <i class="fas fa-file-pdf text-red-500 text-sm"></i> Format PDF
                        </button>
                        <button onclick="openExportModal('excel')" class="flex items-center gap-3 px-4 py-3 text-left hover:bg-slate-50 text-slate-700 text-xs font-black uppercase tracking-widest transition-colors">
                            <i class="fas fa-file-excel text-emerald-500 text-sm"></i> Format Excel
                        </button>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-6 py-4 rounded-xl text-sm font-bold shadow-sm flex items-center gap-3">
                    <i class="fas fa-check-circle text-emerald-500"></i> {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-[2rem] border border-slate-200 overflow-hidden shadow-sm">
                <div class="overflow-x-auto hide-scrollbar">
                    <table id="operatorTable" class="w-full text-left border-collapse whitespace-nowrap min-w-[1500px]">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 text-[9px] font-black uppercase tracking-[0.1em] border-b border-slate-200">
                                <th class="px-4 py-5 w-12 text-center sticky left-0 bg-slate-50 z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">NO</th>
                                <th class="px-4 py-5 sticky left-12 bg-slate-50 z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">Alat</th>
                                <th class="px-4 py-5 text-center">Status</th>
                                <th class="px-4 py-5">Kendala</th>
                                <th class="px-4 py-5 text-center">Tgl BD</th>
                                <th class="px-4 py-5 text-center">Status Kesiapan</th>
                                <th class="px-4 py-5 text-center">T.Shoot</th>
                                <th class="px-4 py-5 text-center">BA</th>
                                <th class="px-4 py-5 text-center">Work Order</th>
                                <th class="px-4 py-5 text-center">PR/PO</th>
                                <th class="px-4 py-5 text-center">S.Part Site</th>
                                <th class="px-4 py-5 text-center">Mulai Kerja</th>
                                <th class="px-4 py-5 text-center">Com Test</th>
                                <th class="px-4 py-5 text-center">Selesai</th>
                                <th class="px-4 py-5">PIC</th>
                                <th class="px-4 py-5 text-center export-ignore">Aksi / Bukti</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($infrastructures as $index => $item)
                                @php
                                    $isBroken = $item->status === 'breakdown';
                                    $activeLog = $isBroken ? ($activeBreakdowns[$item->id] ?? null) : null;
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors {{ $isBroken ? 'bg-red-50/10' : '' }}">
                                    <td class="px-4 py-4 text-center text-slate-400 font-bold text-xs sticky left-0 {{ $isBroken ? 'bg-red-50/90' : 'bg-white' }} z-10">{{ $index + 1 }}</td>
                                    
                                    <td class="px-4 py-4 sticky left-12 {{ $isBroken ? 'bg-red-50/90' : 'bg-white' }} z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
                                        <span class="font-black text-[#003366] text-[11px] uppercase">{{ $item->code_name }}</span>
                                    </td>
                                    
                                    <td class="px-4 py-4 text-center">
                                        @if(!$isBroken)
                                            <span class="bg-emerald-500 text-white px-2 py-1 rounded text-[8px] font-black uppercase tracking-widest shadow-sm">Ready</span>
                                        @else
                                            <span class="bg-red-600 text-white px-2 py-1 rounded text-[8px] font-black uppercase tracking-widest shadow-sm">BD</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-4 text-[10px] font-bold {{ $isBroken ? 'text-red-700' : 'text-slate-300 italic' }} max-w-[150px] truncate" title="{{ $activeLog->issue_detail ?? '' }}">
                                        {{ $activeLog ? $activeLog->issue_detail : '-' }}
                                    </td>

                                    <td class="px-4 py-4 text-center text-[10px] font-bold text-slate-600">{{ $activeLog ? $activeLog->created_at->format('d/m/y') : '-' }}</td>

                                    <td class="px-4 py-4 text-center">
                                        @if($activeLog)
                                            <span class="px-2 py-1 rounded text-[8px] font-black uppercase tracking-widest bg-slate-200 text-slate-700 border border-slate-300">
                                                {{ str_replace('_', ' ', $activeLog->repair_status) }}
                                            </span>
                                        @else
                                            <span class="text-slate-300">-</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-4 text-center text-[10px] font-bold text-slate-600">{{ $activeLog && $activeLog->troubleshoot_date ? \Carbon\Carbon::parse($activeLog->troubleshoot_date)->format('d/m/y') : '-' }}</td>
                                    <td class="px-4 py-4 text-center text-[10px] font-bold text-slate-600">{{ $activeLog && $activeLog->ba_date ? \Carbon\Carbon::parse($activeLog->ba_date)->format('d/m/y') : '-' }}</td>
                                    <td class="px-4 py-4 text-center text-[10px] font-bold text-slate-600">{{ $activeLog && $activeLog->work_order_date ? \Carbon\Carbon::parse($activeLog->work_order_date)->format('d/m/y') : '-' }}</td>
                                    <td class="px-4 py-4 text-center text-[10px] font-bold text-slate-600">{{ $activeLog && $activeLog->pr_po_date ? \Carbon\Carbon::parse($activeLog->pr_po_date)->format('d/m/y') : '-' }}</td>
                                    <td class="px-4 py-4 text-center text-[10px] font-bold text-slate-600">{{ $activeLog && $activeLog->sparepart_date ? \Carbon\Carbon::parse($activeLog->sparepart_date)->format('d/m/y') : '-' }}</td>
                                    <td class="px-4 py-4 text-center text-[10px] font-bold text-slate-600">{{ $activeLog && $activeLog->start_work_date ? \Carbon\Carbon::parse($activeLog->start_work_date)->format('d/m/y') : '-' }}</td>
                                    <td class="px-4 py-4 text-center text-[10px] font-bold text-slate-600">{{ $activeLog && $activeLog->com_test_date ? \Carbon\Carbon::parse($activeLog->com_test_date)->format('d/m/y') : '-' }}</td>
                                    <td class="px-4 py-4 text-center text-[10px] font-black text-emerald-600">{{ $activeLog && $activeLog->resolved_date ? \Carbon\Carbon::parse($activeLog->resolved_date)->format('d/m/y') : '-' }}</td>

                                    <td class="px-4 py-4 text-[10px] font-black {{ $isBroken ? 'text-[#003366]' : 'text-slate-300 italic' }}">
                                        {{ $activeLog ? $activeLog->vendor_pic : '-' }}
                                    </td>

                                    <td class="px-4 py-4 text-center export-ignore">
                                        @if(!$isBroken)
                                            <button @click="selectedAsset = {id: '{{ $item->id }}', code: '{{ $item->code_name }}'}; showReportModal = true;" 
                                                    class="bg-white border border-red-200 text-red-500 hover:bg-red-50 px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest shadow-sm">
                                                Lapor Rusak
                                            </button>
                                        @else
                                            @if($activeLog)
                                                <div class="flex items-center justify-center gap-2">
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
                                                        class="bg-[#003366] hover:bg-[#002244] text-white px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest shadow-md">
                                                        Update Progres
                                                    </button>

                                                    @if($activeLog->document_proof)
                                                        <a href="{{ asset('storage/'.$activeLog->document_proof) }}" target="_blank" class="w-7 h-7 bg-emerald-50 text-emerald-600 rounded flex items-center justify-center border border-emerald-200 hover:bg-emerald-600 hover:text-white transition-colors" title="Lihat Lampiran">
                                                            <i class="fas fa-file-download text-[10px]"></i>
                                                        </a>
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
                
                <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                    {{ $infrastructures->links() }}
                </div>
            </div>
            
        </div>
    </div>

    <x-export-report :infrastructures="$allInfrastructures" :recentBreakdowns="$recentBreakdowns" />
    <x-export-filter-modal />
</x-app-layout>
