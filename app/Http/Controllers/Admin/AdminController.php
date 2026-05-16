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
            return collect($this->firebase->getValue('packages') ?? [])->take(4);
        });

        $recentRegistrations = \Illuminate\Support\Facades\Cache::remember('dashboard_recent_registrations', 30, function () {
            $regs = $this->firebase->getValue('registrations') ?? [];
            return collect($regs)->map(function ($item, $key) {
                if (is_array($item))
                    $item['id'] = $key;
                return $item;
            })->sortByDesc('created_at')->take(3);
        });

        $recentVisitors = \Illuminate\Support\Facades\Cache::remember('dashboard_recent_visitors', 30, function () {
            $log = $this->firebase->getValue('visitor_log') ?? [];
            return collect($log)->sortByDesc('timestamp')->take(5);
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

        return response()->json($this->getDashboardStats());
    }

    /**
     * Helper to get/calculate dashboard stats with short-lived cache
     */
    private function getDashboardStats()
    {
        // Cache dikurangi menjadi 5 detik saja untuk efek "realtime"
        return \Illuminate\Support\Facades\Cache::remember('dashboard_main_stats_v3', 5, function () {
            $allPackages = $this->firebase->getValue('packages') ?? [];
            $allTestimonials = $this->firebase->getValue('testimonials') ?? [];
            $allRegs = collect($this->firebase->getValue('registrations') ?? []);
            $allUsers = $this->firebase->getValue('users') ?? [];

            // Tambahkan deteksi data kosong agar tidak error
            if (empty($allPackages) && empty($allRegs)) {
                return ['packages_count' => 0, 'testimonials_count' => 0, 'users_count' => 0, 'registrations_count' => 0, 'page_views' => 0, 'optimization_score' => 0];
            }

            $activeRegsCount = $allRegs->where('is_archived', false)->count()
                + $allRegs->whereNull('is_archived')->count();

            return [
                'packages_count' => count($allPackages),
                'testimonials_count' => count($allTestimonials),
                'users_count' => count($allUsers),
                'registrations_count' => $activeRegsCount,
                'page_views' => $this->firebase->getValue('metrics/page_views') ?? 0,
                'optimization_score' => 98,
            ];
        });
    }

    public function settings()
    {
        // Admin HARUS selalu melihat data terbaru, jangan pakai cache
        $settings = $this->firebase->getValue('settings') ?? [];

        $testimonials = collect($this->firebase->getValue('testimonials') ?? []);

        return view('admin.settings', compact('settings', 'testimonials'));
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

    public function preview()
    {
        $settings = $this->firebase->getValue('settings') ?? [];
        $packages = collect($this->firebase->getValue('packages') ?? []);
        $testimonials = collect($this->firebase->getValue('testimonials') ?? [])->filter(function ($testi) {
            return isset($testi['is_published']) && $testi['is_published'] == true;
        });
        $facilities = collect($this->firebase->getValue('facilities') ?? []);

        return view('welcome_edit', compact('settings', 'packages', 'testimonials', 'facilities'));
    }

    public function updateSettings(Request $request)
    {
        // Naikkan limit untuk handle video besar
        ini_set('upload_max_filesize', '100M');
        ini_set('post_max_size', '100M');
        ini_set('memory_limit', '256M');
        set_time_limit(300);

        $data = $request->except(['_token', '_method']);

        // Ambil settings lama
        $settings = $this->firebase->getValue('settings') ?? [];

        $logFile = base_path('scratch/save_log.txt');

        file_put_contents(
            $logFile,
            "[" . date('Y-m-d H:i:s') . "] ===== MULAI SAVE SETTINGS =====\n",
            FILE_APPEND
        );

        /* ═══════════════════════════════════════
           LANGKAH 1 — HANDLE DELETE
        ═══════════════════════════════════════ */

        foreach ($data as $key => $value) {

            if (
                str_starts_with($key, 'delete_')
                &&
                $value == '1'
            ) {

                $targetKey = substr($key, 7);

                // Hapus file lama jika ada
                if (!empty($settings[$targetKey])) {

                    $oldPath = public_path(
                        ltrim($settings[$targetKey], '/')
                    );

                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }

                unset($settings[$targetKey]);

                file_put_contents(
                    $logFile,
                    "[" . date('Y-m-d H:i:s') . "] DELETE: {$targetKey}\n",
                    FILE_APPEND
                );
            }
        }

        /* ═══════════════════════════════════════
           LANGKAH 2 — HANDLE FILE UPLOAD
        ═══════════════════════════════════════ */

        foreach ($request->allFiles() as $key => $file) {

            if (!$file->isValid()) {

                file_put_contents(
                    $logFile,
                    "[" . date('Y-m-d H:i:s') . "] FILE INVALID: {$key}\n",
                    FILE_APPEND
                );

                continue;
            }

            $mime = $file->getMimeType();

            $isVideo = str_starts_with($mime, 'video/');

            $folder = $isVideo
                ? 'uploads/videos'
                : 'uploads/images';

            // Maksimal size
            $maxBytes = $isVideo
                ? (100 * 1024 * 1024)
                : (10 * 1024 * 1024);

            if ($file->getSize() > $maxBytes) {

                file_put_contents(
                    $logFile,
                    "[" . date('Y-m-d H:i:s') . "] FILE TERLALU BESAR: {$key}\n",
                    FILE_APPEND
                );

                continue;
            }

            // Hapus file lama jika ada
            if (!empty($settings[$key])) {

                $oldPath = public_path(
                    ltrim($settings[$key], '/')
                );

                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            // Generate nama file aman
            $extension = $file->getClientOriginalExtension();

            if (!$extension) {
                $extension = $isVideo ? 'mp4' : 'jpg';
            }

            $filename =
                time()
                . '_'
                . uniqid()
                . '_'
                . $key
                . '.'
                . $extension;

            $uploadPath = public_path($folder);

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            try {

                $file->move($uploadPath, $filename);

                $settings[$key] =
                    '/'
                    . $folder
                    . '/'
                    . $filename;

                file_put_contents(
                    $logFile,
                    "[" . date('Y-m-d H:i:s') . "] UPLOAD SUCCESS: {$key} => {$filename}\n",
                    FILE_APPEND
                );

            } catch (\Exception $e) {

                file_put_contents(
                    $logFile,
                    "[" . date('Y-m-d H:i:s') . "] ERROR UPLOAD {$key}: "
                    . $e->getMessage()
                    . "\n",
                    FILE_APPEND
                );
            }
        }

        /* ═══════════════════════════════════════
           LANGKAH 3 — HANDLE TEXT INPUT
           (FIX BUG RESET GAMBAR)
        ═══════════════════════════════════════ */

        $uploadedKeys = array_keys($request->allFiles());

        foreach ($data as $key => $value) {

            // Skip delete checkbox
            if (str_starts_with($key, 'delete_')) {
                continue;
            }

            // JANGAN TIMPA upload baru
            if (in_array($key, $uploadedKeys)) {
                continue;
            }

            // Skip UploadedFile
            if ($value instanceof \Illuminate\Http\UploadedFile) {
                continue;
            }

            // Skip array
            if (is_array($value)) {
                continue;
            }

            // VERY IMPORTANT FIX:
            // Jangan timpa media dengan string kosong
            if (
                ($value === '' || $value === null)
                &&
                (
                    str_contains($key, 'img')
                    || str_contains($key, 'image')
                    || str_contains($key, 'video')
                    || str_contains($key, 'logo')
                    || str_contains($key, 'hero')
                    || str_contains($key, 'gallery')
                    || str_contains($key, 'about')
                    || str_contains($key, 'itin')
                )
            ) {
                continue;
            }

            $settings[$key] = $value;
        }

        /* ═══════════════════════════════════════
           LANGKAH 4 — SAVE TO FIREBASE
        ═══════════════════════════════════════ */

        try {

            file_put_contents(
                $logFile,
                "[" . date('Y-m-d H:i:s') . "] SAVE TO FIREBASE...\n",
                FILE_APPEND
            );

            $this->firebase->setValue(
                'settings',
                $settings
            );

            file_put_contents(
                $logFile,
                "[" . date('Y-m-d H:i:s') . "] FIREBASE SUCCESS\n\n",
                FILE_APPEND
            );

            // Clear cache
            \Illuminate\Support\Facades\Cache::forget('site_settings');
            \Illuminate\Support\Facades\Cache::forget('site_packages');

            if (
                $request->ajax()
                ||
                $request->wantsJson()
            ) {

                return response()->json([
                    'success' => true,
                    'message' => 'Settings berhasil disimpan'
                ]);
            }

            return back()->with(
                'success',
                'Settings berhasil disimpan'
            );

        } catch (\Exception $e) {

            file_put_contents(
                $logFile,
                "[" . date('Y-m-d H:i:s') . "] FIREBASE ERROR: "
                . $e->getMessage()
                . "\n\n",
                FILE_APPEND
            );

            if (
                $request->ajax()
                ||
                $request->wantsJson()
            ) {

                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }

            return back()->with(
                'error',
                'Gagal save: ' . $e->getMessage()
            );
        }
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
                'permanently_banned' => false
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
}
