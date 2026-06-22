<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PiketInput;
use App\Models\User;

class CabangController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        // Ambil semua input piket pada tanggal tertentu
        $piketInputs = PiketInput::where('tanggal', $tanggal)->where('status', 'approved')->get();

        // Daftar semua cabang (hardcoded sementara)
        $semuaCabang = [
            'TPK Makassar', 'Makassar New Port', 'TPK Kupang', 'TPK Ambon', 'TPK Bitung', 
            'TPK Kendari', 'TPK Pantoloan', 'TPK Tarakan', 'TPK Jayapura', 'TPK Sorong', 
            'TPK Biak', 'TPK Manokwari', 'TPK Merauke', 'Cabang Balikpapan', 'Cabang Samarinda', 
            'Cabang Tolitoli', 'Gorontalo', 'Cabang Parepare', 'Cabang Ternate', 'Cabang Nunukan', 
            'Cabang Tanjung Redeb', 'Cabang Fakfak'
        ];

        $dataCabang = [];
        $cabangInputCount = 0;

        foreach ($semuaCabang as $namaCabang) {
            $input = $piketInputs->where('lokasi', $namaCabang)->sortByDesc('created_at')->first();
            
            if ($input) {
                $cabangInputCount++;
                $status = 'Sudah Input';
                $persentase = $input->score;
                $kondisi = $persentase >= 65 ? 'Baik' : ($persentase >= 35 ? 'Perlu Atensi' : 'Kritis');
                $updatedAt = $input->updated_at ? $input->updated_at->format('H:i WIB') : '-';
            } else {
                $status = 'Belum Input';
                $persentase = 0;
                $kondisi = '-';
                $updatedAt = '-';
            }

            $dataCabang[] = [
                'nama' => $namaCabang,
                'status' => $status,
                'persentase' => $persentase,
                'kondisi' => $kondisi,
                'terakhir_update' => $updatedAt
            ];
        }

        return view('cabang.index', compact('dataCabang', 'tanggal', 'cabangInputCount'));
    }
}
