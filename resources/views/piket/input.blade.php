@extends('layouts.app')

@section('content')
<style>
    /* CSS for Redesign Form Input */
    .segmented-control { display: flex; background: var(--bg-tertiary); border-radius: 8px; padding: 4px; gap: 4px; width: 100%; }
    .segmented-control label { flex: 1; text-align: center; cursor: pointer; position: relative; margin: 0; }
    .segmented-control input[type="radio"] { position: absolute; opacity: 0; width: 0; height: 0; }
    .segmented-control .segment-btn { display: block; padding: 0.4rem 0.5rem; font-size: 0.8rem; font-weight: 600; border-radius: 6px; color: var(--text-secondary); transition: all 0.2s; white-space: nowrap; }
    
    /* Segmented Control Colors */
    .segmented-control input[type="radio"]:checked + .segment-btn.kondisi-baik { background: #10b981; color: white; box-shadow: 0 2px 4px rgba(16,185,129,0.2); }
    .segmented-control input[type="radio"]:checked + .segment-btn.kondisi-kurang { background: #f59e0b; color: white; box-shadow: 0 2px 4px rgba(245,158,11,0.2); }
    .segmented-control input[type="radio"]:checked + .segment-btn.kondisi-default { background: #3b82f6; color: white; box-shadow: 0 2px 4px rgba(59,130,246,0.2); }
    .segmented-control input[type="radio"]:checked + .segment-btn.metode-btn { background: var(--bg-secondary); border: 1px solid var(--accent-primary); color: var(--accent-primary); box-shadow: 0 2px 4px rgba(0,0,0,0.05); }

    /* Hide TinyMCE API Warning */
    .tox-notifications-container { display: none !important; }

    .accordion-item { margin-bottom: 1.5rem; border-radius: 12px; background: var(--bg-primary); box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.3s ease; }
    .accordion-item:hover { box-shadow: 0 8px 16px rgba(0,0,0,0.06); }
    .accordion-header { cursor: pointer; padding: 1.25rem 1.75rem; background: var(--bg-primary); display: flex; justify-content: space-between; align-items: center; border-radius: 12px; transition: all 0.2s ease; border: 1px solid var(--border-color); }
    .accordion-header:hover { background: var(--bg-secondary); border-color: var(--border-color); }
    .accordion-content { display: none; padding: 0; }
    .accordion-item.active .accordion-content { display: block; margin-bottom: 0; }
    .accordion-item.active .accordion-header { border-bottom-left-radius: 0; border-bottom-right-radius: 0; border-bottom: 1px dashed var(--border-color); background: var(--bg-secondary); margin-bottom: 0; }
    .accordion-icon { transition: transform 0.3s; color: var(--text-secondary); }
    .accordion-item.active .accordion-icon { transform: rotate(180deg); color: var(--accent-primary); }
    .acc-title-text { font-weight: 700; font-size: 1.05rem; color: var(--text-primary); }

    .checklist-row { display: flex; align-items: center; justify-content: space-between; padding: 1.2rem 1.5rem; border: 1px solid var(--border-color); border-top: none; background: var(--bg-primary); gap: 1.5rem; }
    .checklist-row:last-child { border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; }
    .checklist-title { flex: 1; font-weight: 500; font-size: 0.95rem; line-height: 1.4; }
    .checklist-controls { display: flex; gap: 1.5rem; align-items: center; width: 450px; flex-shrink: 0; }
    
    @media (max-width: 992px) {
        .checklist-row { flex-direction: column; align-items: flex-start; gap: 1rem; }
        .checklist-controls { width: 100%; display: grid; grid-template-columns: 1fr 1fr; }
    }

    .sticky-progress { position: sticky; top: 60px; z-index: 40; background: var(--bg-primary); padding: 1rem 0; border-bottom: 1px solid var(--border-color); margin-bottom: 2rem; }
    
    .resume-row { display: flex; flex-direction: column; gap: 0.8rem; padding: 1.2rem 1.5rem; border: 1px solid var(--border-color); border-top: none; background: var(--bg-primary); }
    .resume-row:last-child { border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; }
    
    .checklist-row.error, .resume-row.error { border-left: 4px solid var(--danger); background: rgba(239, 68, 68, 0.05); }
    .error-msg { color: var(--danger); font-size: 0.75rem; font-weight: bold; display: none; margin-top: 5px; }
    .error .error-msg { display: block; }
    
    /* Utility Classes Extracted from Inline Styles */
    .acc-title-wrapper { display: flex; align-items: center; gap: 0.5rem; }
    .acc-title-text { font-weight: 700; font-size: 1.05rem; }
    .acc-action-wrapper { display: flex; align-items: center; gap: 1rem; }
    .btn-add-item { padding: 0.2rem 0.5rem; font-size: 0.75rem; }
    .segment-label-text { font-size: 0.7rem; color: var(--text-secondary); margin-bottom: 0.3rem; font-weight: bold; text-transform: uppercase; }
    .btn-delete-item { color: var(--danger); background: none; border: none; cursor: pointer; font-size: 1.2rem; }
    
    .card-filter { padding: 1.5rem; border-radius: 16px; border: 1px solid var(--border-color); box-shadow: 0 4px 20px rgba(0,0,0,0.03); }
    .filter-wrapper { display: flex; flex-wrap: wrap; gap: 2rem; justify-content: space-between; align-items: center; }
    .filter-group { display: flex; gap: 1.5rem; flex: 1; min-width: 300px; }
    .filter-label { font-size: 0.75rem; color: var(--text-secondary); font-weight: 700; letter-spacing: 0.5px; display: block; margin-bottom: 0.5rem; text-transform: uppercase; }
    .filter-val-wrapper { display: flex; align-items: center; gap: 0.8rem; }
    .icon-box { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
    .icon-box.blue { background: rgba(14, 165, 233, 0.1); color: var(--accent-primary); }
    .icon-box.green { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .filter-text { font-size: 1.1rem; font-weight: 700; }
    .input-lokasi { background: transparent; border: none; border-bottom: 1px solid var(--accent-primary); border-radius: 0; padding: 0.5rem 0; font-weight: bold; font-size: 1.1rem; width: 100%; }
    .filter-divider { border-left: 1px solid var(--border-color); padding-left: 2rem; }
    .select-jenis { width: 250px; font-weight: bold; font-size: 1rem; padding: 0.6rem 1rem; border-radius: 8px; }
    
    .section-title-heading { font-size: 1.2rem; color: var(--text-primary); border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem; }
    .alert-empty { background: rgba(245, 158, 11, 0.1); border-color: #f59e0b; color: #f59e0b; padding: 1.5rem; border-radius: 12px; display: flex; gap: 1rem; align-items: center; }
    
    .card-penyelesaian { padding: 2rem; border-radius: 16px; border: 1px solid var(--border-color); background: var(--bg-secondary); }
    .dropzone { border: 2px dashed var(--border-color); border-radius: 12px; padding: 2rem; text-align: center; background: var(--bg-primary); transition: border-color 0.2s; }
    .btn-submit-main { font-size: 1.1rem; border-radius: 12px; font-weight: 700; padding: 1.2rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3); }
    
    .card-history { padding: 2rem; border-radius: 16px; border: 1px solid var(--border-color); background: var(--bg-primary); }
    .history-title { font-size: 1.25rem; }
</style>

@php
function renderSection($sectionName, $subSectionName, $items, $kondisiOptions = ['Baik', 'Kurang'], $metodeOptions = ['Site Visit', 'Online'], $sIndex = null, $subIndex = null, $templateId = null, $customTitle = null, $draftDetails = []) {
    $accordionId = 'acc_' . md5($sectionName . $subSectionName);
    $title = $customTitle ?: (!empty($subSectionName) ? $subSectionName : $sectionName);
    
        
    $title = ucwords(strtolower($title));
    
    // Perbaikan spesifik untuk singkatan penting
    $title = str_replace(['Hsse', 'Sdm', 'Cctv', 'B/m'], ['HSSE', 'SDM', 'CCTV', 'B/M'], $title);

    // Make accordion closed by default
    $html = '<div class="accordion-item" id="'.$accordionId.'">';
    
    // Header
    $html .= '<div class="accordion-header" onclick="toggleAccordion(\''.$accordionId.'\')">';
    $html .= '<div class="acc-title-wrapper">';
    $html .= '<span class="acc-title-text">' . htmlspecialchars($title) . '</span>';
    $html .= '</div>';
    
    $html .= '<div class="acc-action-wrapper">';
    $html .= '<span class="section-score-badge" style="display:none; font-size: 0.75rem; font-weight: bold; padding: 0.3rem 0.6rem; border-radius: 6px;">0%</span>';
    if (auth()->check() && auth()->user()->role === 'admin' && $templateId !== null) {
        // Button removed per user request
    }
    $html .= '<svg class="accordion-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>';
    $html .= '</div>';
    $html .= '</div>';
    
    // Content
    $html .= '<div class="accordion-content">';
    $safeSubName = $subSectionName === '' ? 'None' : $subSectionName;
    
    foreach ($items as $itemIdx => $item) {
        $html .= '<div class="checklist-row">';
        $html .= '<div class="checklist-title">' . htmlspecialchars($item) . '</div>';
        
        $html .= '<div class="checklist-controls">';
        
        $draftKondisi = $draftDetails[$sectionName][$safeSubName][$item]['kondisi'] ?? null;
        $draftMetode = $draftDetails[$sectionName][$safeSubName][$item]['metode'] ?? null;
        
        // Kondisi Segmented Control
        if (!empty($kondisiOptions)) {
            $html .= '<div style="flex: 1;">';
            $html .= '<div class="segment-label-text">Kondisi</div>';
            $html .= '<div class="segmented-control">';
            foreach($kondisiOptions as $k) {
                $req = $k === $kondisiOptions[0] ? 'data-required="true"' : '';
                $colorClass = strtolower($k) == 'baik' ? 'kondisi-baik' : (strtolower($k) == 'kurang' ? 'kondisi-kurang' : 'kondisi-default');
                $inputId = 'kondisi_' . md5($sectionName.$safeSubName.$item.$k);
                $checked = ($draftKondisi === $k) ? 'checked' : '';
                $html .= '<label for="'.$inputId.'">';
                $html .= '<input type="radio" id="'.$inputId.'" name="items['.$sectionName.']['.$safeSubName.']['.$item.'][kondisi]" value="'.$k.'" '.$req.' '.$checked.' onchange="updateProgress()">';
                $html .= '<span class="segment-btn '.$colorClass.'">'.$k.'</span>';
                $html .= '</label>';
            }
            $html .= '</div></div>';
        }

        // Metode Segmented Control
        if (!empty($metodeOptions)) {
            $html .= '<div style="flex: 1;">';
            $html .= '<div class="segment-label-text">Metode</div>';
            $html .= '<div class="segmented-control">';
            foreach($metodeOptions as $m) {
                $req = $m === $metodeOptions[0] ? 'data-required="true"' : '';
                $inputId = 'metode_' . md5($sectionName.$safeSubName.$item.$m);
                $checked = ($draftMetode === $m) ? 'checked' : '';
                $html .= '<label for="'.$inputId.'">';
                $html .= '<input type="radio" id="'.$inputId.'" name="items['.$sectionName.']['.$safeSubName.']['.$item.'][metode]" value="'.$m.'" '.$req.' '.$checked.' onchange="updateProgress()">';
                $html .= '<span class="segment-btn metode-btn">'.$m.'</span>';
                $html .= '</label>';
            }
            $html .= '</div></div>';
        }
        
        $html .= '</div>'; // End controls
        $html .= '</div>'; // End row
    }
    
    // Add TinyMCE textarea for section notes
    $draftCatatan = $draftDetails[$sectionName][$safeSubName]['_catatan_']['kondisi'] ?? '';
    $html .= '<div style="padding: 1.5rem; background: var(--bg-secondary); border-top: 1px dashed var(--border-color); border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">';
    $html .= '<label style="display: block; font-size: 0.85rem; color: var(--text-secondary); font-weight: 700; margin-bottom: 0.8rem; text-transform: uppercase;">Catatan / Temuan Khusus pada '.htmlspecialchars($title).' (Opsional)</label>';
    $html .= '<textarea class="tinymce-editor" name="items['.$sectionName.']['.$safeSubName.'][_catatan_][kondisi]" rows="4" placeholder="Tambahkan catatan khusus untuk bagian ini...">'.htmlspecialchars($draftCatatan).'</textarea>';
    $html .= '</div>';
    
    $html .= '</div>'; // End content
    $html .= '</div>'; // End accordion item
    return $html;
}

function renderResumeSection($sectionName, $items, $sIndex = null, $subIndex = null, $templateId = null, $draftDetails = [], $displayTitle = null) {
    $accordionId = 'acc_' . md5($sectionName . 'Resume');
    
    if ($displayTitle) {
        $displaySectionName = $displayTitle;
    } else {
        $displaySectionName = ucwords(strtolower($sectionName));
        $displaySectionName = str_replace(['Hsse', 'Sdm', 'Cctv', 'B/m', 'Sbpp'], ['HSSE', 'SDM', 'CCTV', 'B/M', 'SBPP'], $displaySectionName);
        $displaySectionName = preg_replace('/^[A-Z]\.\s*/i', '', $displaySectionName);
    }

    // Make accordion closed by default
    $html = '<div class="accordion-item" id="'.$accordionId.'">';
    
    // Header
    $html .= '<div class="accordion-header" onclick="toggleAccordion(\''.$accordionId.'\')">';
    $html .= '<div class="acc-title-wrapper">';
    $html .= '<span class="acc-title-text">' . htmlspecialchars($displaySectionName) . '</span>';
    $html .= '</div>';
    
    $html .= '<div class="acc-action-wrapper">';
    if (auth()->check() && auth()->user()->role === 'admin' && $templateId !== null) {
        // Button removed per user request
    }
    $html .= '<svg class="accordion-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>';
    $html .= '</div>';
    $html .= '</div>';
    
    // Content
    $html .= '<div class="accordion-content">';
    
    foreach ($items as $itemIdx => $item) {
        $html .= '<div class="resume-row">';
        $html .= '<div style="display: flex; justify-content: space-between; align-items: center;">';
        $html .= '<span style="font-weight: 600; font-size: 0.95rem;">' . htmlspecialchars($item) . '</span>';
        $html .= '</div>';
        $draftUraian = $draftDetails[$sectionName]['Resume'][$item]['kondisi'] ?? '';
        $html .= '<textarea name="items['.$sectionName.'][Resume]['.$item.'][uraian]" class="form-control req-resume tinymce-editor" rows="2" placeholder="Tuliskan uraian detail di sini..." required>'.htmlspecialchars($draftUraian).'</textarea>';
        $html .= '</div>';
    }
    
    $html .= '</div>';
    $html .= '</div>';
    return $html;
}
@endphp

@section('content')
<style>
    /* Modern Action Buttons */
    .btn-action {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 6px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        cursor: pointer;
        background-color: #fff;
        white-space: nowrap;
    }
    .btn-action svg {
        margin-right: 0.3rem;
    }
    .btn-action:hover {
        transform: translateY(-1px);
    }
    .btn-draft { color: #f59e0b; border: 1px solid #fcd34d; }
    .btn-draft:hover { background-color: #fffbeb; border-color: #f59e0b; }
    .btn-pdf { color: #ef4444; border: 1px solid #fca5a5; }
    .btn-pdf:hover { background-color: #fef2f2; border-color: #ef4444; }
    .btn-gambar { color: #8b5cf6; border: 1px solid #c4b5fd; }
    .btn-gambar:hover { background-color: #f5f3ff; border-color: #8b5cf6; }
    .btn-download { color: #3b82f6; border: 1px solid #93c5fd; }
    .btn-download:hover { background-color: #eff6ff; border-color: #3b82f6; }
</style>
<div class="container mt-8 pb-8" style="max-width: 1400px;">
    
    <!-- Modern Header / Filters -->
    <div class="card card-filter" style="margin-bottom: 3rem;">
        <div class="filter-wrapper">
            <div class="filter-group">
                <!-- Lokasi Info -->
                <div style="flex: 1;">
                    <label class="filter-label">Lokasi Cabang</label>
                    <div class="filter-val-wrapper">
                        <div class="icon-box blue">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        </div>
                        @if(auth()->check() && auth()->user()->lokasi_fix)
                            <div class="filter-text">{{ auth()->user()->lokasi_fix }}</div>
                        @else
                            <select id="lokasi-input" class="form-control input-lokasi" onchange="window.location.href='?jenis_piket={{ request('jenis_piket') }}&lokasi=' + this.value">
                                <option value="TPK Kupang" {{ $lokasi == 'TPK Kupang' ? 'selected' : '' }}>TPK Kupang</option>
                                <option value="TPK Makassar" {{ $lokasi == 'TPK Makassar' ? 'selected' : '' }}>TPK Makassar</option>
                                <option value="Cabang Balikpapan" {{ $lokasi == 'Cabang Balikpapan' ? 'selected' : '' }}>Cabang Balikpapan</option>
                                <option value="Cabang Tanjung Redeb" {{ $lokasi == 'Cabang Tanjung Redeb' ? 'selected' : '' }}>Cabang Tanjung Redeb</option>
                                <option value="Cabang Bitung" {{ $lokasi == 'Cabang Bitung' ? 'selected' : '' }}>Cabang Bitung</option>
                                <option value="Cabang Kendari" {{ $lokasi == 'Cabang Kendari' ? 'selected' : '' }}>Cabang Kendari</option>
                                <option value="Cabang Ambon" {{ $lokasi == 'Cabang Ambon' ? 'selected' : '' }}>Cabang Ambon</option>
                                <option value="Gorontalo" {{ $lokasi == 'Gorontalo' ? 'selected' : '' }}>Gorontalo</option>
                            </select>
                        @endif
                    </div>
                </div>

                <!-- Tanggal Info -->
                <div style="flex: 1;">
                    <label class="filter-label">Tanggal Piket</label>
                    <div class="filter-val-wrapper">
                        <div class="icon-box green">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        </div>
                        <div class="filter-text">{{ \Carbon\Carbon::parse(date('Y-m-d'))->translatedFormat('d F Y') }}</div>
                    </div>
                </div>
            </div>

            <div class="filter-divider">
                <label class="filter-label">Jenis Template Piket</label>
                <select name="jenis_nav" class="form-control select-jenis" onchange="window.location.href='?lokasi={{ request('lokasi') }}&jenis_piket=' + this.value">
                    <option value="">-- Pilih Jenis Piket --</option>
                    <option value="Daily" {{ $jenis_piket == 'Daily' ? 'selected' : '' }}>Daily</option>
                    <option value="Angkutan Lebaran" {{ $jenis_piket == 'Angkutan Lebaran' ? 'selected' : '' }}>Angkutan Lebaran</option>
                    <option value="Libur Nataru" {{ $jenis_piket == 'Libur Nataru' ? 'selected' : '' }}>Libur Nataru</option>
                </select>
            </div>
        </div>
    </div>

    @if($jenis_piket)
        @php
            $draftDetailsData = [];
            if(isset($draft)) {
                foreach($draft->details as $d) {
                    $draftDetailsData[$d->category][$d->subcategory][$d->item_name] = [
                        'kondisi' => $d->kondisi,
                        'metode' => $d->metode,
                    ];
                }
            }
        @endphp
        
        @if(isset($draft))
        <form method="POST" action="{{ route('piket.update', $draft->id) }}" enctype="multipart/form-data" class="mb-12" id="piketForm">
            @method('PUT')
        @else
        <form method="POST" action="{{ route('piket.store') }}" enctype="multipart/form-data" class="mb-12" id="piketForm">
        @endif
            @csrf
            <!-- Hidden inputs -->
            <input type="hidden" id="hidden-lokasi" name="lokasi" value="{{ (auth()->check() && auth()->user()->lokasi_fix) ? auth()->user()->lokasi_fix : 'TPK Kupang' }}">
            <input type="hidden" name="tanggal" value="{{ date('Y-m-d') }}">
            <input type="hidden" name="jenis_piket" value="{{ $jenis_piket }}">

            <!-- Progress Bar -->
            <div class="card" style="padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; position: sticky; top: 20px; z-index: 10; background: var(--bg-primary); border-left: 4px solid var(--accent-primary); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem;">
                    <div style="font-weight: 800; font-size: 1rem; color: var(--text-primary);">Progress Pengisian Form</div>
                    <div style="font-weight: 900; font-size: 1.2rem; color: var(--accent-primary);" id="progress-text">0%</div>
                </div>
                <div style="width: 100%; height: 10px; background: var(--bg-tertiary); border-radius: 5px; overflow: hidden;">
                    <div id="progress-bar" style="width: 0%; height: 100%; background: linear-gradient(90deg, #3b82f6, #10b981); transition: width 0.3s ease, background 0.3s ease;"></div>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.5rem; font-style: italic;">Progress dapat dilanjutkan nanti dengan menekan tombol "Simpan Draft" di bawah form.</div>
            </div>

            @if(isset($template) && is_array($template->content))
                @foreach($template->content as $sIndex => $section)
                    @php
                        // Hapus format "A. ", "B. ", dll dari judul untuk tampilan
                        $displayTitle = preg_replace('/^[A-Z]\.\s*/', '', $section['section_title']);
                        
                        // Format ke Title Case
                        $displayTitle = ucwords(strtolower($displayTitle));
                        $displayTitle = str_replace(['Hsse', 'Sdm', 'Cctv', 'B/m'], ['HSSE', 'SDM', 'CCTV', 'B/M'], $displayTitle);
                        
                        // Jika hanya ada 1 subsection dan namanya sangat pendek (seperti 'D' atau 'E'), 
                        // maka subsection tersebut akan memakai nama section utama. 
                        // Untuk menghindari judul ganda, kita sembunyikan H3 ini.
                        $hideHeader = false;
                        if (count($section['subsections']) === 1) {
                            $firstSubName = trim($section['subsections'][0]['name']);
                            if (strlen($firstSubName) <= 2 || empty($firstSubName)) {
                                $hideHeader = true;
                            }
                        }
                    @endphp
                    
                    @if(!$hideHeader)
                        <h3 class="font-bold mb-4 mt-8 section-title-heading">{{ $displayTitle }}</h3>
                    @endif
                    
                    @foreach($section['subsections'] as $subIndex => $sub)
                        @if($section['is_resume'])
                            {!! renderResumeSection('RESUME', $sub['items'], $sIndex, $subIndex, $template->id, $draftDetailsData, $displayTitle) !!}
                        @else
                            @php
                                $secName = explode('. ', $section['section_title'])[0] ?? 'Section'; 
                                $secName = explode(' ', $secName)[0];
                                $displaySubName = null;
                                if (strlen(trim($sub['name'])) <= 2 || empty(trim($sub['name']))) {
                                    $displaySubName = $displayTitle;
                                }
                            @endphp
                            {!! renderSection($secName, $sub['name'], $sub['items'], $sub['kondisi_options'] ?? [], $sub['metode_options'] ?? [], $sIndex, $subIndex, $template->id, $displaySubName, $draftDetailsData) !!}
                        @endif
                    @endforeach
                @endforeach
            @else
                <div class="alert alert-warning mb-4 alert-empty">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <div>
                        <strong style="display: block; font-size: 1.1rem; margin-bottom: 0.2rem;">Template Kosong</strong>
                        <span>Template untuk jenis piket ini belum tersedia di database. Hubungi administrator.</span>
                    </div>
                </div>
            @endif


    <!-- DOWNLOAD TEMPLATE SECTION -->
    <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1rem;">
        <a href="{{ route('piket.template', ['type' => 'daily']) }}" class="btn btn-outline" style="font-size: 0.85rem; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600;">
            📄 Download Template Daily
        </a>
        <a href="{{ route('piket.template', ['type' => 'angkutan']) }}" class="btn btn-outline" style="font-size: 0.85rem; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600;">
            📄 Download Template Angkutan
        </a>
    </div>

            <!-- UPLOAD LAMPIRAN & CATATAN -->
            <div class="card mt-8 card-penyelesaian">
                <h3 class="font-bold mb-4" style="font-size: 1.2rem; color: var(--text-primary);">Penyelesaian</h3>
                
                <div class="form-group mb-6">
                    <label class="form-label acc-title-wrapper" style="font-weight: 700;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--accent-primary);"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
                        Upload Lampiran Laporan (Opsional)
                    </label>
                    <div class="dropzone" id="dropzone">
                        <input type="file" name="lampiran[]" id="lampiran" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" multiple style="display: none;" onchange="updateFileName()">
                        <label for="lampiran" style="cursor: pointer; display: block; margin: 0;">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--text-secondary)" stroke-width="2" style="margin: 0 auto 1rem auto;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                            <span style="color: var(--accent-primary); font-weight: 600;">Klik untuk memilih file</span> atau tarik file ke sini
                            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;" id="file-name">
                                {{ isset($draft) && $draft->file_path ? 'File Tersimpan: ' . basename($draft->file_path) : '' }}
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.5rem;">Format: PDF, DOC, DOCX, JPG, PNG. Max: 10MB. {{ isset($draft) && $draft->file_path ? '(Abaikan jika tidak ingin mengubah file)' : '' }}</div>
                        </label>
                    </div>
                </div>

                <div class="form-group mb-8">
                    <label class="form-label" style="font-weight: 700;">Catatan Tambahan (Opsional)</label>
                    <textarea name="catatan" class="form-control tinymce-editor" rows="3" placeholder="Tambahkan informasi ekstra yang relevan..." style="border-radius: 12px;">{{ isset($draft) ? $draft->catatan : '' }}</textarea>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <button type="submit" name="action" value="draft" formnovalidate onclick="submittedAction = 'draft'" class="btn btn-outline w-full" id="draftBtn" style="border-color: #94a3b8; color: #475569; padding: 1rem; border-radius: 12px; font-weight: 700;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline-block; vertical-align:middle; margin-right: 5px;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                        SIMPAN DRAFT
                    </button>
                    <button type="submit" name="action" value="submit" onclick="submittedAction = 'submit'" class="btn btn-primary w-full btn-submit-main" id="submitBtn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline-block; vertical-align:middle; margin-right: 5px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        KIRIM DATA
                    </button>
                </div>
            </div>
        </form>
    @endif


    <!-- HISTORY TABLE (Always shown, at the bottom) -->
    <div class="card mt-4 card-history" id="history-section">
        <div class="flex items-center gap-2 mb-6 text-accent font-bold history-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
            Riwayat Data Input Cabang
        </div>
        
        <div class="table-wrapper" style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; min-width: 1000px;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-color); text-align: left;">
                        <th style="padding: 1rem; color: var(--text-secondary);">TANGGAL</th>
                        <th style="padding: 1rem; color: var(--text-secondary);">LOKASI</th>
                        <th style="padding: 1rem; color: var(--text-secondary);">JENIS</th>
                        <th style="padding: 1rem; color: var(--text-secondary);">PETUGAS</th>
                        <th style="padding: 1rem; color: var(--text-secondary);">STATUS</th>
                        <th style="padding: 1rem; color: var(--text-secondary);">SKOR</th>
                        <th style="padding: 1rem; color: var(--text-secondary);">PROGRESS</th>
                        <th style="padding: 1rem; color: var(--text-secondary);">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Ambil 5 riwayat terakhir dari database untuk ditampilkan di sini
                        // Controller mengirimkan data riwayat dalam variabel $history
                        $riwayat = $history ?? [];
                    @endphp
                    @forelse($riwayat as $row)
                    <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.2s;">
                        <td style="padding: 1rem; color: var(--text-primary); white-space: nowrap;">{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d M Y') }}</td>
                        <td style="padding: 1rem; font-weight: bold; color: var(--text-primary); white-space: nowrap;">
                            {{ $row->lokasi }}
                        </td>
                        <td style="padding: 1rem; color: var(--text-primary); white-space: nowrap;">{{ $row->jenis_piket }}</td>
                        <td style="padding: 1rem; color: var(--text-primary); white-space: nowrap;">{{ $row->user ? $row->user->name : 'N/A' }}</td>
                        <td style="padding: 1rem;">
                            @if($row->status == 'pending')
                                <span style="background: #fef3c7; color: #d97706; padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.8rem; font-weight: bold; white-space: nowrap;">Menunggu Konfirmasi</span>
                            @elseif($row->status == 'approved' || $row->status == 'submitted')
                                <span style="background: #d1fae5; color: #059669; padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.8rem; font-weight: bold; white-space: nowrap;">Disetujui</span>
                            @elseif($row->status == 'rejected')
                                <span style="background: #fee2e2; color: #dc2626; padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.8rem; font-weight: bold; white-space: nowrap;">Ditolak</span>
                                @if($row->alasan_tolak)
                                    <div style="margin-top: 0.5rem; padding: 0.5rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; font-size: 0.75rem;">
                                        <strong style="color: #dc2626;">Alasan Penolakan:</strong><br>
                                        <div style="color: #7f1d1d;">{!! $row->alasan_tolak !!}</div>
                                    </div>
                                @endif
                            @else
                                <span style="background: #e2e8f0; color: #475569; padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.8rem; font-weight: bold; white-space: nowrap;">Draft</span>
                            @endif
                        </td>
                        <td style="padding: 1rem;">
                            @if($row->status == 'submitted')
                                @if($row->score >= 65)
                                    <span style="background: #d1fae5; color: #059669; padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.8rem; font-weight: bold; white-space: nowrap;">{{ $row->score }}% (Baik)</span>
                                @elseif($row->score >= 35)
                                    <span style="background: #fef3c7; color: #d97706; padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.8rem; font-weight: bold; white-space: nowrap;">{{ $row->score }}% (Perlu Atensi)</span>
                                @else
                                    <span style="background: #fee2e2; color: #dc2626; padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.8rem; font-weight: bold; white-space: nowrap;">{{ $row->score }}% (Kritis)</span>
                                @endif
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                        <td style="padding: 1rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="width: 80px; height: 6px; background: var(--bg-tertiary); border-radius: 3px; overflow: hidden;">
                                    <div style="width: {{ $row->persentase }}%; height: 100%; background: {{ $row->persentase == 100 ? '#10b981' : ($row->persentase >= 50 ? '#f59e0b' : '#ef4444') }};"></div>
                                </div>
                                <span style="font-weight: bold; font-size: 0.85rem;">{{ $row->persentase }}%</span>
                            </div>
                        </td>
                        <td style="padding: 1rem; display: flex; gap: 0.5rem; flex-wrap: nowrap;">
                            @if($row->status == 'draft' || $row->status == 'rejected')
                                <a href="{{ route('piket.edit', $row->id) }}" class="btn-action btn-draft" title="Lanjutkan Pengisian">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                    @if($row->status == 'rejected') Perbaiki @else Lanjutkan @endif
                                </a>
                            @else
                                <a href="{{ route('piket.exportPdf', $row->id) }}" class="btn-action btn-pdf" title="Download PDF">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                    PDF
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 2rem; color: var(--text-secondary);">Belum ada penginputan piket.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            @if(isset($history) && $history->total() >= 20)
            <div class="mt-4 text-right">
                <a href="{{ route('piket.history') }}" class="btn btn-primary btn-sm" style="font-size: 0.8rem; font-weight: bold;">Lihat Semua Riwayat &rarr;</a>
            </div>
            @endif
        </div>
    </div>
</div>
</div>

<!-- Custom Modals -->
<div id="customModalOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease;">
    
    <!-- Add Item Modal -->
    <div id="addItemModal" style="display: none; background: #ffffff; width: 90%; max-width: 450px; border-radius: 16px; padding: 2.5rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); transform: translateY(20px); transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
        <h3 style="margin-top: 0; color: #0f172a; font-weight: 800; font-size: 1.3rem;">Tambah Item Baru</h3>
        <p style="color: #64748b; font-size: 0.95rem; margin-bottom: 1.5rem; line-height: 1.5;">Silakan masukkan nama fasilitas atau komponen baru yang ingin ditambahkan ke dalam daftar pemeriksaan.</p>
        
        <input type="text" id="newItemName" class="form-control" placeholder="Ketik nama item di sini..." style="width: 100%; background: #f8fafc; border: 2px solid #e2e8f0; padding: 1rem 1.2rem; border-radius: 10px; font-size: 1.05rem; color: #0f172a; margin-bottom: 2rem; outline: none; transition: all 0.2s; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);" onfocus="this.style.borderColor='#0ea5e9'; this.style.backgroundColor='#ffffff';">
        
        <div style="display: flex; justify-content: flex-end; gap: 1rem;">
            <button type="button" class="btn btn-outline" onclick="closeCustomModal()" style="padding: 0.7rem 1.5rem; border-radius: 10px; font-weight: 600; border-color: #cbd5e1; color: #475569;">Batal</button>
            <button type="button" class="btn btn-primary" id="btnConfirmAdd" style="padding: 0.7rem 1.5rem; border-radius: 10px; font-weight: 700;">Simpan Item</button>
        </div>
    </div>

    <!-- Confirm Delete Modal -->
    <div id="deleteItemModal" style="display: none; background: #ffffff; width: 90%; max-width: 400px; border-radius: 16px; padding: 2.5rem; text-align: center; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); transform: translateY(20px); transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
        <div style="width: 64px; height: 64px; background: #fee2e2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
        </div>
        <h3 style="margin-top: 0; color: #0f172a; font-weight: 800; font-size: 1.3rem;">Hapus Item?</h3>
        <p style="color: #64748b; font-size: 0.95rem; margin-bottom: 2rem; line-height: 1.5;">Apakah Anda yakin ingin menghapus item ini secara permanen? Tindakan ini tidak dapat dibatalkan.</p>
        
        <div style="display: flex; justify-content: center; gap: 1rem;">
            <button type="button" class="btn btn-outline" onclick="closeCustomModal()" style="padding: 0.7rem 1.5rem; border-radius: 10px; font-weight: 600; flex: 1; border-color: #cbd5e1; color: #475569;">Batal</button>
            <button type="button" class="btn" id="btnConfirmDelete" style="background: #ef4444; color: white; padding: 0.7rem 1.5rem; border-radius: 10px; font-weight: 700; flex: 1; border: none; cursor: pointer;">Ya, Hapus</button>
        </div>
    </div>
</div>

<script>
    // Accordion Logic
    function toggleAccordion(id) {
        const item = document.getElementById(id);
        item.classList.toggle('active');
    }

    // Progress Bar Logic
    function updateProgress() {
        const form = document.getElementById('piketForm');
        if (!form) return;

        // Hitung total input (radio buttons group dan textarea yang required)
        const radioGroups = new Set();
        const radios = form.querySelectorAll('input[type="radio"][data-required="true"]');
        radios.forEach(r => radioGroups.add(r.name));
        
        const textareas = form.querySelectorAll('textarea.req-resume');
        
        const totalRequired = radioGroups.size + textareas.length;
        if (totalRequired === 0) return;

        // Hitung yang sudah diisi
        let filled = 0;
        radioGroups.forEach(name => {
            if (form.querySelector('input[name="'+name+'"]:checked')) {
                filled++;
            }
        });
        
        textareas.forEach(ta => {
            if (ta.value.trim().length > 0) {
                filled++;
            }
        });

        const percentage = Math.round((filled / totalRequired) * 100);
        
        const progressBar = document.getElementById('progress-bar');
        const progressText = document.getElementById('progress-text');
        
        progressBar.style.width = percentage + '%';
        progressText.innerText = percentage + '%';
        
        // Change color based on completion
        if (percentage === 100) {
            progressBar.style.background = '#10b981';
            progressText.style.color = '#10b981';
        } else {
            progressBar.style.background = 'linear-gradient(90deg, #3b82f6, #10b981)';
            progressText.style.color = 'var(--accent-primary)';
        }

        // Hitung skor per accordion section
        const accordions = document.querySelectorAll('.accordion-item');
        accordions.forEach(acc => {
            const accRadioGroups = new Set();
            acc.querySelectorAll('input[type="radio"][data-required="true"]').forEach(r => {
                // Hanya hitung grup untuk "Kondisi" karena skor berdasarkan Baik/Kurang
                if (r.name.includes('[kondisi]')) {
                    accRadioGroups.add(r.name);
                }
            });
            const accTotal = accRadioGroups.size;
            
            if (accTotal > 0) {
                let accBaik = 0;
                let accFilled = 0;
                accRadioGroups.forEach(name => {
                    const checked = acc.querySelector('input[name="'+name+'"]:checked');
                    if (checked) {
                        accFilled++;
                        if (checked.value.toLowerCase() === 'baik') {
                            accBaik++;
                        }
                    }
                });
                
                const badge = acc.querySelector('.section-score-badge');
                if (badge) {
                    if (accFilled > 0) {
                        // Hitung skor berdasarkan item yang sudah diisi, ATAU berdasarkan total item di section?
                        // Untuk live preview, lebih baik berdasarkan total item agar merefleksikan final score.
                        const accScore = Math.round((accBaik / accTotal) * 100);
                        badge.style.display = 'inline-block';
                        badge.innerText = accScore + '%';
                        if (accScore >= 65) {
                            badge.style.background = 'rgba(16, 185, 129, 0.15)';
                            badge.style.color = '#059669';
                            badge.innerText += ' (Baik)';
                        } else if (accScore >= 35) {
                            badge.style.background = 'rgba(245, 158, 11, 0.15)';
                            badge.style.color = '#d97706';
                            badge.innerText += ' (Atensi)';
                        } else {
                            badge.style.background = 'rgba(239, 68, 68, 0.15)';
                            badge.style.color = '#dc2626';
                            badge.innerText += ' (Kritis)';
                        }
                    } else {
                        badge.style.display = 'none';
                    }
                }
            }
        });
    }

    // Initialize first accordion and progress
    document.addEventListener('DOMContentLoaded', () => {
        const accordions = document.querySelectorAll('.accordion-item');
        if (accordions.length > 0) {
            accordions[0].classList.add('active'); // Open first by default
        }
        updateProgress();
    });

    function updateFileName() {
        const input = document.getElementById('lampiran');
        const display = document.getElementById('file-name');
        if (input.files.length > 0) {
            if (input.files.length === 1) {
                display.innerText = "File Terpilih: " + input.files[0].name;
            } else {
                display.innerText = input.files.length + " File Terpilih";
            }
            document.getElementById('dropzone').style.borderColor = "var(--accent-primary)";
            document.getElementById('dropzone').style.background = "rgba(14, 165, 233, 0.05)";
        } else {
            display.innerText = "";
            document.getElementById('dropzone').style.borderColor = "var(--border-color)";
            document.getElementById('dropzone').style.background = "var(--bg-primary)";
        }
    }

    let submittedAction = 'submit';

    // Validate Checkboxes inside active tab before form submit
    function initValidation() {
        const formEl = document.getElementById('piketForm');
        if (formEl) {
            formEl.addEventListener('submit', function(e) {
                if (submittedAction === 'draft') {
                    return; // Skip validation if saving draft
                }

                // Remove previous error highlights
            document.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
            
            let hasError = false;
            let firstErrorElement = null;

            // Check radios
            const radioGroups = new Set();
            formEl.querySelectorAll('input[type="radio"][data-required="true"]').forEach(r => radioGroups.add(r.name));
            
            radioGroups.forEach(name => {
                if (!formEl.querySelector('input[name="'+name+'"]:checked')) {
                    hasError = true;
                    // Find the row containing this radio group
                    const row = formEl.querySelector('input[name="'+name+'"]').closest('.checklist-row');
                    if (row) {
                        row.classList.add('error');
                        if (!firstErrorElement) firstErrorElement = row;
                        
                        // Expand the parent accordion if it's closed
                        const accordionItem = row.closest('.accordion-item');
                        if (accordionItem && !accordionItem.classList.contains('active')) {
                            accordionItem.classList.add('active');
                        }
                    }
                }
            });

            // Check textareas
            const textareas = formEl.querySelectorAll('textarea.req-resume');
            textareas.forEach(ta => {
                if (ta.value.trim().length === 0) {
                    hasError = true;
                    const row = ta.closest('.resume-row');
                    if (row) {
                        row.classList.add('error');
                        if (!firstErrorElement) firstErrorElement = row;
                        
                        const accordionItem = row.closest('.accordion-item');
                        if (accordionItem && !accordionItem.classList.contains('active')) {
                            accordionItem.classList.add('active');
                        }
                    }
                }
            });

            if (hasError) {
                e.preventDefault(); // Stop submission
                
                // Show a brief alert to the user using SweetAlert2
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Terdapat bagian form yang belum diisi. Silakan periksa bagian yang ditandai merah.',
                    confirmButtonColor: '#0ea5e9'
                });
                
                // Scroll to the first error smoothly
                if (firstErrorElement) {
                    firstErrorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
        }
    }
    
    // Call the validation setup
    document.addEventListener('DOMContentLoaded', initValidation);

    // Custom Modal Logic
    const modalOverlay = document.getElementById('customModalOverlay');
    const addItemModal = document.getElementById('addItemModal');
    const deleteItemModal = document.getElementById('deleteItemModal');
    const inputNewItem = document.getElementById('newItemName');
    
    let currentAddAction = null;
    let currentDeleteAction = null;

    function openCustomModal(type) {
        modalOverlay.style.display = 'flex';
        // Force reflow for transition
        void modalOverlay.offsetWidth;
        modalOverlay.style.opacity = '1';
        
        if (type === 'add') {
            addItemModal.style.display = 'block';
            deleteItemModal.style.display = 'none';
            inputNewItem.value = '';
            setTimeout(() => {
                addItemModal.style.transform = 'translateY(0)';
                inputNewItem.focus();
            }, 10);
        } else if (type === 'delete') {
            deleteItemModal.style.display = 'block';
            addItemModal.style.display = 'none';
            setTimeout(() => {
                deleteItemModal.style.transform = 'translateY(0)';
            }, 10);
        }
    }

    function closeCustomModal() {
        modalOverlay.style.opacity = '0';
        addItemModal.style.transform = 'translateY(20px)';
        deleteItemModal.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            modalOverlay.style.display = 'none';
            addItemModal.style.display = 'none';
            deleteItemModal.style.display = 'none';
            currentAddAction = null;
            currentDeleteAction = null;
        }, 300);
    }

    document.getElementById('btnConfirmAdd').addEventListener('click', function() {
        if (currentAddAction) {
            const itemName = inputNewItem.value.trim();
            if (!itemName) {
                inputNewItem.focus();
                return;
            }
            // Show loading state
            const btn = document.getElementById('btnConfirmAdd');
            const originalText = btn.innerText;
            btn.innerText = 'Menyimpan...';
            btn.style.opacity = '0.7';
            btn.style.pointerEvents = 'none';
            
            currentAddAction(itemName, btn, originalText);
        }
    });

    document.getElementById('btnConfirmDelete').addEventListener('click', function() {
        if (currentDeleteAction) {
            const btn = document.getElementById('btnConfirmDelete');
            const originalText = btn.innerText;
            btn.innerText = 'Menghapus...';
            btn.style.opacity = '0.7';
            btn.style.pointerEvents = 'none';
            
            currentDeleteAction(btn, originalText);
        }
    });
    
    document.getElementById('newItemName').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('btnConfirmAdd').click();
        }
    });

    function addTemplateItem(templateId, sectionIndex, subsectionIndex) {
        currentAddAction = function(itemName, btn, originalText) {
            fetch('{{ route("admin.templates.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    template_id: templateId,
                    section_index: sectionIndex,
                    subsection_index: subsectionIndex,
                    item_name: itemName
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal menambahkan item: ' + (data.message || 'Unknown error'),
                        confirmButtonColor: '#ef4444'
                    });
                    closeCustomModal();
                    btn.innerText = originalText;
                    btn.style.opacity = '1';
                    btn.style.pointerEvents = 'auto';
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan sistem saat menghubungi server.',
                    confirmButtonColor: '#ef4444'
                });
                closeCustomModal();
                btn.innerText = originalText;
                btn.style.opacity = '1';
                btn.style.pointerEvents = 'auto';
            });
        };
        openCustomModal('add');
    }

    function deleteTemplateItem(templateId, sectionIndex, subsectionIndex, itemIndex) {
        currentDeleteAction = function(btn, originalText) {
            fetch('{{ route("admin.templates.delete") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    template_id: templateId,
                    section_index: sectionIndex,
                    subsection_index: subsectionIndex,
                    item_index: itemIndex
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal menghapus item: ' + (data.message || 'Unknown error'),
                        confirmButtonColor: '#ef4444'
                    });
                    closeCustomModal();
                    btn.innerText = originalText;
                    btn.style.opacity = '1';
                    btn.style.pointerEvents = 'auto';
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan sistem saat menghubungi server.',
                    confirmButtonColor: '#ef4444'
                });
                closeCustomModal();
                btn.innerText = originalText;
                btn.style.opacity = '1';
                btn.style.pointerEvents = 'auto';
            });
        };
        openCustomModal('delete');
    }
