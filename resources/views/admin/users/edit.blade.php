<x-app-layout>
    <div class="max-w-3xl mx-auto w-full space-y-8 pb-16 animate-fade-up">
        
        <div class="bg-white p-8 md:p-10 rounded-[2rem] border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-amber-400 to-amber-500"></div>

            <div class="mb-10 text-center">
                <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Edit Akun Operator</h2>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Perbarui profil atau hak akses pegawai</p>
            </div>
            
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-6" x-data="{ role: '{{ $user->role }}' }">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ $user->name }}" class="w-full border-slate-200 bg-slate-50 rounded-xl text-sm font-bold p-4 focus:ring-4 focus:ring-amber-50 focus:border-amber-500 transition-all" required>
                    </div>

                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Alamat Email</label>
                        <input type="email" name="email" value="{{ $user->email }}" class="w-full border-slate-200 bg-slate-50 rounded-xl text-sm font-bold p-4 focus:ring-4 focus:ring-amber-50 focus:border-amber-500 transition-all" required>
                    </div>
                </div>

                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4 italic">Ganti Password (Kosongkan jika tidak ingin ganti)</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <input type="password" name="password" placeholder="Password Baru" class="w-full border-slate-200 bg-white rounded-xl text-sm font-bold p-4 focus:ring-4 focus:ring-amber-50 focus:border-amber-500 transition-all">
                        <input type="password" name="password_confirmation" placeholder="Konfirmasi Password Baru" class="w-full border-slate-200 bg-white rounded-xl text-sm font-bold p-4 focus:ring-4 focus:ring-amber-50 focus:border-amber-500 transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Hak Akses (Role)</label>
                        <select name="role" x-model="role" class="w-full border-slate-200 bg-slate-50 rounded-xl text-sm font-bold p-4 focus:ring-4 focus:ring-amber-50 focus:border-amber-500 transition-all uppercase">
                            <option value="operator" {{ $user->role == 'operator' ? 'selected' : '' }}>Operator Cabang</option>
                            <option value="superadmin" {{ $user->role == 'superadmin' ? 'selected' : '' }}>Super Admin Pusat</option>
                        </select>
                    </div>

                    <div x-show="role === 'operator'" x-transition>
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Penempatan Bagian</label>
                        <select name="entity_id" class="w-full border-slate-200 bg-slate-50 rounded-xl text-sm font-bold p-4 focus:ring-4 focus:ring-amber-50 focus:border-amber-500 transition-all uppercase">
                            <option value="">-- Pilih Lokasi Tugas --</option>
                            @foreach($entities as $entity)
                                <option value="{{ $entity->id }}" {{ $user->entity_id == $entity->id ? 'selected' : '' }}>
                                    {{ $entity->name }} ({{ $entity->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="pt-8 flex flex-col sm:flex-row gap-4">
                    <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white py-4 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-amber-500/20 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Perbarui Akun
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="px-8 py-4 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-black uppercase tracking-widest transition-all text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
