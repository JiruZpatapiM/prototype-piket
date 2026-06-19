@extends('layouts.app')

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

    .btn-gambar {
        color: #8b5cf6;
        border: 1px solid #c4b5fd;
    }
    .btn-gambar:hover {
        background-color: #f5f3ff;
        border-color: #8b5cf6;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .btn-download {
        color: #3b82f6;
        border: 1px solid #93c5fd;
    }
    .btn-download:hover {
        background-color: #eff6ff;
        border-color: #3b82f6;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
</style>
<div class="container mt-8 pb-8">
    
    <div class="card" style="padding: 1.5rem;">
        <div class="flex items-center gap-2 mb-6 text-accent font-bold" style="font-size: 1.25rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            Daftar Lampiran Laporan Cabang
        </div>

        <div class="flex justify-between items-center mb-4 text-sm">
            <div>
                Tampilkan 
                <select class="form-control" style="display: inline-block; width: auto; padding: 0.2rem 0.5rem;">
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                </select>
                baris
            </div>
            <div>
                <form method="GET" action="{{ route('piket.laporan') }}" style="display: flex; gap: 0.5rem; align-items: center;">
                    <label>Tanggal:
                        <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control" style="display: inline-block; width: auto; padding: 0.2rem 0.5rem;">
                    </label>
                    <label>Cari: 
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" style="display: inline-block; width: auto; padding: 0.2rem 0.5rem;">
                    </label>
                    <button type="submit" class="btn btn-primary btn-sm" style="padding: 0.3rem 0.8rem;">Filter</button>
                    @if(request('tanggal') || request('search'))
                        <a href="{{ route('piket.laporan') }}" class="btn btn-outline btn-sm" style="padding: 0.3rem 0.8rem;">Reset</a>
                    @endif
                </form>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>TANGGAL <span style="font-size: 0.6rem;">▲▼</span></th>
                        <th>LOKASI <span style="font-size: 0.6rem;">▲▼</span></th>
                        <th>JENIS PIKET <span style="font-size: 0.6rem;">▲▼</span></th>
                        <th>NAMA USER <span style="font-size: 0.6rem;">▲▼</span></th>
                        <th>STATUS <span style="font-size: 0.6rem;">▲▼</span></th>
                        <th>SKOR <span style="font-size: 0.6rem;">▲▼</span></th>
                        <th>CATATAN <span style="font-size: 0.6rem;">▲▼</span></th>
                        <th>FILE <span style="font-size: 0.6rem;">▲▼</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $item)
                    <tr>
                        <td>{{ $item->tanggal }}</td>
                        <td>{{ $item->lokasi }}</td>
                        <td style="text-transform: uppercase;">{{ $item->jenis_piket }}</td>
                        <td>
                            <span style="font-weight: 600; color: var(--text-primary);">
                                {{ $item->user ? $item->user->name : 'N/A' }}
                            </span>
                        </td>
                        <td>
                            @if($item->status == 'draft')
                                <span style="background: #e2e8f0; color: #475569; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: bold; font-size: 0.8rem;">Draft</span>
                            @else
                                <span style="background: #d1fae5; color: #059669; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: bold; font-size: 0.8rem;">Submitted</span>
                            @endif
                        </td>
                        <td>
                            @if($item->status == 'submitted')
                                @if($item->score >= 65)
                                    <span style="background: #d1fae5; color: #059669; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: bold; font-size: 0.8rem;">{{ $item->score }}% (Baik)</span>
                                @elseif($item->score >= 35)
                                    <span style="background: #fef3c7; color: #d97706; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: bold; font-size: 0.8rem;">{{ $item->score }}% (Perlu Atensi)</span>
                                @else
                                    <span style="background: #fee2e2; color: #dc2626; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: bold; font-size: 0.8rem;">{{ $item->score }}% (Kritis)</span>
                                @endif
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                        <td>{!! $item->catatan !!}</td>
                        <td>
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                @if($item->file_path)
                                    @php
                                        $filePaths = [];
                                        $decoded = json_decode($item->file_path, true);
                                        $filePaths = is_array($decoded) ? $decoded : [$item->file_path];
                                        
                                        $imagePaths = array_filter($filePaths, function($path) {
                                            return preg_match('/\.(jpg|jpeg|png)$/i', $path);
                                        });
                                        
                                        $imageUrls = array_map(function($path) {
                                            return asset('storage/' . $path);
                                        }, $imagePaths);
                                    @endphp
                                    
                                    @if(count($imageUrls) > 0)
                                        <button type="button" onclick='openImageGallery({!! json_encode(array_values($imageUrls)) !!})' class="btn-action btn-gambar" title="Lihat Gambar">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                            Gambar {{ count($imageUrls) > 1 ? '('.count($imageUrls).')' : '' }}
                                        </button>
                                    @endif
                                    
                                    @if(count($filePaths) > 0)
                                        <a href="{{ route('piket.downloadLampiran', $item->id) }}" class="btn-action btn-download" title="Download Lampiran">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                            Download
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 2rem;">Tidak ada laporan dengan lampiran yang ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex justify-between items-center mt-4 text-sm">
            <div>
                Menampilkan {{ $history->firstItem() ?? 0 }} sampai {{ $history->lastItem() ?? 0 }} dari {{ $history->total() }} entri
            </div>
            <div class="flex gap-2">
                <a href="{{ $history->previousPageUrl() }}" class="btn btn-outline" style="padding: 0.2rem 0.8rem; {{ $history->onFirstPage() ? 'opacity: 0.5; pointer-events: none;' : '' }}">Sebelumnya</a>
                <span class="btn btn-primary" style="padding: 0.2rem 0.8rem;">{{ $history->currentPage() }}</span>
                <a href="{{ $history->nextPageUrl() }}" class="btn btn-outline" style="padding: 0.2rem 0.8rem; {{ !$history->hasMorePages() ? 'opacity: 0.5; pointer-events: none;' : '' }}">Berikutnya</a>
            </div>
        </div>
    </div>

