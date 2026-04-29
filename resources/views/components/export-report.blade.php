@props(['infrastructures' => collect(), 'recentBreakdowns' => collect()])

@php
    // FUNGSI RAHASIA (HANYA UNTUK PDF/EXCEL): Pencari Gambar Agresif Server Lokal
    if (!function_exists('getBase64Image')) {
        function getBase64Image($imagePath) {
            if (empty($imagePath)) return null;
            $cleanPath = ltrim($imagePath, '/');
            $basename = basename($cleanPath);
            
            $paths = [
                public_path($cleanPath),
                storage_path('app/public/' . $cleanPath),
                public_path('storage/' . $cleanPath),
                public_path('assets/infrastructures/' . $basename),
                storage_path('app/public/assets/infrastructures/' . $basename)
            ];
            
            foreach($paths as $p) {
                if (file_exists($p) && is_file($p)) {
                    $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
                    $type = in_array($ext, ['jpg', 'jpeg']) ? 'jpeg' : ($ext == 'png' ? 'png' : $ext);
                    return 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($p));
                }
            }
            return null;
        }
    }

    $equipment = $infrastructures->where('category', 'equipment');
    $facility = $infrastructures->where('category', 'facility');
    $utility = $infrastructures->where('category', 'utility');

    $groupedEquipment = $equipment->groupBy(fn($i) => optional($i->entity)->name ?? 'Unknown Entity');
    $groupedFacility = $facility->groupBy(fn($i) => optional($i->entity)->name ?? 'Unknown Entity');
    $groupedUtility = $utility->groupBy(fn($i) => optional($i->entity)->name ?? 'Unknown Entity');
    
    $allGroups = [
        'EQUIPMENT' => $groupedEquipment,
        'FACILITY' => $groupedFacility,
        'UTILITY' => $groupedUtility
    ];
@endphp

