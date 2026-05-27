<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('create:dummy', function (\App\Services\FirebaseService $firebase) {
    $this->info("=== MEMULAI GENERASI DATA DUMMY TRAVEL HAJI ===");

    // ----------------------------------------------------
    // 1. GENERASI 10 FASILITAS
    // ----------------------------------------------------
    $this->info("Menghapus data fasilitas lama & men-generate 10 fasilitas baru...");
    $firebase->deleteValue('facilities');

    $facilities = [
        [
            'title' => 'Hotel Bintang 5 Premium',
            'description' => 'Akomodasi hotel mewah bintang 5 yang sangat dekat dengan Masjidil Haram (Makkah) dan Masjid Nabawi (Madinah).',
            'icon' => '🏨',
            'hotel' => 'Pulman Zamzam / Swissotel Makkah',
            'image_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600&auto=format,compress&q=60'
        ],
        [
            'title' => 'Ustadz Pembimbing Sunnah',
            'description' => 'Bimbingan ibadah intensif sesuai dengan tuntunan Al-Qur\'an dan Sunnah oleh Ustadz berpengalaman.',
            'icon' => '🕋',
            'hotel' => 'Semua Paket',
            'image_url' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=600&auto=format,compress&q=60'
        ],
        [
            'title' => 'Tiket Pesawat PP (Direct)',
            'description' => 'Penerbangan langsung (tanpa transit) pulang-pergi menggunakan maskapai ternama seperti Saudi Arabian Airlines atau Garuda Indonesia.',
            'icon' => '✈️',
            'hotel' => 'Saudi Airlines / Garuda',
            'image_url' => 'https://images.unsplash.com/photo-1436491865332-7a61a109cc05?w=600&auto=format,compress&q=60'
        ],
        [
            'title' => 'Mutawwif & Muthawwifah',
            'description' => 'Didampingi oleh pemandu ibadah (Mutawwif) profesional berbahasa Indonesia selama prosesi ibadah di Tanah Suci.',
            'icon' => '🗣️',
            'hotel' => 'Semua Paket',
            'image_url' => 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=600&auto=format,compress&q=60'
        ],
        [
            'title' => 'Bus AC Eksekutif Premium',
            'description' => 'Transportasi bus mewah ber-AC dengan kenyamanan prima selama ziarah dan transfer antar kota suci.',
            'icon' => '🚌',
            'hotel' => 'Mercedes-Benz / VIP Class',
            'image_url' => 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=600&auto=format,compress&q=60'
        ],
        [
            'title' => 'Konsumsi Khas Indonesia 3x Prasmanan',
            'description' => 'Sajian makanan khas Nusantara 3 kali sehari yang disiapkan secara higienis melalui sistem prasmanan/katering hotel.',
            'icon' => '🍛',
            'hotel' => 'Full Board Catering',
            'image_url' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&auto=format,compress&q=60'
        ],
        [
            'title' => 'Air Zam-Zam 5 Liter',
            'description' => 'Setiap jemaah mendapatkan air Zam-Zam kemasan resmi 5 liter secara gratis saat tiba kembali di bandara Indonesia.',
            'icon' => '💧',
            'hotel' => 'Gratis Semua Paket',
            'image_url' => 'https://images.unsplash.com/photo-1608889174637-3c44f6326f1a?w=600&auto=format,compress&q=60'
        ],
        [
            'title' => 'Perlengkapan Premium Lengkap',
            'description' => 'Mendapatkan koper travel fiber bag, kain ihram / mukena, seragam batik travel, tas paspor, buku doa, dan ID card eksklusif.',
            'icon' => '🎒',
            'hotel' => 'Full Set Perlengkapan',
            'image_url' => 'https://images.unsplash.com/photo-1553531384-cc64ac80f931?w=600&auto=format,compress&q=60'
        ],
        [
            'title' => 'Visa Umrah / Haji Resmi',
            'description' => 'Pengurusan visa resmi jemaah langsung terintegrasi dengan e-visa Kementerian Haji & Umrah Arab Saudi.',
            'icon' => '📑',
            'hotel' => 'E-Visa Terintegrasi',
            'image_url' => 'https://images.unsplash.com/photo-1554774853-aae0a22c8aa4?w=600&auto=format,compress&q=60'
        ],
        [
            'title' => 'Layanan Ziarah Bersejarah',
            'description' => 'Kunjungan ziarah ke tempat-tempat bersejarah di Makkah (Jabal Nur, Arafah, Muzdalifah) dan Madinah (Masjid Quba, Jabal Uhud).',
            'icon' => '🕌',
            'hotel' => 'Termasuk Tour',
            'image_url' => 'https://images.unsplash.com/photo-1564507592333-c60657eea523?w=600&auto=format,compress&q=60'
        ]
    ];

    foreach ($facilities as $fac) {
        $firebase->push('facilities', $fac);
    }
    $this->info("✔ Berhasil menambahkan 10 fasilitas.");

    // ----------------------------------------------------
    // 2. GENERASI 40 PAKET (HAJI & UMRAH)
    // ----------------------------------------------------
    $this->info("Menghapus paket lama & men-generate 40 paket baru...");
    $firebase->deleteValue('packages');

    $paketNames = [
        // Umrah
        ['nama' => 'Umrah Awal Musim Hemat Syawal', 'type' => 'umrah', 'category' => 'Hemat', 'price' => 'Rp 27.500.000', 'duration' => '9 Hari', 'hotel' => 'Olayan Al-Haram (★3) / Al-Eiman (★3)'],
        ['nama' => 'Umrah Reguler Premium Bintang 5', 'type' => 'umrah', 'category' => 'Premium', 'price' => 'Rp 35.900.000', 'duration' => '9 Hari', 'hotel' => 'Swissotel Makkah (★5) / Pullman Zamzam (★5)'],
        ['nama' => 'Umrah Akbar Ramadhan Full Sebulan', 'type' => 'umrah', 'category' => 'VIP', 'price' => 'Rp 58.500.000', 'duration' => '30 Hari', 'hotel' => 'Anjum Makkah (★5) / Maden Madinah (★5)'],
        ['nama' => 'Umrah Awal Ramadhan Berkah', 'type' => 'umrah', 'category' => 'Regular', 'price' => 'Rp 32.500.000', 'duration' => '12 Hari', 'hotel' => 'Le Meridien Towers Makkah (★4) / Front Taiba (★4)'],
        ['nama' => 'Umrah Akhir Ramadhan & Lailatul Qadar', 'type' => 'umrah', 'category' => 'VIP', 'price' => 'Rp 49.900.000', 'duration' => '15 Hari', 'hotel' => 'Hilton Suites Makkah (★5) / Dallah Taibah (★5)'],
        ['nama' => 'Umrah Milenial Libur Akhir Tahun', 'type' => 'umrah', 'category' => 'Hemat', 'price' => 'Rp 29.800.000', 'duration' => '9 Hari', 'hotel' => 'Kiswah Towers (★3) / Al-Eiman (★3)'],
        ['nama' => 'Umrah Plus Wisata Turki Selat Bosphorus', 'type' => 'umrah', 'category' => 'Regular', 'price' => 'Rp 41.500.000', 'duration' => '12 Hari', 'hotel' => 'Grand Hyatt Istanbul (★5) / Swissotel Makkah (★5)'],
        ['nama' => 'Umrah Plus Wisata Mesir & Piramida Giza', 'type' => 'umrah', 'category' => 'Regular', 'price' => 'Rp 42.900.000', 'duration' => '13 Hari', 'hotel' => 'Fairmont Nile City (★5) / Hilton Suites Makkah (★5)'],
        ['nama' => 'Umrah Plus Aqsa & Petra Yordania', 'type' => 'umrah', 'category' => 'VIP', 'price' => 'Rp 47.500.000', 'duration' => '14 Hari', 'hotel' => 'National Palace Jerusalem (★4) / Anjum Makkah (★5)'],
        ['nama' => 'Umrah VIP Private Family Package', 'type' => 'umrah', 'category' => 'VIP', 'price' => 'Rp 62.000.000', 'duration' => '9 Hari', 'hotel' => 'Fairmont Makkah Clock Royal Tower (★5) / Oberoi Madinah (★5)'],
        
        // Haji
        ['nama' => 'Haji Furoda Eksklusif Langsung Berangkat', 'type' => 'haji', 'category' => 'VIP', 'price' => 'Rp 295.000.000', 'duration' => '25 Hari', 'hotel' => 'Fairmont Makkah (★5) / Shaza Madinah (★5)'],
        ['nama' => 'Haji Furoda Luxury Maktab VIP 111', 'type' => 'haji', 'category' => 'VIP', 'price' => 'Rp 325.000.000', 'duration' => '22 Hari', 'hotel' => 'Swissotel Makkah (★5) / Pullman Zamzam (★5)'],
        ['nama' => 'Haji Plus Kuota Kemenag Resmi', 'type' => 'haji', 'category' => 'Regular', 'price' => 'Rp 145.000.000', 'duration' => '30 Hari', 'hotel' => 'Movenpick Anwar Al Madinah (★5) / Anjum Makkah (★5)'],
        ['nama' => 'Haji Mujamalah Royal Class Tanpa Antre', 'type' => 'haji', 'category' => 'VIP', 'price' => 'Rp 275.000.000', 'duration' => '26 Hari', 'hotel' => 'Raffles Makkah Palace (★5) / Dar Al Taqwa (★5)'],
        ['nama' => 'Haji Khusus Reguler Bintang 4', 'type' => 'haji', 'category' => 'Regular', 'price' => 'Rp 115.000.000', 'duration' => '28 Hari', 'hotel' => 'Le Meridien Towers Makkah (★4) / Front Taiba (★4)']
    ];

    $imageUrls = [
        'https://images.unsplash.com/photo-1591604021695-0c69b7c05981?w=800&auto=format,compress&q=70',
        'https://images.unsplash.com/photo-1542838132-92c53300491e?w=800&auto=format,compress&q=70',
        'https://images.unsplash.com/photo-1564507592333-c60657eea523?w=800&auto=format,compress&q=70',
        'https://images.unsplash.com/photo-1580835239846-5bb9ce03c8c3?w=800&auto=format,compress&q=70',
        'https://images.unsplash.com/photo-1604999333679-b86d54738315?w=800&auto=format,compress&q=70'
    ];

    $featuresList = [
        "Tiket Pesawat Pulang Pergi Ekonomi Class\nVisa Umrah / Haji Resmi Arab Saudi\nHotel Dekat dengan Masjidil Haram & Nabawi\nBus AC Transportasi Mewah Selama di Arab\nMakan 3 Kali Sehari Prasmanan Khas Indonesia\nZiarah Kota Makkah, Madinah, & Jeddah\nMutawwif Profesional Berbahasa Indonesia\nAir Zam-Zam 5 Liter Kemasan Resmi Bandara\nManasik Haji & Umrah Intensif Sebelum Terbang",
        "Penerbangan Direct Tanpa Transit (Garuda/Saudi Airlines)\nAkomodasi Hotel Bintang 5 Terdekat Ring 1\nKereta Cepat Haramain (Makkah - Madinah)\nMaktab Tenda AC VIP Arafah & Mina saat Masyair\nDokter Pendamping & Layanan Medis 24 Jam\nPerlengkapan VIP (Koper Fiber, Batik Sutera, Kain Ihram)\nBimbingan Kitab Fadhilah Manasik Sunnah\nAsuransi Perjalanan Internasional Lengkap"
    ];

    // Generate 40 paket secara matematis
    for ($i = 1; $i <= 40; $i++) {
        // Ambil template atau buat variasi baru
        $template = $paketNames[($i - 1) % count($paketNames)];
        $name = $template['nama'];
        
        // Buat nama unik agar tidak double persis
        if ($i > count($paketNames)) {
            $name .= ' - Angkatan ' . (ceil($i / count($paketNames)));
        }

        $type = $template['type'];
        $category = $template['category'];
        $price = $template['price'];
        $duration = $template['duration'];
        $hotel = $template['hotel'];
        $image = $imageUrls[($i - 1) % count($imageUrls)];
        $features = $featuresList[($i - 1) % count($featuresList)];

        // Tambah variasi harga dikit biar keliatan natural
        if ($i > count($paketNames)) {
            $rawPrice = (int)preg_replace('/[^0-9]/', '', $price);
            $newRawPrice = $rawPrice + (($i % 5) * 500000);
            $price = 'Rp ' . number_format($newRawPrice, 0, ',', '.');
        }

        $pkgData = [
            'name' => $name,
            'type' => $type,
            'category' => $category,
            'duration' => $duration,
            'price' => $price,
            'hotel' => $hotel,
            'image_url' => $image,
            'contact_phone' => '628123456789' . ($i % 10),
            'promo_until' => now()->addDays(30 + ($i % 15))->toDateString(),
            'features' => array_values(array_filter(explode("\n", $features))),
            'hotel_facilities' => ['Free WiFi', 'Breakfast Buffet', '24h Room Service', 'Lounge Bar', 'Dekat Masjid (Ring 1)'],
            'is_featured' => ($i % 6 == 0),
            'created_at' => now()->subDays(60 - $i)->toDateTimeString()
        ];

        $firebase->push('packages', $pkgData);
    }
    $this->info("✔ Berhasil menambahkan 40 paket.");

    // ----------------------------------------------------
    // 3. GENERASI 40 PENDAFTARAN JEMAAH
    // ----------------------------------------------------
    $this->info("Menghapus pendaftaran lama & men-generate 40 pendaftaran baru...");
    $firebase->deleteValue('registrations');

    $firstNames = ['Ahmad', 'Muhammad', 'Siti', 'Budi', 'Joko', 'Andi', 'Putri', 'Dewi', 'Hendra', 'Eko', 'Rudi', 'Nur', 'Sri', 'Kartika', 'Adi', 'Rian', 'Anisa', 'Yuni', 'Hasan', 'Umar'];
    $lastNames = ['Hidayat', 'Saputra', 'Rahmawati', 'Kurniawan', 'Santoso', 'Pratama', 'Wijaya', 'Lestari', 'Nugroho', 'Subagyo', 'Sari', 'Fitriani', 'Susanto', 'Wahyudi', 'Ghofur', 'Zainuddin'];
    
    $statuses = ['Menunggu Verifikasi', 'Diproses', 'Selesai', 'Ditolak', 'Baru'];

    // Ambil list nama paket untuk dipasang ke pendaftaran
    $allPkgs = collect($firebase->getValue('packages') ?? []);
    $pkgNamesList = $allPkgs->pluck('name')->all();

    if (empty($pkgNamesList)) {
        $pkgNamesList = ['Umrah Reguler Premium Bintang 5', 'Haji Furoda Eksklusif Langsung Berangkat', 'Umrah Awal Musim Hemat Syawal'];
    }

    for ($i = 1; $i <= 40; $i++) {
        $firstName = $firstNames[array_rand($firstNames)];
        $lastName = $lastNames[array_rand($lastNames)];
        $namaLengkap = $firstName . ' ' . $lastName;
        
        $nik = '3515' . str_pad($i, 12, '0', STR_PAD_LEFT);
        $noHp = '081234' . str_pad($i, 6, '0', STR_PAD_LEFT);
        
        $refId = 'REG-' . strtoupper(Str::random(3)) . '-' . str_pad($i + 1000, 4, '0', STR_PAD_LEFT);
        $paket = $pkgNamesList[array_rand($pkgNamesList)];
        $status = $statuses[$i % count($statuses)];
        
        // Buat tanggal pendaftaran yang bervariasi selama 30 hari terakhir agar grafik statistiknya cantik
        $createdAt = now()->subDays(30 - ($i % 30))->subHours($i % 24)->subMinutes($i % 60)->toDateTimeString();

        $regData = [
            'nama_lengkap' => $namaLengkap,
            'nik' => $nik,
            'no_hp' => $noHp,
            'ref_id' => $refId,
            'paket' => $paket,
            'status' => $status,
            'is_archived' => ($status === 'Selesai' && $i % 4 === 0),
            'created_at' => $createdAt,
            'dynamic_fields' => [
                ['label' => 'Nama Lengkap', 'value' => $namaLengkap],
                ['label' => 'NIK (KTP)', 'value' => $nik],
                ['label' => 'Nomor HP/WhatsApp', 'value' => $noHp],
                ['label' => 'Alamat Lengkap', 'value' => 'Jl. Kebon Jeruk No. ' . $i . ', Sidoarjo, Jawa Timur'],
                ['label' => 'Nama Mahram / Pendamping', 'value' => ($i % 2 == 0 ? $firstName . ' Companion' : '-')],
                ['label' => 'Pilihan Paket Utama', 'value' => $paket]
            ]
        ];

        $firebase->push('registrations', $regData);
    }
    $this->info("✔ Berhasil menambahkan 40 pendaftaran jemaah.");

    // ----------------------------------------------------
    // 4. BERSIHKAN CACHE & UPDATE VISITOR LOG
    // ----------------------------------------------------
    $this->info("Membersihkan cache sistem...");
    \Illuminate\Support\Facades\Cache::flush();

    // Tambah log kunjungan agar statistik pengunjung juga terlihat hidup
    $this->info("Men-generate visitor log baru...");
    $firebase->deleteValue('visitor_log');
    $browsers = ['Chrome', 'Safari', 'Firefox', 'Edge'];
    $devices = ['Desktop 💻', 'Mobile 📱'];
    for ($i = 1; $i <= 50; $i++) {
        $firebase->push('visitor_log', [
            'ip_address' => '192.168.1.' . (50 + $i),
            'browser' => $browsers[$i % count($browsers)],
            'device' => $devices[$i % count($devices)],
            'timestamp' => now()->subDays(10 - ($i % 10))->subHours($i % 24)->toDateTimeString()
        ]);
    }
    $firebase->setValue('metrics/page_views', 1582);

    $this->info("=== PROSES GENERASI DATA DUMMY SELESAI DENGAN SUKSES! ===");
})->purpose('Generate 40 paket, 10 fasilitas, dan 40 pendaftaran dummy secara instan');

