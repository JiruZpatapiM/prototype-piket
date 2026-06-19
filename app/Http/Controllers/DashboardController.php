<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PiketInput;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tanggal_mulai = $request->input('tanggal_mulai', date('Y-m-d'));
        $tanggal_akhir = $request->input('tanggal_akhir', date('Y-m-d'));
        
        $user = auth()->user();
        if ($user && $user->role !== 'admin' && $user->lokasi_fix) {
            $lokasi = $user->lokasi_fix;
        } else {
            $lokasi = $request->input('lokasi', 'Semua Lokasi');
        }
        
        $tipe_detail = $request->input('tipe_detail');

        $query = PiketInput::with('details')->whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir]);
        
        if ($lokasi !== 'Semua Lokasi') {
            $query->where('lokasi', $lokasi);
        }

        $piketInputs = $query->get();
        $rataRataPersentase = $piketInputs->count() > 0 ? round($piketInputs->avg('persentase'), 1) : 0;

        // Dashboard Metrics Calculation
        $totalCabang = 22;
        
        $latestSubmittedInputs = $piketInputs->where('status', 'submitted')->sortByDesc('created_at')->unique(function ($item) {
            return $item->lokasi . '_' . $item->jenis_piket;
        });
        
        // Menghitung cabang yang sudah input hari ini berdasarkan lokasi yang unik
        $cabangInput = $piketInputs->where('status', 'submitted')->unique('lokasi')->count();
        
        // Menghitung yang belum input
        $belumInput = max(0, $totalCabang - $cabangInput);
        
        $totalLaporan = $latestSubmittedInputs->count();
        $statusBaik = $latestSubmittedInputs->where('score', '>=', 65)->count();
        $perluAtensi = $latestSubmittedInputs->whereBetween('score', [35, 64])->count();
        $kondisiKritis = $latestSubmittedInputs->where('score', '<', 35)->count();

        // Data for Cabang needing attention
        $cabangPerluAtensi = $latestSubmittedInputs->where('score', '<', 65)->values();

        // Dynamic calculation for Ringkasan Grading per Area Monitoring
        $latestSubmittedInputs->load('details');
        
        $areaMapping = [
            'Area 1' => ['desc' => 'Kesiapan Fasilitas Pelabuhan', 'baik' => 0, 'kurang' => 0, 'total' => 0, 'keys' => ['a'], 'keywords' => ['kesiapan', 'fasilitas']],
            'Area 2' => ['desc' => 'Pelayanan Bongkar Muat & Operasional', 'baik' => 0, 'kurang' => 0, 'total' => 0, 'keys' => ['b'], 'keywords' => ['bongkar', 'muat', 'operasional']],
            'Area 3' => ['desc' => 'Sarana Bantu Pemanduan & Penundaan (SBPP)', 'baik' => 0, 'kurang' => 0, 'total' => 0, 'keys' => ['c'], 'keywords' => ['pemanduan', 'penundaan', 'sbpp']],
            'Area 4' => ['desc' => 'Komplain, Hubungan Kelembagaan & Media Handling', 'baik' => 0, 'kurang' => 0, 'total' => 0, 'keys' => ['d'], 'keywords' => ['komplain', 'kelembagaan', 'media']],
        ];

        foreach ($latestSubmittedInputs as $input) {
            foreach ($input->details as $detail) {
                $categoryName = strtolower(trim($detail->category));
                $kondisi = strtolower(trim($detail->kondisi));
                
                if (in_array($kondisi, ['baik', 'kurang'])) {
                    $matchedArea = null;
                    foreach ($areaMapping as $areaKey => $areaData) {
                        if (in_array($categoryName, $areaData['keys'])) {
                            $matchedArea = $areaKey;
                            break;
                        }
                        foreach ($areaData['keywords'] as $keyword) {
                            if (str_contains($categoryName, $keyword)) {
                                $matchedArea = $areaKey;
                                break 2;
                            }
                        }
                    }
                    
                    if ($matchedArea) {
                        $areaMapping[$matchedArea]['total']++;
                        if ($kondisi === 'baik') {
                            $areaMapping[$matchedArea]['baik']++;
                        } else if ($kondisi === 'kurang') {
                            $areaMapping[$matchedArea]['kurang']++;
                        }
                    }
                }
            }
        }

        $ringkasanGrading = [];
        foreach ($areaMapping as $areaKey => $data) {
            $pct = $data['total'] > 0 ? round(($data['baik'] / $data['total']) * 100) : 0;
            $ringkasanGrading[] = [
                'area' => $areaKey,
                'desc' => $data['desc'],
                'baik' => $data['baik'],
                'cukup' => 0, // Cukup is not an option in the current form structure
                'kurang' => $data['kurang'],
                'pct' => $pct
            ];
        }

        $topCabang = [
            ['no' => 1, 'cabang' => 'Gorontalo', 'area' => 'Area 2', 'isu' => 'YOR mencapai 70%, potensi kongesti lapangan', 'status' => 'Kuning'],
            ['no' => 2, 'cabang' => 'Makassar', 'area' => 'Area 2', 'isu' => 'Kerusakan HMC 02 berdampak pada antrian kapal', 'status' => 'Kuning'],
        ];

        $aktivitasTerbaru = [
            ['cabang' => 'Cabang Balikpapan', 'desc' => 'Menginput laporan piket', 'time' => '10:28 WIB', 'status' => 'success'],
            ['cabang' => 'Cabang Tanjung Redeb', 'desc' => 'Menginput laporan piket', 'time' => '10:20 WIB', 'status' => 'success'],
            ['cabang' => 'Cabang Bitung', 'desc' => 'Menginput laporan piket', 'time' => '10:18 WIB', 'status' => 'success'],
            ['cabang' => 'Cabang Kendari', 'desc' => 'Menginput laporan piket', 'time' => '10:15 WIB', 'status' => 'success'],
            ['cabang' => 'Cabang Ambon', 'desc' => 'Menginput laporan piket', 'time' => '10:12 WIB', 'status' => 'success'],
        ];

        $jadwalDireksi = [
            ['name' => 'Achmad Muchasyar', 'title' => 'Direktur Utama', 'date' => '14', 'month' => 'JUN', 'desc' => 'Piket Akhir Pekan Regional 4'],
            ['name' => 'Arif Suhartono', 'title' => 'Executive Director', 'date' => '21', 'month' => 'JUN', 'desc' => 'Piket Akhir Pekan Regional 2'],
            ['name' => 'Putut Sri Muljanto', 'title' => 'Executive Director', 'date' => '28', 'month' => 'JUN', 'desc' => 'Piket Akhir Pekan Regional 3'],
            ['name' => 'Ali Mulyono', 'title' => 'Executive Director', 'date' => '05', 'month' => 'JUL', 'desc' => 'Piket Akhir Pekan Regional 1'],
        ];

        return view('dashboard', compact(
            'tanggal_mulai', 'tanggal_akhir', 'lokasi', 'tipe_detail',
            'rataRataPersentase', 'cabangInput', 'belumInput', 'totalCabang',
            'statusBaik', 'perluAtensi', 'kondisiKritis', 'cabangPerluAtensi', 'totalLaporan',
            'ringkasanGrading', 'topCabang', 'aktivitasTerbaru', 'jadwalDireksi'
        ));
    }
}
