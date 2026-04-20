<x-app-layout>
    <div class="max-w-6xl mx-auto w-full space-y-8 pb-16 animate-fade-up" 
         x-data="{ 
            showDeleteModal: false, 
            deleteUrl: '', 
            userName: '' 
         }">
        
        <template x-teleport="body">
            <div x-show="showDeleteModal" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
                 style="display: none;">
                
                <div @click.away="showDeleteModal = false" 
                     class="bg-white rounded-[2rem] shadow-xl max-w-sm w-full overflow-hidden border border-slate-200 animate-in zoom-in-95 duration-300">
                    
                    <div class="p-8 text-center">
                        <div class="w-20 h-20 bg-red-50 text-red-600 rounded-full flex items-center justify-center text-4xl mx-auto mb-6 border border-red-100">
                            <i class="fas fa-user-minus"></i>
                        </div>
                        
                        <h2 class="text-xl font-black text-[#003366] uppercase tracking-tight mb-2">Hapus Operator?</h2>
                        <p class="text-sm text-slate-500 font-medium leading-relaxed mb-6">
                            Anda yakin ingin menghapus akses untuk akun <br>
                            <strong class="text-red-600 text-base" x-text="userName"></strong>? <br>
                            <span class="text-xs">Tindakan ini tidak dapat dibatalkan.</span>
                        </p>

                        <div class="flex gap-3">
                            <button type="button" @click="showDeleteModal = false" 
                                    class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                                Batal
                            </button>
                            
                            <form :action="deleteUrl" method="POST" class="flex-1">
                                @csrf @method('DELETE')
                                <button type="submit" 
                                        class="w-full py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-md shadow-red-600/20">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 px-2">
            <div>
                <h2 class="text-2xl font-black text-[#003366] uppercase tracking-tight">Manajemen Akun Operator</h2>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Kelola hak akses personil per bagian terminal</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="bg-[#003366] hover:bg-[#001e3c] text-white px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-blue-900/20 transition-all flex items-center justify-center gap-2">
                <i class="fas fa-user-plus text-blue-400"></i> Tambah User Baru
            </a>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-6 py-4 rounded-xl text-sm font-bold shadow-sm flex items-center gap-3 animate-fade-in">
                <i class="fas fa-check-circle text-emerald-500"></i> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] border-b border-slate-100">
                            <th class="px-8 py-5 w-16 text-center">No</th>
                            <th class="px-8 py-5">Nama Pegawai</th>
                            <th class="px-8 py-5">Email / Username</th>
                            <th class="px-8 py-5">Role</th>
                            <th class="px-8 py-5">Penempatan Bagian</th>
                            <th class="px-8 py-5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($users as $index => $user)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-8 py-5 text-center text-slate-400 font-bold">{{ $index + 1 }}</td>
                            <td class="px-8 py-5 font-black text-[#003366] uppercase">{{ $user->name }}</td>
                            <td class="px-8 py-5 text-slate-500 font-medium">{{ $user->email }}</td>
                            <td class="px-8 py-5">
                                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $user->role === 'superadmin' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                @if($user->entity)
                                    <span class="text-xs font-bold text-slate-600 uppercase italic">
                                        <i class="fas fa-map-marker-alt mr-1 text-slate-400"></i> {{ $user->entity->name }}
                                    </span>
                                @else
                                    <span class="text-[10px] font-black text-slate-300 uppercase italic">Akses Pusat (Global)</span>
                                @endif
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Edit Akun">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    
                                    @if($user->id !== auth()->id())
                                    <button type="button" 
                                            @click="
                                                deleteUrl = '{{ route('admin.users.destroy', $user->id) }}'; 
                                                userName = '{{ addslashes($user->name) }}'; 
                                                showDeleteModal = true;
                                            " 
                                            class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Hapus Akun">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-8 py-16 text-center">
                                <div class="flex flex-col items-center justify-center opacity-30">
                                    <i class="fas fa-users text-4xl mb-3 text-slate-400"></i>
                                    <p class="font-black uppercase tracking-[0.2em] text-sm text-slate-600">Tidak ada pengguna</p>
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
