<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\FirebaseService;

$firebase = app(FirebaseService::class);

$names = [
    'Baim Wong',
    'Paula Verhoeven',
    'Atta Halilintar',
    'Aurel Hermansyah',
    'Raffi Ahmad'
];

$packages = [
    'Umroh Berkah Ramadhan',
    'Haji Plus Eksklusif 2026',
    'Umroh Hemat Syawal',
    'Haji Furoda Langsung Berangkat',
    'Umroh Milenial'
];

$statuses = ['Menunggu Verifikasi', 'Sedang Diproses', 'Sudah Dikonfirmasi', 'Selesai'];

echo "Sedang menambahkan 5 data jemaah dummy...\n";

for ($i = 0; $i < 5; $i++) {
    $data = [
        'nama_lengkap' => $names[$i],
        'paket' => $packages[$i],
        'no_hp' => '0812345678' . $i,
        'email' => strtolower(str_replace(' ', '', $names[$i])) . '@example.com',
        'status' => $statuses[array_rand($statuses)],
        'created_at' => now()->subDays(rand(1, 10))->toDateTimeString(),
        'dynamic_fields' => [
            ['label' => 'Nama Lengkap', 'value' => $names[$i]],
            ['label' => 'Email', 'value' => strtolower(str_replace(' ', '', $names[$i])) . '@example.com'],
            ['label' => 'Telepon', 'value' => '0812345678' . $i],
            ['label' => 'Alamat', 'value' => 'Jl. Kebenaran No. ' . rand(1, 100)],
            ['label' => 'Paket', 'value' => $packages[$i]],
        ]
    ];
    
    $firebase->push('registrations', $data);
    echo "Berhasil menambahkan: " . $names[$i] . "\n";
}

echo "Selesai! 5 data jemaah telah ditambahkan.";
