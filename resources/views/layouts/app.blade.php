<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'DIA Portal') }}</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

            body { font-family: 'Inter', sans-serif; }
            .sidebar-transition { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
            .sidebar-hidden { margin-left: -18rem; }
            @media (max-width: 1023px) {
                #main-sidebar { transform: translateX(-100%); }
                #main-sidebar.show { transform: translateX(0); }
            }
        </style>
    </head>
    <body class="antialiased flex h-screen overflow-hidden bg-[#f4f7fa]">

        <div id="mobile-overlay" class="fixed inset-0 bg-slate-900/60 z-[80] hidden lg:hidden opacity-0 transition-opacity duration-300" onclick="toggleSidebar()"></div>

        @include('layouts.sidebar')

        <div class="flex-1 flex flex-col h-screen overflow-y-auto relative z-10 sidebar-transition">

            <!-- HEADER -->
            <header class="bg-white/90 backdrop-blur-md sticky top-0 z-[70] px-6 py-4 flex items-center justify-between border-b border-slate-200 shadow-sm">

                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="w-10 h-10 flex items-center justify-center rounded-lg bg-slate-50 text-slate-600 hover:text-[#0055a4] hover:bg-blue-50 border border-slate-200 transition-all shadow-sm">
                        <i id="toggle-icon" class="fas fa-bars-staggered"></i>
                    </button>

                    <!-- Indikator Halaman (Opsional, opsional bisa dihapus jika tidak perlu) -->
                    <div class="hidden md:block">
                        <p class="text-xs font-bold text-slate-800">Dashboard Infrastructure Availability</p>
                        <p class="text-[10px] text-slate-500 font-medium">Sistem Monitoring Pelindo</p>
                    </div>
                </div>

                <!-- REAL-TIME CLOCK ENTERPRISE STYLE -->
                <div class="flex items-center bg-white border border-slate-200 rounded-lg shadow-sm p-1.5 select-none">
                    <div class="flex items-center gap-2 px-3 border-r border-slate-200 hidden sm:flex">
                        <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_5px_rgba(16,185,129,0.5)]"></div>
                        <span id="realtime-date" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Memuat...</span>
                    </div>
                    <div class="flex items-center gap-2 px-3">
                        <i class="far fa-clock text-[#0055a4]"></i>
                        <span id="realtime-time" class="text-sm font-black text-[#003366] tracking-widest font-mono">00:00:00</span>
                        <span class="text-[9px] font-bold text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">WIB</span>
                    </div>
                </div>

            </header>

            <main class="p-6">
                {{ $slot }}
            </main>
        </div>

        <script>
            // Sidebar Toggle Logic
            function toggleSidebar() {
                const sidebar = document.getElementById('main-sidebar');
                const overlay = document.getElementById('mobile-overlay');
                const isMobile = window.innerWidth < 1024;

                if (isMobile) {
                    sidebar.classList.toggle('show');
                    if (overlay.classList.contains('hidden')) {
                        overlay.classList.remove('hidden');
                        setTimeout(() => overlay.classList.add('opacity-100'), 10);
                    } else {
                        overlay.classList.remove('opacity-100');
                        setTimeout(() => overlay.classList.add('hidden'), 300);
                    }
                } else {
                    sidebar.classList.toggle('sidebar-hidden');
                }
            }

            // Real-Time Digital Clock Logic
            function initRealTimeClock() {
                const dateElement = document.getElementById('realtime-date');
                const timeElement = document.getElementById('realtime-time');

                const days = ['MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];
                const months = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];

                function updateClock() {
                    const now = new Date();

                    // Render Tanggal (Contoh: KAMIS, 30 APR 2026)
                    if(dateElement) {
                        const dayName = days[now.getDay()];
                        const day = String(now.getDate()).padStart(2, '0');
                        const monthName = months[now.getMonth()];
                        const year = now.getFullYear();
                        dateElement.textContent = `${dayName}, ${day} ${monthName} ${year}`;
                    }

                    // Render Jam (Contoh: 14:05:30)
                    if(timeElement) {
                        const hours = String(now.getHours()).padStart(2, '0');
                        const minutes = String(now.getMinutes()).padStart(2, '0');
                        const seconds = String(now.getSeconds()).padStart(2, '0');
                        timeElement.textContent = `${hours}:${minutes}:${seconds}`;
                    }
                }

                // Eksekusi langsung agar tidak delay 1 detik, lalu jalankan interval
                updateClock();
                setInterval(updateClock, 1000);
            }

            // Jalankan jam saat halaman dimuat
            document.addEventListener('DOMContentLoaded', initRealTimeClock);
        </script>
    </body>
</html>