<!-- Container tersembunyi dengan absolute positioning agar tidak terpotong oleh viewport saat dirender -->
<div id="exportContainer" style="position: absolute; top: -9999px; left: -9999px; width: 1200px;">
    <div id="hiddenExportTable" style="background-color: #ffffff; width: 100%; padding: 30px; box-sizing: border-box;">
        <table style="width: 100%; margin-bottom: 20px; border-collapse: collapse; font-family: Arial, sans-serif;">
            <tr>
                <td style="width: 20%;">
                    @php
                        $pelindoBase64 = getBase64Image('pelindo.png');
                        $pelindoUrl = asset('pelindo.png');
                    @endphp
                    @if($pelindoBase64)
                        <img src="{{ $pelindoBase64 }}" data-excel-src="{{ $pelindoUrl }}" style="height: 50px;" height="50">
                    @else
                        <h1 style="color:#003366; font-weight:bold;">PELINDO</h1>
                    @endif
                </td>
                <td style="text-align: center; width: 60%;">
                    <h2 style="margin: 0; font-size: 18px; font-weight: bold; font-family: Arial, sans-serif;">DASHBOARD EQUIPMENT AVAILABILITY</h2>
                    <h3 style="margin: 5px 0 0 0; font-size: 14px; font-weight: bold; font-family: Arial, sans-serif;">PORT OF TELUK BAYUR</h3>
                </td>
                <td style="width: 20%; text-align: right; font-size: 12px; vertical-align: top; font-family: Arial, sans-serif;">
                    <strong>Last Update:</strong><br>{{ now()->format('d-m-Y H:i') }}
                </td>
            </tr>
        </table>

        @foreach($allGroups as $catName => $groupedItems)
            @if($groupedItems->count() > 0)
                <div style="background-color: #003366; color: white; padding: 8px 15px; font-weight: bold; font-size: 14px; margin-top: 30px; margin-bottom: 15px; font-family: Arial, sans-serif;">
                    DATA KATEGORI: {{ $catName }}
                </div>
                
                @foreach($groupedItems as $entityName => $items)
                    @php
                        $types = $items->groupBy('type');
                        $typeNames = $types->keys()->toArray();
                    @endphp
                    
                    <table style="width: 100%; border-collapse: collapse; text-align: center; margin-bottom: 25px; border: 1px solid #1e293b; font-family: Arial, sans-serif;">
                        <tr>
                            <td style="width: 250px; font-weight: bold; padding: 12px; background-color: #f8fafc; text-align: left; border: 1px solid #1e293b; color: #0f172a;">{{ $entityName }}</td>
                            @foreach($typeNames as $t)
                                <td style="font-weight: bold; padding: 12px; font-size: 11px; background-color: #f8fafc; border: 1px solid #1e293b; color: #0f172a;">{{ strtoupper($t) }}</td>
                            @endforeach
                        </tr>
                        
                        <tr>
                            <td style="padding: 10px; font-size: 12px; text-align: left; border: 1px solid #000;">(Gambar Aset Area)</td>
                            @foreach($typeNames as $t)
                                @php
                                    $repImage = $types[$t]->whereNotNull('image')->first();
                                    $imgBase64 = $repImage ? getBase64Image($repImage->image) : null;
                                    $imgUrl = $repImage ? asset('storage/assets/infrastructures/' . basename($repImage->image)) : null;
                                @endphp
                                
                                <td style="padding: 10px; height: 110px; vertical-align: middle; border: 1px solid #1e293b;">
                                    @if($imgBase64)
                                        <img src="{{ $imgBase64 }}" data-excel-src="{{ $imgUrl }}" style="max-height: 90px; max-width: 130px; object-fit: contain;" width="130" height="90">
                                    @else
                                        <span style="color: #cbd5e1; font-size: 10px;">NO IMAGE</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        
                        <tr>
                            <td style="font-weight: bold; font-size: 12px; text-align: left; padding: 10px; border: 1px solid #000;">AVAILABLE</td>
                            @foreach($typeNames as $t)
                                @php $av = $types[$t]->where('status', 'available')->count(); @endphp
                                <td style="background-color: #10b981; color: white; font-size: 16px; font-weight: bold; padding: 10px; border: 1px solid #000;">{{ $av }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td style="font-weight: bold; font-size: 12px; text-align: left; padding: 10px; border: 1px solid #000;">BREAKDOWN</td>
                            @foreach($typeNames as $t)
                                @php $bd = $types[$t]->where('status', 'breakdown')->count(); @endphp
                                <td style="background-color: {{ $bd > 0 ? '#ef4444' : '#f59e0b' }}; color: white; font-size: 16px; font-weight: bold; padding: 10px; border: 1px solid #000;">{{ $bd }}</td>
                            @endforeach
                        </tr>
                    </table>
                @endforeach
            @endif
        @endforeach

        <div style="background-color: #00e5ff; color: black; padding: 8px 15px; font-weight: bold; font-size: 14px; margin-top: 30px; margin-bottom: 10px; font-family: Arial, sans-serif;">
            RINCIAN LOG INSIDEN AKTIF
        </div>
        <table style="width: 100%; border-collapse: collapse; text-align: center; border: 1px solid #1e293b; font-size: 11px; font-family: Arial, sans-serif;">
            <tr style="background-color: #f8fafc; color: #0f172a;">
                <th style="padding: 10px; border: 1px solid #1e293b;">NO</th>
                <th style="padding: 10px; border: 1px solid #1e293b;">PELINDO ENTITY</th>
                <th style="padding: 10px; border: 1px solid #1e293b;">EQUIPMENT</th>
                <th style="padding: 10px; text-align: left; border: 1px solid #1e293b;">BREAKDOWN DETAIL</th>
                <th style="padding: 10px; border: 1px solid #1e293b;">STATUS</th>
                <th style="padding: 10px; border: 1px solid #1e293b;">PIC</th>
            </tr>
            @foreach($recentBreakdowns ?? [] as $index => $log)
                <tr>
                    <td style="padding: 10px; border: 1px solid #1e293b;">{{ $index + 1 }}</td>
                    <td style="padding: 10px; border: 1px solid #1e293b; font-weight: bold;">{{ optional(optional($log->infrastructure)->entity)->name ?? "-" }}</td>
                    <td style="padding: 10px; border: 1px solid #1e293b;">{{ optional($log->infrastructure)->code_name ?? "-" }}</td>
                    <td style="padding: 10px; text-align: left; border: 1px solid #1e293b; line-height: 1.4;">{{ $log->issue_detail }}</td>
                    <td style="padding: 10px; border: 1px solid #1e293b; font-weight: bold;">{{ ucwords(str_replace("_", " ", $log->repair_status)) }}</td>
                    <td style="padding: 10px; border: 1px solid #1e293b;">{{ $log->vendor_pic ?? "Internal" }}</td>
                </tr>
            @endforeach
            @if(empty($recentBreakdowns) || count($recentBreakdowns) == 0)
                <tr><td colspan="6" style="padding: 15px; color: #94a3b8; border: 1px solid #000;">Tidak ada kerusakan alat saat ini</td></tr>
            @endif
        </table>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function exportReportToPDF() {
        return new Promise((resolve, reject) => {
            let container = document.getElementById('exportContainer');
            let exportTable = document.getElementById('hiddenExportTable');
            document.body.style.cursor = 'wait';
            
            // Pindahkan container ke atas secara absolut agar html2canvas merekam full width
            container.style.top = '0px';
            container.style.left = '0px';
            container.style.zIndex = '-1000';

            let opt = {
                margin:       [0.3, 0.3], // top/bottom, left/right
                filename:     "Laporan_Aset_Pelindo_{{ date('d_M_Y') }}.pdf",
                image:        { type: 'jpeg', quality: 1.0 },
                html2canvas:  { scale: 2, useCORS: true, backgroundColor: '#ffffff', windowWidth: 1200 }, 
                jsPDF:        { unit: 'in', format: 'a3', orientation: 'landscape' }
            };

            setTimeout(() => {
                html2pdf().set(opt).from(exportTable).save().then(function() {
                    container.style.top = '-9999px';
                    container.style.left = '-9999px';
                    document.body.style.cursor = 'default';
                    resolve();
                }).catch(function(error) {
                    console.log('PDF Error:', error);
                    container.style.top = '-9999px';
                    container.style.left = '-9999px';
                    document.body.style.cursor = 'default';
                    reject(error);
                });
            }, 300);
        });
    }

    function exportReportToExcel() {
        return new Promise((resolve) => {
            // Clone elemen agar tidak merusak original DOM saat swap src
            let clone = document.getElementById('hiddenExportTable').cloneNode(true);
            
            // Excel tidak support Base64 untuk tag img di dalam HTML. 
            // Kita harus menggantinya dengan absolute URL.
            let images = clone.querySelectorAll('img');
            images.forEach(img => {
                let excelSrc = img.getAttribute('data-excel-src');
                if (excelSrc) {
                    img.src = excelSrc;
                }
            });

            let tableHtml = clone.innerHTML;
        let style = `<style>
            table { border-collapse: collapse; font-family: Arial, sans-serif; }
            th, td { border: 1px solid black; }
        </style>`;
        
        let ns = 'x:';
        // Buat struktur dokumen Excel murni
        let html = `<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta charset="utf-8">
            ${style}
            <!--[if gte mso 9]>
            <xml>
                <${ns}ExcelWorkbook>
                    <${ns}ExcelWorksheets>
                        <${ns}ExcelWorksheet>
                            <${ns}Name>Laporan Peralatan</${ns}Name>
                            <${ns}WorksheetOptions>
                                <${ns}DisplayGridlines/>
                            </${ns}WorksheetOptions>
                        </${ns}ExcelWorksheet>
                    </${ns}ExcelWorksheets>
                </${ns}ExcelWorkbook>
            </xml>
            <![endif]-->
        </head>
        <body>
            ${tableHtml}
        </body>
        </html>`;
        
        let blob = new Blob([html], { type: 'application/vnd.ms-excel' });
        let link = document.createElement('a');
        link.href = window.URL.createObjectURL(blob);
        link.download = "Laporan_Aset_Pelindo_{{ date('d_M_Y') }}.xls";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            resolve();
        });
    }
</script>
