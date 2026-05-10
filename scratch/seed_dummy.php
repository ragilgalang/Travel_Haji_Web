<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\FirebaseService;
$firebase = app(FirebaseService::class);

echo "MEMULAI PENGISIAN DATA DUMMY...\n";

// 1. DUMMY PAKET (10)
$packages = [
    ['name' => 'Umroh Berkah Ramadhan', 'type' => 'umrah', 'price' => '35 jt', 'duration' => '12 Hari', 'hotel' => 'Hilton Makkah', 'category' => 'Premium'],
    ['name' => 'Haji Plus Eksklusif 2026', 'type' => 'haji', 'price' => '150 jt', 'duration' => '25 Hari', 'hotel' => 'Movenpick', 'category' => 'VIP'],
    ['name' => 'Umroh Hemat Syawal', 'type' => 'umrah', 'price' => '22 jt', 'duration' => '9 Hari', 'hotel' => 'Olayan Ajyad', 'category' => 'Ekonomis'],
    ['name' => 'Umroh Akhir Tahun', 'type' => 'umrah', 'price' => '28 jt', 'duration' => '10 Hari', 'hotel' => 'Pullman Zamzam', 'category' => 'Reguler'],
    ['name' => 'Haji Furoda Langsung Berangkat', 'type' => 'haji', 'price' => '250 jt', 'duration' => '20 Hari', 'hotel' => 'Fairmont', 'category' => 'VVIP'],
    ['name' => 'Umroh Milenial', 'type' => 'umrah', 'price' => '20 jt', 'duration' => '9 Hari', 'hotel' => 'Le Meridien', 'category' => 'Ekonomis'],
    ['name' => 'Umroh Private Keluarga', 'type' => 'umrah', 'price' => '45 jt', 'duration' => '12 Hari', 'hotel' => 'Conrad Makkah', 'category' => 'Premium'],
    ['name' => 'Haji Khusus Tanpa Antri', 'type' => 'haji', 'price' => '180 jt', 'duration' => '22 Hari', 'hotel' => 'Swissotel', 'category' => 'VIP'],
    ['name' => 'Umroh Itikaf Lailatul Qadar', 'type' => 'umrah', 'price' => '38 jt', 'duration' => '15 Hari', 'hotel' => 'Anjum Hotel', 'category' => 'Premium'],
    ['name' => 'Umroh Plus Turki', 'type' => 'umrah', 'price' => '32 jt', 'duration' => '14 Hari', 'hotel' => 'CVK Park Bosphorus', 'category' => 'Wisata'],
];

foreach ($packages as $pkg) {
    $pkg['created_at'] = now()->toDateTimeString();
    $firebase->push('packages', $pkg);
}
echo "10 Paket berhasil ditambahkan.\n";

// 2. DUMMY FASILITAS (10)
$facilities = [
    ['name' => 'Pesawat Saudia Airlines', 'description' => 'Penerbangan langsung tanpa transit.'],
    ['name' => 'Hotel Bintang 5', 'description' => 'Akomodasi dekat dengan Masjidil Haram.'],
    ['name' => 'Makan 3x Sehari', 'description' => 'Menu masakan Indonesia yang lezat.'],
    ['name' => 'Bus AC Terbaru', 'description' => 'Transportasi nyaman selama di Arab Saudi.'],
    ['name' => 'Mutawwif Berpengalaman', 'description' => 'Pembimbing ibadah yang sesuai sunnah.'],
    ['name' => 'Ziarah Makkah & Madinah', 'description' => 'Kunjungan ke tempat-tempat bersejarah.'],
    ['name' => 'Perlengkapan Ibadah Lengkap', 'description' => 'Koper, kain ihram, mukena, dan seragam.'],
    ['name' => 'Air Zamzam 5 Liter', 'description' => 'Oleh-oleh air zamzam untuk setiap jemaah.'],
    ['name' => 'Asuransi Perjalanan', 'description' => 'Perlindungan kesehatan selama perjalanan.'],
    ['name' => 'Manasik Haji & Umroh', 'description' => 'Bimbingan sebelum keberangkatan.'],
];

foreach ($facilities as $fac) {
    $fac['created_at'] = now()->toDateTimeString();
    $firebase->push('facilities', $fac);
}
echo "10 Fasilitas berhasil ditambahkan.\n";

// 3. DUMMY TESTIMONI (10)
$testimonials = [
    ['name' => 'H. Ahmad Subarjo', 'message' => 'Pelayanan sangat luar biasa, mutawwif sabar membimbing kami.', 'rating' => 5, 'category' => 'Umrah'],
    ['name' => 'Hj. Siti Aminah', 'message' => 'Alhamdulillah bisa haji tanpa antri lama, fasilitas hotel sangat dekat.', 'rating' => 5, 'category' => 'Haji'],
    ['name' => 'Bpk. Budi Santoso', 'message' => 'Sangat puas dengan makanan dan busnya yang nyaman.', 'rating' => 4, 'category' => 'Umrah'],
    ['name' => 'Ibu Ratna Sari', 'message' => 'Travel yang sangat amanah dan profesional.', 'rating' => 5, 'category' => 'Umrah'],
    ['name' => 'H. Lukman Hakim', 'message' => 'Pengalaman haji yang tidak terlupakan bersama UMB.', 'rating' => 5, 'category' => 'Haji'],
    ['name' => 'dr. Andi Wijaya', 'message' => 'Manajemen waktu sangat baik, ibadah jadi lebih khusyuk.', 'rating' => 4, 'category' => 'Umrah'],
    ['name' => 'Hj. Maryam', 'message' => 'Terima kasih UMB, umroh jadi terasa sangat mudah.', 'rating' => 5, 'category' => 'Umrah'],
    ['name' => 'Bpk. Yusuf', 'message' => 'Harga bersaing dengan fasilitas yang sangat mewah.', 'rating' => 5, 'category' => 'Umrah'],
    ['name' => 'Ibu Fatimah', 'message' => 'Pembimbingnya sangat paham ilmu agama, mantap.', 'rating' => 5, 'category' => 'Haji'],
    ['name' => 'H. Ridwan', 'message' => 'Koper dan seragamnya sangat berkualitas.', 'rating' => 4, 'category' => 'Umrah'],
];

foreach ($testimonials as $testi) {
    $testi['created_at'] = now()->toDateTimeString();
    $testi['is_visible'] = true;
    $firebase->push('testimonials', $testi);
}
echo "10 Testimoni berhasil ditambahkan.\n";

\Illuminate\Support\Facades\Cache::flush();
echo "PROSES SELESAI. SEMUA CACHE TELAH DIBERSIHKAN.\n";
