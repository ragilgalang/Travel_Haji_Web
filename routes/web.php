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
Route::get('/galeri', [HomeController::class, 'gallery'])->name('gallery');

// Rute Darurat Pembersih Cache (Jalankan ini di hosting jika ada bug)
Route::get('/clear-system-cache', function() {
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    return "✅ Seluruh cache sistem berhasil dibersihkan! Silakan coba login lagi.";
});

// [KEAMANAN] Rute /fix-passwords dan /masuk-sebagai dihapus karena berbahaya di production.

// Pendaftaran Customer
Route::get('/daftar', [RegistrationController::class, 'show'])->name('register.show');
Route::post('/daftar', [RegistrationController::class, 'store'])->name('register.store');
Route::get('/daftar/sukses', [RegistrationController::class, 'success'])->name('register.success');
Route::get('/tiket/{ref_id}', [RegistrationController::class, 'showTicket'])->name('register.ticket');
Route::post('/daftar/cek-status', [RegistrationController::class, 'checkStatus'])->name('register.checkStatus');

// Ulasan & Kontak
Route::post('/review/submit', [HomeController::class, 'submitReview'])->name('review.submit');
Route::post('/contact/submit', [HomeController::class, 'submitContact'])->name('contact.submit');

// SEO Sitemap
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// [KEAMANAN] Rute /buat-dummy-pendaftar dihapus karena tidak dilindungi autentikasi.

// Log Pembaruan Sistem (Tanpa Login) - Mendukung '-' dan '_'
Route::get('/log-pembaruan', function () {
    $path = storage_path('app/changelog.txt');
    $content = file_exists($path) ? file_get_contents($path) : '';
    return view('changelog', compact('content'));
});
Route::get('/log_pembaruan', function () {
    $path = storage_path('app/changelog.txt');
    $content = file_exists($path) ? file_get_contents($path) : '';
    return view('changelog', compact('content'));
});

Route::post('/log-pembaruan', function (\Illuminate\Http\Request $request) {
    $path = storage_path('app/changelog.txt');
    $action = $request->input('action', 'edit');

    if ($action === 'add') {
        $newEntry = trim($request->input('new_entry') ?? '');
        if ($newEntry) {
            \Carbon\Carbon::setLocale('id');
            $dateHeader = now()->translatedFormat('d F Y');
            $separator = '--------------------';
            $block = $dateHeader . "\n" . $separator . "\n" . $newEntry . "\n";
            $existing = file_exists($path) ? file_get_contents($path) : '';
            file_put_contents($path, $block . ($existing ? "\n" . $existing : ''));
        }
        return redirect('/log-pembaruan')->with('success', 'Pembaruan baru berhasil ditambahkan!');
    }

    // Default: edit full history
    file_put_contents($path, $request->input('content') ?? '');
    return redirect('/log-pembaruan')->with('success', 'Catatan pembaruan berhasil disimpan!');
});
Route::post('/log_pembaruan', function (\Illuminate\Http\Request $request) {
    $path = storage_path('app/changelog.txt');
    $action = $request->input('action', 'edit');

    if ($action === 'add') {
        $newEntry = trim($request->input('new_entry') ?? '');
        if ($newEntry) {
            \Carbon\Carbon::setLocale('id');
            $dateHeader = now()->translatedFormat('d F Y');
            $separator = '--------------------';
            $block = $dateHeader . "\n" . $separator . "\n" . $newEntry . "\n";
            $existing = file_exists($path) ? file_get_contents($path) : '';
            file_put_contents($path, $block . ($existing ? "\n" . $existing : ''));
        }
        return redirect('/log_pembaruan')->with('success', 'Pembaruan baru berhasil ditambahkan!');
    }

    // Default: edit full history
    file_put_contents($path, $request->input('content') ?? '');
    return redirect('/log_pembaruan')->with('success', 'Catatan pembaruan berhasil disimpan!');
});

