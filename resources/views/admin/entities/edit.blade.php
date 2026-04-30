<x-app-layout>
    <div class="max-w-3xl mx-auto w-full space-y-8 pb-16 animate-fade-up">
        
        <div class="bg-white p-8 md:p-10 rounded-[2rem] border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-[#003366] to-[#0055a4]"></div>

            <div class="mb-8">
                <h2 class="text-2xl font-black text-[#003366] uppercase tracking-tight">Edit Data Entitas</h2>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Perbarui informasi cabang / lokasi</p>
            </div>
            
            <form action="{{ route('admin.entities.update', $entity->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT') <div>
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2 ml-1">Nama Entitas Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $entity->name) }}" class="w-full border-slate-200 bg-slate-50 rounded-xl text-sm font-bold p-4 focus:ring-4 focus:ring-blue-50 focus:border-[#003366] transition-all uppercase" required>
                    @error('name') <p class="text-red-500 text-xs mt-2 font-bold ml-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2 ml-1">Kode Internal (Singkatan)</label>
                    <input type="text" name="code" value="{{ old('code', $entity->code) }}" class="w-full border-slate-200 bg-slate-50 rounded-xl text-sm font-bold p-4 focus:ring-4 focus:ring-blue-50 focus:border-[#003366] transition-all uppercase" required>
                    @error('code') <p class="text-red-500 text-xs mt-2 font-bold ml-1">{{ $message }}</p> @enderror
                </div>

                <div class="pt-8 border-t border-slate-100 flex flex-col sm:flex-row gap-4">
                    <button type="submit" class="flex-1 bg-[#003366] hover:bg-[#001e3c] text-white py-4 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-blue-900/20 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-save text-white"></i> Perbarui Data
                    </button>
                    <a href="{{ route('admin.entities.index') }}" class="px-8 py-4 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-black uppercase tracking-widest transition-all text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
