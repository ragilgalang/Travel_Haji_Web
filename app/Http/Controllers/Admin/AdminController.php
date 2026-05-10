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
        // IMPLEMENTASI POIN 3: Caching
        // Kita simpan seluruh hasil perhitungan statistik selama 10 menit
        $stats = \Illuminate\Support\Facades\Cache::remember('dashboard_main_stats_v2', 10 * 60, function() {
            // IMPLEMENTASI POIN 1: Server-Side Aggregation
            // Ambil data dari Firebase hanya jika cache kosong
            $allPackages = $this->firebase->getValue('packages') ?? [];
            $allTestimonials = $this->firebase->getValue('testimonials') ?? [];
            $allRegs = $this->firebase->getValue('registrations') ?? [];
            $allUsers = $this->firebase->getValue('users') ?? [];
            
            // Hitung hanya yang sudah diarsipkan
            $archivedRegsCount = collect($allRegs)->where('is_archived', true)->count();

            return [
                'packages_count' => count($allPackages),
                'testimonials_count' => count($allTestimonials),
                'users_count' => count($allUsers),
                'registrations_count' => $archivedRegsCount,
                'page_views' => $this->firebase->getValue('metrics/page_views') ?? 0,
                'optimization_score' => 98, // Nilai statis untuk indikator performa
            ];
        });
        
        $packagesList = \Illuminate\Support\Facades\Cache::remember('dashboard_packages', 5 * 60, function() {
            return collect($this->firebase->getValue('packages') ?? [])->take(4);
        });

        $recentRegistrations = \Illuminate\Support\Facades\Cache::remember('dashboard_recent_registrations', 1 * 60, function() {
            $regs = $this->firebase->getValue('registrations') ?? [];
            return collect($regs)->map(function($item, $key) {
                if (is_array($item)) $item['id'] = $key;
                return $item;
            })->sortByDesc('created_at')->take(3);
        });

        $recentVisitors = \Illuminate\Support\Facades\Cache::remember('dashboard_recent_visitors', 1 * 60, function() {
            $log = $this->firebase->getValue('visitor_log') ?? [];
            return collect($log)->sortByDesc('timestamp')->take(5);
        });

        return view('admin.dashboard', compact('stats', 'packagesList', 'recentRegistrations', 'recentVisitors'));
    }

    public function settings()
    {
        $settings = \Illuminate\Support\Facades\Cache::remember('site_settings', 60*24, function() {
            return $this->firebase->getValue('settings') ?? [];
        });

        $testimonials = \Illuminate\Support\Facades\Cache::remember('firebase_testimonials', 60*24, function() {
            return collect($this->firebase->getValue('testimonials') ?? []);
        });

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

        // Handle all file uploads automatically
        foreach ($request->allFiles() as $key => $file) {
            if ($file->isValid()) {
                $mime = $file->getMimeType();
                $isVideo = str_starts_with($mime, 'video/');
                $folder = $isVideo ? 'uploads/videos' : 'uploads/images';

                // Validasi ukuran: Foto max 5MB, Video max 50MB
                $maxBytes = $isVideo ? (50 * 1024 * 1024) : (5 * 1024 * 1024);
                if ($file->getSize() > $maxBytes) {
                    continue; // Lewati file terlalu besar
                }
                
                // Gunakan path yang lebih aman untuk shared hosting
                $uploadPath = public_path($folder);
                if (!is_dir($uploadPath)) {
                    $uploadPath = base_path('public/' . $folder);
                }

                // Ensure folder exists
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                $filename = time() . '_' . $key . '_' . preg_replace('/[^A-Za-z0-9.\-]/', '', $file->getClientOriginalName());
                
                try {
                    $file->move($uploadPath, $filename);
                    $settings[$key] = asset($folder . '/' . $filename);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Upload failed: " . $e->getMessage());
                    continue; 
                }
            }
        }

        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'delete_') && $value == '1') {
                $targetKey = substr($key, 7); // remove 'delete_'
                unset($settings[$targetKey]);
                continue;
            }
            // Jangan simpan object file ke database
            if (!($value instanceof \Illuminate\Http\UploadedFile)) {
                $settings[$key] = $value;
            }
        }
        
        // Simpan ke Firebase DALAM 1 KALI REQUEST SAJA (Turbo Update 🚀)
        $this->firebase->setValue('settings', $settings);

        // Hapus Cache agar data terbaru langsung muncul
        \Illuminate\Support\Facades\Cache::forget('site_settings');
        \Illuminate\Support\Facades\Cache::forget('site_global_data');
        \Illuminate\Support\Facades\Cache::put('site_settings', $settings, 60*24);
        
        // Return JSON for AJAX, redirect for normal form
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['success' => true, 'message' => 'Settings berhasil disimpan!']);
        }

        return back()->with('success', 'Settings updated successfully to Firebase!');
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

        // Ambil akun yang terkena banned permanen
        $bannedAccounts = \Illuminate\Support\Facades\DB::table('users')
                            ->where('permanently_banned', true)
                            ->get();

        return view('admin.audit_logs', compact('auditLogs', 'bannedAccounts'));
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

        // 2. Hapus pinalti di Firebase
        try {
            $penaltyKey = str_replace(['.', '#', '$', '[', ']', '/'], '_', $email);
            $this->firebase->setValue("account_penalties/{$penaltyKey}", null);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal hapus penalty Firebase saat unlock: ' . $e->getMessage());
        }

        return back()->with('success', "🔓 Berhasil! Akses untuk akun {$email} telah dipulihkan.");
    }

    public function resetAccountPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'new_password' => 'required|min:6'
        ]);

        // Update password di SQLite
        \Illuminate\Support\Facades\DB::table('users')
            ->where('email', $request->email)
            ->update([
                'password' => \Illuminate\Support\Facades\Hash::make($request->new_password)
            ]);

        return back()->with('success', "🔑 Password baru untuk {$request->email} telah berhasil disimpan!");
    }
}
