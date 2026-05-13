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
        
        $packagesList = \Illuminate\Support\Facades\Cache::remember('dashboard_packages', 60, function() {
            return collect($this->firebase->getValue('packages') ?? [])->take(4);
        });

        $recentRegistrations = \Illuminate\Support\Facades\Cache::remember('dashboard_recent_registrations', 30, function() {
            $regs = $this->firebase->getValue('registrations') ?? [];
            return collect($regs)->map(function($item, $key) {
                if (is_array($item)) $item['id'] = $key;
                return $item;
            })->sortByDesc('created_at')->take(3);
        });

        $recentVisitors = \Illuminate\Support\Facades\Cache::remember('dashboard_recent_visitors', 30, function() {
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
        if (session_id()) session_write_close();
        
        return response()->json($this->getDashboardStats());
    }

    /**
     * Helper to get/calculate dashboard stats with short-lived cache
     */
    private function getDashboardStats()
    {
        // Cache dikurangi menjadi 5 detik saja untuk efek "realtime"
        return \Illuminate\Support\Facades\Cache::remember('dashboard_main_stats_v3', 5, function() {
            $allPackages     = $this->firebase->getValue('packages') ?? [];
            $allTestimonials = $this->firebase->getValue('testimonials') ?? [];
            $allRegs         = collect($this->firebase->getValue('registrations') ?? []);
            $allUsers        = $this->firebase->getValue('users') ?? [];

            // Tambahkan deteksi data kosong agar tidak error
            if (empty($allPackages) && empty($allRegs)) {
                return ['packages_count' => 0, 'testimonials_count' => 0, 'users_count' => 0, 'registrations_count' => 0, 'page_views' => 0, 'optimization_score' => 0];
            }

            $activeRegsCount   = $allRegs->where('is_archived', false)->count()
                               + $allRegs->whereNull('is_archived')->count();
            
            return [
                'packages_count'      => count($allPackages),
                'testimonials_count'  => count($allTestimonials),
                'users_count'         => count($allUsers),
                'registrations_count' => $activeRegsCount,
                'page_views'          => $this->firebase->getValue('metrics/page_views') ?? 0,
                'optimization_score'  => 98,
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

    public function preview()
    {
        $settings = $this->firebase->getValue('settings') ?? [];
        $packages = collect($this->firebase->getValue('packages') ?? []);
        $testimonials = collect($this->firebase->getValue('testimonials') ?? [])->filter(function($testi) {
            return isset($testi['is_published']) && $testi['is_published'] == true;
        });
        $facilities = collect($this->firebase->getValue('facilities') ?? []);
        
        return view('welcome_edit', compact('settings', 'packages', 'testimonials', 'facilities'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->except(['_token', '_method']);
        $settings = $this->firebase->getValue('settings') ?? [];
        $logFile = base_path('scratch/save_log.txt');
        file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Mulai proses simpan...\n", FILE_APPEND);

        // LANGKAH 1: Proses DELETE dulu (hapus key dari settings)
        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'delete_') && $value == '1') {
                $targetKey = substr($key, 7); // hapus prefix 'delete_'
                unset($settings[$targetKey]);
                file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] HAPUS: $targetKey\n", FILE_APPEND);
            }
        }

        // LANGKAH 2: Proses FILE UPLOAD (tidak akan ditimpa teks)
        foreach ($request->allFiles() as $key => $file) {
            if (!$file->isValid()) continue;

            $mime = $file->getMimeType();
            $isVideo = str_starts_with($mime, 'video/');
            $folder = $isVideo ? 'uploads/videos' : 'uploads/images';

            $maxBytes = $isVideo ? (100 * 1024 * 1024) : (10 * 1024 * 1024);
            if ($file->getSize() > $maxBytes) {
                file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] TOLAK (terlalu besar): $key\n", FILE_APPEND);
                continue;
            }

            $filename = time() . '_' . $key . '_' . preg_replace('/[^A-Za-z0-9._-]/', '', $file->getClientOriginalName());
            $uploadPath = public_path($folder);

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            try {
                $file->move($uploadPath, $filename);
                $settings[$key] = '/' . $folder . '/' . $filename;
                file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] UPLOAD OK: $key -> $filename\n", FILE_APPEND);
            } catch (\Exception $e) {
                file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] ERROR UPLOAD ($key): " . $e->getMessage() . "\n", FILE_APPEND);
            }
        }

        // LANGKAH 3: Simpan data TEKS (skip key delete_* dan skip key yang sudah diisi upload)
        $uploadedKeys = array_keys($request->allFiles());
        foreach ($data as $key => $value) {
            // Skip semua key delete_*
            if (str_starts_with($key, 'delete_')) continue;
            // Skip jika sudah diisi oleh upload file
            if (in_array($key, $uploadedKeys)) continue;
            // Skip nilai null/kosong untuk field yang sudah ada datanya (file input kosong = null)
            if (($value === '' || $value === null) && isset($settings[$key]) && $settings[$key] !== '') continue;
            // Skip objek UploadedFile yang lolos
            if ($value instanceof \Illuminate\Http\UploadedFile) continue;
            // Skip array (biasanya dari multi-file input)
            if (is_array($value)) continue;

            $settings[$key] = $value;
        }

        // LANGKAH 4: Simpan ke Firebase
        set_time_limit(120);
        try {
            $start = microtime(true);
            file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Kirim ke Firebase (" . count($settings) . " fields)...\n", FILE_APPEND);

            $this->firebase->setValue('settings', $settings);

            $duration = round(microtime(true) - $start, 2);
            file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Firebase OK! ({$duration}s)\n\n", FILE_APPEND);

            \Illuminate\Support\Facades\Cache::forget('site_settings');
            \Illuminate\Support\Facades\Cache::forget('site_packages');

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Berhasil disimpan!']);
            }
            return back()->with('success', 'Berhasil disimpan!');

        } catch (\Exception $e) {
            file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] ERROR: " . $e->getMessage() . "\n\n", FILE_APPEND);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Gagal: ' . $e->getMessage());
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
        
        $currentHour = (int)$now->format('H');
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
                        if ($ts->isToday() && (int)$ts->format('H') == $i) {
                            $status = strtoupper($log['status'] ?? '');
                            if (str_contains($status, 'SUCCESS')) $success++;
                            if (str_contains($status, 'FAILED') || str_contains($status, 'LOCKED') || str_contains($status, 'BANNED')) $failed++;
                        }
                    } catch (\Exception $e) {}
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

        // Ambil akun yang terkena banned permanen
        $bannedAccounts = \Illuminate\Support\Facades\DB::table('users')
                            ->where('permanently_banned', true)
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
        $email = $request->email;
        if (!$email) return back()->with('error', 'Email tidak valid.');

        // 1. Reset di SQLite (Database Lokal)
        \Illuminate\Support\Facades\DB::table('users')
            ->where('email', $email)
            ->update([
                'login_attempts' => 0,
                'locked_until' => null,
                'permanently_banned' => false,
                'banned_reason' => null
            ]);

        // 2. Update di Firebase (Sync Status)
        try {
            $key = str_replace(['@', '.'], '_', $email);
            $this->firebase->updateValue("users/{$key}", [
                'login_attempts' => 0,
                'locked_until' => null,
                'permanently_banned' => false
            ]);
            
            // Hapus pinalti di node penalties
            $penaltyKey = str_replace(['.', '#', '$', '[', ']', '/'], '_', $email);
            $this->firebase->setValue("account_penalties/{$penaltyKey}", null);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal sync unlock ke Firebase: ' . $e->getMessage());
        }

        return back()->with('success', "🔓 Berhasil! Akses untuk akun {$email} telah dipulihkan.");
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
