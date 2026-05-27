<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DeployController extends Controller
{
    /**
     * Trigger deployment to the remote hosting server.
     */
    public function deploy(Request $request)
    {
        // 1. Ambil URL target dan Token dari file .env
        $targetUrl = env('DEPLOY_TARGET_URL');
        $secretToken = env('DEPLOY_SECRET');

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
        $log = [];
        $hasLocalChanges = false;

        // [1/4] Cek apakah ada perubahan lokal
        $statusOutput = [];
        exec('git status -s 2>&1', $statusOutput, $statusCode);

        if ($statusCode !== 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menjalankan git status. Pastikan Git terinstall dan terkonfigurasi di PATH.',
                'log' => implode("\n", $statusOutput)
            ], 500);
        }

        if (!empty($statusOutput)) {
            $hasLocalChanges = true;
            $log[] = "[1/4] Menemukan perubahan lokal:";
            $log = array_merge($log, $statusOutput);

            // [2/4] Tambahkan semua file
            $log[] = "\n[2/4] Menambahkan file yang diubah (git add .)...";
            exec('git add . 2>&1', $outAdd, $retAdd);
            $log = array_merge($log, $outAdd);

            if ($retAdd !== 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal menambahkan file (git add .)',
                    'log' => implode("\n", $log)
                ], 500);
            }

            // Commit perubahan
            $log[] = "\nMelakukan commit perubahan...";
            $date = date('d-m-Y H:i:s');
            $commitMsg = "Auto-update from Admin Dashboard: " . $date;
            exec('git commit -m "' . $commitMsg . '" 2>&1', $outCommit, $retCommit);
            $log = array_merge($log, $outCommit);

            if ($retCommit !== 0 && !str_contains(implode(' ', $outCommit), 'nothing to commit')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal melakukan commit perubahan.',
                    'log' => implode("\n", $log)
                ], 500);
            }
        } else {
            $log[] = "[1/4] Tidak ada perubahan lokal untuk di-commit.";
        }

        // [3/4] Sinkronisasi dengan remote (git pull --rebase origin main)
        $log[] = "\n[3/4] Sinkronisasi dengan GitHub (git pull --rebase origin main)...";
        exec('git pull --rebase origin main 2>&1', $outPull, $retPull);
        $log = array_merge($log, $outPull);

        if ($retPull !== 0) {
            // Abort rebase jika terjadi kegagalan/konflik agar repo tetap bersih
            exec('git rebase --abort 2>&1', $outAbort, $retAbort);
            $log[] = "\n[WARNING] git pull --rebase gagal. Membatalkan rebase (git rebase --abort)...";
            $log = array_merge($log, $outAbort);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyelaraskan dengan remote GitHub (git pull --rebase). Kemungkinan terdapat konflik atau masalah jaringan/kredensial.',
                'log' => implode("\n", $log)
            ], 500);
        }

        // [4/4] Push ke GitHub jika ada perubahan lokal yang baru saja dicommit
        if ($hasLocalChanges) {
            $log[] = "\n[4/4] Mengunggah ke GitHub (git push origin main)...";
            exec('git push origin main 2>&1', $outPush, $retPush);
            $log = array_merge($log, $outPush);

            if ($retPush !== 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal mengunggah kode ke GitHub. Pastikan kredensial Git Anda sudah tersimpan di sistem.',
                    'log' => implode("\n", $log)
                ], 500);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil mengunggah semua perubahan baru ke GitHub!',
                'log' => implode("\n", $log)
            ]);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => 'Repository sudah sinkron dengan GitHub (tidak ada perubahan baru untuk diunggah).',
                'log' => implode("\n", $log)
            ]);
        }
    }
}

