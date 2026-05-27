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
        $status = 0;
        $statusOutput = [];
        
        // Cek status repositori lokal
        exec('git status -s 2>&1', $statusOutput, $status);
        if ($status !== 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menjalankan git status di lokal. Pastikan Git terinstall dan terkonfigurasi di PATH.',
                'log' => implode("\n", $statusOutput)
            ], 500);
        }

        if (empty($statusOutput) || count($statusOutput) === 0) {
            return response()->json([
                'status' => 'success',
                'message' => 'Tidak ada perubahan baru untuk diunggah ke GitHub.',
                'log' => "Working tree clean. Nothing to commit."
            ]);
        }

        $log = [];
        $log[] = "[1/3] Menambahkan file yang diubah (git add .)...";
        exec('git add . 2>&1', $out1, $ret1);
        $log = array_merge($log, $out1);
        
        if ($ret1 !== 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan file (git add .)',
                'log' => implode("\n", $log)
            ], 500);
        }

        $log[] = "\n[2/3] Melakukan commit perubahan...";
        $date = date('d-m-Y H:i:s');
        $commitMsg = "Auto-update from Admin Dashboard: " . $date;
        exec('git commit -m "' . $commitMsg . '" 2>&1', $out2, $ret2);
        $log = array_merge($log, $out2);

        if ($ret2 !== 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal melakukan commit perubahan.',
                'log' => implode("\n", $log)
            ], 500);
        }

        $log[] = "\n[3/3] Mengunggah ke GitHub (git push origin main)...";
        exec('git push origin main 2>&1', $out3, $ret3);
        $log = array_merge($log, $out3);

        if ($ret3 !== 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengunggah kode ke GitHub. Kredensial mungkin belum tersimpan di sistem.',
                'log' => implode("\n", $log)
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil mengunggah semua perubahan ke GitHub!',
            'log' => implode("\n", $log)
        ]);
    }
}

