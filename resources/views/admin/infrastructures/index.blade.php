<x-app-layout>
    <div class="min-h-screen bg-slate-50 -mt-8 pt-12">
        <div class="max-w-[1600px] mx-auto w-full space-y-8 pb-16 px-4 animate-fade-up" 
             x-data="{ 
                search: '', 
                filterEntity: 'all',
                filterCategory: 'all',
                filterStatus: 'all',
                showDeleteModal: false,
                @php
                    $jsData = $infrastructures->map(function($item) {
                        return [
                            'id' => $item->id,
                            'code' => strtoupper($item->code_name ?? 'TANPA-KODE'),
                            'type' => strtoupper($item->type ?? 'TIDAK DIKETAHUI'),
                            'category' => $item->category ?? '',
                            'entity' => optional($item->entity)->name ?? 'Lokasi Tidak Valid',
                            'status' => $item->status ?? 'available',
                            'image' => $item->image ? asset('storage/'.$item->image) : null,
                            'edit_url' => route('admin.infrastructures.edit', $item->id),
                            'delete_url' => route('admin.infrastructures.destroy', $item->id),
                            'updated_by' => optional($item->updatedBy)->name ?? 'System'
                        ];
                    });
                    
                    // Ambil daftar lokasi unik untuk dropdown filter
                    $uniqueEntities = $infrastructures->map(fn($i) => optional($i->entity)->name ?? 'Lokasi Tidak Valid')->unique()->values();
                @endphp
                items: {{ json_encode($jsData) }},
                
                // 1. Fungsi Filter Data
                get filteredItems() {
                    return this.items.filter(i => {
                        const matchSearch = i.code.toLowerCase().includes(this.search.toLowerCase()) || 
                                          i.type.toLowerCase().includes(this.search.toLowerCase());
                        const matchEntity = this.filterEntity === 'all' || i.entity === this.filterEntity;
                        const matchCat = this.filterCategory === 'all' || i.category === this.filterCategory;
                        const matchStatus = this.filterStatus === 'all' || i.status === this.filterStatus;
                        return matchSearch && matchEntity && matchCat && matchStatus;
                    })
                },

                // 2. Fungsi Grouping Data per Entitas (Terminal)
                get groupedItems() {
                    return this.filteredItems.reduce((groups, item) => {
                        if (!groups[item.entity]) {
                            groups[item.entity] = [];
                        }
                        groups[item.entity].push(item);
                        return groups;
                    }, {});
                }
             }">


            <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm space-y-8 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-[#003366] to-[#0055a4]"></div>

                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        <div class="w-14 h-14 bg-blue-50 text-[#0055a4] rounded-2xl flex items-center justify-center text-2xl border border-blue-100">
                            <i class="fas fa-boxes-stacked"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-black text-[#003366] uppercase tracking-tight">Katalog Infrastruktur</h1>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Sistem Manajemen Aset Terpadu</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        

                        <a href="{{ route('admin.infrastructures.create') }}" class="bg-[#003366] hover:bg-[#001e3c] text-white px-8 py-3.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shadow-md shadow-blue-900/10">
                            <i class="fas fa-plus"></i> Tambah Aset
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 pt-6 border-t border-slate-100">
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" x-model="search" placeholder="Cari Kode atau Nama Aset..." 
                               class="w-full pl-10 pr-4 py-3 bg-slate-50 border-slate-200 rounded-xl text-xs font-bold text-slate-700 placeholder-slate-400 focus:ring-[#0055a4] focus:border-[#0055a4] transition-all">
                    </div>
                    
                    <select x-model="filterEntity" class="w-full px-4 py-3 bg-slate-50 border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:ring-[#0055a4] uppercase transition-all">
                        <option value="all">Semua Terminal / Entitas</option>
                        @foreach($uniqueEntities as $entityName)
                            <option value="{{ $entityName }}">{{ $entityName }}</option>
                        @endforeach
                    </select>

                    <select x-model="filterCategory" class="w-full px-4 py-3 bg-slate-50 border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:ring-[#0055a4] uppercase transition-all">
                        <option value="all">Semua Kategori</option>
                        <option value="equipment">EQUIPMENT</option>
                        <option value="facility">FACILITY</option>
                        <option value="utility">UTILITY</option>
                    </select>

                    <select x-model="filterStatus" class="w-full px-4 py-3 bg-slate-50 border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:ring-[#0055a4] uppercase transition-all">
                        <option value="all">Semua Status</option>
                        <option value="available">Ready (Tersedia)</option>
                        <option value="breakdown">Down (Rusak)</option>
                    </select>
                </div>
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
                            <tr class="bg-slate-50 text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] border-b border-slate-200">
                                <th class="px-8 py-5 w-16 text-center">NO</th>
                                <th class="px-8 py-5">Identitas Unit</th>
                                <th class="px-8 py-5">Spesifikasi Kategori</th>
                                <th class="px-8 py-5 text-center">Status Operasional</th>
                                <th class="px-8 py-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        
                        <template x-for="(items, entity) in groupedItems" :key="entity">
                            <tbody class="divide-y divide-slate-100">
                                
                                <tr class="bg-blue-50/50 border-y border-blue-100">
                                    <td colspan="5" class="px-8 py-4">
                                        <div class="flex items-center justify-between">
                                            <h3 class="font-black text-[#003366] uppercase tracking-widest text-[11px] flex items-center gap-2">
                                                <i class="fas fa-map-marker-alt text-[#0055a4] text-sm"></i> <span x-text="entity"></span>
                                            </h3>
                                            <span class="text-[9px] font-black text-[#0055a4] uppercase bg-white px-3 py-1.5 rounded-md border border-blue-100 shadow-sm" x-text="items.length + ' Unit Aset'"></span>
                                        </div>
                                    </td>
                                </tr>

                                <template x-for="(item, index) in items" :key="item.id">
                                    <tr class="hover:bg-slate-50/70 transition-colors group">
                                        <td class="px-8 py-5 text-center text-slate-400 font-bold text-xs" x-text="index + 1"></td>
                                        <td class="px-8 py-5">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-lg overflow-hidden border border-slate-200 bg-white shrink-0 shadow-sm">
                                                    <template x-if="item.image">
                                                        <img :src="item.image" class="w-full h-full object-cover">
                                                    </template>
                                                    <template x-if="!item.image">
                                                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                            <i class="fas fa-image text-xs"></i>
                                                        </div>
                                                    </template>
                                                </div>
                                                <span class="font-black text-[#003366] text-xs tracking-wider uppercase bg-white px-2.5 py-1 rounded border border-slate-200 shadow-sm" x-text="item.code"></span>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5">
                                            <p class="text-xs font-black text-slate-700 uppercase" x-text="item.type"></p>
                                            <p class="text-[9px] font-bold text-slate-400 uppercase mt-0.5" x-text="item.category"></p>
                                            <div class="flex items-center gap-1.5 mt-2 opacity-60">
                                                <i class="fas fa-user-edit text-[8px] text-slate-400"></i>
                                                <p class="text-[8px] font-black tracking-widest text-slate-400 uppercase" x-text="item.updated_by"></p>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            <template x-if="item.status === 'available'">
                                                <span class="bg-emerald-50 text-emerald-600 px-3 py-1.5 rounded text-[9px] font-black uppercase tracking-widest border border-emerald-200 inline-flex items-center gap-1.5">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Ready
                                                </span>
                                            </template>
                                            <template x-if="item.status !== 'available'">
                                                <span class="bg-red-50 text-red-600 px-3 py-1.5 rounded text-[9px] font-black uppercase tracking-widest border border-red-200 inline-flex items-center gap-1.5">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span> Down
                                                </span>
                                            </template>
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                <a :href="item.edit_url" class="w-8 h-8 bg-white border border-slate-200 text-slate-500 hover:text-[#0055a4] hover:bg-blue-50 hover:border-blue-200 rounded-lg flex items-center justify-center transition-all shadow-sm">
                                                    <i class="fas fa-pen text-[10px]"></i>
                                                </a>
                                                <form :action="item.delete_url" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus aset ini?');" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="w-8 h-8 bg-white border border-slate-200 text-slate-500 hover:text-red-600 hover:bg-red-50 hover:border-red-200 rounded-lg flex items-center justify-center transition-all shadow-sm">
                                                        <i class="fas fa-trash-alt text-[10px]"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </template>
                        
                        <tbody x-show="filteredItems.length === 0" style="display: none;">
                            <tr>
                                <td colspan="5" class="px-8 py-24 text-center bg-slate-50/50">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center border border-slate-200 shadow-sm mb-4">
                                            <i class="fas fa-folder-open text-2xl text-slate-300"></i>
                                        </div>
                                        <p class="font-black uppercase tracking-[0.2em] text-sm text-slate-600">Data Tidak Ditemukan</p>
                                        <p class="text-[10px] mt-1 font-bold uppercase tracking-widest text-slate-400">Sesuaikan filter atau kata kunci pencarian</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
