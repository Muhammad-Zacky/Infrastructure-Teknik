<x-app-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        [x-cloak] { display: none !important; }

        /* Focus state minimalis korporat */
        input:focus, select:focus, textarea:focus {
            box-shadow: 0 0 0 1px #dc2626 !important; /* Warna merah untuk form insiden */
            border-color: #dc2626 !important;
        }

        /* Custom scrollbar untuk dropdown */
        .dropdown-scroll::-webkit-scrollbar { width: 4px; }
        .dropdown-scroll::-webkit-scrollbar-track { background: #f1f5f9; }
        .dropdown-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    </style>

    <div class="min-h-screen py-8">
        <div class="max-w-4xl mx-auto w-full px-4 sm:px-6 lg:px-8 space-y-6 animate-fade">

            <!-- HEADER KORPORAT (Aksen Merah untuk Urgensi) -->
            <div class="bg-white p-6 rounded-lg border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative overflow-hidden">
                <!-- Aksen Garis Kiri Merah -->
                <div class="absolute left-0 top-0 h-full w-1.5 bg-red-600 rounded-l-lg"></div>

                <div>
                    <h1 class="text-lg font-bold text-[#003366] flex items-center gap-2">
                        <i class="fas fa-triangle-exclamation text-red-600"></i> Registrasi Laporan Insiden
                    </h1>
                    <p class="text-xs font-medium text-slate-500 mt-1">Registrasi kerusakan aset. Status alat akan otomatis berubah menjadi <span class="font-bold text-red-500">"Down"</span>.</p>
                </div>

                <div class="shrink-0">
                    <a href="{{ route('admin.breakdowns.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-red-600 text-xs font-semibold transition-colors">
                        <i class="fas fa-arrow-left"></i> Batal & Kembali
                    </a>
                </div>
            </div>

            <!-- ERROR ALERTS -->
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 p-4 rounded-lg flex items-start gap-3 shadow-sm">
                    <i class="fas fa-exclamation-triangle text-red-600 mt-0.5"></i>
                    <div>
                        <h3 class="text-sm font-bold text-red-800">Gagal Mengirim Laporan</h3>
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
                    <i class="fas fa-clipboard-list text-red-400"></i>
                    <h2 class="text-xs font-bold text-white uppercase tracking-widest">Formulir Detail Kerusakan</h2>
                </div>

                <form action="{{ route('admin.breakdowns.store') }}" method="POST" class="p-6 md:p-8 space-y-6">
                    @csrf

                    <!-- PILIH ASET (WITH SEARCH FILTER) -->
                    @php
                        // Siapkan data aset untuk Alpine.js
                        $infraOptions = $infrastructures->map(function($infra) {
                            return [
                                'id' => $infra->id,
                                'code' => $infra->code_name,
                                'text' => $infra->code_name . ' | ' . $infra->type . ' (Lokasi: ' . ($infra->entity->name ?? 'Pusat') . ')'
                            ];
                        })->values();
                    @endphp

                    <div class="space-y-1.5"
                         x-data="{
                            open: false,
                            search: '',
                            selectedId: '{{ old('infrastructure_id') }}',
                            selectedText: '-- Ketik atau Pilih Aset yang Bermasalah --',
                            options: {{ json_encode($infraOptions) }},
                            get filteredOptions() {
                                if (this.search === '') return this.options;
                                return this.options.filter(i => i.text.toLowerCase().includes(this.search.toLowerCase()));
                            },
                            init() {
                                if(this.selectedId) {
                                    let selected = this.options.find(i => i.id == this.selectedId);
                                    if(selected) this.selectedText = selected.text;
                                }
                            }
                         }">

                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">Pilih Infrastruktur / Aset Bermasalah <span class="text-red-500">*</span></label>

                        <!-- Hidden Input untuk dikirim ke Controller -->
                        <input type="hidden" name="infrastructure_id" :value="selectedId">

                        <div class="relative">
                            <!-- Trigger Button -->
                            <button type="button" @click="open = !open"
                                    class="w-full bg-white border text-sm rounded-md p-2.5 text-left flex justify-between items-center transition-colors focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 {{ $errors->has('infrastructure_id') ? 'border-red-500 bg-red-50' : 'border-slate-300' }}">
                                <span x-text="selectedText" :class="selectedId ? 'text-slate-900 font-semibold' : 'text-slate-500'"></span>
                                <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" @click.away="open = false" x-cloak
                                 x-transition.opacity.duration.200ms
                                 class="absolute z-50 w-full mt-1 bg-white border border-slate-300 rounded-md shadow-xl overflow-hidden">

                                <!-- Search Box -->
                                <div class="p-2 border-b border-slate-100 bg-slate-50 sticky top-0">
                                    <div class="relative">
                                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input type="text" x-model="search" placeholder="Ketik Kode Aset atau Tipe Alat..."
                                               class="w-full pl-8 pr-3 py-2 text-xs border border-slate-300 rounded focus:ring-red-500 focus:border-red-500">
                                    </div>
                                </div>

                                <!-- Options List -->
                                <ul class="max-h-56 overflow-y-auto dropdown-scroll">
                                    <template x-for="option in filteredOptions" :key="option.id">
                                        <li @click="selectedId = option.id; selectedText = option.text; open = false; search = ''"
                                            class="px-4 py-2.5 text-xs cursor-pointer border-b border-slate-50 last:border-0 transition-colors"
                                            :class="selectedId == option.id ? 'bg-red-50 text-red-700 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium'">
                                            <span x-text="option.text"></span>
                                        </li>
                                    </template>
                                    <li x-show="filteredOptions.length === 0" class="px-4 py-3 text-xs text-slate-500 text-center bg-slate-50">
                                        Aset tidak ditemukan. Coba kata kunci lain.
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <p class="text-[10px] font-medium text-slate-500 mt-1">Hanya aset dengan status beroperasi normal (Ready) yang dapat dilaporkan.</p>

                        @error('infrastructure_id')
                            <p class="text-[10px] font-bold text-red-500 mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- DESKRIPSI KENDALA -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">Deskripsi Rincian Kendala Teknis <span class="text-red-500">*</span></label>
                        <textarea name="issue_detail" rows="4" placeholder="Jelaskan secara spesifik masalah, error, atau anomali yang terjadi pada alat..."
                                  class="w-full bg-white border border-slate-300 text-slate-900 text-sm rounded-md block p-2.5 transition-colors resize-none {{ $errors->has('issue_detail') ? 'border-red-500 bg-red-50' : '' }}" required>{{ old('issue_detail') }}</textarea>
                        @error('issue_detail')
                            <p class="text-[10px] font-bold text-red-500 mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                        <!-- PIC / VENDOR -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">Pelapor / Vendor Perbaikan (Opsional)</label>
                            <input type="text" name="vendor_pic" value="{{ old('vendor_pic') }}" placeholder="Contoh: PT. BIMA / TIM TEKNIK INTERNAL"
                                   class="w-full bg-white border border-slate-300 text-slate-900 text-sm rounded-md block p-2.5 transition-colors uppercase font-semibold">
                            <p class="text-[10px] font-medium text-slate-500 mt-1">Kosongkan jika belum ada PIC yang ditunjuk.</p>
                            @error('vendor_pic')
                                <p class="text-[10px] font-bold text-red-500 mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <!-- STATUS LAPORAN -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">Status Laporan Awal <span class="text-red-500">*</span></label>
                            <select name="repair_status" class="w-full bg-white border border-slate-300 text-slate-900 text-sm rounded-md block p-2.5 transition-colors uppercase font-semibold cursor-pointer" required>
                                <option value="reported" {{ old('repair_status') == 'reported' ? 'selected' : '' }}>Reported (Baru Dilaporkan)</option>
                                <option value="order_part" {{ old('repair_status') == 'order_part' ? 'selected' : '' }}>Order Part (Butuh Suku Cadang)</option>
                                <option value="on_progress" {{ old('repair_status') == 'on_progress' ? 'selected' : '' }}>On Progress (Langsung Dikerjakan)</option>
                            </select>
                            @error('repair_status')
                                <p class="text-[10px] font-bold text-red-500 mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- ACTION BUTTONS -->
                    <div class="pt-6 border-t border-slate-200 flex flex-col-reverse sm:flex-row justify-end gap-3">
                        <a href="{{ route('admin.breakdowns.index') }}" class="w-full sm:w-auto px-6 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-md text-sm font-semibold transition-colors hover:bg-slate-50 text-center">
                            Batal
                        </a>
                        <button type="submit" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-md text-sm font-semibold transition-colors shadow-sm flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i> Submit Laporan & Update Aset
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
