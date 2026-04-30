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

    <div class="min-h-screen py-8" x-data="{ showConfirmModal: false }">

        <!-- MODAL KONFIRMASI SIMPAN (Enterprise Style) -->
        <template x-teleport="body">
            <div x-show="showConfirmModal" x-cloak
                 class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
                <div @click.away="showConfirmModal = false"
                     x-show="showConfirmModal"
                     x-transition.scale.origin.bottom.duration.200ms
                     class="bg-white rounded-lg shadow-xl max-w-sm w-full border border-slate-200 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-blue-50 text-[#0055a4] rounded-full flex items-center justify-center shrink-0 border border-blue-100">
                                <i class="fas fa-question-circle text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Konfirmasi Pembaruan</h3>
                                <p class="text-xs text-slate-500 mt-1">Apakah Anda yakin ingin menyimpan perubahan data entitas ini ke dalam sistem?</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-6 py-3 border-t border-slate-200 flex justify-end gap-2">
                        <button @click="showConfirmModal = false" type="button" class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded text-xs font-semibold hover:bg-slate-50 transition-colors">Periksa Kembali</button>
                        <!-- Tombol ini men-trigger submit form aslinya -->
                        <button @click="$refs.entityForm.submit()" type="button" class="px-4 py-2 bg-[#0055a4] text-white rounded text-xs font-semibold hover:bg-[#003366] transition-colors shadow-sm">Ya, Simpan Data</button>
                    </div>
                </div>
            </div>
        </template>

        <div class="max-w-3xl mx-auto w-full px-4 sm:px-6 lg:px-8 space-y-6 animate-fade">

            <!-- HEADER KORPORAT -->
            <div class="bg-white p-6 rounded-lg border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative overflow-hidden">
                <div class="absolute left-0 top-0 h-full w-1.5 bg-[#0055a4]"></div>

                <div>
                    <h1 class="text-lg font-bold text-[#003366] flex items-center gap-2">
                        <i class="fas fa-building-circle-arrow-right text-[#0055a4]"></i> Edit Data Entitas
                    </h1>
                    <p class="text-xs font-medium text-slate-500 mt-1">Perbarui informasi, penamaan, atau kode lokasi operasional.</p>
                </div>

                <div class="shrink-0">
                    <a href="{{ route('admin.entities.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-[#003366] text-xs font-semibold transition-colors">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>

            <!-- ERROR ALERTS -->
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 p-4 rounded-lg flex items-start gap-3 shadow-sm">
                    <i class="fas fa-exclamation-triangle text-red-600 mt-0.5"></i>
                    <div>
                        <h3 class="text-sm font-bold text-red-800">Pembaruan Gagal</h3>
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
                    <i class="fas fa-map-location-dot text-blue-400"></i>
                    <h2 class="text-xs font-bold text-white uppercase tracking-widest">Informasi Utama Entitas</h2>
                </div>

                <!-- Perhatikan penambahan x-ref="entityForm" agar bisa di-submit dari modal -->
                <form x-ref="entityForm" action="{{ route('admin.entities.update', $entity->id) }}" method="POST" class="p-6 md:p-8 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- NAMA ENTITAS -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">Nama Entitas Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $entity->name) }}" placeholder="Contoh: PT Pelindo Terminal Petikemas"
                               class="w-full bg-white border border-slate-300 text-slate-900 text-sm rounded-md block p-2.5 transition-colors uppercase font-semibold {{ $errors->has('name') ? 'border-red-500 bg-red-50' : '' }}" required>
                        @error('name')
                            <p class="text-[10px] font-bold text-red-500 mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- KODE INTERNAL -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">Kode Internal (Singkatan) <span class="text-red-500">*</span></label>
                        <input type="text" name="code" value="{{ old('code', $entity->code) }}" placeholder="Contoh: TPK, SPJM, REG2"
                               class="w-full bg-white border border-slate-300 text-slate-900 text-sm rounded-md block p-2.5 transition-colors uppercase font-mono font-bold {{ $errors->has('code') ? 'border-red-500 bg-red-50' : '' }}" required>
                        <p class="text-[10px] font-medium text-slate-500">Digunakan sebagai prefix pelaporan dan penandaan area aset.</p>
                        @error('code')
                            <p class="text-[10px] font-bold text-red-500 mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- ACTION BUTTONS -->
                    <div class="pt-6 border-t border-slate-200 flex flex-col-reverse sm:flex-row justify-end gap-3">
                        <a href="{{ route('admin.entities.index') }}" class="w-full sm:w-auto px-6 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-md text-sm font-semibold transition-colors hover:bg-slate-50 text-center">
                            Batal
                        </a>
                        <!-- Tipe button diubah jadi button biasa agar tidak langsung submit -->
                        <button type="button" @click="showConfirmModal = true" class="w-full sm:w-auto bg-[#0055a4] hover:bg-[#003366] text-white px-6 py-2.5 rounded-md text-sm font-semibold transition-colors shadow-sm flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i> Perbarui Data
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
