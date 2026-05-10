<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\FirebaseService;

class FirebaseSeeder extends Seeder
{
    public function run(): void
    {
        $firebase = new FirebaseService();

        // 1. Settings
        $settings = [
            'site_name' => 'TravelHaji',
            'hero_title' => "Wujudkan Perjalanan\nSuci ke Baitullah",
            'hero_description' => 'Lebih dari 12.000 jemaah telah kami antarkan ke Tanah Suci dengan aman, nyaman, dan penuh keberkahan. Percayakan ibadah Haji & Umrah Anda bersama kami.',
            'about_title' => 'Amanah Mengantarkan Anda ke Rumah Allah',
            'about_description' => 'Sejak 2004, TravelHaji hadir melayani jemaah Indonesia dengan penuh dedikasi. Kami bukan sekadar biro perjalanan — kami adalah mitra ibadah Anda.',
            'contact_phone' => '0800-123-4567',
            'contact_email' => 'info@travelhaji.co.id',
            'contact_wa' => '0812-3456-7890',
            'footer_description' => 'Melayani dengan sepenuh hati untuk mengantarkan Anda ke Baitullah. Berizin resmi Kemenag RI sejak 2004.',
        ];
        foreach ($settings as $key => $value) {
            $firebase->setValue('settings/' . $key, $value);
        }

        // 2. Packages
        $packages = [
            'pkg1' => [
                'name' => 'Umrah Reguler',
                'type' => 'umrah',
                'duration' => '9 Hari',
                'price' => 'Rp 28 Juta',
                'includes' => 'Sudah termasuk tiket & visa',
                'features' => ['Tiket pesawat economy', 'Visa Umrah resmi', 'Hotel bintang 4', 'Katering 2x sehari'],
                'is_featured' => false
            ],
            'pkg2' => [
                'name' => 'Haji Plus',
                'type' => 'haji',
                'duration' => '21 Hari',
                'price' => 'Rp 125 Juta',
                'includes' => 'All inclusive · Antrian lebih cepat',
                'features' => ['Visa haji khusus', 'Hotel bintang 5', 'Business class flight'],
                'is_featured' => true
            ]
        ];
        $firebase->setValue('packages', $packages);

        // 3. Testimonials
        $testimonials = [
            'testi1' => [
                'name' => 'Hj. Siti Rahayu',
                'location' => 'Surabaya',
                'rating' => 5,
                'text' => 'Alhamdulillah, semua terorganisir dengan sangat baik.',
            ]
        ];
        $firebase->setValue('testimonials', $testimonials);

        // 4. Facilities
        $facilities = [
            'fac1' => [
                'title' => 'Penerbangan Langsung',
                'description' => 'Direct flight dari kota Anda ke Madinah/Jeddah.',
                'icon' => '✈️'
            ]
        ];
        $firebase->setValue('facilities', $facilities);

        // 5. Admin User
        $adminEmail = 'admin123@gmail.com';
        $adminPassword = \Illuminate\Support\Facades\Hash::make('admin123');
        $adminUser = [
            'email' => $adminEmail,
            'password' => $adminPassword,
            'name' => 'Administrator Utama',
            'role' => 'admin',
            'created_at' => now()->toDateTimeString(),
        ];
        // Use unique key based on email for easy tracking
        $userKey = 'admin_' . str_replace(['@', '.'], '_', $adminEmail);
        $firebase->setValue('users/' . $userKey, $adminUser);

        $this->command->info('Data successfully pushed directly to Firebase RTDB!');
    }
}