</script>

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<style>
    .ck-editor__editable_inline {
        min-height: 150px;
        padding: 1rem 1.5rem !important;
    }
    /* Kembalikan styling list bawaan yang ter-reset oleh CSS global */
    .ck.ck-content ul {
        list-style-type: disc !important;
        padding-left: 2.5rem !important;
        margin-left: 0 !important;
    }
    .ck.ck-content ol {
        list-style-type: decimal !important;
        padding-left: 2.5rem !important;
        margin-left: 0 !important;
    }
    .ck.ck-content li {
        display: list-item !important;
        margin-bottom: 0.3rem !important;
    }
    
    /* Theme-Aware CKEditor Overrides */
    .ck.ck-editor__main > .ck-editor__editable,
    .ck.ck-toolbar {
        background-color: var(--bg-primary) !important;
        border-color: var(--border-color) !important;
        color: var(--text-primary) !important;
    }
    .ck.ck-toolbar__items {
        background-color: var(--bg-primary) !important;
    }
    .ck.ck-button {
        color: var(--text-primary) !important;
    }
    .ck.ck-button:hover {
        background-color: var(--bg-secondary) !important;
    }
    .ck.ck-button.ck-on {
        background-color: var(--bg-secondary) !important;
        color: var(--accent-primary) !important;
    }
    .ck.ck-dropdown__panel {
        background-color: var(--bg-primary) !important;
        border-color: var(--border-color) !important;
    }
    .ck.ck-list__item > .ck-button:hover {
        background-color: var(--bg-secondary) !important;
    }
</style>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.tinymce-editor').forEach(function(textarea) {
            ClassicEditor
                .create(textarea, {
                    toolbar: [ 'heading', '|', 'bold', 'italic', 'bulletedList', 'numberedList', '|', 'undo', 'redo' ]
                })
                .then(function(editor) {
                    editor.model.document.on('change:data', function() {
                        editor.updateSourceElement();
                        if(typeof updateProgress === 'function') updateProgress();
                    });
                })
                .catch(function(error) {
                    console.error(error);
                });
        });
    });
</script>

@endsection
