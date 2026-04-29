<x-app-layout>
    <div class="min-h-screen bg-slate-50 p-8 flex flex-col items-center justify-center animate-fade">
        <div class="bg-white p-12 rounded-[3rem] shadow-xl text-center max-w-md w-full border border-slate-200 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-b from-[#003366]/5 to-transparent pointer-events-none"></div>
            
            <div class="w-24 h-24 bg-blue-50 text-[#003366] rounded-3xl flex items-center justify-center text-4xl mx-auto mb-6 shadow-inner relative z-10">
                <i class="fas fa-cog fa-spin"></i>
            </div>
            
            <h1 class="text-2xl font-black text-[#003366] uppercase tracking-tight relative z-10">Memproses Export...</h1>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-2 relative z-10">Sistem sedang merender laporan sesuai filter yang Anda minta.</p>
            
            <div class="mt-8 w-full bg-slate-100 h-2 rounded-full overflow-hidden relative z-10">
                <div class="h-full bg-[#003366] w-full origin-left animate-[progress_2s_ease-in-out_infinite]"></div>
            </div>
            
            <p id="downloadMsg" class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mt-6 opacity-0 transition-opacity duration-500 relative z-10">
                <i class="fas fa-check-circle mr-1"></i> File berhasil digenerate!
            </p>
        </div>
    </div>

    <!-- Hidden Export Report Data (Filtered) -->
    <x-export-report :infrastructures="$allInfrastructures" :recentBreakdowns="$allActiveBreakdowns" />

    <style>
        @keyframes progress {
            0% { transform: scaleX(0); }
            50% { transform: scaleX(1); }
            100% { transform: scaleX(0); transform-origin: right; }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Beri waktu sejenak agar browser memuat chart/gambar jika ada
            setTimeout(() => {
                @if($format === 'pdf')
                    exportReportToPDF();
                @else
                    exportReportToExcel();
                @endif
                
                document.getElementById('downloadMsg').classList.remove('opacity-0');
                
                // Kembali ke halaman sebelumnya setelah 3.5 detik (asumsi download sudah terpicu)
                setTimeout(() => {
                    window.history.back();
                }, 3500);
                
            }, 1000);
        });
    </script>
</x-app-layout>
