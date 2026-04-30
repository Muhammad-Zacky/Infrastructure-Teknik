<div id="exportFilterModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-[2rem] shadow-xl max-w-lg w-full overflow-hidden border border-slate-200 transform scale-95 transition-transform duration-300" id="exportFilterModalContent">
        <div class="bg-[#003366] p-6 border-b border-[#002244] flex items-center justify-between text-white">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center text-xl"><i class="fas fa-filter"></i></div>
                <div>
                    <h2 class="text-lg font-black uppercase tracking-tight">Filter Laporan</h2>
                    <p class="text-[10px] font-bold text-blue-200 uppercase tracking-widest" id="exportFormatText">Format PDF</p>
                </div>
            </div>
            <button type="button" onclick="closeExportModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-red-500 flex items-center justify-center transition-colors"><i class="fas fa-times"></i></button>
        </div>
        
        <form action="{{ route('admin.export.process') }}" method="GET" class="p-6 space-y-5">
            <input type="hidden" name="format" id="exportFormatInput" value="pdf">
            
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3"><i class="fas fa-calendar-alt mr-1"></i> Rentang Tanggal Insiden (Opsional)</label>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-1">Dari Tanggal</p>
                        <input type="date" name="start_date" class="w-full bg-white border-slate-200 rounded-lg text-xs font-bold focus:ring-[#003366] transition-all text-slate-600">
                    </div>
                    <div>
                        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-1">Sampai Tanggal</p>
                        <input type="date" name="end_date" class="w-full bg-white border-slate-200 rounded-lg text-xs font-bold focus:ring-[#003366] transition-all text-slate-600">
                    </div>
                </div>
            </div>

            @if(auth()->user()->role === 'superadmin')
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2"><i class="fas fa-building mr-1"></i> Terminal / Cabang</label>
                <select name="entity_id" class="w-full bg-slate-50 border-slate-200 rounded-xl text-xs font-bold focus:ring-[#003366] transition-all text-slate-700">
                    <option value="">-- Semua Cabang / Keseluruhan --</option>
                    @foreach(\App\Models\Entity::all() as $ent)
                        <option value="{{ $ent->id }}">{{ $ent->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2"><i class="fas fa-layer-group mr-1"></i> Kategori Aset</label>
                    <select name="category" class="w-full bg-slate-50 border-slate-200 rounded-xl text-xs font-bold focus:ring-[#003366] transition-all text-slate-700">
                        <option value="">-- Semua Kategori --</option>
                        <option value="equipment">Peralatan</option>
                        <option value="facility">Fasilitas</option>
                        <option value="utility">Utilitas</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2"><i class="fas fa-heart pulse mr-1"></i> Status Kondisi</label>
                    <select name="status" class="w-full bg-slate-50 border-slate-200 rounded-xl text-xs font-bold focus:ring-[#003366] transition-all text-slate-700">
                        <option value="">-- Semua Status --</option>
                        <option value="available">Tersedia (Ready)</option>
                        <option value="breakdown">Rusak (Breakdown)</option>
                    </select>
                </div>
            </div>

            <div class="pt-2 flex gap-3">
                <button type="button" onclick="closeExportModal()" class="flex-1 py-3.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-sm">Batal</button>
                <button type="submit" onclick="showLoadingState(this)" class="flex-1 py-3.5 bg-[#003366] hover:bg-[#002244] text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-blue-900/20 flex items-center justify-center gap-2">
                    <i class="fas fa-download"></i> <span id="btnText">Proses Export</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openExportModal(format) {
        const modal = document.getElementById('exportFilterModal');
        const content = document.getElementById('exportFilterModalContent');
        
        document.getElementById('exportFormatInput').value = format;
        document.getElementById('exportFormatText').innerText = format === 'pdf' ? 'DOKUMEN FORMAT PDF' : 'SPREADSHEET FORMAT EXCEL';
        
        modal.classList.remove('hidden');
        // trigger animation
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);
    }

    function closeExportModal() {
        const modal = document.getElementById('exportFilterModal');
        const content = document.getElementById('exportFilterModalContent');
        
        modal.classList.add('opacity-0');
        content.classList.add('scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
    
    function showLoadingState(btn) {
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyiapkan...';
        btn.classList.add('opacity-80', 'cursor-not-allowed');
    }
</script>
