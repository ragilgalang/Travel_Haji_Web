<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\RegistrationAdminController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\ChatAssistantController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Auth\AuthController;
use App\Models\Package;
use App\Models\Testimonial;
use App\Models\Facility;

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/fix-passwords', function () {
    $passwordHash = \Illuminate\Support\Facades\Hash::make('password123');
    
    // Update/Buat 5 Role Demo
    $roles = [
        'admin'   => 'Administrator Travel',
        'manager' => 'Operational Manager',
        'finance' => 'Finance Staff',
        'cs'      => 'Customer Service',
        'staff'   => 'General Staff / Lapangan'
    ];

    foreach ($roles as $role => $name) {
        $email = $role . '@gmail.com';
        // Akun admin khusus
        if ($role == 'admin') $email = 'admin123@gmail.com';

        \Illuminate\Support\Facades\DB::table('users')->updateOrInsert(
            ['email' => $email],
            [
                'name' => $name,
                'password' => $passwordHash,
                'role' => $role,
                'login_attempts' => 0,
                'locked_until' => null
            ]
        );
    }
    
    return "Selesai! 5 Akun Role telah disiapkan dengan password: password123";
});

// Fitur Pintasan Login (Hanya untuk testing)
Route::get('/masuk-sebagai/{role}', function ($role) {
    $email = ($role == 'admin') ? 'admin123@gmail.com' : $role . '@gmail.com';
    
    // Cari user di SQLite
    $user = \Illuminate\Support\Facades\DB::table('users')->where('email', $email)->first();
    
    if (!$user) return "User dengan role $role tidak ditemukan. Jalankan /fix-passwords dulu.";

    // Buat object model User untuk di-login-kan
    $authUser = new \App\Models\User((array) $user);
    $authUser->id = $user->id; 
    
    // Paksa login!
    \Illuminate\Support\Facades\Auth::login($authUser);

    return redirect('/admin/dashboard')->with('success', 'Berhasil masuk sebagai ' . ucfirst($role));
});

// Pendaftaran Customer
Route::get('/daftar', [RegistrationController::class, 'show'])->name('register.show');
Route::post('/daftar', [RegistrationController::class, 'store'])->name('register.store');
Route::get('/daftar/sukses', [RegistrationController::class, 'success'])->name('register.success');
Route::post('/daftar/cek-status', [RegistrationController::class, 'checkStatus'])->name('register.checkStatus');

// Ulasan & Kontak
Route::post('/review/submit', [HomeController::class, 'submitReview'])->name('review.submit');
Route::post('/contact/submit', [HomeController::class, 'submitContact'])->name('contact.submit');

// SEO Sitemap
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// Dummy Registration for Testing Notifications
Route::get('/buat-dummy-pendaftar', function () {
    $firebase = app(\App\Services\FirebaseService::class);
    $faker = \Faker\Factory::create('id_ID');
    
    $refId = 'REG-' . strtoupper(\Illuminate\Support\Str::random(8));
    $nama = $faker->name;
    
    $data = [
        'nama_lengkap' => $nama,
        'nik' => $faker->numerify('################'),
        'no_hp' => $faker->phoneNumber,
        'ttl' => $faker->city,
        'tgl' => $faker->date('Y-m-d', '2000-01-01'),
        'gender' => $faker->randomElement(['Laki-laki', 'Perempuan']),
        'alamat' => $faker->address,
        'paket' => 'Paket Umrah Reguler (Dummy)',
        'kamar' => 'Quad',
        'catatan' => 'Pendaftaran uji coba sistem notifikasi',
        'wali' => $faker->name,
        'hubungan' => 'Saudara',
        'hp_darurat' => $faker->phoneNumber,
        'status' => 'Menunggu Verifikasi',
        'created_at' => now()->toDateTimeString(),
        'ref_id' => $refId
    ];

    // Dynamic fields for admin view
    $data['dynamic_fields'] = [
        ['label' => 'NIK (16 Digit)', 'value' => $data['nik'], 'type' => 'text'],
        ['label' => 'Tempat Lahir', 'value' => $data['ttl'], 'type' => 'text'],
        ['label' => 'Paket Dipilih', 'value' => $data['paket'], 'type' => 'text'],
        ['label' => 'No. HP Darurat', 'value' => $data['hp_darurat'], 'type' => 'text'],
    ];

    $firebase->getReference('registrations')->push($data);

    return "<h3>✅ Berhasil membuat pendaftaran dummy!</h3>
            <p>Nama: $nama</p>
            <p>Ref ID: $refId</p>
            <p>Silakan buka dashboard admin dan tunggu maksimal 30 detik untuk melihat angka pada lonceng berubah.</p>
            <a href='/admin/dashboard'>Kembali ke Dashboard</a>";
});


