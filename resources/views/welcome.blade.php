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
            padding: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            height: 100%;
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
            padding: 8px 12px;
            border-radius: 8px;
            margin-bottom: 8px;
        }

        .stat-available { background-color: #f0fdf4; border: 1px solid #dcfce7; }
        .stat-available .stat-label { color: #166534; }
        .stat-available .stat-value { background-color: #10b981; color: white; }

        .stat-breakdown { background-color: #fef2f2; border: 1px solid #fee2e2; }
        .stat-breakdown .stat-label { color: #991b1b; }
        .stat-breakdown .stat-value { background-color: #ef4444; color: white; }

        .stat-label { font-size: 10px; font-weight: 800; text-transform: uppercase; }
        .stat-value { font-size: 11px; font-weight: 900; padding: 2px 8px; border-radius: 6px; }

        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="min-h-screen flex flex-col" x-data="{ filter: 'all' }">

    <nav class="bg-white/90 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200 shadow-sm">
        <div class="max-w-[1600px] mx-auto px-4 md:px-10 h-20 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <img src="{{ asset('danantara.png') }}" alt="Danantara" class="h-10 object-contain">
                <div class="w-px h-10 bg-slate-200"></div>
                <img src="{{ asset('pelindo.png') }}" alt="Pelindo" class="h-10 object-contain">
            </div>
            <div>
                @auth
                    <a href="{{ route('dashboard') }}" class="px-6 py-2.5 bg-[#003366] text-white rounded-full text-xs font-black uppercase tracking-widest shadow-md transition-all">
                        <i class="fas fa-desktop mr-2"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-6 py-2.5 bg-white text-[#003366] border-2 border-[#003366] rounded-full text-xs font-black uppercase tracking-widest shadow-sm transition-all hover:bg-slate-50">
                        <i class="fas fa-lock mr-2"></i> Login Admin
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="flex-1 w-full max-w-[1600px] mx-auto px-4 md:px-10 py-10 space-y-12">

        <div class="text-center py-8 animate-fade-up">
            <h1 class="text-4xl md:text-5xl font-black text-[#003366] uppercase tracking-tight leading-tight">
                Dashboard Infrastructure <br>
                <span class="text-[#0055a4]">Availability</span>
            </h1>
            <p class="mt-4 text-sm font-bold text-slate-500 tracking-widest uppercase">Pelindo Regional Group</p>
            
            <div class="inline-flex items-center gap-3 mt-6 bg-white border border-slate-200 px-6 py-3 rounded-full shadow-sm text-xs font-bold text-slate-600 uppercase tracking-widest">
                <span class="relative flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                Update : {{ now()->format('d M Y - H:i') }}
            </div>

            <div class="mt-10 overflow-x-auto hide-scrollbar pb-2">
                <div class="inline-flex gap-2 bg-white/80 border border-slate-200 rounded-full p-2 shadow-sm">
                    <button @click="filter = 'all'" :class="filter === 'all' ? 'bg-[#003366] text-white shadow-md' : 'text-slate-500'" class="px-6 py-3 rounded-full text-xs font-black uppercase tracking-widest transition-all">Semua Aset</button>
                    <button @click="filter = 'equipment'" :class="filter === 'equipment' ? 'bg-[#003366] text-white shadow-md' : 'text-slate-500'" class="px-6 py-3 rounded-full text-xs font-black uppercase tracking-widest transition-all">Peralatan</button>
                    <button @click="filter = 'facility'" :class="filter === 'facility' ? 'bg-[#003366] text-white shadow-md' : 'text-slate-500'" class="px-6 py-3 rounded-full text-xs font-black uppercase tracking-widest transition-all">Fasilitas</button>
                    <button @click="filter = 'utility'" :class="filter === 'utility' ? 'bg-[#003366] text-white shadow-md' : 'text-slate-500'" class="px-6 py-3 rounded-full text-xs font-black uppercase tracking-widest transition-all">Utilitas</button>
                </div>
            </div>
        </div>

        <div class="space-y-10">
            @forelse ($entities as $index => $entity)
            
            @php
                // Logika Filter agar Section Hilang jika tidak ada kategori yang dipilih
                $availableCategories = $entity->infrastructures->pluck('category')->unique()->values()->toJson();
            @endphp
            
            <section class="animate-fade-up" x-show="filter === 'all' || {{ $availableCategories }}.includes(filter)">
                
                <div class="bg-gradient-to-r from-[#003366] to-[#0055a4] text-white px-8 py-5 rounded-t-2xl shadow-md flex items-center justify-between">
                    <h3 class="font-black text-lg uppercase tracking-widest flex items-center gap-3">
                        <i class="fas fa-building text-blue-300"></i> {{ $entity->name }}
                    </h3>
                    
                    <span class="text-[10px] font-bold text-blue-100 uppercase bg-black/20 px-4 py-1.5 rounded-full border border-white/20">
                        Total Unit: {{ $entity->infrastructures->count() }}
                    </span>
                </div>

                <div class="bg-white/80 border-x border-b border-slate-200 rounded-b-2xl p-8 shadow-sm">
                    @if($entity->infrastructures->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-7 gap-6">
                            
                            @foreach ($entity->infrastructures->groupBy('type') as $type => $items)
                                @php
                                    // PERBAIKAN: Menggunakan count() agar unit terjumlahkan dengan benar
                                    $availableQty = $items->where('status', 'available')->count();
                                    $breakdownQty = $items->where('status', 'breakdown')->count();
                                    
                                    $representativeItem = $items->whereNotNull('image')->first();
                                    $itemCategory = $items->first()->category ?? 'equipment';
                                @endphp
                                
                                <div class="asset-card group" x-show="filter === 'all' || filter === '{{ $itemCategory }}'">
                                    <div class="asset-image-placeholder">
                                        @if($representativeItem && $representativeItem->image)
                                            <img src="{{ asset('storage/' . $representativeItem->image) }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="fas fa-truck-loading text-5xl text-slate-300"></i>
                                        @endif
                                    </div>

                                    <h4 class="text-[10px] font-black text-[#003366] text-center uppercase mb-4 h-8 flex items-center justify-center">{{ $type }}</h4>
                                    
                                    <div class="mt-auto border-t border-slate-100 pt-4">
                                        <div class="stat-row stat-available">
                                            <span class="stat-label">Available</span>
                                            <span class="stat-value">{{ $availableQty }}</span>
                                        </div>
                                        <div class="stat-row stat-breakdown">
                                            <span class="stat-label">Breakdown</span>
                                            <span class="stat-value">{{ $breakdownQty }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    @else
                        <p class="text-center text-slate-400 font-bold py-10 uppercase text-xs">Belum ada infrastruktur didaftarkan</p>
                    @endif
                </div>
            </section>
            @empty
            <div class="text-center py-20 text-slate-300">
                <i class="fas fa-database text-6xl mb-4"></i>
                <p class="font-black uppercase">Data Tidak Tersedia</p>
            </div>
            @endforelse
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mt-16">
            <div class="bg-[#00152b] px-8 py-5 flex items-center justify-between">
                <div class="flex items-center gap-4 text-white">
                    <i class="fas fa-clipboard-list text-red-500 text-xl"></i>
                    <h3 class="font-black text-sm uppercase tracking-widest">Log Insiden Aktif</h3>
                </div>
            </div>

            <div class="w-full overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#002244] text-slate-300 text-[10px] font-black uppercase tracking-[0.2em]">
                            <th class="px-8 py-5 w-16 text-center">NO</th>
                            <th class="px-8 py-5">Entitas Pelabuhan</th>
                            <th class="px-8 py-5 text-center">Identitas Alat</th>
                            <th class="px-8 py-5">Kendala</th>
                            <th class="px-8 py-5 text-center">Status</th>
                            <th class="px-8 py-5">PIC</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($breakdowns as $index => $log)
                        <tr class="text-xs uppercase font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                            <td class="px-8 py-5 text-center text-slate-400">{{ $index + 1 }}</td>
                            <td class="px-8 py-5">{{ $log->infrastructure->entity->name ?? '-' }}</td>
                            <td class="px-8 py-5 text-center">
                                <span class="bg-blue-50 text-[#003366] px-3 py-1 rounded border border-blue-100 text-[10px] font-black">
                                    {{ $log->infrastructure->code_name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-slate-500 lowercase first-letter:uppercase font-medium italic">{{ $log->issue_detail }}</td>
                            <td class="px-8 py-5 text-center">
                                <span class="bg-red-50 text-red-600 px-3 py-1 rounded-full text-[9px] font-black border border-red-100">
                                    {{ str_replace('_', ' ', $log->repair_status) }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-slate-500"><i class="fas fa-user-gear mr-2 opacity-30"></i>{{ $log->vendor_pic ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-8 py-10 text-center font-black text-slate-400 uppercase tracking-widest text-[10px]">
                                <i class="fas fa-check-circle mr-2 text-emerald-400 text-lg"></i> Seluruh alat saat ini beroperasi dengan normal.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <footer class="bg-white border-t border-slate-200 py-8 mt-12 text-center">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
            &copy; {{ date('Y') }} Danantara Indonesia x Pelindo. All Rights Reserved.
        </p>
    </footer>

</body>
</html>
