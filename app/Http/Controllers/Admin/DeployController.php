<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\Services\FirebaseService;

class DeployController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Trigger deployment to the remote hosting server.
     */
    public function deploy(Request $request)
    {
        // Keamanan: Blokir aksi jika dijalankan di server production (kecuali via CLI/Console)
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
        $currentHost = request()->getHost();
        if (!app()->runningInConsole() && ($currentHost === $appHost || str_contains($currentHost, 'umrohceriaabadi.com'))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Fitur deployment hanya diizinkan dijalankan dari server lokal.'
            ], 403);
        }

        // 1. Ambil URL target dan Token dari Firebase settings (dengan fallback .env)
        $settings = $this->getFirebaseData('site_settings', 300, function() {
            return $this->firebase->getValue('settings') ?? [];
        });
        $targetUrl = $settings['deploy_target_url'] ?? env('DEPLOY_TARGET_URL');
        $secretToken = $settings['deploy_secret'] ?? env('DEPLOY_SECRET');

        if (!$targetUrl || !$secretToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'Konfigurasi deploy belum diset di .env (DEPLOY_TARGET_URL atau DEPLOY_SECRET).'
            ], 500);
        }

        try {
            // 2. Kirim request ke script deploy.php di hosting
            // Set timeout yang agak panjang karena proses deploy (download & extract) butuh waktu
            $response = Http::timeout(120)
                ->withHeaders([
                    'X-Deploy-Token' => $secretToken,
                    'Accept' => 'application/json'
                ])
                ->get($targetUrl);

            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Proses deploy telah di-trigger. Server merespon dengan sukses.',
                    'response' => $response->json()
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal melakukan deploy. Server merespon dengan error.',
                    'response' => $response->json() ?? $response->body()
                ], $response->status());
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghubungi server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload local changes to GitHub repository.
     */
    public function uploadToGithub(Request $request)
    {
        // Keamanan: Blokir aksi jika dijalankan di server production (kecuali via CLI/Console)
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
        $currentHost = request()->getHost();
        if (!app()->runningInConsole() && ($currentHost === $appHost || str_contains($currentHost, 'umrohceriaabadi.com'))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Fitur pengunggahan kode ke GitHub hanya diizinkan dijalankan dari server lokal.'
            ], 403);
        }

        $request->validate([
            'repo_url' => 'required|string'
        ]);

        $repoUrl = trim($request->input('repo_url'));
        $parsedUrl = parse_url($repoUrl);

        // Ambil PAT dari URL (jika user memasukkan https://PAT@github.com/...)
        $pat = $parsedUrl['user'] ?? null;

        if (empty($pat)) {
            // Ambil PAT dari Firebase Settings (jika ada) atau dari .env
            try {
                $settings = $this->getFirebaseData('site_settings', 300, function() {
                    return $this->firebase->getValue('settings') ?? [];
                });
                $pat = $settings['deploy_github_pat'] ?? ($settings['github_pat'] ?? env('DEPLOY_GITHUB_PAT'));
            } catch (\Exception $e) {
                // Jika Firebase gagal/eror, fallback ke .env
                $pat = env('DEPLOY_GITHUB_PAT');
            }
        }

        if (empty($pat)) {
            return response()->json([
                'status' => 'error',
                'message' => 'GitHub Personal Access Token (PAT) tidak ditemukan di URL, Settings, maupun di .env (DEPLOY_GITHUB_PAT).'
            ], 400);
        }

        // Inject PAT into URL: https://PAT@github.com/user/repo.git
        if (isset($parsedUrl['scheme'], $parsedUrl['host'], $parsedUrl['path'])) {
            $authenticatedUrl = $parsedUrl['scheme'] . '://' . $pat . '@' . $parsedUrl['host'] . $parsedUrl['path'];
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Format link repository GitHub tidak valid.'
            ], 400);
        }

        $log = [];
        $hasLocalChanges = false;
        $log[] = "[0/4] Memperbarui remote URL...";
        exec('git remote 2>&1', $outRemoteCheck, $retRemoteCheck);
        $hasOrigin = false;
        foreach ($outRemoteCheck as $remoteName) {
            if (trim($remoteName) === 'origin') {
                $hasOrigin = true;
                break;
            }
        }

        if ($hasOrigin) {
            exec('git remote set-url origin ' . escapeshellarg($authenticatedUrl) . ' 2>&1', $outRemoteUpdate, $retRemoteUpdate);
            $log = array_merge($log, $outRemoteUpdate);
        } else {
            exec('git remote add origin ' . escapeshellarg($authenticatedUrl) . ' 2>&1', $outRemoteUpdate, $retRemoteUpdate);
            $log = array_merge($log, $outRemoteUpdate);
        }

        if ($retRemoteUpdate !== 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui remote URL GitHub.',
                'log' => implode("\n", $log)
            ], 500);
        }

        $statusOutput = [];
        exec('git status -s 2>&1', $statusOutput, $statusCode);

        if ($statusCode !== 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menjalankan git status.',
                'log' => implode("\n", $log)
            ], 500);
        }

        if (!empty($statusOutput)) {
            $hasLocalChanges = true;
            $log[] = "\n[1/4] Menemukan perubahan lokal:";
            $log = array_merge($log, $statusOutput);

            $log[] = "\n[2/4] Menambahkan file yang diubah (git add .)...";
            exec('git add . 2>&1', $outAdd, $retAdd);
            $log = array_merge($log, $outAdd);

            if ($retAdd !== 0) {
                return response()->json(['status' => 'error', 'message' => 'Gagal git add', 'log' => implode("\n", $log)], 500);
            }

            $log[] = "\nMelakukan commit perubahan...";
            $date = date('d-m-Y H:i:s');
            $commitMsg = "Auto-update from Admin Dashboard: " . $date;
            exec('git commit -m "' . $commitMsg . '" 2>&1', $outCommit, $retCommit);
            $log = array_merge($log, $outCommit);
        }

        $log[] = "\n[3/4] Sinkronisasi (git pull --rebase origin main)...";
        exec('git -c credential.helper= -c core.askpass= -c credential.interactive=false pull --rebase origin main 2>&1', $outPull, $retPull);
        $log = array_merge($log, $outPull);

        if ($retPull !== 0) {
            exec('git rebase --abort 2>&1');
            return response()->json(['status' => 'error', 'message' => 'Gagal git pull --rebase', 'log' => implode("\n", $log)], 500);
        }

        if ($hasLocalChanges) {
            $log[] = "\n[4/4] Mengunggah ke GitHub (git push origin main)...";
            exec('git -c credential.helper= -c core.askpass= -c credential.interactive=false push origin main 2>&1', $outPush, $retPush);
            $log = array_merge($log, $outPush);

            if ($retPush !== 0) {
                return response()->json(['status' => 'error', 'message' => 'Gagal git push', 'log' => implode("\n", $log)], 500);
            }

            return response()->json(['status' => 'success', 'message' => 'Berhasil diunggah!', 'log' => implode("\n", $log)]);
        }

        return response()->json(['status' => 'success', 'message' => 'Sudah sinkron.', 'log' => implode("\n", $log)]);
    }
}
// End of DeployController - test comment for verification