</div>

<!-- Image Modal -->
<div id="imageModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div id="imageCounter" style="position: absolute; top: 30px; left: 50%; transform: translateX(-50%); color: white; font-weight: bold; background: rgba(0,0,0,0.5); padding: 5px 15px; border-radius: 20px; z-index: 10001; display: none;"></div>
    
    <button type="button" onclick="closeImageModal()" style="position: absolute; top: 20px; right: 30px; background: rgba(255,255,255,0.2); border: none; color: white; font-size: 2rem; width: 50px; height: 50px; border-radius: 50%; cursor: pointer; z-index: 10000; display: flex; align-items: center; justify-content: center; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.4)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">&times;</button>
    
    <button id="prevImageBtn" type="button" onclick="prevImage(event)" style="position: absolute; left: 30px; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.2); border: none; color: white; font-size: 2rem; width: 50px; height: 50px; border-radius: 50%; cursor: pointer; z-index: 10000; display: none; align-items: center; justify-content: center;" onmouseover="this.style.background='rgba(255,255,255,0.4)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">&#10094;</button>
    
    <button id="nextImageBtn" type="button" onclick="nextImage(event)" style="position: absolute; right: 30px; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.2); border: none; color: white; font-size: 2rem; width: 50px; height: 50px; border-radius: 50%; cursor: pointer; z-index: 10000; display: none; align-items: center; justify-content: center;" onmouseover="this.style.background='rgba(255,255,255,0.4)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">&#10095;</button>

    <img id="modalImage" src="" style="max-width: 90%; max-height: 90vh; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); object-fit: contain;">
</div>

<script>
    let currentGallery = [];
    let currentGalleryIndex = 0;

    function openImageGallery(urls) {
        if(!urls || urls.length === 0) return;
        currentGallery = urls;
        currentGalleryIndex = 0;
        document.getElementById('modalImage').src = currentGallery[currentGalleryIndex];
        document.getElementById('imageModal').style.display = 'flex';
        
        document.getElementById('prevImageBtn').style.display = currentGallery.length > 1 ? 'flex' : 'none';
        document.getElementById('nextImageBtn').style.display = currentGallery.length > 1 ? 'flex' : 'none';
        updateImageCounter();
    }
    
    function nextImage(e) {
        e.stopPropagation();
        if(currentGallery.length <= 1) return;
        currentGalleryIndex = (currentGalleryIndex + 1) % currentGallery.length;
        document.getElementById('modalImage').src = currentGallery[currentGalleryIndex];
        updateImageCounter();
    }

    function prevImage(e) {
        e.stopPropagation();
        if(currentGallery.length <= 1) return;
        currentGalleryIndex = (currentGalleryIndex - 1 + currentGallery.length) % currentGallery.length;
        document.getElementById('modalImage').src = currentGallery[currentGalleryIndex];
        updateImageCounter();
    }

    function updateImageCounter() {
        const counter = document.getElementById('imageCounter');
        if (currentGallery.length > 1) {
            counter.style.display = 'block';
            counter.innerText = (currentGalleryIndex + 1) + ' / ' + currentGallery.length;
        } else {
            counter.style.display = 'none';
        }
    }

    function closeImageModal() {
        document.getElementById('imageModal').style.display = 'none';
        document.getElementById('modalImage').src = '';
    }
    
    // Close modal when clicking outside the image
    document.getElementById('imageModal').addEventListener('click', function(e) {
        if(e.target === this) {
            closeImageModal();
        }
    });
</script>
@endsection
