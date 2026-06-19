@extends('layouts.app')

@section('content')
<div class="container mt-8 pb-8" style="max-width: 1400px;">
    
    <div class="card" style="padding: 1.5rem;">
        <div class="flex items-center gap-2 mb-6 text-accent font-bold" style="font-size: 1.25rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
            Riwayat Data Input
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
                <form method="GET" action="{{ route('piket.history') }}" style="display: flex; gap: 0.5rem; align-items: center;">
                    <label>Tanggal:
                        <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control" style="display: inline-block; width: auto; padding: 0.2rem 0.5rem;">
                    </label>
                    <label>Cari: 
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" style="display: inline-block; width: auto; padding: 0.2rem 0.5rem;">
                    </label>
                    <button type="submit" class="btn btn-primary btn-sm" style="padding: 0.3rem 0.8rem;">Filter</button>
                    @if(request('tanggal') || request('search'))
                        <a href="{{ route('piket.history') }}" class="btn btn-outline btn-sm" style="padding: 0.3rem 0.8rem;">Reset</a>
                    @endif
                </form>
            </div>
        </div>

        <div class="table-wrapper" style="overflow-x: auto;">
            <table style="min-width: 1000px;">
                <thead>
                    <tr>
                        <th>TANGGAL <span style="font-size: 0.6rem;">▲▼</span></th>
                        <th>LOKASI <span style="font-size: 0.6rem;">▲▼</span></th>
                        <th>JENIS PIKET <span style="font-size: 0.6rem;">▲▼</span></th>
                        <th>STATUS <span style="font-size: 0.6rem;">▲▼</span></th>
                        <th>SKOR <span style="font-size: 0.6rem;">▲▼</span></th>
                        <th>PROGRESS <span style="font-size: 0.6rem;">▲▼</span></th>
                        <th>CATATAN <span style="font-size: 0.6rem;">▲▼</span></th>
                        <th>AKSI <span style="font-size: 0.6rem;">▲▼</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $item)
                    <tr>
                        <td>{{ $item->tanggal }}</td>
                        <td>{{ $item->lokasi }}</td>
                        <td style="text-transform: uppercase;">{{ $item->jenis_piket }}</td>
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
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="width: 60px; height: 6px; background: var(--bg-tertiary); border-radius: 3px; overflow: hidden;">
                                    <div style="width: {{ $item->persentase }}%; height: 100%; background: {{ $item->persentase == 100 ? '#10b981' : ($item->persentase >= 50 ? '#f59e0b' : '#ef4444') }};"></div>
                                </div>
                                <span style="font-weight: bold; font-size: 0.8rem;">{{ $item->persentase }}%</span>
                            </div>
                        </td>
                        <td>{{ $item->catatan }}</td>
                        <td>
                            <div style="display: flex; gap: 0.5rem; flex-wrap: nowrap;">
                                @if($item->status == 'draft')
                                    <a href="{{ route('piket.edit', $item->id) }}" class="btn btn-primary btn-sm" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; background: #f59e0b; border-color: #f59e0b; color: #fff;" title="Lanjutkan Pengisian Draft">
                                        ✏️ Lanjutkan
                                    </a>
                                @else
                                    <a href="{{ route('piket.exportPdf', $item->id) }}" class="btn btn-outline btn-sm" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; border-color: #ef4444; color: #ef4444;" title="Download PDF">
                                        📄 PDF
                                    </a>
                                @endif
                                
                                @if($item->file_path)
                                    <a href="{{ route('piket.downloadLampiran', $item->id) }}" class="btn btn-outline btn-sm" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; border-color: #3b82f6; color: #3b82f6;" title="Download Lampiran">
                                        📎 Lampiran
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 2rem;">Tidak ada riwayat data.</td>
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