// Auth
Route::get('/ptumb', [AuthController::class, 'login'])->name('login');
Route::post('/ptumb', [AuthController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/guide', [AdminController::class, 'guide'])->name('guide');
    
    // Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::get('/settings/preview', [AdminController::class, 'preview'])->name('settings.preview');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    Route::post('/clear-cache', [AdminController::class, 'clearCache'])->name('clearCache');

    // Profile / Akun
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // CRUDs (Hanya Admin dan Manager)
    Route::middleware(['crud'])->group(function () {
        Route::post('/packages/bulk-action', [PackageController::class, 'bulkAction'])->name('packages.bulkAction');
        Route::resource('packages', PackageController::class);
        
        Route::post('/testimonials/bulk-action', [TestimonialController::class, 'bulkAction'])->name('testimonials.bulkAction');
        Route::resource('testimonials', TestimonialController::class);
        Route::post('/testimonials/sync-gmaps', [TestimonialController::class, 'syncGmaps'])->name('testimonials.syncGmaps');
        Route::patch('/testimonials/{id}/toggle-visibility', [TestimonialController::class, 'toggleVisibility'])->name('testimonials.toggleVisibility');
        
        Route::post('/facilities/bulk-action', [FacilityController::class, 'bulkAction'])->name('facilities.bulkAction');
        Route::resource('facilities', FacilityController::class);
    });

    // Keuangan / Pendapatan
    Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');

    // Visitors
    Route::get('/visitors', [\App\Http\Controllers\Admin\VisitorAdminController::class, 'index'])->name('visitors.index');
    Route::post('/visitors/clear', [\App\Http\Controllers\Admin\VisitorAdminController::class, 'clear'])->name('visitors.clear');
    Route::post('/visitors/sync', [\App\Http\Controllers\Admin\VisitorAdminController::class, 'syncCounter'])->name('visitors.sync');

    // Registrations
    Route::get('/registrations/check-new', [RegistrationAdminController::class, 'checkNew'])->name('registrations.checkNew');
    Route::get('/registrations', [RegistrationAdminController::class, 'index'])->name('registrations.index');
    Route::get('/registrations/{id}', [RegistrationAdminController::class, 'show'])->name('registrations.show');
    Route::patch('/registrations/{id}/status', [RegistrationAdminController::class, 'updateStatus'])->name('registrations.status');
    Route::post('/registrations/archive-all-finished', [RegistrationAdminController::class, 'archiveAllFinished'])->name('registrations.archiveAllFinished');
    Route::post('/registrations/{id}/archive', [RegistrationAdminController::class, 'archive'])->name('registrations.archive');
    Route::post('/registrations/{id}/unarchive', [RegistrationAdminController::class, 'unarchive'])->name('registrations.unarchive');
    Route::post('/registrations/bulk-delete', [RegistrationAdminController::class, 'bulkDestroy'])->name('registrations.bulkDestroy');
    Route::post('/registrations/bulk-status', [RegistrationAdminController::class, 'bulkUpdateStatus'])->name('registrations.bulkStatus');
    Route::delete('/registrations/{id}', [RegistrationAdminController::class, 'destroy'])->name('registrations.destroy');

    Route::middleware(['auth', 'role:admin'])->get('/admin', function () {
        return "Halaman Admin - Akses Penuh";
    })->name('admin.page');

    Route::middleware(['auth', 'role:manager'])->get('/manager', function () {
        return "Halaman Manager - Akses Laporan & Persetujuan";
    })->name('manager.page');

    Route::middleware(['auth', 'role:finance'])->get('/finance-dashboard', function () {
        return "Halaman Keuangan - Akses Laporan Keuangan & Transaksi";
    })->name('finance.page');

    Route::middleware(['auth', 'role:cs'])->get('/cs', function () {
        return "Halaman Customer Service - Akses Pesan & Pendaftaran";
    })->name('cs.page');

    Route::middleware(['auth', 'role:user'])->get('/user', function () {
        return "Halaman User - Akses Profil & Riwayat Pendaftaran";
    })->name('user.page');

    // Pemicu Storage Link untuk Hosting
    Route::get('/run-link', function () {
        $target = storage_path('app/public');
        $shortcut = public_path('storage');
        if (file_exists($shortcut)) {
            return "Folder 'public/storage' sudah ada.";
        }
        symlink($target, $shortcut);
        return "Berhasil membuat link storage! Sekarang gambar seharusnya muncul.";
    });

    // Chat Assistant
    Route::get('/chat-assistant', [ChatAssistantController::class, 'index'])->name('chat.index');

    // Audit Logs (Monitoring Login)
    Route::get('/audit-logs', [AdminController::class, 'auditLogs'])->name('audit.logs');
    Route::post('/audit-logs/clear', [AdminController::class, 'clearAuditLogs'])->name('audit.clear');
    Route::post('/audit-logs/unlock', [AdminController::class, 'unlockAccount'])->name('audit.unlock');
    Route::post('/audit-logs/reset-password', [AdminController::class, 'resetAccountPassword'])->name('audit.resetPassword');
});
