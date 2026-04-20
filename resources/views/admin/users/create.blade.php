<x-app-layout>
    <div class="max-w-3xl mx-auto w-full space-y-8 pb-16 animate-fade-up">
        
        <div class="bg-white p-8 md:p-10 rounded-[2rem] border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-[#003366] to-[#0055a4]"></div>

            <div class="mb-10 text-center">
                <h2 class="text-2xl font-black text-[#003366] uppercase tracking-tight">Registrasi Akun Baru</h2>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Daftarkan personil baru ke dalam sistem portal</p>
            </div>

            @if (session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-3">
                    <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                    <p class="text-sm font-bold text-emerald-700">{{ session('success') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-red-500 text-lg mt-0.5"></i>
                    <div>
                        <p class="text-sm font-bold text-red-700">Penyimpanan gagal! Periksa kembali isian form Anda.</p>
                    </div>
                </div>
            @endif
            
            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6" x-data="{ role: '{{ old('role', 'operator') }}' }">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="w-full border-slate-200 bg-slate-50 rounded-xl text-sm font-bold p-4 focus:ring-4 focus:ring-blue-50 focus:border-[#003366] transition-all" placeholder="Nama Lengkap" required>
                        @error('name') <p class="text-[10px] font-bold text-red-500 mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full border-slate-200 bg-slate-50 rounded-xl text-sm font-bold p-4 focus:ring-4 focus:ring-blue-50 focus:border-[#003366] transition-all" placeholder="email@pelindo.co.id" required>
                        @error('email') <p class="text-[10px] font-bold text-red-500 mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Password</label>
                        <input type="password" name="password" class="w-full border-slate-200 bg-slate-50 rounded-xl text-sm font-bold p-4 focus:ring-4 focus:ring-blue-50 focus:border-[#003366] transition-all" required>
                        @error('password') <p class="text-[10px] font-bold text-red-500 mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="w-full border-slate-200 bg-slate-50 rounded-xl text-sm font-bold p-4 focus:ring-4 focus:ring-blue-50 focus:border-[#003366] transition-all" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Hak Akses (Role)</label>
                        <select name="role" x-model="role" class="w-full border-slate-200 bg-slate-50 rounded-xl text-sm font-bold p-4 focus:ring-4 focus:ring-blue-50 focus:border-[#003366] transition-all uppercase">
                            <option value="operator">Operator Cabang</option>
                            <option value="superadmin">Super Admin Pusat</option>
                        </select>
                        @error('role') <p class="text-[10px] font-bold text-red-500 mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div x-show="role === 'operator'" x-transition>
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Penempatan Bagian</label>
                        <select name="entity_id" class="w-full border-slate-200 bg-slate-50 rounded-xl text-sm font-bold p-4 focus:ring-4 focus:ring-blue-50 focus:border-[#003366] transition-all uppercase">
                            <option value="">-- Pilih Lokasi Tugas --</option>
                            @foreach($entities as $entity)
                                <option value="{{ $entity->id }}" {{ old('entity_id') == $entity->id ? 'selected' : '' }}>{{ $entity->name }} ({{ $entity->code }})</option>
                            @endforeach
                        </select>
                        @error('entity_id') <p class="text-[10px] font-bold text-red-500 mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="pt-8 flex flex-col sm:flex-row gap-4">
                    <button type="submit" class="flex-1 bg-[#003366] hover:bg-[#001e3c] text-white py-4 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-blue-900/20 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-save text-blue-400"></i> Simpan Akun
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="px-8 py-4 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-black uppercase tracking-widest transition-all text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
