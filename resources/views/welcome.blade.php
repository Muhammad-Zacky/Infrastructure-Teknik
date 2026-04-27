<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Portal Infrastruktur | Pelindo Regional Group</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');

        :root {
            --pelindo-primary: #003366;
            --pelindo-secondary: #0055a4;
            --bg-body: #f4f7fb;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: #1e293b;
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 30px 30px;
        }

        [x-cloak] { display: none !important; }

        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-up {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }

        .asset-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 12px; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        @media (min-width: 768px) {
            .asset-card { padding: 16px; }
        }

        .asset-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px -5px rgba(0, 51, 102, 0.1);
            border-color: var(--pelindo-secondary);
        }

        .asset-image-placeholder {
            width: 100%;
            aspect-ratio: 4/3;
            background: #f8fafc;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            overflow: hidden; 
        }

        .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 8px;
            border-radius: 8px;
            margin-bottom: 6px;
        }

        @media (min-width: 768px) {
            .stat-row { padding: 8px 12px; margin-bottom: 8px; }
        }

        .stat-available { background-color: #f0fdf4; border: 1px solid #dcfce7; }
        .stat-available .stat-label { color: #166534; }
        .stat-available .stat-value { background-color: #10b981; color: white; }

        .stat-breakdown { background-color: #fef2f2; border: 1px solid #fee2e2; }
        .stat-breakdown .stat-label { color: #991b1b; }
        .stat-breakdown .stat-value { background-color: #ef4444; color: white; }

        .stat-label { font-size: 9px; font-weight: 800; text-transform: uppercase; }
        .stat-value { font-size: 10px; font-weight: 900; padding: 2px 6px; border-radius: 6px; }

        @media (min-width: 768px) {
            .stat-label { font-size: 10px; }
            .stat-value { font-size: 11px; padding: 2px 8px; }
        }

        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="min-h-screen flex flex-col" x-data="{ filter: 'all' }">

    <nav class="bg-white/90 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200 shadow-sm">
        <div class="max-w-[1600px] mx-auto px-4 md:px-10 h-16 md:h-20 flex items-center justify-between">
            <div class="flex items-center gap-3 md:gap-6">
                <img src="{{ asset('danantara.png') }}" alt="Danantara" class="h-6 md:h-10 object-contain">
                <div class="w-px h-6 md:h-10 bg-slate-200"></div>
                <img src="{{ asset('pelindo.png') }}" alt="Pelindo" class="h-6 md:h-10 object-contain">
            </div>
            <div>
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 md:px-6 md:py-2.5 bg-[#003366] text-white rounded-full text-[10px] md:text-xs font-black uppercase tracking-widest shadow-md transition-all whitespace-nowrap">
                        <i class="fas fa-desktop md:mr-2"></i> <span class="hidden md:inline">Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 md:px-6 md:py-2.5 bg-white text-[#003366] border-2 border-[#003366] rounded-full text-[10px] md:text-xs font-black uppercase tracking-widest shadow-sm transition-all hover:bg-slate-50 whitespace-nowrap">
                        <i class="fas fa-lock md:mr-2"></i> <span class="hidden md:inline">Login</span>
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="flex-1 w-full max-w-[1600px] mx-auto px-4 md:px-10 py-8 md:py-10 space-y-10 md:space-y-12">

        <div class="text-center py-4 md:py-8 animate-fade-up">
            <h1 class="text-3xl md:text-5xl font-black text-[#003366] uppercase tracking-tight leading-tight">
                Dashboard Infrastructure <br>
                <span class="text-[#0055a4]">Availability</span>
            </h1>
            <p class="mt-3 md:mt-4 text-xs md:text-sm font-bold text-slate-500 tracking-widest uppercase">Pelindo Regional Group</p>
            
            <div class="inline-flex items-center gap-2 md:gap-3 mt-6 bg-white border border-slate-200 px-4 md:px-6 py-2 md:py-3 rounded-full shadow-sm text-[10px] md:text-xs font-bold text-slate-600 uppercase tracking-widest">
                <span class="relative flex h-2.5 w-2.5 md:h-3 md:w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2.5 w-2.5 md:h-3 md:w-3 bg-emerald-500"></span>
                </span>
                Update : {{ now()->format('d M Y - H:i') }}
            </div>

            <div class="mt-8 md:mt-10 overflow-x-auto hide-scrollbar pb-2 px-2 -mx-4 md:mx-0">
                <div class="inline-flex gap-2 bg-white/80 border border-slate-200 rounded-full p-1.5 md:p-2 shadow-sm min-w-max mx-auto">
                    <button @click="filter = 'all'" :class="filter === 'all' ? 'bg-[#003366] text-white shadow-md' : 'text-slate-500 hover:bg-slate-100'" class="px-5 py-2 md:px-6 md:py-3 rounded-full text-[10px] md:text-xs font-black uppercase tracking-widest transition-all">Semua</button>
                    <button @click="filter = 'equipment'" :class="filter === 'equipment' ? 'bg-[#003366] text-white shadow-md' : 'text-slate-500 hover:bg-slate-100'" class="px-5 py-2 md:px-6 md:py-3 rounded-full text-[10px] md:text-xs font-black uppercase tracking-widest transition-all">Peralatan</button>
                    <button @click="filter = 'facility'" :class="filter === 'facility' ? 'bg-[#003366] text-white shadow-md' : 'text-slate-500 hover:bg-slate-100'" class="px-5 py-2 md:px-6 md:py-3 rounded-full text-[10px] md:text-xs font-black uppercase tracking-widest transition-all">Fasilitas</button>
                    <button @click="filter = 'utility'" :class="filter === 'utility' ? 'bg-[#003366] text-white shadow-md' : 'text-slate-500 hover:bg-slate-100'" class="px-5 py-2 md:px-6 md:py-3 rounded-full text-[10px] md:text-xs font-black uppercase tracking-widest transition-all">Utilitas</button>
                </div>
            </div>
        </div>

        <div class="space-y-8 md:space-y-10">
            @forelse ($entities as $index => $entity)
            
            @php
                $availableCategories = $entity->infrastructures->pluck('category')->unique()->values()->toJson();
            @endphp
            
            <section class="animate-fade-up" x-show="filter === 'all' || {{ $availableCategories }}.includes(filter)" x-data="{ showModal: false }">
                
                <div class="bg-gradient-to-r from-[#003366] to-[#0055a4] text-white px-5 py-4 md:px-8 md:py-5 rounded-t-2xl shadow-md flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <h3 class="font-black text-sm md:text-lg uppercase tracking-widest flex items-center gap-2 md:gap-3">
                        <i class="fas fa-building text-blue-300"></i> {{ $entity->name }}
                    </h3>
                    
                    <span class="text-[9px] md:text-[10px] font-bold text-blue-100 uppercase bg-black/20 px-3 py-1 md:px-4 md:py-1.5 rounded-full border border-white/20">
                        Total Unit: {{ $entity->infrastructures->count() }}
                    </span>
                </div>

                <div class="bg-white/80 border-x border-b border-slate-200 rounded-b-2xl p-4 md:p-8 shadow-sm">
                    @if($entity->infrastructures->count() > 0)
                        
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-7 gap-3 md:gap-6">
                            @foreach ($entity->infrastructures->groupBy('type') as $type => $items)
                                @php
                                    $availableQty = $items->where('status', 'available')->count();
                                    $breakdownQty = $items->where('status', 'breakdown')->count();
                                    $representativeItem = $items->whereNotNull('image')->first();
                                    $itemCategory = $items->first()->category ?? 'equipment';
                                @endphp
                                
                                <div class="asset-card group" x-show="filter === 'all' || filter === '{{ $itemCategory }}'">
                                    <div class="asset-image-placeholder relative group">
                                        @if($representativeItem && $representativeItem->image)
                                            <img src="{{ asset('storage/' . ltrim($representativeItem->image, '/')) }}" 
                                                 onerror="this.onerror=null; this.src='{{ asset(ltrim($representativeItem->image, '/')) }}';"
                                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                                 alt="{{ $type }}">
                                        @else
                                            <i class="fas fa-truck-loading text-4xl md:text-5xl text-slate-300 transition-transform duration-500 group-hover:scale-110"></i>
                                        @endif
                                    </div>

                                    <h4 class="text-[9px] md:text-[10px] font-black text-[#003366] text-center uppercase mb-3 md:mb-4 h-8 flex items-center justify-center leading-tight">{{ $type }}</h4>
                                    
                                    <div class="mt-auto border-t border-slate-100 pt-3 md:pt-4">
                                        <div class="stat-row stat-available">
                                            <span class="stat-label">Ready</span>
                                            <span class="stat-value">{{ $availableQty }}</span>
                                        </div>
                                        <div class="stat-row stat-breakdown">
                                            <span class="stat-label">Down</span>
                                            <span class="stat-value">{{ $breakdownQty }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 md:mt-8 pt-4 md:pt-6 border-t border-slate-200 border-dashed text-center">
                            <button @click="showModal = true" class="inline-flex items-center justify-center px-6 py-2.5 bg-slate-50 hover:bg-blue-50 text-[#0055a4] border border-slate-200 hover:border-blue-200 rounded-full text-[10px] md:text-xs font-black uppercase tracking-widest transition-all">
                                <i class="fas fa-list-ul mr-2"></i> Lihat Semua Alat
                            </button>
                        </div>

                        <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center px-4 bg-slate-900/60 backdrop-blur-sm transition-opacity" x-cloak>
                            <div @click.away="showModal = false" x-show="showModal" x-transition.scale.origin.bottom class="bg-white rounded-2xl w-full max-w-4xl max-h-[85vh] flex flex-col shadow-2xl overflow-hidden">
                                
                                <div class="bg-[#00152b] px-6 py-4 flex items-center justify-between shrink-0">
                                    <div>
                                        <h3 class="text-white font-black uppercase tracking-widest text-sm">Daftar Aset</h3>
                                        <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider mt-1">{{ $entity->name }}</p>
                                    </div>
                                    <button @click="showModal = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-red-500 text-white flex items-center justify-center transition-colors">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                <div class="flex-1 overflow-y-auto p-6 bg-slate-50">
                                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                                        <table class="w-full text-left text-xs whitespace-nowrap">
                                            <thead class="bg-slate-100 text-slate-500 text-[9px] font-black uppercase tracking-widest border-b border-slate-200">
                                                <tr>
                                                    <th class="px-5 py-3 w-12 text-center">No</th>
                                                    <th class="px-5 py-3">Kode Alat</th>
                                                    <th class="px-5 py-3">Tipe / Jenis</th>
                                                    <th class="px-5 py-3">Kategori</th>
                                                    <th class="px-5 py-3 text-center">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100">
                                                @foreach($entity->infrastructures as $idx => $asset)
                                                <tr class="hover:bg-slate-50 transition-colors">
                                                    <td class="px-5 py-3 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>
                                                    <td class="px-5 py-3 font-black text-[#003366] uppercase">{{ $asset->code_name }}</td>
                                                    <td class="px-5 py-3 font-bold text-slate-600">{{ $asset->type }}</td>
                                                    <td class="px-5 py-3 text-slate-500 uppercase text-[10px] tracking-widest">{{ $asset->category }}</td>
                                                    <td class="px-5 py-3 text-center">
                                                        @if($asset->status == 'available')
                                                            <span class="px-2 py-1 rounded bg-emerald-50 text-emerald-600 border border-emerald-100 text-[9px] font-black uppercase">Ready</span>
                                                        @else
                                                            <span class="px-2 py-1 rounded bg-red-50 text-red-600 border border-red-100 text-[9px] font-black uppercase">Breakdown</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <p class="text-center text-slate-400 font-bold py-8 md:py-10 uppercase text-[10px] md:text-xs">Belum ada infrastruktur didaftarkan</p>
                    @endif
                </div>
            </section>
            @empty
            <div class="text-center py-16 md:py-20 text-slate-300">
                <i class="fas fa-database text-5xl md:text-6xl mb-4"></i>
                <p class="font-black uppercase text-sm md:text-base">Data Tidak Tersedia</p>
            </div>
            @endforelse
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mt-10 md:mt-16">
            <div class="bg-[#00152b] px-5 py-4 md:px-8 md:py-5 flex items-center justify-between">
                <div class="flex items-center gap-3 md:gap-4 text-white">
                    <i class="fas fa-clipboard-list text-red-500 text-lg md:text-xl"></i>
                    <h3 class="font-black text-xs md:text-sm uppercase tracking-widest">Log Insiden Aktif</h3>
                </div>
            </div>

            <div class="w-full overflow-x-auto hide-scrollbar">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="bg-[#002244] text-slate-300 text-[9px] md:text-[10px] font-black uppercase tracking-[0.2em] whitespace-nowrap">
                            <th class="px-5 py-4 md:px-8 md:py-5 w-16 text-center">NO</th>
                            <th class="px-5 py-4 md:px-8 md:py-5">Entitas Pelabuhan</th>
                            <th class="px-5 py-4 md:px-8 md:py-5 text-center">Identitas Alat</th>
                            <th class="px-5 py-4 md:px-8 md:py-5">Kendala</th>
                            <th class="px-5 py-4 md:px-8 md:py-5 text-center">Status</th>
                            <th class="px-5 py-4 md:px-8 md:py-5">PIC</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap">
                        @forelse ($breakdowns as $index => $log)
                        <tr class="text-[10px] md:text-xs uppercase font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-4 md:px-8 md:py-5 text-center text-slate-400">{{ $index + 1 }}</td>
                            <td class="px-5 py-4 md:px-8 md:py-5">{{ $log->infrastructure->entity->name ?? '-' }}</td>
                            <td class="px-5 py-4 md:px-8 md:py-5 text-center">
                                <span class="bg-blue-50 text-[#003366] px-3 py-1.5 rounded border border-blue-100 text-[9px] md:text-[10px] font-black">
                                    {{ $log->infrastructure->code_name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 md:px-8 md:py-5 text-slate-500 lowercase first-letter:uppercase font-medium italic max-w-xs truncate" title="{{ $log->issue_detail }}">{{ $log->issue_detail }}</td>
                            <td class="px-5 py-4 md:px-8 md:py-5 text-center">
                                <span class="bg-red-50 text-red-600 px-3 py-1.5 rounded-full text-[8px] md:text-[9px] font-black border border-red-100">
                                    {{ str_replace('_', ' ', $log->repair_status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 md:px-8 md:py-5 text-slate-500"><i class="fas fa-user-gear mr-2 opacity-30"></i>{{ $log->vendor_pic ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 md:px-8 md:py-10 text-center font-black text-slate-400 uppercase tracking-widest text-[9px] md:text-[10px]">
                                <i class="fas fa-check-circle mr-2 text-emerald-400 text-base md:text-lg align-middle"></i> Seluruh alat saat ini beroperasi dengan normal.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <footer class="bg-white border-t border-slate-200 py-6 md:py-8 mt-10 md:mt-12 text-center">
        <p class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] md:tracking-[0.3em] px-4">
            &copy; {{ date('Y') }} Danantara Indonesia x Pelindo. All Rights Reserved.
        </p>
    </footer>

</body>
</html>
