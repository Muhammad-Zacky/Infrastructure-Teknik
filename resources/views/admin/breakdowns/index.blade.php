<x-app-layout>
    <div class="max-w-[1600px] mx-auto w-full space-y-8 pb-16 animate-fade-up">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 bg-red-50 text-red-600 rounded-xl flex items-center justify-center text-2xl border border-red-100 shadow-inner">
                    <i class="fas fa-tools"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-[#003366] uppercase tracking-tight">Log Kerusakan Alat</h1>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Pemantauan progres perbaikan infrastruktur secara real-time</p>
                </div>
            </div>
            <a href="{{ route('admin.breakdowns.create') }}" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3.5 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-red-900/20 transition-all flex items-center gap-2 group">
                <i class="fas fa-plus text-white group-hover:rotate-90 transition-transform duration-300"></i> Buat Laporan Baru
            </a>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-6 py-4 rounded-xl text-sm font-bold shadow-sm flex items-center gap-3 animate-fade-up">
                <i class="fas fa-check-circle text-emerald-500 text-lg"></i> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-[2rem] border border-slate-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#001e3c] text-white text-[10px] font-black uppercase tracking-[0.2em] border-b border-slate-700">
                            <th class="px-8 py-6 w-16 text-center">NO</th>
                            <th class="px-8 py-5">Unit ID</th>
                            <th class="px-8 py-5">Lokasi Entitas</th>
                            <th class="px-8 py-5">Detail Kendala</th>
                            <th class="px-8 py-5 text-center">Status Kerja</th>
                            <th class="px-8 py-5">PIC/Vendor</th>
                            <th class="px-8 py-5 text-right">Opsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($logs as $index => $log)
                        <tr class="hover:bg-blue-50/30 transition-colors group">
                            <td class="px-8 py-6 text-center text-slate-400 font-bold text-xs">{{ $index + 1 }}</td>
                            <td class="px-8 py-6">
                                <span class="font-black text-[#003366] text-xs uppercase px-3 py-1.5 bg-slate-100 rounded-lg border border-slate-200">
                                    {{ $log->infrastructure->code_name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-xs font-bold text-slate-500 uppercase tracking-tight">
                                {{ $log->infrastructure->entity->name ?? '-' }}
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-xs text-slate-600 font-medium max-w-xs leading-relaxed">{{ $log->issue_detail }}</p>
                                <div class="flex items-center gap-2 mt-2">
                                    <i class="far fa-clock text-[10px] text-slate-400"></i>
                                    <span class="text-[9px] text-slate-400 font-bold uppercase">{{ $log->created_at->format('d M Y | H:i') }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <form action="{{ route('admin.breakdowns.update', $log->id) }}" method="POST" class="inline-flex">
                                    @csrf @method('PUT')
                                    <select name="repair_status" onchange="this.form.submit()" 
                                        class="text-[9px] font-black uppercase tracking-widest rounded-lg border-slate-200 py-1.5 px-3 focus:ring-2 focus:ring-[#003366] cursor-pointer transition-all
                                        {{ $log->repair_status == 'resolved' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                                        <option value="reported" {{ $log->repair_status == 'reported' ? 'selected' : '' }}>Reported</option>
                                        <option value="order_part" {{ $log->repair_status == 'order_part' ? 'selected' : '' }}>Order Part</option>
                                        <option value="on_progress" {{ $log->repair_status == 'on_progress' ? 'selected' : '' }}>On Progress</option>
                                        <option value="resolved" {{ $log->repair_status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-2">
                                    <div class="w-1.5 h-1.5 rounded-full bg-slate-400"></div>
                                    <span class="text-xs font-black text-slate-700 uppercase">{{ $log->vendor_pic ?? 'Internal' }}</span>
                                </div>
                                @if($log->updated_by)
                                    <div class="flex items-center gap-1.5 mt-2 opacity-60">
                                        <i class="fas fa-user-edit text-[8px] text-slate-400"></i>
                                        <p class="text-[8px] font-black tracking-widest text-slate-400 uppercase">{{ $log->updatedBy->name ?? 'System' }}</p>
                                    </div>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    
                                    <a href="{{ route('admin.breakdowns.edit', $log->id) }}" 
                                       class="w-8 h-8 bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white rounded-lg flex items-center justify-center transition-all shadow-sm" 
                                       title="Edit Detail Laporan">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>

                                    <form action="{{ route('admin.breakdowns.destroy', $log->id) }}" method="POST" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus log kerusakan ini? Status alat akan dikembalikan menjadi Ready.');">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-8 h-8 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg flex items-center justify-center transition-all shadow-sm" 
                                                title="Hapus Laporan">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-8 py-24 text-center">
                                <div class="flex flex-col items-center justify-center opacity-30">
                                    <i class="fas fa-clipboard-check text-6xl mb-4"></i>
                                    <p class="font-black uppercase tracking-[0.3em] text-sm text-slate-800">No Active Incident Reports</p>
                                    <p class="text-[10px] mt-2 font-bold uppercase tracking-widest text-slate-500">Seluruh alat saat ini beroperasi secara normal</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="flex items-center justify-between px-4 text-slate-400">
            <p class="text-[9px] font-bold uppercase tracking-widest">&copy; 2026 Pelindo Regional Group</p>
            <div class="flex gap-4">
                <div class="flex items-center gap-1.5">
                    <div class="w-2 h-2 rounded-full bg-red-500"></div>
                    <span class="text-[9px] font-bold uppercase">Breakdown</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                    <span class="text-[9px] font-bold uppercase">Resolved</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
