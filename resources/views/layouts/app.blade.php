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
            .sidebar-transition { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
            .sidebar-hidden { margin-left: -18rem; }
            @media (max-width: 1023px) { #main-sidebar { transform: translateX(-100%); } #main-sidebar.show { transform: translateX(0); } }
        </style>
    </head>
    <body class="font-sans antialiased flex h-screen overflow-hidden bg-[#f4f7fa]">
        
        <div id="mobile-overlay" class="fixed inset-0 bg-slate-900/60 z-[80] hidden lg:hidden opacity-0 transition-opacity duration-300" onclick="toggleSidebar()"></div>

        @include('layouts.sidebar')

        <div class="flex-1 flex flex-col h-screen overflow-y-auto relative z-10 sidebar-transition">
            <header class="bg-white/80 backdrop-blur-md sticky top-0 z-[70] px-6 py-4 flex items-center justify-between border-b border-slate-200">
                <button onclick="toggleSidebar()" class="w-10 h-10 flex items-center justify-center rounded-lg bg-slate-100 text-slate-600 hover:bg-white border border-slate-200 transition-all">
                    <i id="toggle-icon" class="fas fa-bars-staggered"></i>
                </button>
                <div class="flex items-center gap-4 bg-slate-100 px-4 py-2 rounded-full border border-slate-200 shadow-inner">
                    <span class="text-[10px] font-black text-blue-700 uppercase tracking-widest">{{ now()->format('d-m-Y H:i') }}</span>
                </div>
            </header>

            <main class="p-6">
                {{ $slot }}
            </main>
        </div>

        <script>
            function toggleSidebar() {
                const sidebar = document.getElementById('main-sidebar');
                const overlay = document.getElementById('mobile-overlay');
                const isMobile = window.innerWidth < 1024;
                if (isMobile) {
                    sidebar.classList.toggle('show');
                    if (overlay.classList.contains('hidden')) { overlay.classList.remove('hidden'); setTimeout(() => overlay.classList.add('opacity-100'), 10); } 
                    else { overlay.classList.remove('opacity-100'); setTimeout(() => overlay.classList.add('hidden'), 300); }
                } else {
                    sidebar.classList.toggle('sidebar-hidden');
                }
            }
        </script>
    </body>
</html>
