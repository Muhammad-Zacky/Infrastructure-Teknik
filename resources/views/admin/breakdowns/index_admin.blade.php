<x-app-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        [x-cloak] { display: none !important; }

        /* Custom Scrollbar Korporat */
        ::-webkit-scrollbar { height: 8px; width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Styling Form Select Khusus Status */
        .status-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }
    </style>

    <!-- Tambahkan x-data di container utama untuk state pencarian -->
    <div class="min-h-screen py-8" x-data="{ search: '', filterStatus: 'all' }">
        <div class="max-w-[1600px] mx-auto w-full px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- HEADER KORPORAT -->
            <div class="bg-white border border-slate-200 rounded-lg px-6 py-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 shadow-sm animate-fade">
                <div>
                    <h1 class="text-lg font-bold text-[#003366] flex items-center gap-2">
                        <i class="fas fa-clipboard-list text-[#0055a4]"></i> Log Insiden & Kerusakan Aset
                    </h1>
                    <p class="text-xs font-medium text-slate-500 mt-1">Sistem manajemen tiket perbaikan infrastruktur Pelindo.</p>
                </div>
                <div class="shrink-0">
                    <a href="{{ route('admin.breakdowns.create') }}" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded text-xs font-semibold shadow-sm transition-colors">
                        <i class="fas fa-plus"></i> Lapor Insiden Baru
                    </a>
                </div>
            </div>

            <!-- ALERTS -->
            @if(session('success'))
                <div class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-5 py-3 rounded-lg text-xs font-semibold shadow-sm flex items-center gap-3">
                    <i class="fas fa-check-circle text-emerald-500"></i> {{ session('success') }}
                </div>
            @endif

            <!-- TABLE CONTAINER -->
            <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden flex flex-col animate-fade" style="animation-delay: 100ms;">

                <!-- FILTER BAR (FITUR BARU) -->
                <div class="bg-slate-50 border-b border-slate-200 px-5 py-4 flex flex-col sm:flex-row gap-3">
                    <!-- Search Input -->
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" x-model="search" placeholder="Cari Kode Aset, Lokasi, atau Detail Masalah..."
                               class="w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded text-xs font-medium text-slate-700 focus:ring-[#0055a4] focus:border-[#0055a4] transition-colors shadow-sm">
                    </div>

                    <!-- Status Filter -->
                    <div class="w-full sm:w-56 shrink-0">
                        <select x-model="filterStatus" class="w-full px-3 py-2 bg-white border border-slate-300 rounded text-xs font-medium text-slate-700 focus:ring-[#0055a4] focus:border-[#0055a4] transition-colors shadow-sm cursor-pointer">
                            <option value="all">Semua Status Perbaikan</option>
                            <option value="reported">Dilaporkan (Reported)</option>
                            <option value="order_part">Menunggu Part (Order Part)</option>
                            <option value="on_progress">Sedang Diperbaiki (On Progress)</option>
                            <option value="resolved">Telah Selesai (Resolved)</option>
                        </select>
                    </div>
                </div>

                <!-- Table Wrapper -->
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse min-w-[1050px]">
                        <thead>
                            <tr class="bg-white border-b border-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                                <th class="px-5 py-3.5 w-12 text-center">No</th>
                                <th class="px-5 py-3.5 w-40">Kode Aset</th>
                                <th class="px-5 py-3.5 w-48">Lokasi Entitas</th>
                                <th class="px-5 py-3.5 min-w-[250px]">Detail Laporan</th>
                                <th class="px-5 py-3.5 w-48 text-center">Status Perbaikan</th>
                                <th class="px-5 py-3.5 w-48">PIC / Vendor</th>
                                <th class="px-5 py-3.5 w-24 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                            @forelse($logs as $index => $log)
                            <!-- ALPINE LOGIC UNTUK FILTER BARIS -->
                            <tr x-data="{
                                    rowCode: '{{ strtolower(addslashes($log->infrastructure->code_name ?? '')) }}',
                                    rowEntity: '{{ strtolower(addslashes($log->infrastructure->entity->name ?? '')) }}',
                                    rowDetail: '{{ strtolower(addslashes($log->issue_detail ?? '')) }}',
                                    rowPic: '{{ strtolower(addslashes($log->vendor_pic ?? '')) }}',
                                    rowStatus: '{{ $log->repair_status }}'
                                }"
                                x-show="(filterStatus === 'all' || filterStatus === rowStatus) &&
                                        (search === '' || rowCode.includes(search.toLowerCase()) || rowEntity.includes(search.toLowerCase()) || rowDetail.includes(search.toLowerCase()) || rowPic.includes(search.toLowerCase()))"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                class="hover:bg-slate-50/70 transition-colors">

                                <td class="px-5 py-4 text-center font-medium text-slate-500">
                                    {{ $index + 1 }}
                                </td>

                                <td class="px-5 py-4">
                                    <div class="inline-flex items-center gap-2 bg-slate-100 border border-slate-200 px-2 py-1 rounded text-[#003366] font-mono font-bold text-[11px]">
                                        {{ $log->infrastructure->code_name ?? 'TANPA-KODE' }}
                                    </div>
                                </td>

                                <td class="px-5 py-4 font-semibold text-slate-700">
                                    {{ $log->infrastructure->entity->name ?? '-' }}
                                </td>

                                <td class="px-5 py-4">
                                    <!-- Line clamp agar tidak terlalu panjang memakan ruang vertikal -->
                                    <p class="text-slate-700 font-medium leading-relaxed line-clamp-2" title="{{ $log->issue_detail }}">
                                        {{ $log->issue_detail }}
                                    </p>
                                    <div class="flex items-center gap-1.5 mt-1.5 text-[10px] text-slate-400 font-medium">
                                        <i class="far fa-clock"></i> Dilaporkan: {{ $log->created_at->format('d M Y, H:i') }}
                                    </div>
                                </td>

                                <td class="px-5 py-4 text-center">
                                    <form action="{{ route('admin.breakdowns.update', $log->id) }}" method="POST" class="w-full">
                                        @csrf @method('PUT')
                                        <select name="repair_status" onchange="this.form.submit()"
                                            class="status-select w-full text-[11px] font-semibold rounded border py-1.5 px-3 focus:ring-1 focus:ring-offset-0 focus:ring-[#003366] transition-colors cursor-pointer
                                            {{ $log->repair_status == 'resolved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200 focus:border-emerald-500' :
                                              ($log->repair_status == 'on_progress' ? 'bg-amber-50 text-amber-700 border-amber-200 focus:border-amber-500' :
                                              ($log->repair_status == 'order_part' ? 'bg-purple-50 text-purple-700 border-purple-200 focus:border-purple-500' :
                                              'bg-red-50 text-red-700 border-red-200 focus:border-red-500')) }}">

                                            <option value="reported" {{ $log->repair_status == 'reported' ? 'selected' : '' }}>Dilaporkan</option>
                                            <option value="order_part" {{ $log->repair_status == 'order_part' ? 'selected' : '' }}>Order Suku Cadang</option>
                                            <option value="on_progress" {{ $log->repair_status == 'on_progress' ? 'selected' : '' }}>Sedang Diperbaiki</option>
                                            <option value="resolved" {{ $log->repair_status == 'resolved' ? 'selected' : '' }}>Telah Selesai (Resolved)</option>
                                        </select>
                                    </form>
                                </td>

                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-800">{{ $log->vendor_pic ?? 'Tim Internal' }}</p>
                                    @if($log->updated_by)
                                        <p class="text-[10px] text-slate-500 mt-1 flex items-center gap-1">
                                            <i class="fas fa-user-edit text-slate-400"></i> Update: {{ $log->updatedBy->name ?? 'System' }}
                                        </p>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.breakdowns.edit', $log->id) }}"
                                           class="w-7 h-7 inline-flex items-center justify-center bg-white border border-slate-300 text-slate-500 hover:bg-slate-50 hover:text-[#0055a4] rounded transition-colors"
                                           title="Edit Laporan">
                                            <i class="fas fa-pen text-[10px]"></i>
                                        </a>

                                        <form action="{{ route('admin.breakdowns.destroy', $log->id) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Peringatan: Menghapus log ini secara otomatis akan mengembalikan status aset menjadi Ready jika tidak ada laporan aktif lain. Lanjutkan?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="w-7 h-7 inline-flex items-center justify-center bg-white border border-slate-300 text-slate-500 hover:bg-red-50 hover:text-red-600 hover:border-red-200 rounded transition-colors"
                                                    title="Hapus Laporan">
                                                <i class="fas fa-trash-alt text-[10px]"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-5 py-16 text-center">
                                    <div class="w-12 h-12 bg-slate-50 border border-slate-200 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-clipboard-check text-xl text-slate-300"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-700">Tidak Ada Log Insiden</p>
                                    <p class="text-xs text-slate-500 mt-1">Saat ini tidak ada laporan kerusakan alat yang tercatat di sistem.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Section -->
                @if(method_exists($logs, 'links') && $logs->hasPages())
                <div class="bg-slate-50 border-t border-slate-200 px-6 py-3">
                    {{ $logs->links() }}
                    <p class="text-[10px] text-slate-400 mt-2 italic">* Fitur pencarian instan di atas hanya menyaring data pada halaman yang sedang aktif.</p>
                </div>
                @endif
            </div>

            <!-- FOOTER INFO -->
            <div class="flex items-center justify-between text-slate-500 pt-2">
                <p class="text-[10px] font-semibold uppercase tracking-wider">&copy; {{ date('Y') }} Pelindo Command Center</p>
                <div class="flex gap-4">
                    <span class="text-[10px] font-medium flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-red-500"></span> Dilaporkan</span>
                    <span class="text-[10px] font-medium flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Proses</span>
                    <span class="text-[10px] font-medium flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Selesai</span>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
