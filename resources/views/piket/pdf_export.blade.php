<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Monitoring Piket</title>
    <style>
        @page { margin: 100px 40px 60px 40px; }
        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            font-size: 10pt; 
            color: #333; 
            line-height: 1.4; 
        }
        
        /* HEADER FIXED PADA SETIAP HALAMAN */
        header { 
            position: fixed; 
            top: -70px; 
            left: 0px; 
            right: 0px; 
            height: 60px; 
            border-bottom: 2px solid #333; 
        }
        
        /* FOOTER FIXED PADA SETIAP HALAMAN */
        footer { 
            position: fixed; 
            bottom: -40px; 
            left: 0px; 
            right: 0px; 
            height: 30px; 
            font-size: 8pt; 
            color: #777; 
            border-top: 1px solid #ccc; 
            padding-top: 5px; 
        }
        footer .pagenum:before {
            content: counter(page);
        }

        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { padding: 5px; vertical-align: middle; }
        .header-logo { width: 120px; font-weight: bold; color: #333; font-size: 14pt; }
        .header-title { text-align: center; font-size: 14pt; font-weight: bold; letter-spacing: 1px; color: #111; }
        .header-meta { text-align: right; font-size: 8pt; color: #555; line-height: 1.2; }

        .meta-box {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 25px;
            background-color: #fff;
            border: 1px solid #64b5f6; /* Pelindo Blue border */
        }
        .meta-box td {
            padding: 8px 12px;
            border: 1px solid #64b5f6;
            font-size: 9pt;
        }
        .meta-label {
            font-weight: bold;
            color: #0d47a1; /* Pelindo Dark Blue */
            width: 15%;
            background-color: #e3f2fd; /* Light Blue background */
        }
        .meta-value {
            width: 35%;
            color: #111;
            font-weight: 500;
        }

        .section-title { 
            background: #bbdefb; /* Medium Light Blue */
            color: #0d47a1; 
            padding: 6px 10px; 
            font-weight: bold; 
            margin-top: 15px; 
            font-size: 10pt; 
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #64b5f6;
            border-bottom: none;
        }
        
        .data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 10px; 
        }
        .data-table th, .data-table td { 
            border: 1px solid #90caf9; 
            padding: 7px 10px; 
            text-align: left; 
            font-size: 9pt;
            vertical-align: middle;
        }
        .data-table th { 
            background: #e3f2fd; 
            color: #0d47a1;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
        }
        .data-table tbody tr:nth-child(even) {
            background-color: #f8fbff;
        }

        .status-badge { 
            font-weight: bold; 
            text-transform: uppercase; 
            font-size: 8pt;
        }
        .status-baik { color: #333; }
        .status-kurang { color: #333; }
        .status-default { color: #333; }

        .signature-box {
            margin-top: 40px;
            width: 100%;
            page-break-inside: avoid;
        }
        .signature-table {
            width: 100%;
            text-align: center;
        }
        .signature-table td {
            width: 50%;
            padding: 10px;
        }
        .signature-line {
            display: inline-block;
            width: 200px;
            border-bottom: 1px solid #333;
            margin-top: 60px;
            margin-bottom: 5px;
        }
        
        .note-box {
            border: 1px solid #cbd5e1;
            padding: 10px;
            background: #f8fafc;
            min-height: 40px;
            font-style: italic;
            color: #475569;
            font-size: 9pt;
        }
    </style>
</head>
<body>

    <header>
        <table class="header-table">
            <tr>
                <td class="header-logo" style="vertical-align: middle;">
                    @php
                        $logoPath = public_path('images/logo-pelindo.png');
                        $logoBase64 = '';
                        if (file_exists($logoPath)) {
                            $logoData = file_get_contents($logoPath);
                            $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
                        }
                    @endphp
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" style="height: 40px; width: auto; max-width: 150px;" alt="Pelindo">
                    @else
                        <div style="font-family: 'Arial Black', Arial, sans-serif; font-weight: 900; font-size: 22pt; letter-spacing: -1px; margin: 0; padding: 0;">
                            <span style="color: #0b4e96;">PEL</span><span style="color: #00a2e9;">INDO</span>
                        </div>
                    @endif
                </td>
                <td class="header-title">LAPORAN MONITORING PIKET</td>
                <td class="header-meta">
                    No. Dok: PLD-REG4-{{ date('ymd', strtotime($input->tanggal)) }}-{{ sprintf('%03d', $input->id) }}<br>
                    Revisi: 00<br>
                    Tanggal: {{ \Carbon\Carbon::now()->format('d M Y') }}
                </td>
            </tr>
        </table>
    </header>

    <footer>
        <table style="width: 100%;">
            <tr>
                <td style="text-align: left;">Dicetak oleh: {{ auth()->check() ? auth()->user()->name : 'Sistem' }} | Waktu: {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}</td>
                <td style="text-align: right;">Halaman <span class="pagenum"></span></td>
            </tr>
        </table>
    </footer>

    <main>
        <!-- Document Meta Data -->
        <table class="meta-box">
            <tr>
                <td class="meta-label">Cabang / Lokasi</td>
                <td class="meta-value">{{ strtoupper($input->lokasi) }}</td>
                <td class="meta-label">Jenis Inspeksi</td>
                <td class="meta-value">{{ strtoupper($input->jenis_piket) }}</td>
            </tr>
            <tr>
                <td class="meta-label">Tanggal Inspeksi</td>
                <td class="meta-value">{{ \Carbon\Carbon::parse($input->tanggal)->translatedFormat('d F Y') }}</td>
                <td class="meta-label">Nama Inspektur</td>
                <td class="meta-value">{{ strtoupper($input->user->name ?? 'N/A') }}</td>
            </tr>
        </table>

        @php
            $groupedDetails = $input->details->groupBy('category');
            $templateData = \App\Models\Template::where('jenis_piket', $input->jenis_piket)->first();
            
            // Buat pemetaan dari kategori singkat (misal 'A') ke judul penuh dari template
            $categoryMap = [];
            if ($templateData && is_array($templateData->content)) {
                foreach ($templateData->content as $section) {
                    $secName = explode('. ', $section['section_title'])[0] ?? 'Section'; 
                    $secName = explode(' ', $secName)[0]; // "A", "B", "C"
                    $displayTitle = preg_replace('/^[A-Z][\.\s]+|^[A-Z]\.?\s*/i', '', $section['section_title']);
                    $categoryMap[$secName] = $displayTitle;
                    
                    if (!empty($section['is_resume']) && $section['is_resume'] == true) {
                        $categoryMap['F. RESUME'] = 'RESUME';
                        $categoryMap['RESUME'] = 'RESUME';
                    }
                }
            }
        @endphp

        @foreach($groupedDetails as $category => $details)
            @php
                // Jika kategori ada di mapping, gunakan nama panjangnya. Jika tidak, bersihkan saja.
                $displayCategory = $categoryMap[$category] ?? preg_replace('/^[A-Z][\.\s]+|^[A-Z]\.?\s*/i', '', $category);
                if (str_contains(strtoupper($category), 'RESUME')) {
                    $displayCategory = 'RESUME';
                }
            @endphp
            
            <div class="section-title">{{ strtoupper($displayCategory) }}</div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 45%;">Item Pengecekan / Uraian</th>
                        @if(str_contains($category, 'RESUME'))
                            <th style="width: 50%;">Keterangan / Temuan</th>
                        @else
                            <th style="width: 25%; text-align: center;">Kondisi</th>
                            <th style="width: 25%; text-align: center;">Metode</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $no = 1; 
                        $catatanKhusus = null;
                    @endphp
                    @foreach($details as $detail)
                        @if($detail->item_name === '_catatan_')
                            @php $catatanKhusus = $detail->kondisi; @endphp
                            @continue
                        @endif
                        <tr>
                            <td style="text-align: center;">{{ $no++ }}</td>
                            <td>{{ $detail->item_name }}</td>
                            
                            @if(str_contains($category, 'RESUME'))
                                <td>{!! $detail->kondisi !!}</td>
                            @else
                                <td style="text-align: center;">
                                    @php
                                        $k = strtolower($detail->kondisi);
                                        $class = $k == 'baik' ? 'status-baik' : ($k == 'kurang' ? 'status-kurang' : 'status-default');
                                    @endphp
                                    <span class="status-badge {{ $class }}">{{ $detail->kondisi }}</span>
                                </td>
                                <td style="text-align: center;">{{ $detail->metode }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            @if(!empty($catatanKhusus))
                <div style="background: #f8f9fa; border: 1px dashed #64b5f6; padding: 10px; margin-bottom: 20px; font-size: 9pt;">
                    <strong>Catatan / Temuan Khusus pada {{ strtoupper($displayCategory) }}:</strong>
                    <div style="margin-top: 5px; color: #444;">
                        {!! $catatanKhusus !!}
                    </div>
                </div>
            @endif
        @endforeach

        @if($input->catatan)
            <div class="section-title">CATATAN TAMBAHAN / REKOMENDASI</div>
            <div class="note-box">
                {!! $input->catatan !!}
            </div>
        @endif

        <!-- Lampiran Foto -->
        @php
            $filePaths = [];
            if ($input->file_path) {
                $decoded = json_decode($input->file_path, true);
                $filePaths = is_array($decoded) ? $decoded : [$input->file_path];
            }
            $imagePaths = array_filter($filePaths, function($path) {
                return preg_match('/\.(jpg|jpeg|png)$/i', $path);
            });
        @endphp

        @if(count($imagePaths) > 0)
            <div style="page-break-inside: avoid; margin-top: 30px;">
                <div class="section-title">LAMPIRAN FOTO / DOKUMENTASI</div>
                <div style="text-align: center; margin-top: 15px;">
                    @foreach($imagePaths as $path)
                        @php
                            $imgPath = public_path('storage/' . $path);
                            if (!file_exists($imgPath)) {
                                $imgPath = storage_path('app/public/' . $path);
                            }
                            
                            $imgBase64 = '';
                            if (file_exists($imgPath)) {
                                $ext = strtolower(pathinfo($imgPath, PATHINFO_EXTENSION));
                                $mime = $ext == 'jpg' ? 'jpeg' : $ext;
                                $data = file_get_contents($imgPath);
                                $imgBase64 = 'data:image/' . $mime . ';base64,' . base64_encode($data);
                            }
                        @endphp
                        @if($imgBase64)
                            <img src="{{ $imgBase64 }}" style="max-width: 45%; max-height: 250px; border: 1px solid #ccc; padding: 5px; margin: 5px; display: inline-block; vertical-align: top;">
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Signature Section -->
        <div class="signature-box">
            <table class="signature-table">
                <tr>
                    <td>
                        <div>Mengetahui,</div>
                        <div><strong>Manajemen / Supervisor</strong></div>
                        <span class="signature-line"></span>
                        <div>Nama: ______________________</div>
                    </td>
                    <td>
                        <div>Dibuat oleh,</div>
                        <div><strong>Inspektur Bertugas</strong></div>
                        <span class="signature-line"></span>
                        <div>Nama: {{ $input->user->name ?? 'N/A' }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </main>

</body>
</html>
