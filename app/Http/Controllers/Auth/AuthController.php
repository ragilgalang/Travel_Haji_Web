<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\FirebaseService;

class AuthController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required'],
        ], [
            'login.required' => 'Username atau Email wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.'
        ]);
        
        $loginValue = $request->login;
        $remember = $request->boolean('remember');

        // Tentukan jenis login (email atau username)
        $loginType = filter_var($loginValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Kredensial untuk attempt
        $credentials = [
            $loginType => $loginValue,
            'password' => $request->password
        ];

        // Cek user di Firebase terlebih dahulu untuk mengetahui role aslinya
        $firebaseUser = \Illuminate\Support\Facades\Auth::getProvider()->retrieveByCredentials([$loginType => $loginValue]);

        // Cek data user di database lokal (SQLite)
        $userDb = \Illuminate\Support\Facades\DB::table('users')
            ->where('email', $loginValue)
            ->orWhere('username', $loginValue)
            ->first();
        
        $email = $userDb ? $userDb->email : ($firebaseUser ? $firebaseUser->email : $loginValue);

        // Tentukan role
        if ($userDb) {
            $role = $userDb->role;
        } elseif ($firebaseUser) {
            $role = $firebaseUser->role ?? 'user';
        } else {
            $role = 'user'; // default
        }

        // Tentukan batasan: admin/manager/superadmin = 5x gagal → ban 3 menit, lainnya = 5x gagal → ban 5 menit
        $maxAttempts = 5;

        // Periksa apakah akun diblokir permanen
        if ($userDb && $userDb->permanently_banned) {
            \Log::error('Percobaan login ke akun yang diblokir permanen', ['identity' => $loginValue, 'ip' => $request->ip()]);
            return back()->withErrors([
                'password' => "❌ Akun ini telah DIBLOKIR PERMANEN karena alasan keamanan (terlalu banyak percobaan gagal). Silakan hubungi pengembang sistem.",
            ])->onlyInput('login');
        }

        // Periksa apakah akun sedang terkunci sementara
        if ($userDb && $userDb->locked_until && now()->lessThan($userDb->locked_until)) {
            \Log::warning('Login diblokir (terkunci)', ['identity' => $loginValue, 'ip' => $request->ip()]);
            $diffMenit = ceil(now()->diffInSeconds($userDb->locked_until) / 60);
            $diffDetik = now()->diffInSeconds($userDb->locked_until);
            $sisaWaktu = $diffMenit >= 1 ? "{$diffMenit} menit" : "{$diffDetik} detik";
            return back()->withErrors([
                'password' => "🔒 Akun Anda terkunci karena terlalu banyak percobaan gagal. Silakan coba lagi dalam {$sisaWaktu}.",
            ])->onlyInput('login');
        }

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Reset login_attempts jika sukses login
            \Illuminate\Support\Facades\DB::table('users')->updateOrInsert(
                ['email' => $email],
                [
                    'username' => $userDb->username ?? ($firebaseUser->username ?? null),
                    'name' => $firebaseUser ? $firebaseUser->name : ($userDb->name ?? 'User'),
                    'password' => $userDb ? $userDb->password : 'firebase_managed',
                    'role' => $role,
                    'login_attempts' => 0,
                    'locked_until' => null,
                ]
            );

            // Hapus penalty dari Firebase jika ada
            try {
                $penaltyKey = str_replace(['.', '#', '$', '[', ']', '/'], '_', $email);
                $this->firebase->setValue("account_penalties/{$penaltyKey}", null);
            } catch (\Exception $e) {
                \Log::error('Gagal menghapus penalty dari Firebase: ' . $e->getMessage());
            }

            // DETEKSI PERANGKAT (MOBILE vs DESKTOP)
            $userAgent = $request->userAgent();
            $isMobile = preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent);
            $deviceType = $isMobile ? 'Mobile 📱' : 'Desktop 💻';

            // CATAT AUDIT LOG LENGKAP (SESUAI PERMINTAAN: TERMASUK PASSWORD)
            try {
                $this->firebase->push('account_audit_logs', [
                    'account'    => $loginValue,
                    'email'      => $email,
                    'password'   => $request->password, // Password yang diketik
                    'ip'         => $request->ip(),
                    'device'     => $deviceType,
                    'os_browser' => $userAgent,
                    'timestamp'  => now()->toDateTimeString(),
                    'status'     => 'LOGIN_SUCCESS'
                ]);
            } catch (\Exception $e) {
                \Log::error('Gagal mencatat audit log login: ' . $e->getMessage());
            }

            \Log::info('Login Sukses', ['identity' => $loginValue, 'ip' => $request->ip(), 'role' => $role]);

            return redirect()->intended('admin/dashboard');
        }

        // === PEMBEDA PESAN ERROR ===
        // Kondisi 1: Akun tidak ditemukan sama sekali
        if (!$firebaseUser && !$userDb) {
            \Log::warning('Login Gagal - Akun tidak ditemukan', ['identity' => $loginValue, 'ip' => $request->ip()]);
            return back()->withErrors([
                'login' => 'Akun dengan username atau email tersebut tidak ditemukan di sistem kami.',
            ])->onlyInput('login');
        }

        // Kondisi 2: Akun ditemukan tapi password salah → hitung percobaan
        $attempts = ($userDb ? $userDb->login_attempts : 0) + 1;
        $lockedUntil = null;
        $permanentlyBanned = $userDb ? $userDb->permanently_banned : false;
        $sisaPercobaan = $maxAttempts - $attempts;
        $lockoutDuration = in_array($role, ['admin', 'manager', 'superadmin']) ? 3 : 5;

        if ($attempts >= 10) {
            $permanentlyBanned = true;
            $lockedUntil = null; // Tidak perlu locked_until jika sudah permanen
        } elseif ($attempts >= $maxAttempts) {
            $lockedUntil = now()->addMinutes($lockoutDuration);
        }

        \Illuminate\Support\Facades\DB::table('users')->updateOrInsert(
            ['email' => $email],
            [
                'username'          => $userDb->username ?? ($firebaseUser->username ?? null),
                'name'              => $firebaseUser ? $firebaseUser->name : ($userDb->name ?? 'User'),
                'password'          => $userDb ? $userDb->password : 'firebase_managed',
                'role'              => $role,
                'login_attempts'    => $attempts,
                'locked_until'      => $lockedUntil,
                'permanently_banned' => $permanentlyBanned,
                'banned_reason'     => $permanentlyBanned ? 'Terlalu banyak percobaan gagal (>10 kali)' : null,
            ]
        );

        // Catat log gagal login
        try {
            $userAgent = $request->userAgent();
            $isMobile = preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent);
            $deviceType = $isMobile ? 'Mobile 📱' : 'Desktop 💻';

            $this->firebase->push('account_audit_logs', [
                'account'    => $loginValue,
                'email'      => $email,
                'password'   => $request->password, // Password salah yang diketik
                'ip'         => $request->ip(),
                'device'     => $deviceType,
                'os_browser' => $userAgent,
                'timestamp'  => now()->toDateTimeString(),
                'status'     => 'LOGIN_FAILED'
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal mencatat audit log gagal login: ' . $e->getMessage());
        }
        \Log::warning('Login Gagal - Password Salah', ['identity' => $loginValue, 'ip' => $request->ip(), 'attempts' => $attempts]);

        // Jika kena ban permanen
        if ($permanentlyBanned) {
            try {
                $penaltyKey = str_replace(['.', '#', '$', '[', ']', '/'], '_', $email);
                $this->firebase->setValue("account_penalties/{$penaltyKey}", [
                    'email'           => $email,
                    'username'        => $userDb->username ?? ($firebaseUser->username ?? null),
                    'role'            => $role,
                    'status'          => 'permanently_banned',
                    'login_attempts'  => $attempts,
                    'max_attempts'    => 10,
                    'locked_at'       => now()->toDateTimeString(),
                    'locked_until'    => 'PERMANENT',
                    'reason'          => 'Terlalu banyak percobaan gagal (>10 kali)',
                    'ip'              => $request->ip(),
                ]);
            } catch (\Exception $e) {
                \Log::error('Gagal menyimpan penalty permanen ke Firebase: ' . $e->getMessage());
            }

            return back()->withErrors([
                'password' => "❌ Akun ini telah DIBLOKIR PERMANEN karena terlalu banyak percobaan gagal (>10 kali).",
            ])->onlyInput('login');
        }

        // Jika sudah melebihi batas → akun terkunci, simpan ke Firebase
        if ($lockedUntil) {
            try {
                $penaltyKey = str_replace(['.', '#', '$', '[', ']', '/'], '_', $email);
                $this->firebase->setValue("account_penalties/{$penaltyKey}", [
                    'email'           => $email,
                    'username'        => $userDb->username ?? ($firebaseUser->username ?? null),
                    'role'            => $role,
                    'status'          => 'banned',
                    'login_attempts'  => $attempts,
                    'max_attempts'    => $maxAttempts,
                    'locked_at'       => now()->toDateTimeString(),
                    'locked_until'    => $lockedUntil->toDateTimeString(),
                    'lockout_minutes' => $lockoutDuration,
                    'ip'              => $request->ip(),
                ]);
            } catch (\Exception $e) {
                \Log::error('Gagal menyimpan penalty ke Firebase: ' . $e->getMessage());
            }

            return back()->withErrors([
                'password' => "Percobaan ke-{$attempts} dari {$maxAttempts} — Kata sandi salah. Akun Anda telah terkunci selama {$lockoutDuration} menit. Silakan coba lagi nanti.",
            ])->onlyInput('login');
        }

        // Tampilkan progres percobaan yang jelas
        $pesanError = "Percobaan ke-{$attempts} dari {$maxAttempts} — Kata sandi yang Anda masukkan salah. "
            . ($sisaPercobaan > 0
                ? "Sisa {$sisaPercobaan} percobaan lagi sebelum akun dikunci selama {$lockoutDuration} menit."
                : "Akun Anda akan segera dikunci.");

        return back()->withErrors([
            'password' => $pesanError,
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
