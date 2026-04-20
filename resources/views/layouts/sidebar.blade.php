<style>
    .hide-sidebar-scroll::-webkit-scrollbar { 
        display: none; 
    }
    .hide-sidebar-scroll { 
        -ms-overflow-style: none; 
        scrollbar-width: none; 
    }
    .sidebar-link-active {
        background-color: rgba(0, 85, 164, 0.15) !important;
        color: #ffffff !important;
        border-left: 4px solid #0055a4 !important;
    }
</style>

<aside id="main-sidebar" class="sidebar-transition w-[280px] bg-[#001a33] flex flex-col fixed lg:relative inset-y-0 left-0 z-50 shadow-2xl shrink-0 border-r border-white/5">
    
    <div class="h-24 flex items-center px-8 bg-[#001122] border-b border-white/5">
        <img src="{{ asset('pelindo.png') }}" alt="Pelindo Logo" class="h-8 md:h-9 object-contain filter brightness-110">
    </div>

    <div class="flex-1 overflow-y-auto hide-sidebar-scroll py-6 flex flex-col gap-6">
        
        <nav class="space-y-1">
            <a href="{{ route('dashboard') }}" 
               class="group flex items-center gap-4 px-8 py-3.5 transition-all duration-300 {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                <i class="fas fa-desktop text-sm w-5 text-center {{ request()->routeIs('dashboard') ? 'text-[#0055a4]' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                <span class="text-xs font-bold tracking-wide uppercase">Dashboard Utama</span>
            </a>
        </nav>

        <nav class="space-y-1">
            <p class="px-8 text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3">Operasional</p>
            
            <a href="{{ route('admin.infrastructures.index') }}" 
               class="group flex items-center gap-4 px-8 py-3.5 transition-all duration-300 {{ request()->routeIs('admin.infrastructures.*') ? 'sidebar-link-active' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                <i class="fas fa-boxes text-sm w-5 text-center {{ request()->routeIs('admin.infrastructures.*') ? 'text-[#0055a4]' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                <span class="text-xs font-bold tracking-wide uppercase">Infrastruktur</span>
            </a>
            
            <a href="{{ route('admin.breakdowns.index') }}" 
               class="group flex items-center gap-4 px-8 py-3.5 transition-all duration-300 {{ request()->routeIs('admin.breakdowns.*') ? 'sidebar-link-active' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                <i class="fas fa-clipboard-list text-sm w-5 text-center {{ request()->routeIs('admin.breakdowns.*') ? 'text-[#0055a4]' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                <span class="text-xs font-bold tracking-wide uppercase">Log Kerusakan</span>
            </a>
        </nav>

        @if(auth()->check() && auth()->user()->role === 'superadmin')
        <nav class="space-y-1">
            <p class="px-8 text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3">Sistem Pusat</p>
            
            <a href="{{ route('admin.entities.index') }}" 
               class="group flex items-center gap-4 px-8 py-3.5 transition-all duration-300 {{ request()->routeIs('admin.entities.*') ? 'sidebar-link-active' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                <i class="fas fa-building text-sm w-5 text-center {{ request()->routeIs('admin.entities.*') ? 'text-[#0055a4]' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                <span class="text-xs font-bold tracking-wide uppercase">Manajemen Bagian</span>
            </a>

            <a href="{{ route('admin.users.index') }}" 
               class="group flex items-center gap-4 px-8 py-3.5 transition-all duration-300 {{ request()->routeIs('admin.users.*') ? 'sidebar-link-active' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                <i class="fas fa-users-cog text-sm w-5 text-center {{ request()->routeIs('admin.users.*') ? 'text-[#0055a4]' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                <span class="text-xs font-bold tracking-wide uppercase">Kelola Akun</span>
            </a>
        </nav>
        @endif

    </div>

    <div class="p-6">
        @auth
            <div class="bg-black/20 rounded-2xl border border-white/5 p-4 transition-all">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-lg bg-[#0055a4] flex items-center justify-center text-white font-black text-sm shadow-lg shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    
                    <div class="overflow-hidden">
                        <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</p>
                        <span class="inline-block mt-0.5 text-[8px] font-black uppercase tracking-widest px-2 py-0.5 rounded bg-[#0055a4]/20 text-blue-400 border border-[#0055a4]/30">
                            {{ auth()->user()->role }}
                        </span>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full py-2.5 bg-transparent hover:bg-white/5 border border-white/10 hover:border-white/20 text-slate-400 hover:text-white text-[10px] font-black rounded-xl transition-all duration-300 uppercase tracking-widest flex items-center justify-center gap-2">
                        <i class="fas fa-power-off text-[9px]"></i> Akhiri Sesi
                    </button>
                </form>
            </div>
        @endauth
        
        <div class="mt-4 text-center">
            <p class="text-[8px] font-bold text-slate-600 uppercase tracking-[0.2em]">DIA PORTAL v4.0</p>
        </div>
    </div>
</aside>
