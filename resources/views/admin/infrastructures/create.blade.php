<x-app-layout>
    <div class="max-w-4xl mx-auto w-full space-y-8 pb-16 animate-fade-up">
        
        <div class="bg-white p-8 md:p-10 rounded-[2rem] border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-[#003366] to-[#0055a4]"></div>

            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-[#003366] uppercase tracking-tight">Tambah Inventaris Aset</h2>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Sistem 1 Data = 1 Unit Fisik</p>
                </div>
                <i class="fas fa-boxes-stacked text-slate-100 text-4xl"></i>
            </div>

            @if ($errors->any())
                <div class="mb-8 p-5 bg-red-50 border-2 border-red-200 rounded-2xl flex items-start gap-4 animate-fade">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-red-700 uppercase tracking-widest mb-1">Gagal Menyimpan Data!</h3>
                        <ul class="list-disc list-inside text-xs font-bold text-red-500/80 uppercase tracking-wide">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            
            <form action="{{ route('admin.infrastructures.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" 
                  x-data="{ 
                      inputMode: '{{ old('type_select') == 'new' ? 'text' : 'select' }}', 
                      selectedType: '{{ old('type_select') }}',
                      selectedCategory: '{{ old('category') }}',
                      typeMap: {{ json_encode($typeCategoryMap ?? []) }} 
                  }">
                @csrf
                
                <div onclick="document.getElementById('image-input').click()" class="bg-slate-50 p-6 rounded-2xl border-2 border-dashed border-slate-300 group hover:border-blue-500 hover:bg-blue-50/30 transition-all cursor-pointer">
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4 text-center cursor-pointer">Foto Infrastruktur</label>
                    
                    <div class="flex flex-col items-center">
                        <div id="image-preview-container" class="hidden mb-4">
                            <img id="image-preview" src="#" alt="Preview" class="max-h-48 rounded-xl shadow-md border-4 border-white object-cover">
                        </div>
                        
                        <div id="upload-placeholder" class="text-center">
                            <i class="fas fa-cloud-upload-alt text-4xl text-slate-300 mb-2 group-hover:text-blue-500 transition-colors"></i>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Klik untuk pilih gambar aset</p>
                        </div>
                        <input type="file" name="image" id="image-input" class="hidden" accept="image/*" onchange="previewImage(this)">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2 ml-1">Entitas Kepemilikan (Lokasi)</label>
                    
                    @if(auth()->user()->role === 'superadmin')
                        <select name="entity_id" class="w-full border-slate-200 bg-slate-50 rounded-xl text-sm font-bold p-4 focus:ring-4 focus:ring-blue-50 focus:border-[#003366] transition-all" required>
                            <option value="">-- Pilih Cabang / Entitas --</option>
                            @foreach($entities as $entity)
                                <option value="{{ $entity->id }}" {{ old('entity_id') == $entity->id ? 'selected' : '' }}>
                                    {{ $entity->name }} ({{ $entity->code }})
                                </option>
                            @endforeach
                        </select>
                    @else
                        <div class="w-full border border-slate-200 bg-slate-100/70 rounded-xl text-sm font-black p-4 text-slate-500 uppercase cursor-not-allowed flex items-center justify-between">
                            <span>{{ auth()->user()->entity->name ?? 'Entitas Tidak Diketahui' }}</span>
                            <i class="fas fa-lock text-slate-300"></i>
                        </div>
                        <p class="text-[9px] font-bold text-emerald-500 mt-1.5 ml-1 uppercase tracking-widest"><i class="fas fa-shield-alt mr-1"></i> Terkunci sesuai area tugas Anda</p>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2 ml-1">Kategori Aset</label>
                        <select name="category" x-model="selectedCategory" class="w-full border-slate-200 bg-slate-50 rounded-xl text-sm font-bold p-4 focus:ring-4 focus:ring-blue-50 focus:border-[#003366] transition-all uppercase" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="equipment">Peralatan (Equipment)</option>
                            <option value="facility">Fasilitas (Facility)</option>
                            <option value="utility">Utilitas (Utility)</option>
                        </select>
                        <p x-show="selectedType !== '' && selectedType !== 'new' && typeMap[selectedType]" style="display: none;" class="text-[9px] font-bold text-emerald-500 mt-2 ml-1">
                            <i class="fas fa-check-circle"></i> Kategori otomatis disesuaikan
                        </p>
                    </div>

                    <div>
                        <label class="block text-[11px] font-black {{ $errors->has('code_name') ? 'text-red-600' : 'text-[#0055a4]' }} uppercase tracking-[0.2em] mb-2 ml-1">
                            Kode Identitas (Unik)
                        </label>
                        <div class="relative">
                            <input type="text" name="code_name" value="{{ old('code_name') }}" placeholder="Contoh: GLC-01, GLC-02" 
                                   class="w-full rounded-xl text-sm font-black p-4 transition-all uppercase {{ $errors->has('code_name') ? 'border-red-400 bg-red-50 text-red-700 focus:ring-red-200 focus:border-red-500 shadow-[0_0_15px_rgba(239,68,68,0.2)]' : 'border-[#0055a4]/30 bg-blue-50/30 focus:ring-blue-50 focus:border-[#003366]' }}" required>
                            
                            @error('code_name')
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-red-500 animate-pulse">
                                    <i class="fas fa-exclamation-circle text-lg"></i>
                                </div>
                            @enderror
                        </div>
                        
                        @error('code_name') 
                            <div class="flex items-center gap-1.5 mt-2 ml-1 text-red-500">
                                <i class="fas fa-times-circle text-[10px]"></i>
                                <p class="text-[10px] font-black uppercase">{{ $message }}</p> 
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200">
                    <label class="block text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-1">Jenis Alat / Fasilitas</label>
                    
                    <div x-show="inputMode === 'select'">
                        <select name="type_select" x-model="selectedType" 
                                @change="
                                    if(selectedType === 'new') { 
                                        inputMode = 'text'; 
                                        selectedType = 'new'; 
                                    } else if(selectedType !== '') {
                                        selectedCategory = typeMap[selectedType];
                                    }
                                " 
                                class="w-full border-slate-200 bg-white rounded-xl text-sm font-bold p-4 focus:ring-4 focus:ring-blue-50 focus:border-[#003366] transition-all uppercase cursor-pointer">
                            <option value="">-- Pilih dari yang sudah ada --</option>
                            @foreach($typeCategoryMap ?? [] as $type => $cat)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                            <option value="new" class="font-black text-[#0055a4]">➕ BUAT JENIS ALAT BARU</option>
                        </select>
                    </div>

                    <div x-show="inputMode === 'text'" style="display: none;" class="flex gap-3">
                        <input type="text" name="type_new" value="{{ old('type_new') }}" placeholder="Ketik jenis alat baru di sini..." class="w-full border-slate-200 bg-white rounded-xl text-sm font-bold p-4 focus:ring-4 focus:ring-blue-50 focus:border-[#003366] transition-all uppercase">
                        <button type="button" @click="inputMode = 'select'; selectedType = ''; selectedCategory = '';" class="px-6 bg-red-50 text-red-600 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-red-100 transition-all">Batal</button>
                    </div>
                    @error('type_new') 
                        <div class="flex items-center gap-1.5 mt-2 ml-1 text-red-500">
                            <i class="fas fa-times-circle text-[10px]"></i>
                            <p class="text-[10px] font-black uppercase">{{ $message }}</p> 
                        </div>
                    @enderror
                </div>

                <input type="hidden" name="status" value="available">

                <div class="pt-8 border-t border-slate-100 flex flex-col sm:flex-row gap-4">
                    <button type="submit" class="flex-1 bg-[#003366] hover:bg-[#001e3c] text-white py-4 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-blue-900/20 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-save text-blue-400"></i> Simpan Unit Aset
                    </button>
                    <a href="{{ route('admin.infrastructures.index') }}" class="px-8 py-4 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-black uppercase tracking-widest transition-all text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const previewContainer = document.getElementById('image-preview-container');
            const previewImage = document.getElementById('image-preview');
            const placeholder = document.getElementById('upload-placeholder');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-app-layout>
