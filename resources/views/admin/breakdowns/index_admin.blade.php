<x-app-layout>
    <div class="max-w-[1600px] mx-auto w-full space-y-8 pb-16 pt-8 px-4 animate-fade-up">
        
        <div class="bg-white p-6 md:p-8 rounded-[2rem] border border-slate-200 shadow-sm relative flex flex-col md:flex-row items-center justify-between gap-6 z-[60]">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-red-600 to-red-400 rounded-t-[2rem]"></div>
            
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center text-2xl border border-red-100 shadow-inner">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-[#003366] uppercase tracking-tight">Riwayat Log Kerusakan</h1>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Pemantauan Kendala Seluruh Cabang Pelindo</p>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
            <div class="relative group z-50">
                <button class="bg-[#003366] hover:bg-[#002244] text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-blue-900/20 transition-all flex items-center gap-2">
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

                <div class="bg-slate-50 px-6 py-3 rounded-xl border border-slate-200 text-center hidden md:block">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Akses Mode</p>
                    <p class="text-sm font-black text-red-600 uppercase">Administrator Pusat</p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-6 py-4 rounded-xl text-sm font-bold shadow-sm flex items-center gap-3 animate-fade-in">
                <i class="fas fa-check-circle text-emerald-500 text-lg"></i> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-[2rem] border border-slate-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto hide-scrollbar">
                <table id="logTable" class="w-full text-left border-collapse whitespace-nowrap min-w-[1600px]">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-[9px] font-black uppercase tracking-[0.15em] border-b border-slate-200">
                            <th class="px-4 py-5 w-12 text-center sticky left-0 bg-slate-50 z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">NO</th>
                            <th class="px-4 py-5 sticky left-12 bg-slate-50 z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">Identitas Alat</th>
                            <th class="px-4 py-5">Lokasi Entitas</th>
                            <th class="px-4 py-5">Detail Laporan</th>
                            <th class="px-4 py-5 text-center">Status Akhir</th>
                            <th class="px-4 py-5 text-center">Tgl Lapor</th>
                            <th class="px-4 py-5 text-center">T.Shoot</th>
                            <th class="px-4 py-5 text-center">BA</th>
                            <th class="px-4 py-5 text-center">Work Order</th>
                            <th class="px-4 py-5 text-center">PR/PO</th>
                            <th class="px-4 py-5 text-center">S.Part Site</th>
                            <th class="px-4 py-5 text-center">Mulai Kerja</th>
                            <th class="px-4 py-5 text-center">Com Test</th>
                            <th class="px-4 py-5 text-center">Selesai</th>
                            <th class="px-4 py-5">PIC/Vendor</th>
                            <th class="px-4 py-5 text-right export-ignore">Aksi / Bukti</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($logs as $index => $log)
                        <tr class="hover:bg-red-50/30 transition-colors group">
                            <td class="px-4 py-4 text-center text-slate-400 font-bold text-xs sticky left-0 bg-white group-hover:bg-red-50/90 z-10">{{ $index + 1 }}</td>
                            <td class="px-4 py-4 sticky left-12 bg-white group-hover:bg-red-50/90 z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
                                <span class="font-black text-[#003366] text-[11px] uppercase">{{ $log->infrastructure->code_name ?? 'TERHAPUS' }}</span>
                            </td>

                            <td class="px-4 py-4 text-[10px] font-bold text-slate-600 uppercase tracking-tight">
                                {{ $log->infrastructure->entity->name ?? 'TERHAPUS' }}
                            </td>
                            <td class="px-4 py-4 text-[10px] text-slate-700 font-medium max-w-[150px] truncate italic" title="{{ $log->issue_detail }}">
                                "{{ $log->issue_detail }}"
                            </td>
                            <td class="px-4 py-4 text-center">
                                @if($log->repair_status == 'resolved')
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-1 rounded text-[8px] font-black uppercase tracking-widest">Ready (Selesai)</span>
                                @else
                                    <span class="bg-red-50 text-red-600 border border-red-200 px-2 py-1 rounded text-[8px] font-black uppercase tracking-widest">{{ str_replace('_', ' ', $log->repair_status) }}</span>
                                @endif
                            </td>

                            <td class="px-4 py-4 text-center text-[10px] font-bold text-[#0055a4]">{{ $log->created_at->format('d/m/y') }}</td>
                            <td class="px-4 py-4 text-center text-[10px] font-bold text-slate-600">{{ $log->troubleshoot_date ? \Carbon\Carbon::parse($log->troubleshoot_date)->format('d/m/y') : '-' }}</td>
                            <td class="px-4 py-4 text-center text-[10px] font-bold text-slate-600">{{ $log->ba_date ? \Carbon\Carbon::parse($log->ba_date)->format('d/m/y') : '-' }}</td>
                            <td class="px-4 py-4 text-center text-[10px] font-bold text-slate-600">{{ $log->work_order_date ? \Carbon\Carbon::parse($log->work_order_date)->format('d/m/y') : '-' }}</td>
                            <td class="px-4 py-4 text-center text-[10px] font-bold text-slate-600">{{ $log->pr_po_date ? \Carbon\Carbon::parse($log->pr_po_date)->format('d/m/y') : '-' }}</td>
                            <td class="px-4 py-4 text-center text-[10px] font-bold text-slate-600">{{ $log->sparepart_date ? \Carbon\Carbon::parse($log->sparepart_date)->format('d/m/y') : '-' }}</td>
                            <td class="px-4 py-4 text-center text-[10px] font-bold text-slate-600">{{ $log->start_work_date ? \Carbon\Carbon::parse($log->start_work_date)->format('d/m/y') : '-' }}</td>
                            <td class="px-4 py-4 text-center text-[10px] font-bold text-slate-600">{{ $log->com_test_date ? \Carbon\Carbon::parse($log->com_test_date)->format('d/m/y') : '-' }}</td>
                            <td class="px-4 py-4 text-center text-[10px] font-black text-emerald-600">{{ $log->resolved_date ? \Carbon\Carbon::parse($log->resolved_date)->format('d/m/y') : '-' }}</td>

                            <td class="px-4 py-4 text-[10px] font-black text-slate-500 uppercase">
                                {{ $log->vendor_pic ?? 'Internal' }}
                                @if($log->updated_by)
                                    <br><span class="text-[8px] text-slate-400 font-normal lowercase tracking-widest mt-1 inline-block">by {{ $log->updatedBy->name ?? 'System' }}</span>
                                @endif
                            </td>
                            
                            <td class="px-4 py-4 text-right export-ignore">
                                <div class="flex items-center justify-end gap-2">
                                    @if($log->document_proof)
                                        <a href="{{ asset('storage/'.$log->document_proof) }}" target="_blank" class="w-7 h-7 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded flex items-center justify-center border border-emerald-200 transition-colors" title="Lihat Dokumen Bukti">
                                            <i class="fas fa-file-pdf text-[10px]"></i>
                                        </a>
                                    @endif
                                    
                                    <form action="{{ route('admin.breakdowns.destroy', $log->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus log ini secara permanen?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-7 h-7 bg-white border border-slate-200 text-slate-400 hover:text-red-600 hover:bg-red-50 hover:border-red-200 rounded flex items-center justify-center transition-all shadow-sm">
                                            <i class="fas fa-trash-alt text-[10px]"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="16" class="px-8 py-32 text-center bg-slate-50/50">
                                <div class="flex flex-col items-center justify-center opacity-40">
                                    <i class="fas fa-shield-check text-6xl mb-4 text-emerald-500"></i>
                                    <p class="font-black uppercase tracking-[0.3em] text-sm text-slate-800">Tidak Ada Log Kerusakan</p>
                                    <p class="text-[10px] mt-2 font-bold uppercase tracking-widest text-slate-500">Sistem bersih, tidak ada riwayat yang tercatat.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                {{ $logs->links() }}
            </div>
        </div>
    </div>

    <x-export-report :infrastructures="$allInfrastructures" :recentBreakdowns="$recentBreakdowns" />
    <x-export-filter-modal />

    <style>
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</x-app-layout>