// ==========================================
// [TANDA: HALAMAN LOGIN / AUTH]
// ==========================================
// Auth
Route::get('/ptumb', [AuthController::class, 'login'])->name('login');
Route::post('/ptumb', [AuthController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Redirect /admin ke /admin/dashboard
Route::get('/admin', function () {
    return redirect()->route('admin.dashboard');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [AdminController::class, 'getStatsApi'])->name('stats.api');
    Route::get('/guide', [AdminController::class, 'guide'])->name('guide');
    Route::post('/clear-cache', [AdminController::class, 'clearCache'])->name('clearCache');
    Route::post('/deploy', [\App\Http\Controllers\Admin\DeployController::class, 'deploy'])->name('deploy');
    Route::post('/github-upload', [\App\Http\Controllers\Admin\DeployController::class, 'uploadToGithub'])->name('github.upload');
    
    // Pengaturan Web (Forms-Only)
    Route::get('/settings', [AdminController::class, 'settingsForm'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettingsForm'])->name('settings.update');
    
    // Gallery Admin Page
    Route::get('/gallery', [AdminController::class, 'galleryIndex'])->name('gallery.index');
    Route::post('/gallery/upload', [AdminController::class, 'uploadGallery'])->name('gallery.upload');
    Route::patch('/gallery/{id}/toggle-visibility', [AdminController::class, 'toggleVisibilityGallery'])->name('gallery.toggleVisibility');
    Route::post('/gallery/bulk-visibility', [AdminController::class, 'bulkVisibilityGallery'])->name('gallery.bulkVisibility');
    Route::post('/gallery/delete-local', [AdminController::class, 'deleteLocalGallery'])->name('gallery.delete-local');
    Route::post('/gallery/bulk-delete', [AdminController::class, 'bulkDeleteGallery'])->name('gallery.bulk-delete');
    Route::post('/gallery/delete-legacy', [AdminController::class, 'deleteLegacyGallery'])->name('gallery.delete-legacy');

    // Profile / Akun
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // CRUDs (Hanya Admin dan Manager)
    // ==========================================
    // [TANDA: PENERAPAN MIDDLEWARE CRUD PADA GRUP ROUTE]
    // ==========================================
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
    Route::get('/registrations/trash', [RegistrationAdminController::class, 'trashIndex'])->name('registrations.trash');
    Route::get('/registrations', [RegistrationAdminController::class, 'index'])->name('registrations.index');
    Route::get('/registrations/{id}', [RegistrationAdminController::class, 'show'])->name('registrations.show');
    Route::patch('/registrations/{id}/status', [RegistrationAdminController::class, 'updateStatus'])->name('registrations.status');
    Route::post('/registrations/archive-all-finished', [RegistrationAdminController::class, 'archiveAllFinished'])->name('registrations.archiveAllFinished');
    Route::post('/registrations/{id}/archive', [RegistrationAdminController::class, 'archive'])->name('registrations.archive');
    Route::post('/registrations/{id}/unarchive', [RegistrationAdminController::class, 'unarchive'])->name('registrations.unarchive');
    Route::post('/registrations/{id}/move-to-trash', [RegistrationAdminController::class, 'moveToTrash'])->name('registrations.moveToTrash');
    Route::post('/registrations/{id}/restore', [RegistrationAdminController::class, 'restore'])->name('registrations.restore');
    Route::delete('/registrations/{id}/force-delete', [RegistrationAdminController::class, 'forceDelete'])->name('registrations.forceDelete');
    Route::post('/registrations/empty-trash', [RegistrationAdminController::class, 'emptyTrash'])->name('registrations.emptyTrash');
    Route::post('/registrations/bulk-delete', [RegistrationAdminController::class, 'bulkDestroy'])->name('registrations.bulkDestroy');
    Route::post('/registrations/bulk-restore', [RegistrationAdminController::class, 'bulkRestore'])->name('registrations.bulkRestore');
    Route::post('/registrations/bulk-status', [RegistrationAdminController::class, 'bulkUpdateStatus'])->name('registrations.bulkStatus');

    // ==========================================
    // [TANDA: PENERAPAN MIDDLEWARE ROLE UNTUK MENENTUKAN OTORISASI]
    // ==========================================
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
