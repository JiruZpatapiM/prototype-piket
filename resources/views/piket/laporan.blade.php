@extends('layouts.app')

@section('content')
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
                        <td>{{ $item->catatan }}</td>
                        <td>
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                @if($item->file_path)
                                    <a href="{{ route('piket.downloadLampiran', $item->id) }}" class="btn btn-primary btn-sm" style="padding: 0.3rem 0.8rem; font-size: 0.75rem;" title="Download Lampiran">
                                        📎 Download Lampiran
                                    </a>
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
@endsection
