<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\FirebaseService;
$firebase = app(FirebaseService::class);

echo "MEMULAI PENGISIAN DATA PENDAFTARAN KONFIRMASI...\n";

$names = ['Budi Sudarsono', 'Siti Fatimah', 'Ahmad Dani', 'Rina Nose', 'Yusuf Mansur', 'Deddy Corbuzier', 'Najwa Shihab', 'Raffi Ahmad', 'Nagita Slavina', 'Irwansyah'];
$packages = [
    'Umroh Berkah Ramadhan', 
    'Haji Plus Eksklusif 2026', 
    'Umroh Hemat Syawal', 
    'Umroh Akhir Tahun', 
    'Haji Furoda Langsung Berangkat',
    'Umroh Milenial',
    'Umroh Private Keluarga',
    'Haji Khusus Tanpa Antri',
    'Umroh Berkah Ramadhan',
    'Umroh Hemat Syawal'
];

for ($i = 0; $i < 10; $i++) {
    $data = [
        'nama_lengkap' => $names[$i],
        'email' => strtolower(str_replace(' ', '', $names[$i])) . '@example.com',
        'telepon' => '0812' . rand(10000000, 99999999),
        'alamat' => 'Jl. Kebenaran No. ' . ($i + 1),
        'paket' => $packages[$i],
        'status' => 'Confirmed', // INI YANG MENGAKTIFKAN PENDAPATAN
        'created_at' => now()->subDays(rand(1, 30))->toDateTimeString()
    ];
    
    $firebase->push('registrations', $data);
}

\Illuminate\Support\Facades\Cache::flush();
echo "10 Pendaftaran 'Confirmed' berhasil ditambahkan. Silakan cek Laporan Keuangan!\n";
