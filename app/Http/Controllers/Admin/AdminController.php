<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirebaseService;

class AdminController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function index()
    {
        $stats = $this->getDashboardStats();

        $packagesList = \Illuminate\Support\Facades\Cache::remember('dashboard_packages', 60, function () {
            try {
                return collect($this->firebase->getValue('packages') ?? [])->take(4);
            } catch (\Exception $e) {
                \Log::warning('Firebase packages fetch error in dashboard index (ignored): ' . $e->getMessage());
                return collect([]);
            }
        });

        $recentRegistrations = \Illuminate\Support\Facades\Cache::remember('dashboard_recent_registrations', 30, function () {
            try {
                $regs = $this->firebase->getValue('registrations') ?? [];
                return collect($regs)->map(function ($item, $key) {
                    if (is_array($item))
                        $item['id'] = $key;
                    return $item;
                })->sortByDesc('created_at')->take(3);
            } catch (\Exception $e) {
                \Log::warning('Firebase registrations fetch error in dashboard index (ignored): ' . $e->getMessage());
                return collect([]);
            }
        });

        $recentVisitors = \Illuminate\Support\Facades\Cache::remember('dashboard_recent_visitors', 30, function () {
            try {
                $log = $this->firebase->getValue('visitor_log') ?? [];
                return collect($log)->sortByDesc('timestamp')->take(5);
            } catch (\Exception $e) {
                \Log::warning('Firebase visitor log fetch error in dashboard index (ignored): ' . $e->getMessage());
                return collect([]);
            }
        });

        return view('admin.dashboard', compact('stats', 'packagesList', 'recentRegistrations', 'recentVisitors'));
    }

    /**
     * Get stats as JSON for Real-time Dashboard Updates
     */
    public function getStatsApi()
    {
        // Lepaskan session lock agar tidak menghalangi request lain (seperti proses SIMPAN)
        if (session_id())
            session_write_close();

        return response()->json($this->getDashboardStats(true));
    }

    /**
     * Helper to get/calculate dashboard stats with short-lived cache
     */
    private function getDashboardStats($bypassCache = false)
    {
        $fetchStats = function () {
            try {
                $allPackages = $this->firebase->getValue('packages') ?? [];
                $allTestimonials = $this->firebase->getValue('testimonials') ?? [];
                $allRegs = collect($this->firebase->getValue('registrations') ?? []);
                $allUsers = $this->firebase->getValue('users') ?? [];

                // Tambahkan deteksi data kosong agar tidak error
                if (empty($allPackages) && empty($allRegs)) {
                    return ['packages_count' => 0, 'testimonials_count' => 0, 'users_count' => 0, 'registrations_count' => 0, 'page_views' => 0, 'optimization_score' => 0];
                }

                $activeRegsCount = $allRegs->filter(function ($reg) {
                    return is_array($reg) && !($reg['is_archived'] ?? false);
                })->count();

                return [
                    'packages_count' => count($allPackages),
                    'testimonials_count' => count($allTestimonials),
                    'users_count' => count($allUsers),
                    'registrations_count' => $activeRegsCount,
                    'page_views' => $this->firebase->getValue('metrics/page_views') ?? 0,
                    'optimization_score' => 98,
                ];
            } catch (\Exception $e) {
                \Log::error('Firebase connection error inside getDashboardStats: ' . $e->getMessage());
                return [
                    'packages_count' => 0,
                    'testimonials_count' => 0,
                    'users_count' => 0,
                    'registrations_count' => 0,
                    'page_views' => 0,
                    'optimization_score' => 98,
                ];
            }
        };

        if ($bypassCache) {
            $stats = $fetchStats();
            \Illuminate\Support\Facades\Cache::put('dashboard_main_stats_v3', $stats, 5);
            return $stats;
        }

        // Cache dikurangi menjadi 5 detik saja untuk efek "realtime" pada page load biasa
        return \Illuminate\Support\Facades\Cache::remember('dashboard_main_stats_v3', 5, $fetchStats);
    }




    public function galleryIndex()
    {
        $settings = $this->firebase->getValue('settings') ?? [];
        $galleryNode = $this->firebase->getValue('gallery') ?? [];
        $galleryVisibility = $this->firebase->getValue('gallery_visibility') ?? [];
        
        $localPath = public_path('Gambar perjalanan/Gambar-video');
        $allMedia = [];
        $photoCount = 0;
        $videoCount = 0;

        // 1. Ambil dari Koleksi Dinamis (UNLIMITED)
        foreach($galleryNode as $id => $item) {
            $p = $item['url'] ?? '';
            if(!empty($p)) {
                if(str_starts_with($p, 'http')) {
                    $url = $p;
                } else {
                    $url = asset(str_starts_with($p, '/') ? $p : '/' . $p);
                }
                
                $type = preg_match('/\.(mp4|webm|ogg)$/i', $url) ? 'video' : 'foto';
                $isPublished = $item['is_published'] ?? true;

                $allMedia[] = [
                    'id' => $id, // ID Unik dari Firebase
                    'url' => $url, 
                    'type' => $type, 
                    'source' => 'dynamic',
                    'is_published' => $isPublished
                ];
                if ($isPublished) {
                    $type == 'foto' ? $photoCount++ : $videoCount++;
                }
            }
        }

        // 2. Fallback: Cari di settings lama
        for($i = 1; $i <= 50; $i++) {
            $p = $settings['gallery_img_' . $i] ?? '';
            if(!empty($p)) {
                $url = asset(str_starts_with($p, '/') ? $p : '/' . $p);
                $type = preg_match('/\.(mp4|webm|ogg)$/i', $url) ? 'video' : 'foto';
                $legacyId = 'gallery_img_' . $i;
                $isPublished = $galleryVisibility[$legacyId] ?? true;
                
                $allMedia[] = [
                    'id' => $legacyId, 
                    'url' => $url, 
                    'type' => $type, 
                    'source' => 'legacy',
                    'is_published' => $isPublished
                ];
                if($isPublished) {
                    $type == 'foto' ? $photoCount++ : $videoCount++;
                }
            }
        }

        // 3. Dari Folder Lokal
        if (file_exists($localPath)) {
            $files = scandir($localPath);
            foreach ($files as $file) {
                if (preg_match('/\.(jpg|jpeg|png|webp|mp4|webm)$/i', $file)) {
                    $url = asset('Gambar%20perjalanan/Gambar-video/' . rawurlencode($file));
                    $type = preg_match('/\.(mp4|webm)$/i', $file) ? 'video' : 'foto';
                    $allMedia[] = [
                        'id' => $file,
                        'url' => $url, 
                        'type' => $type, 
                        'is_local' => true, 
                        'filename' => $file,
                        'is_published' => true
                    ];
                    $type == 'foto' ? $photoCount++ : $videoCount++;
                }
            }
        }

        // Urutkan: Yang baru (dynamic) di atas
        $allMedia = collect($allMedia)->sortBy(function($item) {
            return isset($item['source']) && $item['source'] == 'dynamic' ? 0 : 1;
        })->values()->all();

        return view('admin.gallery.index', compact('settings', 'allMedia', 'photoCount', 'videoCount'));
    }

    public function deleteLocalGallery(Request $request)
    {
        $filename = $request->filename;
        if(!$filename) return back()->with('error', 'Nama file tidak valid');

        $path = public_path('Gambar perjalanan/Gambar-video/' . $filename);

        if (file_exists($path)) {
            unlink($path);
            return back()->with('success', 'File lokal berhasil dihapus');
        }

        return back()->with('error', 'File tidak ditemukan di folder lokal');
    }

    public function bulkDeleteGallery(Request $request)
    {
        $items = json_decode($request->items, true);
        if(empty($items)) return back()->with('error', 'Tidak ada item yang dipilih');

        $settings = $this->firebase->getValue('settings') ?? [];
        $deletedCount = 0;

        foreach($items as $item) {
            if(isset($item['is_local']) && $item['is_local']) {
                // Hapus File Lokal
                $path = public_path('Gambar perjalanan/Gambar-video/' . $item['id']);
                if(file_exists($path)) {
                    @unlink($path);
                    $deletedCount++;
                }
            } else {
                $id = $item['id'];
                
                // Jika ini dari slot lama (gallery_img_X)
                if(str_starts_with($id, 'gallery_img_')) {
                    if(!empty($settings[$id])) {
                        $oldPath = public_path(ltrim($settings[$id], '/'));
                        if(file_exists($oldPath)) @unlink($oldPath);
                        unset($settings[$id]);
                        $deletedCount++;
                    }
                } else {
                    // Jika ini dari koleksi dinamis (UNLIMITED)
                    $mediaItem = $this->firebase->getValue('gallery/' . $id);
                    if($mediaItem) {
                        $oldPath = public_path(ltrim($mediaItem['url'], '/'));
                        if(file_exists($oldPath)) @unlink($oldPath);
                        $this->firebase->deleteValue('gallery/' . $id);
                        $deletedCount++;
                    }
                }
            }
        }

        // Simpan perubahan settings lama jika ada yang dihapus
        $this->firebase->setValue('settings', $settings);
        \Illuminate\Support\Facades\Cache::forget('site_settings');
        \Illuminate\Support\Facades\Cache::forget('site_gallery');
        \Illuminate\Support\Facades\Cache::forget('site_gallery_visibility');
        \Illuminate\Support\Facades\Cache::forget('site_gallery_settings');
        \Illuminate\Support\Facades\Cache::forget('dashboard_main_stats_v3');

        return back()->with('success', $deletedCount . ' media berhasil dihapus secara massal');
    }

    public function uploadGallery(Request $request)
    {
        // Naikkan limit
        ini_set('upload_max_filesize', '100M');
        ini_set('post_max_size', '100M');
        set_time_limit(300);

        // Ambil file dari request (hanya satu yang diupload via modal)
        $files = $request->allFiles();
        if (empty($files)) {
            return back()->with('error', 'Tidak ada file yang dipilih');
        }

        // Key-nya biasanya gallery_img_X
        $key = array_key_first($files);
        $file = $files[$key];

        if (!$file->isValid()) {
            return back()->with('error', 'File tidak valid atau rusak');
        }

        $mime = $file->getMimeType();
        $isVideo = str_starts_with($mime, 'video/');
        $folder = $isVideo ? 'uploads/videos' : 'uploads/images';

        // Generate nama aman
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $uploadPath = public_path($folder);

        if (!file_exists($uploadPath)) mkdir($uploadPath, 0755, true);

        try {
            $file->move($uploadPath, $filename);
            $filePath = '/' . $folder . '/' . $filename;

            // SIMPAN TANPA BATAS (Push ke node 'gallery')
            $this->firebase->push('gallery', [
                'url' => $filePath,
                'created_at' => now()->toDateTimeString(),
                'type' => $isVideo ? 'video' : 'foto',
                'is_published' => true // Default langsung tampil
            ]);

            \Illuminate\Support\Facades\Cache::forget('site_settings');
            \Illuminate\Support\Facades\Cache::forget('site_gallery');
            \Illuminate\Support\Facades\Cache::forget('site_gallery_visibility');
            \Illuminate\Support\Facades\Cache::forget('site_gallery_settings');
            \Illuminate\Support\Facades\Cache::forget('dashboard_main_stats_v3');

            return back()->with('success', 'Media berhasil diunggah ke Galeri Tanpa Batas!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal upload: ' . $e->getMessage());
        }
    }

    public function toggleVisibilityGallery($id)
    {
        $newStatus = true;
        if (str_starts_with($id, 'gallery_img_')) {
            // Untuk Legacy
            $visibility = $this->firebase->getValue('gallery_visibility') ?? [];
            $newStatus = !($visibility[$id] ?? true);
            $visibility[$id] = $newStatus;
            $this->firebase->setValue('gallery_visibility', $visibility);
        } else {
            // Untuk Dynamic
            $item = $this->firebase->getValue('gallery/' . $id);
            if (!$item) return response()->json(['success' => false, 'message' => 'Media tidak ditemukan'], 404);
            $newStatus = !($item['is_published'] ?? true);
            $this->firebase->updateValue('gallery/' . $id, ['is_published' => $newStatus]);
        }
        
        \Illuminate\Support\Facades\Cache::forget('site_settings');
        \Illuminate\Support\Facades\Cache::forget('site_gallery');
        \Illuminate\Support\Facades\Cache::forget('site_gallery_visibility');
        \Illuminate\Support\Facades\Cache::forget('site_gallery_settings');
        
        return response()->json(['success' => true, 'new_status' => $newStatus]);
    }

    public function bulkVisibilityGallery(Request $request)
    {
        $ids = $request->ids;
        $status = $request->status == 'show' ? true : false;
        
        if (empty($ids)) return back()->with('error', 'Tidak ada item dipilih');

        foreach ($ids as $id) {
            if (str_starts_with($id, 'gallery_img_')) {
                $visibility = $this->firebase->getValue('gallery_visibility') ?? [];
                $visibility[$id] = $status;
                $this->firebase->setValue('gallery_visibility', $visibility);
            } else {
                $this->firebase->updateValue('gallery/' . $id, ['is_published' => $status]);
            }
        }

        \Illuminate\Support\Facades\Cache::forget('site_settings');
        \Illuminate\Support\Facades\Cache::forget('site_gallery');
        \Illuminate\Support\Facades\Cache::forget('site_gallery_visibility');
        \Illuminate\Support\Facades\Cache::forget('site_gallery_settings');

        return back()->with('success', 'Status visibilitas media berhasil diperbarui');
    }

    public function deleteLegacyGallery(Request $request)
    {
        $data = $request->all();
        $settings = $this->firebase->getValue('settings') ?? [];
        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'delete_') && $value == '1') {
                $targetKey = substr($key, 7);
                if (!empty($settings[$targetKey])) {
                    $oldPath = public_path(ltrim($settings[$targetKey], '/'));
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }
                unset($settings[$targetKey]);
            }
        }
        $this->firebase->setValue('settings', $settings);
        \Illuminate\Support\Facades\Cache::forget('site_settings');
        \Illuminate\Support\Facades\Cache::forget('site_gallery_settings');
        return back()->with('success', 'Media lama berhasil dihapus.');
    }

    public function guide()
    {
        return view('admin.guide');
    }

    public function clearCache()
    {
        \Illuminate\Support\Facades\Cache::flush();
        return back()->with('success', 'Semua cache website berhasil dihapus! Data terbaru akan dimuat ulang.');
    }

    public function auditLogs()
    {
        // Ambil data audit logs dari Firebase
        $logs = $this->firebase->getValue('account_audit_logs') ?? [];

        // ============================================================
        // [AUTO-CLEANUP LOGS] Hapus otomatis log jika akunnya sudah tidak ada di RTDB (users)
        // ============================================================
        $firebaseUsers = $this->firebase->getValue('users') ?? [];
        $activeEmails = [];
        foreach ($firebaseUsers as $u) {
            if (!empty($u['email'])) {
                $activeEmails[] = strtolower(trim($u['email']));
            }
        }

        $filteredLogs = [];
        $deletedLogKeys = [];
        foreach ($logs as $key => $log) {
            $logEmail = strtolower(trim($log['email'] ?? ''));
            $logAccount = strtolower(trim($log['account'] ?? ''));

            $exists = false;
            foreach ($activeEmails as $email) {
                if ($logEmail === $email || $logAccount === $email || str_contains($logEmail, $email)) {
                    $exists = true;
                    break;
                }
            }

            // Pertahankan log jika akun masih aktif atau email kosong (system/anonim)
            if ($exists || empty($logEmail)) {
                $filteredLogs[$key] = $log;
            } else {
                $deletedLogKeys[] = $key;
            }
        }

        // Hapus log dari Firebase RTDB secara otomatis jika akunnya sudah terhapus
        if (!empty($deletedLogKeys)) {
            foreach ($deletedLogKeys as $key) {
                try {
                    $this->firebase->deleteValue("account_audit_logs/{$key}");
                } catch (\Exception $e) {
                    \Log::error("Gagal menghapus log kadaluarsa {$key}: " . $e->getMessage());
                }
            }
            $logs = $filteredLogs;
        }

        // Balikkan urutan agar data terbaru muncul di atas
        $auditLogs = collect($logs)->sortByDesc('timestamp');

        // --- DATA GRAFIK REAL-TIME (HARI INI) ---
        $now = now();
        $hourlyLabels = [];
        $successCounts = [];
        $failedCounts = [];

        $currentHour = (int) $now->format('H');
        for ($i = 0; $i <= $currentHour; $i++) {
            $hourLabel = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
            $hourlyLabels[] = $hourLabel;

            $success = 0;
            $failed = 0;
            foreach ($logs as $log) {
                if (!empty($log['timestamp'])) {
                    try {
                        $ts = \Carbon\Carbon::parse($log['timestamp']);
                        // Pastikan pengecekan hari ini dan jam yang sama akurat
                        if ($ts->isToday() && (int) $ts->format('H') == $i) {
                            $status = strtoupper($log['status'] ?? '');
                            if (str_contains($status, 'SUCCESS'))
                                $success++;
                            if (str_contains($status, 'FAILED') || str_contains($status, 'LOCKED') || str_contains($status, 'BANNED'))
                                $failed++;
                        }
                    } catch (\Exception $e) {
                    }
                }
            }
            $successCounts[] = $success;
            $failedCounts[] = $failed;
        }

        $chartData = [
            'labels' => $hourlyLabels,
            'success' => $successCounts,
            'failed' => $failedCounts,
        ];

        // Ambil akun yang sedang terblokir (baik permanen maupun sementara)
        $bannedAccounts = \Illuminate\Support\Facades\DB::table('users')
            ->where('permanently_banned', true)
            ->orWhere(function ($query) {
                $query->whereNotNull('locked_until')
                    ->where('locked_until', '>', now());
            })
            ->get();

        return view('admin.audit_logs', compact('auditLogs', 'bannedAccounts', 'chartData'));
    }

    public function clearAuditLogs()
    {
        // Hapus seluruh node audit logs di Firebase
        $this->firebase->setValue('account_audit_logs', null);
        return back()->with('success', 'Seluruh histori monitoring login berhasil dihapus!');
    }

    public function unlockAccount(Request $request)
    {
        $email = strtolower(trim($request->email));
        if (!$email)
            return back()->with('error', 'Email tidak valid.');

        // 1. Reset di SQLite (Database Lokal) - Paksa Lowercase
        \Illuminate\Support\Facades\DB::table('users')
            ->whereRaw('LOWER(email) = ?', [$email])
            ->update([
                'login_attempts' => 0,
                'locked_until' => null,
                'permanently_banned' => false,
                'banned_reason' => null
            ]);

        // 2. Bersihkan di Firebase (Dua Tempat + Histori Audit)
        try {
            $userKey = str_replace(['@', '.'], '_', $email);
            $this->firebase->updateValue("users/{$userKey}", [
                'login_attempts' => 0,
                'locked_until' => null,
                'permanently_banned' => true
            ]);

            // B. Hapus total dari node account_penalties
            $penaltyKey = str_replace(['.', '#', '$', '[', ']', '/'], '_', $email);
            $this->firebase->setValue("account_penalties/{$penaltyKey}", null);

            // C. Hapus juga jika ada format key user_key di penalties
            $this->firebase->setValue("account_penalties/{$userKey}", null);

            // D. HAPUS HISTORI AUDIT LOG (Agar hilang dari tabel)
            $allLogs = $this->firebase->getValue('account_audit_logs') ?? [];
            foreach ($allLogs as $logKey => $logData) {
                $logEmail = strtolower($logData['email'] ?? '');
                if ($logEmail === $email) {
                    $this->firebase->setValue("account_audit_logs/{$logKey}", null);
                }
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal total sync unlock ke Firebase: ' . $e->getMessage());
        }

        return back()->with('success', "🔓 Akses Pulih! Seluruh hitungan pinalti untuk {$email} telah di-reset ke Nol.");
    }

    public function resetAccountPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'new_password' => 'required|min:6'
        ]);

        // 1. Update password di SQLite
        $newHash = \Illuminate\Support\Facades\Hash::make($request->new_password);
        \Illuminate\Support\Facades\DB::table('users')
            ->where('email', $request->email)
            ->update([
                'password' => $newHash,
                'login_attempts' => 0, // Sekalian reset hitungan salah
                'locked_until' => null,
                'permanently_banned' => false
            ]);

        // 2. Update password di Firebase (PENTING AGAR TIDAK BUG)
        try {
            $key = str_replace(['@', '.'], '_', $request->email);
            $this->firebase->updateValue("users/{$key}", [
                'password' => $newHash,
                'login_attempts' => 0,
                'locked_until' => null,
                'permanently_banned' => false
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal sync reset password ke Firebase: ' . $e->getMessage());
        }

        return back()->with('success', "🔑 Password baru untuk {$request->email} telah berhasil disimpan!");
    }

    /**
     * Tampilkan formulir pengaturan website.
     */
    public function settingsForm()
    {
        $settings = $this->firebase->getValue('settings') ?? [];
        return view('admin.settings', compact('settings'));
    }

    /**
     * Simpan pembaruan pengaturan website.
     */
    public function updateSettingsForm(Request $request)
    {
        $settings = $this->firebase->getValue('settings') ?? [];
        
        // Data input teks/non-file
        $data = $request->except([
            '_token', 'site_logo', 'og_image', 'footer_logo', 'about_image', 
            'about_video', 'hero_video_url', 'itin_aside_img', 'itin_aside_video',
            'hero_bg_1', 'hero_bg_2', 'hero_bg_3', 'hero_bg_4'
        ]);
        
        // List checkbox hapus gambar/video
        $deletes = [
            'delete_site_logo' => 'site_logo',
            'delete_og_image' => 'og_image',
            'delete_footer_logo' => 'footer_logo',
            'delete_about_image' => 'about_image',
            'delete_about_video' => 'about_video',
            'delete_hero_video_url' => 'hero_video_url',
            'delete_itin_aside_img' => 'itin_aside_img',
            'delete_itin_aside_video' => 'itin_aside_video',
        ];

        foreach ($deletes as $chk => $field) {
            if ($request->has($chk) && $request->input($chk) == '1') {
                if (!empty($settings[$field])) {
                    $oldPath = public_path(ltrim($settings[$field], '/'));
                    if (file_exists($oldPath)) @unlink($oldPath);
                }
                $settings[$field] = null;
            }
        }

        // List upload file gambar utama
        $imageFields = ['site_logo', 'og_image', 'footer_logo', 'about_image', 'itin_aside_img'];
        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                if ($file->isValid()) {
                    if (!empty($settings[$field])) {
                        $oldPath = public_path(ltrim($settings[$field], '/'));
                        if (file_exists($oldPath)) @unlink($oldPath);
                    }
                    $filename = time() . '_' . $field . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/images'), $filename);
                    $settings[$field] = '/uploads/images/' . $filename;
                }
            }
        }

        // List upload file video utama (about_video, hero_video_url, itin_aside_video)
        $videoFields = ['about_video', 'hero_video_url', 'itin_aside_video'];
        foreach ($videoFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                if ($file->isValid()) {
                    if (!empty($settings[$field])) {
                        $oldPath = public_path(ltrim($settings[$field], '/'));
                        if (file_exists($oldPath)) @unlink($oldPath);
                    }
                    $filename = time() . '_' . $field . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/videos'), $filename);
                    $settings[$field] = '/uploads/videos/' . $filename;
                }
            }
        }

        // Handle upload hero background slideshow (hero_bg_1 s/d hero_bg_4)
        for ($i = 1; $i <= 4; $i++) {
            $bgField = 'hero_bg_' . $i;
            $delField = 'delete_hero_bg_' . $i;
            if ($request->has($delField) && $request->input($delField) == '1') {
                if (!empty($settings[$bgField])) {
                    $oldPath = public_path(ltrim($settings[$bgField], '/'));
                    if (file_exists($oldPath)) @unlink($oldPath);
                }
                $settings[$bgField] = null;
            }
            if ($request->hasFile($bgField)) {
                $file = $request->file($bgField);
                if ($file->isValid()) {
                    if (!empty($settings[$bgField])) {
                        $oldPath = public_path(ltrim($settings[$bgField], '/'));
                        if (file_exists($oldPath)) @unlink($oldPath);
                    }
                    $filename = time() . '_hero_bg_' . $i . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/images'), $filename);
                    $settings[$bgField] = '/uploads/images/' . $filename;
                }
            }
        }

        // Gabungkan sisa data teks
        foreach ($data as $key => $val) {
            if (str_starts_with($key, 'delete_')) continue;
            $settings[$key] = $val;
        }

        // Sinkronisasi otomatis keunggulan ketiga (about_item3_desc = about_item3_text)
        if (isset($settings['about_item3_text'])) {
            $settings['about_item3_desc'] = $settings['about_item3_text'];
        }

        // Sinkronisasi social media footer: site_instagram ↔ social_ig, site_facebook ↔ social_fb
        if (isset($settings['site_instagram'])) {
            $settings['social_ig'] = $settings['site_instagram'];
        } elseif (isset($settings['social_ig'])) {
            $settings['site_instagram'] = $settings['social_ig'];
        }
        if (isset($settings['site_facebook'])) {
            $settings['social_fb'] = $settings['site_facebook'];
        } elseif (isset($settings['social_fb'])) {
            $settings['site_facebook'] = $settings['social_fb'];
        }

        // Simpan ke Firebase
        $this->firebase->setValue('settings', $settings);

        // Hapus cache Laravel
        \Illuminate\Support\Facades\Cache::forget('site_settings');
        \Illuminate\Support\Facades\Cache::forget('site_gallery_settings');
        \Illuminate\Support\Facades\Cache::forget('site_global_data');

        return back()->with('success', '⚙️ Pengaturan website berhasil disimpan dan cache sistem dibersihkan!');
    }
}
