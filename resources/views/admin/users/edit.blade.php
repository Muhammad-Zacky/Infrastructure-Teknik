<x-app-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        [x-cloak] { display: none !important; }

        /* Focus state minimalis korporat */
        input:focus, select:focus, textarea:focus {
            box-shadow: 0 0 0 1px #0055a4 !important;
            border-color: #0055a4 !important;
        }
    </style>

    <div class="min-h-screen py-8">
        <div class="max-w-3xl mx-auto w-full px-4 sm:px-6 lg:px-8 space-y-6 animate-fade">

            <!-- HEADER -->
            <div class="bg-white p-6 rounded-lg border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative overflow-hidden">
                <div class="absolute left-0 top-0 h-full w-1.5 bg-[#0055a4]"></div>

                <div>
                    <h1 class="text-lg font-bold text-[#003366] flex items-center gap-2">
                        <i class="fas fa-user-pen text-[#0055a4]"></i> Edit Akun Pengguna
                    </h1>
                    <p class="text-xs font-medium text-slate-500 mt-1">Perbarui profil otentikasi, kata sandi, dan hak akses personel.</p>
                </div>

                <div class="shrink-0">
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-[#003366] text-xs font-semibold transition-colors">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>

            <!-- ERROR ALERTS -->
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 p-4 rounded-lg flex items-start gap-3 shadow-sm">
                    <i class="fas fa-exclamation-triangle text-red-600 mt-0.5"></i>
                    <div>
                        <h3 class="text-sm font-bold text-red-800">Gagal Memperbarui Akun</h3>
                        <ul class="mt-1 space-y-1 text-xs text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- MAIN FORM CARD -->
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-[#00152b] px-6 py-4 border-b border-slate-700 flex items-center gap-3">
                    <i class="fas fa-id-card-clip text-blue-400"></i>
                    <h2 class="text-xs font-bold text-white uppercase tracking-widest">Informasi Autentikasi Pegawai</h2>
                </div>

                <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="p-6 md:p-8 space-y-6" x-data="{ role: '{{ old('role', $user->role) }}' }">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- NAMA LENGKAP -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" placeholder="Masukkan nama pengguna"
                                   class="w-full bg-white border border-slate-300 text-slate-900 text-sm rounded-md block p-2.5 transition-colors" required>
                        </div>

                        <!-- EMAIL -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">Alamat Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" placeholder="email@pelindo.co.id"
                                   class="w-full bg-white border border-slate-300 text-slate-900 text-sm rounded-md block p-2.5 transition-colors" required>
                        </div>
                    </div>

                    <!-- GANTI PASSWORD BOX -->
                    <div class="bg-slate-50 p-5 rounded-lg border border-slate-200 space-y-4">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-shield-halved text-slate-400"></i>
                            <p class="text-xs font-bold text-slate-600 uppercase tracking-wide">Pembaruan Kata Sandi</p>
                        </div>
                        <p class="text-[10px] font-medium text-slate-500 mb-4">Kosongkan kedua kolom di bawah ini jika tidak ingin mengubah password saat ini.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-semibold text-slate-600">Password Baru</label>
                                <input type="password" name="password" placeholder="Ketik kata sandi baru..."
                                       class="w-full bg-white border border-slate-300 text-slate-900 text-sm rounded-md block p-2 transition-colors">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-semibold text-slate-600">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" placeholder="Ulangi kata sandi baru..."
                                       class="w-full bg-white border border-slate-300 text-slate-900 text-sm rounded-md block p-2 transition-colors">
                            </div>
                        </div>
                    </div>

                    <!-- ROLE & ENTITAS -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2 border-t border-slate-100">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">Hak Akses (Role) <span class="text-red-500">*</span></label>
                            <select name="role" x-model="role" class="w-full bg-white border border-slate-300 text-slate-900 text-sm rounded-md block p-2.5 transition-colors uppercase font-semibold cursor-pointer" required>
                                <option value="operator" {{ old('role', $user->role) == 'operator' ? 'selected' : '' }}>Operator Terminal</option>
                                <option value="superadmin" {{ old('role', $user->role) == 'superadmin' ? 'selected' : '' }}>Administrator Pusat</option>
                            </select>
                        </div>

                        <!-- DROPDOWN ENTITAS (Muncul Jika Operator) -->
                        <div x-show="role === 'operator'" x-cloak class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">Penempatan Wilayah <span class="text-red-500">*</span></label>
                            <select name="entity_id" class="w-full bg-white border border-slate-300 text-slate-900 text-sm rounded-md block p-2.5 transition-colors cursor-pointer" :required="role === 'operator'">
                                <option value="" disabled selected>-- Pilih Lokasi Tugas --</option>
                                @foreach($entities as $entity)
                                    <option value="{{ $entity->id }}" {{ old('entity_id', $user->entity_id) == $entity->id ? 'selected' : '' }}>
                                        {{ $entity->code }} - {{ $entity->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-slate-500 font-medium mt-1">Operator hanya dapat mengakses aset di wilayah ini.</p>
                        </div>
                    </div>

                    <!-- ACTION BUTTONS -->
                    <div class="pt-6 border-t border-slate-200 flex flex-col-reverse sm:flex-row justify-end gap-3">
                        <a href="{{ route('admin.users.index') }}" class="w-full sm:w-auto px-6 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-md text-sm font-semibold transition-colors hover:bg-slate-50 text-center">
                            Batal
                        </a>
                        <button type="submit" class="w-full sm:w-auto bg-[#0055a4] hover:bg-[#003366] text-white px-6 py-2.5 rounded-md text-sm font-semibold transition-colors shadow-sm flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i> Perbarui Data Akun
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
