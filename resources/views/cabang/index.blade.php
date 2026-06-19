@extends('layouts.app')

@section('content')
<div class="container mt-4 mb-8" style="max-width: 1400px; font-family: 'Inter', sans-serif;">
    
    <div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--accent-primary); margin: 0;">Monitoring Status Cabang</h1>
            <p style="color: var(--text-secondary); margin: 5px 0 0 0; font-size: 0.9rem;">Daftar kelengkapan input piket masing-masing cabang</p>
        </div>
        
        <form method="GET" action="{{ route('cabang') }}" id="filterForm" style="display: flex; gap: 1rem; align-items: center;">
            <div>
                <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.2rem;">Pilih Tanggal</label>
                <div class="flex gap-2">
                    <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control" onchange="document.getElementById('filterForm').submit()">
                </div>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card" style="padding: 1.5rem; border-radius: 12px; display: flex; align-items: center; gap: 1rem; border-left: 4px solid #3b82f6;">
            <div style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; padding: 1rem; border-radius: 12px;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            </div>
            <div>
                <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.2rem;">Total Cabang</div>
                <div style="font-size: 1.8rem; font-weight: bold;">{{ count($dataCabang) }}</div>
            </div>
        </div>

        <div class="card" style="padding: 1.5rem; border-radius: 12px; display: flex; align-items: center; gap: 1rem; border-left: 4px solid #10b981;">
            <div style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 1rem; border-radius: 12px;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            </div>
            <div>
                <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.2rem;">Sudah Input</div>
                <div style="font-size: 1.8rem; font-weight: bold; color: #10b981;">{{ $cabangInputCount }}</div>
            </div>
        </div>

        <div class="card" style="padding: 1.5rem; border-radius: 12px; display: flex; align-items: center; gap: 1rem; border-left: 4px solid #f59e0b;">
            <div style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; padding: 1rem; border-radius: 12px;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </div>
            <div>
                <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.2rem;">Belum Input</div>
                <div style="font-size: 1.8rem; font-weight: bold; color: #f59e0b;">{{ count($dataCabang) - $cabangInputCount }}</div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="card" style="padding: 1.5rem; border-radius: 12px;">
        <div class="table-wrapper">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-color); text-align: left;">
                        <th style="padding: 1rem; color: var(--text-secondary);">NO</th>
                        <th style="padding: 1rem; color: var(--text-secondary);">NAMA CABANG</th>
                        <th style="padding: 1rem; color: var(--text-secondary);">STATUS INPUT</th>
                        <th style="padding: 1rem; color: var(--text-secondary);">PERSENTASE</th>
                        <th style="padding: 1rem; color: var(--text-secondary);">KONDISI</th>
                        <th style="padding: 1rem; color: var(--text-secondary);">TERAKHIR UPDATE</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dataCabang as $idx => $cabang)
                    <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.2s;">
                        <td style="padding: 1rem;">{{ $idx + 1 }}</td>
                        <td style="padding: 1rem; font-weight: bold;">{{ $cabang['nama'] }}</td>
                        <td style="padding: 1rem;">
                            @if($cabang['status'] == 'Sudah Input')
                                <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.4rem 0.8rem; border-radius: 20px; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.4rem;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    Sudah Input
                                </span>
                            @else
                                <span style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; padding: 0.4rem 0.8rem; border-radius: 20px; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.4rem;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                    Belum Input
                                </span>
                            @endif
                        </td>
                        <td style="padding: 1rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="width: 100px; height: 6px; background: var(--bg-tertiary); border-radius: 3px; overflow: hidden;">
                                    <div style="width: {{ $cabang['persentase'] }}%; height: 100%; background: {{ $cabang['persentase'] == 100 ? '#10b981' : ($cabang['persentase'] >= 50 ? '#f59e0b' : '#ef4444') }};"></div>
                                </div>
                                <span style="font-weight: bold; font-size: 0.85rem;">{{ $cabang['persentase'] }}%</span>
                            </div>
                        </td>
                        <td style="padding: 1rem; font-weight: bold; color: {{ $cabang['kondisi'] == 'Baik' ? '#10b981' : ($cabang['kondisi'] == 'Perlu Atensi' ? '#f59e0b' : ($cabang['kondisi'] == 'Kritis' ? '#ef4444' : 'var(--text-secondary)')) }};">
                            {{ $cabang['kondisi'] }}
                        </td>
                        <td style="padding: 1rem; color: var(--text-secondary); font-size: 0.9rem;">
                            {{ $cabang['terakhir_update'] }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
