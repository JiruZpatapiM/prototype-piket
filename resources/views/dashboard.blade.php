@extends('layouts.app')

@section('content')
<div class="container mt-4 mb-8" style="max-width: 1400px; font-family: 'Inter', sans-serif;">
    
    <!-- Corporate Header -->
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 2px solid var(--border-color); padding-bottom: 1rem;">
        <div>
            <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--text-primary); margin: 0; letter-spacing: -0.5px;">Dashboard Monitoring Piket</h1>
            <p style="color: var(--text-secondary); margin: 5px 0 0 0; font-size: 0.95rem;">Masa Angkutan Lebaran & Akhir Pekan 2026</p>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 0.85rem; color: var(--text-secondary); font-weight: 600; text-transform: uppercase;">Waktu Sistem</div>
            <div style="font-size: 1.1rem; font-weight: 700; color: var(--accent-primary); display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem;">
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                <span id="live-time" style="background: rgba(14, 165, 233, 0.1); padding: 0.2rem 0.5rem; border-radius: 6px; font-variant-numeric: tabular-nums;">{{ \Carbon\Carbon::now()->format('H:i:s') }}</span>
            </div>
        </div>
    </div>

    @if(auth()->check() && auth()->user()->role === 'admin')
    <!-- Filter Bar (Minimalist) -->
    <div class="card mb-6" style="padding: 1.2rem 1.5rem; border-radius: 12px; border: 1px solid var(--border-color); background: var(--bg-primary); box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
        <form method="GET" action="{{ route('dashboard') }}" class="flex items-end gap-4 flex-wrap" id="filterForm">
            
            <div style="flex: 1; min-width: 280px;">
                <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.5rem; text-transform: uppercase;">Rentang Tanggal Laporan</label>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="date" name="tanggal_mulai" value="{{ $tanggal_mulai }}" class="form-control" onchange="document.getElementById('filterForm').submit()" style="background: var(--bg-secondary); border: 1px solid var(--border-color); font-weight: 500;">
                    <span style="color: var(--text-secondary); font-weight: 600;">-</span>
                    <input type="date" name="tanggal_akhir" value="{{ $tanggal_akhir }}" class="form-control" onchange="document.getElementById('filterForm').submit()" style="background: var(--bg-secondary); border: 1px solid var(--border-color); font-weight: 500;">
                </div>
            </div>

            <div style="flex: 1; min-width: 150px;">
                <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.5rem; text-transform: uppercase;">Tipe Piket</label>
                <select name="tipe_detail" class="form-control" onchange="document.getElementById('filterForm').submit()" style="background: var(--bg-secondary); border: 1px solid var(--border-color); font-weight: 500;">
                    <option value="">Semua Tipe</option>
                    <option value="Akhir Pekan" {{ $tipe_detail == 'Akhir Pekan' ? 'selected' : '' }}>Daily</option>
                    <option value="Angkutan Lebaran" {{ $tipe_detail == 'Angkutan Lebaran' ? 'selected' : '' }}>Angkutan Lebaran</option>
                    <option value="Libur Nataru" {{ $tipe_detail == 'Libur Nataru' ? 'selected' : '' }}>Libur Nataru</option>
                </select>
            </div>

            <div style="flex: 1; min-width: 150px;">
                <label style="display: block; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.5rem; text-transform: uppercase;">Cabang</label>
                @if(auth()->check() && auth()->user()->role !== 'admin' && auth()->user()->lokasi_fix)
                    <input type="text" value="{{ auth()->user()->lokasi_fix }}" class="form-control" disabled style="background: rgba(255,255,255,0.05); color: var(--text-secondary); cursor: not-allowed; font-weight: 600;">
                    <input type="hidden" name="lokasi" value="{{ auth()->user()->lokasi_fix }}">
                @else
                    <select name="lokasi" class="form-control" onchange="document.getElementById('filterForm').submit()" style="background: var(--bg-secondary); border: 1px solid var(--border-color); font-weight: 500;">
                        <option value="Semua Lokasi" {{ $lokasi == 'Semua Lokasi' ? 'selected' : '' }}>Semua Cabang</option>
                        @php
                            $cabangs = [
                                'TPK Makassar', 'Makassar New Port', 'TPK Kupang', 'TPK Ambon', 'TPK Bitung', 
                                'TPK Kendari', 'TPK Pantoloan', 'TPK Tarakan', 'TPK Jayapura', 'TPK Sorong', 
                                'TPK Biak', 'TPK Manokwari', 'TPK Merauke', 'Cabang Balikpapan', 'Cabang Samarinda', 
                                'Cabang Tolitoli', 'Gorontalo', 'Cabang Parepare', 'Cabang Ternate', 'Cabang Nunukan', 
                                'Cabang Tanjung Redeb', 'Cabang Fakfak'
                            ];
                        @endphp
                        @foreach($cabangs as $cab)
                            <option value="{{ $cab }}" {{ $lokasi == $cab ? 'selected' : '' }}>{{ $cab }}</option>
                        @endforeach
                    </select>
                @endif
            </div>

            @if(auth()->check() && auth()->user()->role === 'admin')
            <div style="margin-left: auto;">
                <button type="button" class="btn btn-primary" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.2rem; border-radius: 8px; font-weight: 600; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.2);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Export Rekapitulasi Nasional
                </button>
            </div>
            @endif
        </form>
    </div>
    @endif

    <!-- ============================================== -->
    <!-- VIEW UNTUK USER BIASA (CABANG) -->
    <!-- ============================================== -->
    @if(auth()->check() && auth()->user()->role !== 'admin')
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 4rem 2rem; background: var(--bg-secondary); border-radius: 16px; border: 1px solid var(--border-color); text-align: center;">
            <div style="width: 120px; height: 120px; border-radius: 50%; background: rgba(16, 185, 129, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; border: 4px solid rgba(16, 185, 129, 0.2);">
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </div>
            <h2 style="font-size: 2rem; font-weight: 800; color: var(--text-primary); margin: 0 0 0.5rem 0;">Status Pengisian Cabang Anda</h2>
            <p style="font-size: 1.1rem; color: var(--text-secondary); margin: 0 0 2rem 0; max-width: 600px;">Berdasarkan data hari ini, persentase kepatuhan dan kesiapan cabang <strong>{{ auth()->user()->lokasi_fix }}</strong> adalah sebagai berikut:</p>
            
            <div style="font-size: 5rem; font-weight: 900; color: #10b981; line-height: 1; text-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">{{ $rataRataPersentase }}%</div>
            
            <div style="margin-top: 3rem; width: 100%; max-width: 400px;">
                <a href="{{ route('piket.input') }}" class="btn btn-primary w-full" style="padding: 1rem; font-size: 1.1rem; font-weight: bold; border-radius: 12px; text-transform: uppercase; letter-spacing: 1px;">Isi Form Piket Sekarang</a>
            </div>
        </div>
    @endif

    <!-- ============================================== -->
    <!-- VIEW UNTUK ADMIN NASIONAL -->
    <!-- ============================================== -->
    @if(auth()->check() && auth()->user()->role === 'admin')
        <!-- KPI Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            <!-- Total Cabang -->
            <div class="card" style="padding: 1.5rem; border-radius: 12px; border-top: 4px solid #3b82f6; background: var(--bg-primary); display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary); font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem;">Total Cabang</div>
                    <div style="font-size: 2rem; font-weight: 800; color: var(--text-primary); line-height: 1;">{{ $totalCabang }}</div>
                </div>
                <div style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; padding: 1rem; border-radius: 50%;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                </div>
            </div>

            <!-- Cabang Input -->
            <div class="card" style="padding: 1.5rem; border-radius: 12px; border-top: 4px solid #10b981; background: var(--bg-primary); display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary); font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem;">Cabang Sudah Input</div>
                    <div style="display: flex; align-items: baseline; gap: 0.5rem;">
                        <div style="font-size: 2rem; font-weight: 800; color: var(--text-primary); line-height: 1;">{{ $cabangInput }}</div>
                        <div style="font-size: 0.85rem; color: #10b981; font-weight: bold;">({{ $totalCabang > 0 ? round(($cabangInput / $totalCabang) * 100) : 0 }}%)</div>
                    </div>
                </div>
                <div style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 1rem; border-radius: 50%;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                </div>
            </div>

            <!-- Belum Input -->
            <div class="card" style="padding: 1.5rem; border-radius: 12px; border-top: 4px solid #8b5cf6; background: var(--bg-primary); display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary); font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem;">Cabang Belum Input</div>
                    <div style="font-size: 2rem; font-weight: 800; color: var(--text-primary); line-height: 1;">{{ $belumInput }}</div>
                </div>
                <div style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6; padding: 1rem; border-radius: 50%;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
            </div>
            
            <!-- Perlu Atensi -->
            <div class="card" style="padding: 1.5rem; border-radius: 12px; border-top: 4px solid #f59e0b; background: var(--bg-primary); display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary); font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem;">Perlu Atensi</div>
                    <div style="font-size: 2rem; font-weight: 800; color: var(--text-primary); line-height: 1;">{{ $perluAtensi }}</div>
                </div>
                <div style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; padding: 1rem; border-radius: 50%;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                </div>
            </div>

            <!-- Kondisi Kritis -->
            <div class="card" style="padding: 1.5rem; border-radius: 12px; border-top: 4px solid #ef4444; background: var(--bg-primary); display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary); font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem;">Kondisi Kritis</div>
                    <div style="font-size: 2rem; font-weight: 800; color: var(--text-primary); line-height: 1;">{{ $kondisiKritis }}</div>
                </div>
                <div style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 1rem; border-radius: 50%;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                </div>
            </div>
        </div>

        <!-- Charts Section (Grid) -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            
            <!-- Ringkasan Grading -->
            <div class="card" style="padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border-color); background: var(--bg-primary);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <div style="font-weight: 800; font-size: 1.1rem; color: var(--text-primary);">Ringkasan Grading per Area Monitoring</div>
                    <div class="flex gap-4" style="font-size: 0.75rem; font-weight: 600;">
                        <div style="display: flex; align-items: center; gap: 6px;"><span style="width: 10px; height: 10px; background: #10b981; border-radius: 50%;"></span> Baik</div>
                        <div style="display: flex; align-items: center; gap: 6px;"><span style="width: 10px; height: 10px; background: #ef4444; border-radius: 50%;"></span> Kurang</div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; text-align: center;">
                    @foreach($ringkasanGrading as $grade)
                    <div style="padding: 1rem; border: 1px solid var(--border-color); border-radius: 12px; background: var(--bg-secondary);">
                        <div style="font-weight: 800; font-size: 1rem; color: var(--accent-primary); margin-bottom: 0.5rem;">{{ $grade['area'] }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 1rem; min-height: 40px; font-weight: 500;">{{ $grade['desc'] }}</div>
                        
                        <div style="display: flex; justify-content: center; gap: 0.5rem; margin-bottom: 1rem;">
                            <div style="background: rgba(16,185,129,0.1); color: #10b981; padding: 0.3rem 0.6rem; border-radius: 6px; font-weight: bold; font-size: 0.85rem;">{{ $grade['baik'] }}</div>
                            <div style="background: rgba(239,68,68,0.1); color: #ef4444; padding: 0.3rem 0.6rem; border-radius: 6px; font-weight: bold; font-size: 0.85rem;">{{ $grade['kurang'] }}</div>
                        </div>

                        <div style="position: relative; width: 60px; height: 60px; margin: 0 auto;">
                            <svg viewBox="0 0 36 36" style="width: 100%; height: 100%;">
                                <!-- Base Gray (Empty state) -->
                                <path stroke-dasharray="100, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="var(--bg-tertiary)" stroke-width="4"/>
                                
                                @if($grade['baik'] > 0 || $grade['kurang'] > 0)
                                    <!-- Base Red (Kurang) -->
                                    <path stroke-dasharray="100, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#ef4444" stroke-width="4"/>
                                    <!-- Overlay Green (Baik) -->
                                    <path stroke-dasharray="{{ $grade['pct'] }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#10b981" stroke-width="4" stroke-linecap="round"/>
                                @endif
                            </svg>
                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 0.8rem; font-weight: bold; color: var(--text-primary);">{{ $grade['pct'] }}%</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Sebaran Status Cabang -->
            <div class="card" style="padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border-color); background: var(--bg-primary);">
                <div style="font-weight: 800; font-size: 1.1rem; color: var(--text-primary); margin-bottom: 1.5rem;">Sebaran Status Cabang</div>
                @php
                    $totalSubmitted = $totalLaporan > 0 ? $totalLaporan : 1;
                    $pctBaik = round(($statusBaik / $totalSubmitted) * 100);
                    $pctAtensi = round(($perluAtensi / $totalSubmitted) * 100);
                    $pctKritis = round(($kondisiKritis / $totalSubmitted) * 100);
                    
                    $renderGreen = $totalLaporan > 0 ? ($statusBaik / $totalSubmitted) * 100 : 0;
                    $renderYellow = $totalLaporan > 0 ? (($statusBaik + $perluAtensi) / $totalSubmitted) * 100 : 0;
                    $renderRed = $totalLaporan > 0 ? (($statusBaik + $perluAtensi + $kondisiKritis) / $totalSubmitted) * 100 : 0;
                @endphp
                <div style="position: relative; width: 180px; height: 180px; margin: 0 auto 1.5rem auto;">
                    <!-- SVG Donut Chart -->
                    <svg viewBox="0 0 36 36" style="width: 100%; height: 100%; overflow: visible;">
                        <!-- Base Gray (Empty state) -->
                        <path stroke-dasharray="100, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="var(--bg-tertiary)" stroke-width="6"/>
                        
                        @if($totalLaporan > 0)
                            <!-- Red Layer (Kondisi Kritis + Atensi + Baik) -->
                            <path stroke-dasharray="{{ $renderRed }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#ef4444" stroke-width="6"/>
                            <!-- Yellow Layer (Atensi + Baik) -->
                            <path stroke-dasharray="{{ $renderYellow }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#f59e0b" stroke-width="6"/>
                            <!-- Green Layer (Baik only) -->
                            <path stroke-dasharray="{{ $renderGreen }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#10b981" stroke-width="6"/>
                        @endif
                    </svg>
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                        <div style="font-size: 1.8rem; font-weight: 800; color: var(--text-primary); line-height: 1;">{{ $pctBaik }}%</div>
                        <div style="font-size: 0.7rem; color: var(--text-secondary); font-weight: 600;">Status Baik</div>
                    </div>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.85rem; font-weight: 600;">
                        <div style="display: flex; align-items: center; gap: 8px;"><span style="width: 12px; height: 12px; background: #10b981; border-radius: 4px;"></span> Baik ({{ $pctBaik }}%)</div>
                        <div>{{ $statusBaik }} Laporan</div>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.85rem; font-weight: 600;">
                        <div style="display: flex; align-items: center; gap: 8px;"><span style="width: 12px; height: 12px; background: #f59e0b; border-radius: 4px;"></span> Perlu Atensi ({{ $pctAtensi }}%)</div>
                        <div>{{ $perluAtensi }} Laporan</div>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.85rem; font-weight: 600;">
                        <div style="display: flex; align-items: center; gap: 8px;"><span style="width: 12px; height: 12px; background: #ef4444; border-radius: 4px;"></span> Kondisi Kritis ({{ $pctKritis }}%)</div>
                        <div>{{ $kondisiKritis }} Laporan</div>
                    </div>
                </div>
            </div>

        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <!-- Top 5 Cabang Perlu Atensi -->
            <div class="card" style="padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border-color); background: var(--bg-primary);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <div style="font-weight: 800; font-size: 1.1rem; color: var(--text-primary);">Top 5 Cabang Perlu Atensi</div>
                    <a href="#" style="font-size: 0.8rem; color: var(--accent-primary); font-weight: bold; text-decoration: none;">Lihat Semua</a>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @forelse($cabangPerluAtensi->take(5) as $index => $tc)
                    <div style="display: flex; align-items: flex-start; gap: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                        <div style="background: {{ $tc->score < 35 ? 'rgba(239,68,68,0.1)' : 'rgba(245,158,11,0.1)' }}; color: {{ $tc->score < 35 ? '#ef4444' : '#f59e0b' }}; width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem;">{{ $index + 1 }}</div>
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.2rem;">
                                <div style="font-weight: 800; color: var(--text-primary);">{{ $tc->lokasi }} <span style="font-size: 0.75rem; font-weight: 500; color: var(--text-secondary);">({{ $tc->jenis_piket }})</span></div>
                                <div style="font-size: 0.75rem; font-weight: bold; color: {{ $tc->score < 35 ? '#ef4444' : '#f59e0b' }}; background: {{ $tc->score < 35 ? 'rgba(239,68,68,0.1)' : 'rgba(245,158,11,0.1)' }}; padding: 0.2rem 0.6rem; border-radius: 4px;">{{ $tc->score }}%</div>
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-secondary); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                Memerlukan peninjauan lebih lanjut pada laporan piket ini.
                            </div>
                        </div>
                    </div>
                    @empty
                    <div style="text-align: center; color: var(--text-secondary); padding: 1rem;">
                        Tidak ada cabang yang perlu atensi saat ini. Semua kondisi baik!
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Jadwal Piket Direksi -->
            <div class="card" style="padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border-color); background: var(--bg-primary);">
                <div style="font-weight: 800; font-size: 1.1rem; color: var(--text-primary); margin-bottom: 1.5rem;">Jadwal Piket Direksi</div>
                
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @foreach($jadwalDireksi as $jd)
                    <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-secondary); border-radius: 12px; border: 1px solid var(--border-color); transition: transform 0.2s, box-shadow 0.2s;">
                        <div style="background: var(--accent-primary); color: white; width: 50px; height: 50px; border-radius: 10px; display: flex; flex-direction: column; align-items: center; justify-content: center; line-height: 1.2;">
                            <span style="font-weight: 800; font-size: 1.2rem;">{{ $jd['date'] }}</span>
                            <span style="font-size: 0.65rem; font-weight: 600; letter-spacing: 1px;">{{ $jd['month'] }}</span>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 800; font-size: 1rem; color: var(--text-primary);">{{ $jd['name'] }}</div>
                            <div style="font-size: 0.75rem; color: var(--accent-primary); font-weight: 600; margin-bottom: 0.3rem;">{{ $jd['title'] }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-secondary);">{{ $jd['desc'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const timeEl = document.getElementById('live-time');
        if(timeEl) {
            timeEl.innerText = `${hours}:${minutes}:${seconds}`;
        }
    }
    // Update every second
    setInterval(updateClock, 1000);
</script>
@endsection
