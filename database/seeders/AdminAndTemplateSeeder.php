<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Template;
use Illuminate\Support\Facades\Hash;

class AdminAndTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Admin User
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'lokasi_fix' => null, // Admins don't have a fixed location by default
            ]
        );

        // 2. Create Daily Template
        $dailyTemplate = [
            [
                'section_title' => 'A. Monitoring kesiapan fasilitas pelabuhan',
                'is_resume' => false,
                'subsections' => [
                    [
                        'name' => '',
                        'items' => [
                            'Alur dan kolam', 
                            'Fasilitas terminal dan lapangang', 
                            'Fasilitas dan kebersihan terminal penumpang', 
                            'Kecukupan sistem teknologi informasi dan digitalisasi', 
                            'Kesiapan rencana kontinjensi terhadap gangguan sistem dan lonjakan ekstrem aktivitas pelabuhan', 
                            'Kesiapan rencana kontinjensi terhadap gangguan cuaca buruk pada pelayanan penumpang'
                        ],
                        'kondisi_options' => ['Baik', 'Kurang'],
                        'metode_options' => ['Site Visit', 'Online'],
                    ]
                ]
            ],
            [
                'section_title' => 'B. Monitoring Kesiapan Pelayanan BM dan Pelayanan Operasional Pelabuhan',
                'is_resume' => false,
                'subsections' => [
                    [
                        'name' => '',
                        'items' => [
                            'Kesiapan dan keandalan peralatan bongkar muat', 
                            'Kesiapan SDM operasional', 
                            'Indikator Dwelling Time sebagai kontrol kinerja operasional dan tata kelola arus barang di dalam kawasan pelabuhan', 
                            'Indikator pelayanan operasional', 
                            'Aspek keamanan dan zero major incident dalam pelayanan operasional'
                        ],
                        'kondisi_options' => ['Baik', 'Kurang'],
                        'metode_options' => ['Site Visit', 'Online'],
                    ]
                ]
            ],
            [
                'section_title' => 'C. Monitoring SBPP (Sarana Bantu Pemanduan dan Penundaan)',
                'is_resume' => false,
                'subsections' => [
                    [
                        'name' => '',
                        'items' => [
                            'Kesiapan sarana pendukung serta kesiapan tenaga pandu dan awak kapal', 
                            'Aspek K3 dan keselamatan pelayaran', 
                            'Kesiapan rencana kontinjensi menghadapi cuaca ekstrem dan lonjakan trafik kapal'
                        ],
                        'kondisi_options' => ['Baik', 'Kurang'],
                        'metode_options' => ['Site Visit', 'Online'],
                    ]
                ]
            ],
            [
                'section_title' => 'D. Monitoring Komplain Pengguna Jasa, Hubungan Kelembagaan, dan Media Handling',
                'is_resume' => false,
                'subsections' => [
                    [
                        'name' => '',
                        'items' => [
                            'Penyelelesaian komplain dari asosiasi dan pengguna jasa', 
                            'Mekanisme komunikasi publik dan media handling', 
                            'Mekanisme komunikasi dan koordinasi cabang pelabuhan dengan stakeholder strategis'
                        ],
                        'kondisi_options' => ['Baik', 'Kurang'],
                        'metode_options' => ['Site Visit', 'Online'],
                    ]
                ]
            ],
            [
                'section_title' => 'E. Evaluasi dan Resume Pelaksanaan Monitoring',
                'is_resume' => true,
                'subsections' => [
                    [
                        'name' => '',
                        'items' => [
                            'Monitoring Fasilitas dan Operasional Pelabuhan', 
                            'Posisi Sandar Kapal', 
                            'Laporan insiden khusus', 
                            'Catatan'
                        ],
                        'kondisi_options' => [],
                        'metode_options' => [],
                    ]
                ]
            ]
        ];

        Template::updateOrCreate(
            ['jenis_piket' => 'Daily'],
            ['content' => $dailyTemplate]
        );

        // 3. Create Angkutan Lebaran Template
        $angkutanTemplate = [
            [
                'section_title' => 'A. MONITORING FASILITAS PELABUHAN',
                'is_resume' => false,
                'subsections' => [
                    [
                        'name' => '1. Fasilitas Dermaga',
                        'items' => ['Dermaga', 'Bolder', 'Fender'],
                        'kondisi_options' => ['Baik', 'Kurang'],
                        'metode_options' => ['Site Visit', 'Online'],
                    ],
                    [
                        'name' => '2. Fasilitas Terminal penumpang',
                        'items' => ['Ruangan Tunggu Terminal', 'Posko', 'Lunch Room', 'Toilet', 'Mushola', 'Laktasi', 'AC / Kipas', 'XRAY', 'Tangga Penumpang'],
                        'kondisi_options' => ['Baik', 'Kurang'],
                        'metode_options' => ['Site Visit', 'Online'],
                    ],
                ]
            ],
            [
                'section_title' => 'B. MONITORING OPERASIONAL',
                'is_resume' => false,
                'subsections' => [
                    [
                        'name' => '1. Kesiapan SDM',
                        'items' => ['SDM Operasi', 'Pengamanan'],
                        'kondisi_options' => ['Baik', 'Kurang'],
                        'metode_options' => ['Site Visit', 'Online'],
                    ],
                    [
                        'name' => '2. Operasional Embarkasi / Debarkasi',
                        'items' => ['Kapal yang berkegiatan', 'Antrian & Kepadatan Penumpang embarkasi / debarkasi'],
                        'kondisi_options' => ['Baik', 'Kurang'],
                        'metode_options' => ['Site Visit', 'Online'],
                    ]
                ]
            ],
            [
                'section_title' => 'C. MONITORING HSSE',
                'is_resume' => false,
                'subsections' => [
                    [
                        'name' => '1. Pengelolaan HSSE',
                        'items' => ['Incident', 'Unsafe Action', 'Unsafe Condition'],
                        'kondisi_options' => ['Ada', 'Tidak Ada'],
                        'metode_options' => ['Site Visit', 'Online'],
                    ],
                    [
                        'name' => '2. Kelengkapan Fasilitas HSSE',
                        'items' => ['Kelengkapan Fasilitas HSSE'],
                        'kondisi_options' => ['Lengkap', 'Tdk Lengkap'],
                        'metode_options' => ['Site Visit', 'Online'],
                    ],
                    [
                        'name' => '3. Fit to Work',
                        'items' => ['Fit to Work'],
                        'kondisi_options' => ['Baik', 'Kurang'],
                        'metode_options' => ['Site Visit', 'Online'],
                    ]
                ]
            ],
            [
                'section_title' => 'D. MONITORING KOMPLAIN PENGGUNA JASA, HUBUNGAN KELEMBAGAAN DAN MEDIA HANDLING',
                'is_resume' => false,
                'subsections' => [
                    [
                        'name' => '',
                        'items' => ['Penyelesaian Komplain', 'Komunikasi Publik dan Media Handling', 'Komunikasi dengan Stakeholder Terkait'],
                        'kondisi_options' => ['Baik', 'Kurang'],
                        'metode_options' => ['Site Visit', 'Online'],
                    ]
                ]
            ],
            [
                'section_title' => 'E. MONITORING ANTI PENYUAPAN, FRAUD DAN GRATIFIKASI',
                'is_resume' => false,
                'subsections' => [
                    [
                        'name' => '',
                        'items' => ['Tidak terdapat indikasi praktik tipping, pungutan liar, penyuapan, fraud, atau pemberian gratifikasi kepada petugas serta tidak terdapat percepatan layanan tidak resmi di luar prosedur yang berlaku.'],
                        'kondisi_options' => ['Ada', 'Tidak Ada'],
                        'metode_options' => ['Site Visit', 'Online'],
                    ]
                ]
            ],
            [
                'section_title' => 'F. Evaluasi dan Resume Pelaksanaan Monitoring',
                'is_resume' => true,
                'subsections' => [
                    [
                        'name' => '',
                        'items' => [
                            'Monitoring Fasilitas dan Operasional Pelabuhan', 
                            'Posisi Sandar Kapal', 
                            'Laporan insiden khusus', 
                            'Catatan'
                        ],
                        'kondisi_options' => [],
                        'metode_options' => [],
                    ]
                ]
            ]
        ];

        Template::updateOrCreate(
            ['jenis_piket' => 'Angkutan Lebaran'],
            ['content' => $angkutanTemplate]
        );

        Template::updateOrCreate(
            ['jenis_piket' => 'Libur Nataru'],
            ['content' => $angkutanTemplate]
        );
    }
}
