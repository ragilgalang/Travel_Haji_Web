<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LandingPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default Admin User
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@travelhaji.co.id'],
            [
                'name' => 'Admin TravelHaji',
                'password' => bcrypt('admin123'),
            ]
        );

        // App Settings
        $settings = [
            'site_name' => 'TravelHaji',
            'hero_title' => 'Wujudkan Perjalanan Suci ke Baitullah',
            'hero_description' => 'Lebih dari 12.000 jemaah telah kami antarkan ke Tanah Suci dengan aman, nyaman, dan penuh keberkahan. Percayakan ibadah Haji & Umrah Anda bersama kami.',
            'about_title' => 'Amanah Mengantarkan Anda ke Rumah Allah',
            'about_description' => 'Sejak 2004, TravelHaji hadir melayani jemaah Indonesia dengan penuh dedikasi. Kami bukan sekadar biro perjalanan — kami adalah mitra ibadah Anda.',
            'contact_phone' => '0800-123-4567',
            'contact_wa' => '0812-3456-7890',
            'contact_email' => 'info@travelhaji.co.id',
            'footer_description' => 'Melayani dengan sepenuh hati untuk mengantarkan Anda ke Baitullah. Berizin resmi Kemenag RI sejak 2004.',
        ];

        foreach ($settings as $key => $value) {
            \App\Models\AppSetting::set($key, $value);
        }

        // Packages
        \App\Models\Package::updateOrCreate(['name' => 'Umrah Reguler'], [
            'type' => 'umrah',
            'duration' => '9 Hari',
            'price' => '28 Juta',
            'includes' => 'Sudah termasuk tiket & visa',
            'features' => ['Tiket pesawat economy', 'Visa Umrah resmi', 'Hotel bintang 4', 'Katering 2x sehari', 'Pembimbing ibadah', 'Ziarah Madinah'],
            'image_url' => 'https://images.unsplash.com/photo-1515091943-9d5c0ad475af?w=600&q=80&fit=crop',
            'is_featured' => false,
        ]);

        \App\Models\Package::updateOrCreate(['name' => 'Haji Plus'], [
            'type' => 'haji',
            'duration' => '21 Hari',
            'price' => '125 Juta',
            'includes' => 'All inclusive · Antrian lebih cepat',
            'features' => ['Visa haji khusus', 'Hotel bintang 5 dekat Ka\'bah', 'Business class flight', 'Katering 3x sehari', 'Muthawwif pribadi', 'Asuransi perjalanan', 'Perlengkapan premium'],
            'image_url' => 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?w=1600&q=80&fit=crop',
            'is_featured' => true,
        ]);

        // Testimonials
        \App\Models\Testimonial::updateOrCreate(['name' => 'Hj. Siti Rahayu'], [
            'location' => 'Surabaya',
            'rating' => 5,
            'text' => 'Alhamdulillah, semua terorganisir dengan sangat baik. Hotel dekat sekali dengan Masjidil Haram, pembimbing sabar dan ilmunya luas. Sangat merekomendasikan TravelHaji!',
        ]);

        // Facilities
        \App\Models\Facility::updateOrCreate(['title' => 'Penerbangan Langsung'], [
            'description' => 'Direct flight dari kota Anda ke Madinah/Jeddah.',
            'icon' => '✈️',
            'image_url' => 'https://images.unsplash.com/photo-1436491865332-7a61a109cc05?w=400&q=80&fit=crop',
        ]);
    }
}
