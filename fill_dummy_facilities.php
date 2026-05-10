<?php

// Script Mandiri untuk Mengisi Data Dummy Fasilitas ke Firebase
// Jalankan via terminal: php fill_dummy_facilities.php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\FirebaseService;
use Illuminate\Support\Facades\Cache;

$firebase = new FirebaseService();

$dummyFacilities = [
    [
        'title' => 'Penerbangan',
        'icon' => '✈️',
        'description' => 'Tiket pesawat PP dengan maskapai terpercaya (Saudia/Garuda), kursi nyaman kelas ekonomi hingga bisnis.'
    ],
    [
        'title' => 'Hotel',
        'icon' => '🏨',
        'description' => 'Akomodasi premium bintang 5 berjarak 50–300 meter dari Masjidil Haram dan Masjid Nabawi.'
    ],
    [
        'title' => 'Katering',
        'icon' => '🍽️',
        'description' => 'Hidangan halal berkualitas 3x sehari dengan menu masakan Indonesia yang lezat dan bergizi.'
    ],
    [
        'title' => 'Transportasi',
        'icon' => '🚌',
        'description' => 'Bus full AC kelas premium terbaru untuk seluruh perjalanan ziarah di Makkah dan Madinah.'
    ],
    [
        'title' => 'Dokumen & Visa',
        'icon' => '🛂',
        'description' => 'Bantuan pengurusan paspor, visa haji/umrah, dan asuransi perjalanan yang aman, resmi, dan cepat.'
    ],
    [
        'title' => 'Bimbingan',
        'icon' => '📚',
        'description' => 'Manasik haji intensif sesuai sunnah dan paket perlengkapan ibadah lengkap berkualitas premium.'
    ],
    [
        'title' => 'Layanan Masyair',
        'icon' => '⛺',
        'description' => 'Layanan khusus fase Masyair di Arafah dan Mina dengan tenda ber-AC dan konsumsi terjaga.'
    ],
    [
        'title' => 'Kesehatan',
        'icon' => '🏥',
        'description' => 'Pendampingan tim kesehatan dan penyediaan obat-obatan dasar selama proses ibadah di Tanah Suci.'
    ],
    [
        'title' => 'Layanan Domestik',
        'icon' => '🇮🇩',
        'description' => 'Layanan penjemputan dari daerah asal hingga bantuan teknis saat keberangkatan di bandara Indonesia.'
    ]
];

echo "Sedang membersihkan data fasilitas lama...\n";
$firebase->setValue('facilities', []);

echo "Sedang memasukkan data dummy ke Firebase...\n";
foreach ($dummyFacilities as $fac) {
    $firebase->push('facilities', $fac);
    echo "Berhasil menambah: " . $fac['title'] . "\n";
}

echo "Membersihkan cache...\n";
Cache::forget('site_facilities');

echo "\nSELESAI! Semua data dummy fasilitas berhasil dimasukkan ke Firebase.\n";
echo "Silakan refresh halaman landing page Anda.\n";
