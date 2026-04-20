<x-app-layout>
    <div class="max-w-[1600px] mx-auto w-full space-y-8 pb-16 pt-8 px-4 animate-fade-up">
        
        <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-red-600 to-red-400"></div>
            
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center text-2xl border border-red-100 shadow-inner">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-[#003366] uppercase tracking-tight">Riwayat Log Kerusakan</h1>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Pemantauan Kendala Seluruh Cabang Pelindo</p>
                </div>
            </div>
            
            <div class="bg-slate-50 px-6 py-3 rounded-xl border border-slate-200 text-center">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Akses Mode</p>
                <p class="text-sm font-black text-red-600 uppercase">Administrator Pusat</p>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-6 py-4 rounded-xl text-sm font-bold shadow-sm flex items-center gap-3 animate-fade-in">
                <i class="fas fa-check-circle text-emerald-500 text-lg"></i> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-[2rem] border border-slate-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] border-b border-slate-200">
                            <th class="px-8 py-6 w-16 text-center">NO</th>
                            <th class="px-8 py-5">Identitas Alat</th>
                            <th class="px-8 py-5">Lokasi Entitas</th>
                            <th class="px-8 py-5">Detail Laporan</th>
                            <th class="px-8 py-5 text-center">Tahap Pekerjaan</th>
                            <th class="px-8 py-5 text-center">PIC / Vendor</th>
                            <th class="px-8 py-5 text-right">Manajemen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($logs as $index => $log)
                        <tr class="hover:bg-red-50/30 transition-colors group">
                            <td class="px-8 py-6 text-center text-slate-400 font-bold text-xs">{{ $index + 1 }}</td>
                            <td class="px-8 py-6">
                                <span class="font-black text-[#003366] text-xs uppercase px-3 py-1.5 bg-slate-50 rounded border border-slate-200 shadow-sm">
                                    {{ $log->infrastructure->code_name ?? 'TERHAPUS' }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-xs font-bold text-slate-600 uppercase tracking-tight">
                                <i class="fas fa-map-marker-alt text-slate-300 mr-1"></i> {{ $log->infrastructure->entity->name ?? 'TERHAPUS' }}
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-xs text-slate-700 font-medium max-w-xs leading-relaxed italic">"{{ $log->issue_detail }}"</p>
                                <div class="flex items-center gap-1.5 mt-2">
                                    <i class="far fa-clock text-[10px] text-slate-400"></i>
                                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">{{ $log->created_at->format('d M Y | H:i') }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                @if($log->repair_status == 'resolved')
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1.5 rounded text-[9px] font-black uppercase tracking-widest">Selesai (Ready)</span>
                                @else
                                    <span class="bg-red-50 text-red-600 border border-red-200 px-3 py-1.5 rounded text-[9px] font-black uppercase tracking-widest">{{ str_replace('_', ' ', $log->repair_status) }}</span>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-center">
                                <span class="text-[10px] font-black text-slate-500 uppercase bg-slate-50 px-2 py-1 rounded border border-slate-200">{{ $log->vendor_pic ?? 'Internal' }}</span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <form action="{{ route('admin.breakdowns.destroy', $log->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus log ini secara permanen?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 bg-white border border-slate-200 text-slate-400 hover:text-red-600 hover:bg-red-50 hover:border-red-200 rounded-lg flex items-center justify-center transition-all shadow-sm">
                                        <i class="fas fa-trash-alt text-[10px]"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-8 py-32 text-center bg-slate-50/50">
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
        </div>
    </div>
</x-app-layout>
