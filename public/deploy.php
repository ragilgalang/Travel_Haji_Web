<?php
// deploy.php

/**
 * SISTEM DEPLOY OTOMATIS
 * Letakkan file ini di folder public_html atau document root hosting Anda.
 */

// Konfigurasi - Sesuaikan dengan pengaturan Anda
define('SECRET_TOKEN', 'KODE_RAHASIA_DEPLOY_KAMU'); // GANTI DENGAN TOKEN RAHASIA
define('GITHUB_USERNAME', 'MKhullavaulZahril'); // Ganti dengan username GitHub Anda
define('GITHUB_REPO', 'Travel-Haji-Web'); // Ganti dengan nama repository Anda
define('GITHUB_BRANCH', 'main'); // Ganti dengan branch yang ingin di deploy
define('GITHUB_PAT', ''); // ISI JIKA REPOSITORY PRIVATE (Personal Access Token)

// Direktori root aplikasi (satu tingkat di atas public jika deploy.php di dalam public)
// Sesuaikan jika struktur folder hosting berbeda
$baseDir = dirname(__DIR__); 

// Set header response
header('Content-Type: application/json');

// 1. Verifikasi Token
$headers = getallheaders();
$token = isset($headers['X-Deploy-Token']) ? $headers['X-Deploy-Token'] : (isset($_GET['token']) ? $_GET['token'] : '');

if ($token !== SECRET_TOKEN) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak. Token tidak valid.']);
    exit;
}

// Helper function untuk logging
function logMessage($message) {
    echo json_encode(['status' => 'info', 'message' => $message]) . "\n";
    ob_flush();
    flush();
}

logMessage("Memulai proses deploy...");

// 2. Download File ZIP dari GitHub
$zipUrl = "https://github.com/" . GITHUB_USERNAME . "/" . GITHUB_REPO . "/archive/refs/heads/" . GITHUB_BRANCH . ".zip";
$zipFile = $baseDir . '/temp_deploy.zip';

logMessage("Mengunduh source code dari GitHub...");

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $zipUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'AutoDeployScript');

if (!empty(GITHUB_PAT)) {
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: token " . GITHUB_PAT
    ]);
}

$zipData = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || $zipData === false) {
    http_response_code(500);
    logMessage("Gagal mengunduh file dari GitHub. HTTP Code: " . $httpCode);
    exit;
}

file_put_contents($zipFile, $zipData);
logMessage("Unduhan selesai.");

// 3. Ekstrak ZIP
logMessage("Mengekstrak file ZIP...");
$zip = new ZipArchive;
if ($zip->open($zipFile) === TRUE) {
    // Ekstrak ke folder sementara
    $tempDir = $baseDir . '/temp_deploy_dir';
    if (!is_dir($tempDir)) {
        mkdir($tempDir);
    }
    $zip->extractTo($tempDir);
    $zip->close();
    
    // Hapus file ZIP
    unlink($zipFile);
    logMessage("Ekstraksi selesai.");
    
    // 4. Salin File (Kecuali file yang dikecualikan)
    $extractedFolderName = GITHUB_REPO . '-' . GITHUB_BRANCH;
    $sourcePath = $tempDir . '/' . $extractedFolderName;
    
    if (is_dir($sourcePath)) {
        logMessage("Menyalin file ke direktori utama...");
        
        // Daftar file/folder yang TIDAK BOLEH ditimpa
        $exclude = ['.env', 'storage', 'database', 'deploy.php'];
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourcePath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $item) {
            $relativePath = $iterator->getSubPathName();
            $shouldExclude = false;
            
            // Cek apakah file/folder termasuk dalam pengecualian
            foreach ($exclude as $ex) {
                if ($relativePath === $ex || strpos($relativePath, $ex . DIRECTORY_SEPARATOR) === 0 || strpos($relativePath, $ex . '/') === 0) {
                    $shouldExclude = true;
                    break;
                }
            }
            
            if (!$shouldExclude) {
                $targetPath = $baseDir . DIRECTORY_SEPARATOR . $relativePath;
                
                if ($item->isDir()) {
                    if (!is_dir($targetPath)) {
                        mkdir($targetPath, 0755, true);
                    }
                } else {
                    copy($item->getPathname(), $targetPath);
                }
            }
        }
        
        // Hapus folder sementara
        $deleteDir = function($dir) use (&$deleteDir) {
            $files = array_diff(scandir($dir), array('.','..'));
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? $deleteDir("$dir/$file") : unlink("$dir/$file");
            }
            return rmdir($dir);
        };
        $deleteDir($tempDir);
        
        logMessage("Penyalinan file selesai.");
        
        // 5. Jalankan perintah Artisan (Optional, jika hosting mendukung exec)
        logMessage("Menjalankan perintah optimasi...");
        if (function_exists('exec')) {
            $artisan = $baseDir . '/artisan';
            if (file_exists($artisan)) {
                exec("php " . escapeshellarg($artisan) . " optimize:clear 2>&1", $output);
                logMessage("Optimize Clear: " . implode("\n", $output));
                
                $output = [];
                exec("php " . escapeshellarg($artisan) . " view:cache 2>&1", $output);
                logMessage("View Cache: " . implode("\n", $output));
            } else {
                logMessage("File artisan tidak ditemukan.");
            }
        } else {
            logMessage("Fungsi exec() tidak tersedia di hosting ini.");
        }
        
        echo json_encode(['status' => 'success', 'message' => 'Deploy berhasil diselesaikan!']);
        
    } else {
        http_response_code(500);
        logMessage("Gagal menemukan folder hasil ekstraksi.");
    }

} else {
    http_response_code(500);
    logMessage("Gagal membuka file ZIP.");
}
