<x-app-layout>
    <div class="max-w-[1600px] mx-auto w-full space-y-6 pb-16 pt-8 px-4 sm:px-6 lg:px-8 animate-fade-up" 
         x-data="{ 
            showDeleteModal: false, 
            deleteUrl: '', 
            userName: '' 
         }">
        
        <template x-teleport="body">
            <div x-show="showDeleteModal" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 backdrop-blur-none"
                 x-transition:enter-end="opacity-100 backdrop-blur-sm"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 backdrop-blur-sm"
                 x-transition:leave-end="opacity-0 backdrop-blur-none"
                 class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
                 style="display: none;">
                
                <div @click.away="showDeleteModal = false" 
                     class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-200 transform transition-all">
                    
                    <div class="p-6 sm:p-8">
                        <div class="flex items-start gap-5">
                            <div class="flex-shrink-0 w-12 h-12 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-xl">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900 mb-1">Hapus Akses Pengguna</h2>
                                <p class="text-sm text-slate-600 leading-relaxed">
                                    Apakah Anda yakin ingin menghapus akun <span class="font-bold text-slate-900" x-text="userName"></span>? Data akses ini akan dihapus secara permanen dari sistem.
                                </p>
                            </div>
                        </div>

                        <div class="mt-8 flex flex-col-reverse sm:flex-row gap-3 sm:justify-end">
                            <button type="button" @click="showDeleteModal = false" 
                                    class="w-full sm:w-auto px-5 py-2.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-lg text-sm font-semibold transition-colors">
                                Batal
                            </button>
                            
                            <form :action="deleteUrl" method="POST" class="w-full sm:w-auto">
                                @csrf @method('DELETE')
                                <button type="submit" 
                                        class="w-full px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm">
                                    Ya, Hapus Akun
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-6 border-l-4 border-l-[#003366]">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 text-[#0055a4] rounded-xl flex items-center justify-center text-xl border border-blue-100">
                    <i class="fas fa-users-gear"></i>
                </div>
                <div>
                    <h1 class="text-xl md:text-2xl font-bold text-[#003366]">Manajemen Akun</h1>
                    <p class="text-sm text-slate-500 mt-1">Kelola hak akses personil berdasarkan entitas dan bagian terminal.</p>
                </div>
            </div>
            
            <a href="{{ route('admin.users.create') }}" class="w-full md:w-auto bg-[#003366] hover:bg-[#002244] text-white px-5 py-2.5 rounded-lg text-sm font-semibold shadow-sm transition-colors flex items-center justify-center gap-2">
                <i class="fas fa-user-plus text-xs"></i> Tambah Pengguna
            </a>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-xl flex items-start gap-3 shadow-sm animate-fade-in">
                <i class="fas fa-check-circle text-emerald-600 mt-0.5"></i>
                <div>
                    <h3 class="text-sm font-bold text-emerald-800">Berhasil</h3>
                    <p class="text-sm text-emerald-700 mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @php
            // Mengelompokkan koleksi users berdasarkan field 'role'
            $groupedUsers = collect($users)->groupBy('role');
        @endphp

        <div class="space-y-8">
            @forelse($groupedUsers as $role => $roleUsers)
                @php
                    $isSuperadmin = strtolower($role) === 'superadmin';
                    // Superadmin menggunakan aksen emas/amber elegan, selain itu biru Pelindo
                    $badgeBg = $isSuperadmin ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-blue-100 text-[#003366] border-blue-200';
                    $iconClass = $isSuperadmin ? 'fa-user-shield text-amber-500' : 'fa-user-tie text-[#0055a4]';
                @endphp

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    
                    <div class="bg-slate-50/80 border-b border-slate-200 px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center shadow-sm">
                                <i class="fas {{ $iconClass }} text-sm"></i>
                            </div>
                            <h2 class="font-bold text-slate-800 text-base uppercase tracking-wide">
                                {{ $role }}
                            </h2>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $badgeBg }}">
                            {{ $roleUsers->count() }} Pengguna Terdaftar
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="bg-white border-b border-slate-200">
                                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider w-16 text-center">No</th>
                                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Informasi Pegawai</th>
                                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Penempatan Area</th>
                                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($roleUsers as $index => $user)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 text-center text-sm text-slate-500 font-medium">
                                        {{ $index + 1 }}
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-[#003366]">{{ $user->name }}</span>
                                            <span class="text-xs text-slate-500 mt-0.5"><i class="far fa-envelope mr-1"></i> {{ $user->email }}</span>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        @if($user->entity)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-md text-xs font-medium bg-blue-50 text-[#0055a4] border border-blue-100">
                                                <i class="fas fa-map-marker-alt opacity-70"></i> {{ $user->entity->name }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-md text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                                <i class="fas fa-globe opacity-70"></i> Akses Pusat (Global)
                                            </span>
                                        @endif
                                    </td>
                                    
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.users.edit', $user->id) }}" 
                                               class="inline-flex items-center justify-center w-8 h-8 rounded-md text-slate-400 bg-white border border-slate-200 hover:text-[#0055a4] hover:bg-blue-50 hover:border-blue-200 transition-all shadow-sm" 
                                               title="Edit Akun">
                                                <i class="fas fa-pen text-xs"></i>
                                            </a>
                                            
                                            @if($user->id !== auth()->id())
                                            <button type="button" 
                                                    @click="
                                                        deleteUrl = '{{ route('admin.users.destroy', $user->id) }}'; 
                                                        userName = '{{ addslashes($user->name) }}'; 
                                                        showDeleteModal = true;
                                                    " 
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md text-slate-400 bg-white border border-slate-200 hover:text-red-600 hover:bg-red-50 hover:border-red-200 transition-all shadow-sm" 
                                                    title="Hapus Akun">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                            @else
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-md text-slate-300 bg-slate-50 border border-slate-100 cursor-not-allowed" title="Tidak dapat menghapus akun sendiri">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center flex flex-col items-center justify-center">
                    <div class="w-20 h-20 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center text-3xl mb-4 border border-slate-100">
                        <i class="fas fa-users-slash"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-700">Belum Ada Pengguna</h3>
                    <p class="text-sm text-slate-500 mt-1 max-w-sm">Sistem belum memiliki data akun terdaftar selain Anda. Silakan tambahkan pengguna baru.</p>
                    <a href="{{ route('admin.users.create') }}" class="mt-6 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-semibold shadow-sm transition-colors">
                        Tambah Pengguna Pertama
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
