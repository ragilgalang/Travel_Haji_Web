<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseService;

class SeedPackages extends Command
{
    protected $signature   = 'packages:seed';
    protected $description = 'Seed paket Umrah & Haji ke Firebase berdasarkan data brosur';

    protected FirebaseService $firebase;

    public function __construct(FirebaseService $firebase)
    {
        parent::__construct();
        $this->firebase = $firebase;
    }

    public function handle(): void
    {
        $fasilitas = [
            '✈ Tiket Pesawat PP Surabaya–Jeddah (Langsung)',
            '📋 Visa Umroh & Handling',
            '🏨 Hotel Sesuai Paket',
            '🍽 Makan 3x Sehari Sesuai Jadwal Hotel',
            '💧 Air Zam-Zam 5 Liter (Oleh-oleh Pulang)',
            '🚌 Bus Executive & Seluruh Transportasi',
            '👤 Tour Leader Pendamping Bandara s/d Kepulangan',
            '🕌 Muthowif / Pembimbing di Makkah & Madinah',
            '🎓 Manasik 2x (Sunnah & Wajib) di Asrama Haji Sukolilo',
            '🧳 Perlengkapan: Koper, Tas Kabin, Tas Paspor, Kain Ihram, Batik & Syal, Buku Manasik, Hijab, Mukena, Sabuk, Sarung Tangan',
            '🗺 Free City Tour Madinah & Makkah (Jabal Nur, Jabal Uhud, Jabal Shur, Padang Arafat, Jikranah, Musdhalifah, Mina — Kondisional)',
            '📸 Foto Pigura Kenang-kenangan',
            '🚌 Seragam Travel saat Menuju Bandara',
            '⚖ Batas Bagasi: 25 kg + 7 kg Kabin',
        ];

        $packages = [
            // ── UMRAH 12 HARI ──────────────────────────────────────────────
            [
                'name'        => 'Umrah 12 Hari – Vila Hilton Bintang 5',
                'type'        => 'umrah',
                'duration'    => '12 Hari (2 Perjalanan PP + 4 Hari Madinah + 6 Hari Makkah)',
                'price'       => 'Hubungi Kami',
                'includes'    => 'Hotel 0 meter dari Ka\'bah, Tiket PP, Visa, Makan 3x, Perlengkapan Lengkap',
                'is_featured' => true,
                'image_url'   => 'https://images.unsplash.com/photo-1564769610446-b0af96b74b6d?w=800&q=80',
                'features'    => array_merge($fasilitas, [
                    '🌟 Hotel BINTANG 5 – Vila Hilton (0 Meter langsung di Pelataran Ka\'bah, 1 Kamar 6–8 Jamaah)',
                    '🚆 Free Kereta Cepat Madinah → Makkah + City Tour Thaif di Kota Makkah',
                ]),
            ],
            [
                'name'        => 'Umrah 12 Hari – Snood Ajyad Bintang 3',
                'type'        => 'umrah',
                'duration'    => '12 Hari (2 Perjalanan PP + 4 Hari Madinah + 6 Hari Makkah)',
                'price'       => 'Hubungi Kami',
                'includes'    => 'Hotel 200–400 m dari Masjid, Tiket PP, Visa, Makan 3x, Perlengkapan Lengkap',
                'is_featured' => true,
                'image_url'   => 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?w=800&q=80',
                'features'    => array_merge($fasilitas, [
                    '🏨 Hotel BINTANG 3 – Snood Ajyad / Setaraf (200–400 m dari Masjid, 1 Kamar 4 Jamaah)',
                    '🚌 Transportasi Bus dari Madinah ke Makkah',
                ]),
            ],
            [
                'name'        => 'Umrah 12 Hari – Maesan Al Maqam Bintang 3',
                'type'        => 'umrah',
                'duration'    => '12 Hari (2 Perjalanan PP + 4 Hari Madinah + 6 Hari Makkah)',
                'price'       => 'Hubungi Kami',
                'includes'    => 'Hotel 300–600 m dari Masjid (Jalan Kaki), Tiket PP, Visa, Makan 3x, Perlengkapan',
                'is_featured' => false,
                'image_url'   => 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?w=800&q=80',
                'features'    => array_merge($fasilitas, [
                    '🏨 Hotel BINTANG 3 – Maesan Al Maqam / Setaraf (300–600 m dari Masjid, Bisa Jalan Kaki, 1 Kamar 3–4 Jamaah)',
                    '🚌 Transportasi Bus dari Madinah ke Makkah',
                ]),
            ],
            [
                'name'        => 'Umrah 12 Hari – Olayan Palace Bintang 3',
                'type'        => 'umrah',
                'duration'    => '12 Hari (2 Perjalanan PP + 4 Hari Madinah + 6 Hari Makkah)',
                'price'       => 'Hubungi Kami',
                'includes'    => 'Hotel Shuttle Bus Gratis 5 mnt ke Masjid, Tiket PP, Visa, Makan 3x, Perlengkapan',
                'is_featured' => false,
                'image_url'   => 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?w=800&q=80',
                'features'    => array_merge($fasilitas, [
                    '🏨 Hotel BINTANG 3 – Olayan Palace / Setaraf (Shuttle Bus Gratis, 5 Menit ke Masjidil Haram, 1 Kamar 4 Jamaah)',
                    '🚌 Transportasi Bus dari Madinah ke Makkah',
                ]),
            ],

            // ── UMRAH 16 HARI ──────────────────────────────────────────────
            [
                'name'        => 'Umrah 16 Hari – Vila Hilton Bintang 5',
                'type'        => 'umrah',
                'duration'    => '16 Hari (2 Perjalanan PP + 4 Hari Madinah + 10 Hari Makkah)',
                'price'       => 'Hubungi Kami',
                'includes'    => 'Hotel 0 meter dari Ka\'bah, Tiket PP, Visa, Makan 3x, 2x Umroh Tawaf Wada\'',
                'is_featured' => true,
                'image_url'   => 'https://images.unsplash.com/photo-1564769610446-b0af96b74b6d?w=800&q=80',
                'features'    => array_merge($fasilitas, [
                    '🌟 Hotel BINTANG 5 – Vila Hilton (0 Meter langsung di Pelataran Ka\'bah, 1 Kamar 6–8 Jamaah)',
                    '🕌 Fasilitas 2x Umroh Termasuk 1 Tawaf Wada\'',
                    '🚆 Free Kereta Cepat Madinah → Makkah + City Tour Thaif',
                    '➕ Penawaran Umroh ke 3–5x dengan Tambahan Biaya +100rb/Miqot',
                ]),
            ],
            [
                'name'        => 'Umrah 16 Hari – Snood Ajyad Bintang 3',
                'type'        => 'umrah',
                'duration'    => '16 Hari (2 Perjalanan PP + 4 Hari Madinah + 10 Hari Makkah)',
                'price'       => 'Hubungi Kami',
                'includes'    => 'Hotel 200–400 m dari Masjid, Tiket PP, Visa, Makan 3x, 2x Umroh Tawaf Wada\'',
                'is_featured' => false,
                'image_url'   => 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?w=800&q=80',
                'features'    => array_merge($fasilitas, [
                    '🏨 Hotel BINTANG 3 – Snood Ajyad / Setaraf (200–400 m dari Masjid, 1 Kamar 4 Jamaah)',
                    '🕌 Fasilitas 2x Umroh Termasuk 1 Tawaf Wada\'',
                    '🚌 Transportasi Bus dari Madinah ke Makkah',
                    '➕ Penawaran Umroh ke 3–5x dengan Tambahan Biaya +100rb/Miqot',
                ]),
            ],
            [
                'name'        => 'Umrah 16 Hari – Maesan Al Maqam Bintang 3',
                'type'        => 'umrah',
                'duration'    => '16 Hari (2 Perjalanan PP + 4 Hari Madinah + 10 Hari Makkah)',
                'price'       => 'Hubungi Kami',
                'includes'    => 'Hotel 300–600 m dari Masjid, Tiket PP, Visa, Makan 3x, 2x Umroh Tawaf Wada\'',
                'is_featured' => false,
                'image_url'   => 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?w=800&q=80',
                'features'    => array_merge($fasilitas, [
                    '🏨 Hotel BINTANG 3 – Maesan Al Maqam / Setaraf (300–600 m dari Masjid, 1 Kamar 3–4 Jamaah)',
                    '🕌 Fasilitas 2x Umroh Termasuk 1 Tawaf Wada\'',
                    '🚌 Transportasi Bus dari Madinah ke Makkah',
                    '➕ Penawaran Umroh ke 3–5x dengan Tambahan Biaya +100rb/Miqot',
                ]),
            ],
            [
                'name'        => 'Umrah 16 Hari – Olayan Palace Bintang 3',
                'type'        => 'umrah',
                'duration'    => '16 Hari (2 Perjalanan PP + 4 Hari Madinah + 10 Hari Makkah)',
                'price'       => 'Hubungi Kami',
                'includes'    => 'Hotel Shuttle Bus Gratis, Tiket PP, Visa, Makan 3x, 2x Umroh Tawaf Wada\'',
                'is_featured' => false,
                'image_url'   => 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?w=800&q=80',
                'features'    => array_merge($fasilitas, [
                    '🏨 Hotel BINTANG 3 – Olayan Palace / Setaraf (Shuttle Bus Gratis, 5 Menit ke Masjidil Haram, 1 Kamar 4 Jamaah)',
                    '🕌 Fasilitas 2x Umroh Termasuk 1 Tawaf Wada\'',
                    '🚌 Transportasi Bus dari Madinah ke Makkah',
                    '➕ Penawaran Umroh ke 3–5x dengan Tambahan Biaya +100rb/Miqot',
                ]),
            ],
        ];

        $this->info('🕌 Memulai proses seeding paket Umrah ke Firebase...');
        $this->newLine();

        foreach ($packages as $pkg) {
            $this->line("   ▶ Menambahkan: {$pkg['name']}");
            $pkg['created_at'] = now()->toDateTimeString();
            $this->firebase->push('packages', $pkg);
        }

        \Illuminate\Support\Facades\Cache::forget('firebase_packages');
        \Illuminate\Support\Facades\Cache::forget('dashboard_stats');

        $this->newLine();
        $this->info('✅ Selesai! ' . count($packages) . ' paket berhasil ditambahkan ke Firebase.');
        $this->info('🔗 Lihat hasilnya di: http://127.0.0.1:8000/admin/packages');
    }
}
