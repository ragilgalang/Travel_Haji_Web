<?php

// Script Mandiri untuk Mengisi Data Dummy Paket Perjalanan ke Firebase
// Jalankan via terminal: php fill_dummy_packages.php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\FirebaseService;
use Illuminate\Support\Facades\Cache;

$firebase = new FirebaseService();

$dummyPackages = [
    [
        'name' => 'Umroh VIP Ramadhan 2026',
        'type' => 'umrah',
        'category' => 'Umroh VIP',
        'duration' => '12 Hari',
        'price' => 'Rp 45.500.000',
        'hotel' => 'Hilton Suites Makkah',
        'image_url' => 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?q=80&w=1000',
        'contact_phone' => '081234567890',
        'promo_until' => 'Hemat 2 Juta',
        'is_featured' => true,
        'features' => [
            'Tiket Pesawat Saudia Airlines (Direct)',
            'Visa Umroh & Asuransi Perjalanan',
            'Manasik Umroh 2x di Hotel Bintang 5',
            'Makan 3x Sehari Menu Indonesia Full Buffet',
            'Air Zam-zam 5 Liter',
            'Perlengkapan Umroh Eksklusif'
        ],
        'hotel_facilities' => [
            'View Langsung Masjidil Haram',
            'WiFi Kecepatan Tinggi',
            'Layanan Kamar 24 Jam',
            'Sarapan Buffet Internasional',
            'Jarak Hanya 50 Meter ke Pelataran'
        ],
        'created_at' => now()->toDateTimeString()
    ],
    [
        'name' => 'Paket Umroh Reguler Syawal',
        'type' => 'umrah',
        'category' => 'Umroh Reguler',
        'duration' => '9 Hari',
        'price' => 'Rp 29.900.000',
        'hotel' => 'Anjum Hotel Makkah',
        'image_url' => 'https://images.unsplash.com/photo-1542810634-71277d95dcbb?q=80&w=1000',
        'contact_phone' => '081234567890',
        'promo_until' => 'Grup Syawal',
        'is_featured' => false,
        'features' => [
            'Tiket Pesawat Garuda Indonesia',
            'Bus Mercedes Benz Terbaru 2024',
            'Muthawwif Berpengalaman Lulusan Al-Azhar',
            'Ziarah Kota Makkah & Madinah',
            'Handling Bandara & Bagasi'
        ],
        'hotel_facilities' => [
            'Kamar Luas & Bersih',
            'Restoran Masakan Indonesia',
            'Akses Lift Cepat',
            'Dekat Pusat Perbelanjaan',
            'Layanan Laundry Tersedia'
        ],
        'created_at' => now()->toDateTimeString()
    ],
    [
        'name' => 'Haji Furoda Eksklusif 2026',
        'type' => 'haji',
        'category' => 'Haji Furoda',
        'duration' => '25 Hari',
        'price' => 'Rp 285.000.000',
        'hotel' => 'Fairmont Makkah Clock Royal',
        'image_url' => 'https://images.unsplash.com/photo-1564769625905-50e93615e769?q=80&w=1000',
        'contact_phone' => '081234567890',
        'promo_until' => 'Sisa 5 Kursi',
        'is_featured' => true,
        'features' => [
            'Visa Haji Furoda Resmi (Langsung Berangkat)',
            'Tenda AC VIP di Arafah & Mina',
            'Bimbingan Ibadah Intensif Sesuai Sunnah',
            'Ziarah Khusus Thaif',
            'Hotel Transit Sebelum Puncak Haji'
        ],
        'hotel_facilities' => [
            'Akses Langsung ke Mall Clock Tower',
            'Fasilitas Gym & Spa',
            'Dinner Mewah View Ka\'bah',
            'Butler Service Pribadi',
            'Kamar Suite Keluarga'
        ],
        'created_at' => now()->toDateTimeString()
    ],
    [
        'name' => 'Umroh Ekonomis Awal Tahun',
        'type' => 'umrah',
        'category' => 'Umroh Ekonomis',
        'duration' => '9 Hari',
        'price' => 'Rp 24.500.000',
        'hotel' => 'Le Meridien Towers Makkah',
        'image_url' => 'https://images.unsplash.com/photo-1580418827493-f2b22c0a76cb?q=80&w=1000',
        'contact_phone' => '081234567890',
        'promo_until' => 'Early Bird',
        'is_featured' => false,
        'features' => [
            'Tiket Pesawat Lion Air / AirAsia',
            'Hotel Bintang 4 / Setaraf',
            'Makan 3x Sehari Box/Buffet',
            'Bimbingan Manasik di Jakarta',
            'Sertifikat Umroh'
        ],
        'hotel_facilities' => [
            'Layanan Shuttle Bus 24 Jam ke Haram',
            'WiFi Gratis di Lobby',
            'Lobby Luas & Nyaman',
            'Kantin Indonesia Terdekat',
            'Kamar Mandi Dalam & AC'
        ],
        'created_at' => now()->toDateTimeString()
    ]
];

echo "Sedang membersihkan data paket lama...\n";
$firebase->setValue('packages', []);

echo "Sedang memasukkan data dummy paket ke Firebase...\n";
foreach ($dummyPackages as $pkg) {
    $firebase->push('packages', $pkg);
    echo "Berhasil menambah: " . $pkg['name'] . "\n";
}

echo "Membersihkan cache...\n";
Cache::flush();

echo "\nSELESAI! Semua data dummy paket berhasil dimasukkan ke Firebase.\n";
echo "Silakan refresh halaman Kelola Paket di Admin Anda.\n";
