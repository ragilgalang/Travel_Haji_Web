<?php
/**
 * ========================================
 * SCRIPT DEPLOY OTOMATIS - TRAVEL HAJI
 * Metode: Download ZIP dari GitHub (Tanpa Git)
 * ========================================
 * Upload file ini ke: public_html/deploy.php
 */

// ⚠️ WAJIB SAMA dengan nilai DEPLOY_SECRET di file .env lokal
$secretToken = 'BebasKetikKatakunciRahasiaDisini123';

// ⚠️ Format: username/nama-repo
$githubRepo = 'ragilgalang/Travel_Haji_Web';

// ⚠️ Branch yang digunakan
$branch = 'main';

// ⚠️ PAT GitHub (Personal Access Token) - diperlukan jika repo private
// Isi dengan token Anda, atau kosongkan jika repo public
$githubPat = '';

// ============================================================
// JANGAN EDIT DI BAWAH INI
// ============================================================

// --- Tangkap Error Fatal agar tidak blank 500 ---
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'PHP Fatal Error: ' . $error['message'],
            'file' => $error['file'],
            'line' => $error['line'],
            'log' => []
        ]);
    }
});

header('Content-Type: application/json');

// Coba atur time limit, abaikan jika diblokir server
if (function_exists('set_time_limit')) {
    @set_time_limit(300);
}

// --- Validasi Keamanan Token ---
$incomingToken = $_SERVER['HTTP_X_DEPLOY_TOKEN'] ?? '';
if (empty($incomingToken)) {
    $incomingToken = $_GET['token'] ?? '';
}

if ($incomingToken !== $secretToken) {
    http_response_code(403);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Akses ditolak. Token tidak valid atau kosong.'
    ]);
    exit;
}

$log = [];

// --- Tentukan Direktori Target ---
$publicDir  = __DIR__; // Lokasi deploy.php berada (public_html)
$targetDir  = dirname($publicDir); // Root hosting (1 level di atas public_html)

// Fallback: jika tidak ada akses ke parent, deploy ke public_html
if (!is_writable($targetDir)) {
    $targetDir = $publicDir;
}

$log[] = '[INFO] Target direktori: ' . $targetDir;

// --- Download ZIP dari GitHub ---
$zipUrl = "https://api.github.com/repos/{$githubRepo}/zipball/{$branch}";
$tempZip = sys_get_temp_dir() . '/deploy_' . time() . '.zip';

$log[] = '[INFO] Mengunduh kode terbaru dari GitHub...';

$headers = [
    'User-Agent: TravelHajiDeploy/1.0',
    'Accept: application/vnd.github+json',
];

if (!empty($githubPat)) {
    $headers[] = 'Authorization: Bearer ' . $githubPat;
}

$context = stream_context_create([
    'http' => [
        'method'          => 'GET',
        'header'          => implode("\r\n", $headers),
        'follow_location' => 1,
        'timeout'         => 120,
    ],
    'ssl' => [
        'verify_peer'      => false,
        'verify_peer_name' => false,
    ]
]);

$zipContent = @file_get_contents($zipUrl, false, $context);

if ($zipContent === false) {
    // Coba dengan curl sebagai fallback
    if (function_exists('curl_init')) {
        $log[] = '[INFO] Mencoba via cURL...';
        $ch = curl_init($zipUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER     => $headers,
        ]);
        $zipContent = curl_exec($ch);
        $curlError  = curl_error($ch);
        curl_close($ch);

        if (!$zipContent) {
            http_response_code(500);
            echo json_encode([
                'status'  => 'error',
                'message' => 'Gagal mengunduh dari GitHub. cURL error: ' . $curlError,
                'log'     => $log
            ]);
            exit;
        }
    } else {
        http_response_code(500);
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal mengunduh file ZIP dari GitHub. Pastikan server dapat mengakses internet.',
            'log'     => $log
        ]);
        exit;
    }
}

$log[] = '[INFO] Unduhan selesai. Ukuran: ' . round(strlen($zipContent) / 1024, 1) . ' KB';

// --- Simpan ZIP ke file sementara ---
if (file_put_contents($tempZip, $zipContent) === false) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Gagal menyimpan file ZIP sementara.',
        'log'     => $log
    ]);
    exit;
}

$log[] = '[INFO] File ZIP disimpan ke: ' . $tempZip;

// --- Ekstrak ZIP ---
if (!class_exists('ZipArchive')) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Ekstensi ZipArchive tidak tersedia di server ini.',
        'log'     => $log
    ]);
    exit;
}

$zip = new ZipArchive();
if ($zip->open($tempZip) !== true) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Gagal membuka file ZIP.',
        'log'     => $log
    ]);
    exit;
}

$log[] = '[INFO] Mengekstrak ' . $zip->numFiles . ' file...';

// GitHub ZIP memiliki satu folder root di dalamnya, kita perlu mengetahuinya
$rootFolder = '';
$firstEntry = $zip->getNameIndex(0);
if ($firstEntry && strpos($firstEntry, '/') !== false) {
    $rootFolder = substr($firstEntry, 0, strpos($firstEntry, '/') + 1);
}

$log[] = '[INFO] Folder root di ZIP: ' . ($rootFolder ?: '(tidak ada)');

// Ekstrak satu per satu, skip folder root GitHub
$successCount = 0;
$skipFiles    = ['deploy.php', 'clone.php']; // File yang tidak boleh ditimpa

for ($i = 0; $i < $zip->numFiles; $i++) {
    $entryName = $zip->getNameIndex($i);

    // Hapus prefix folder root GitHub
    $relativePath = $rootFolder ? substr($entryName, strlen($rootFolder)) : $entryName;

    if (empty($relativePath)) continue;

    // Tentukan path tujuan
    // File di dalam folder "public" di repo -> masuk ke public_html
    // File lainnya -> masuk ke root hosting (di atas public_html)
    if (strpos($relativePath, 'public/') === 0) {
        $destPath = $publicDir . '/' . substr($relativePath, strlen('public/'));
    } else {
        $destPath = $targetDir . '/' . $relativePath;
    }

    // Jangan timpa file deploy penting
    $fileName = basename($destPath);
    if (in_array($fileName, $skipFiles) && file_exists($destPath)) {
        continue;
    }

    // Buat folder jika belum ada
    if (substr($entryName, -1) === '/') {
        if (!is_dir($destPath)) {
            @mkdir($destPath, 0755, true);
        }
        continue;
    }

    // Pastikan folder parent ada
    $parentDir = dirname($destPath);
    if (!is_dir($parentDir)) {
        @mkdir($parentDir, 0755, true);
    }

    // Tulis file
    $content = $zip->getFromIndex($i);
    if (file_put_contents($destPath, $content) !== false) {
        $successCount++;
    }
}

$zip->close();
@unlink($tempZip);

$log[] = '[INFO] Berhasil mengekstrak ' . $successCount . ' file.';

// --- Clear Laravel Cache Manual (karena exec() diblokir Hostinger) ---
$log[] = '[INFO] Membersihkan cache Laravel secara manual...';
$cachePaths = [
    $targetDir . '/bootstrap/cache/config.php',
    $targetDir . '/bootstrap/cache/events.php',
    $targetDir . '/bootstrap/cache/packages.php',
    $targetDir . '/bootstrap/cache/routes.php',
    $targetDir . '/bootstrap/cache/services.php',
];

$cleared = 0;
foreach ($cachePaths as $cPath) {
    if (file_exists($cPath)) {
        @unlink($cPath);
        $cleared++;
    }
}

// Hapus isi folder storage/framework/views
$viewsDir = $targetDir . '/storage/framework/views';
if (is_dir($viewsDir)) {
    $files = glob($viewsDir . '/*');
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== '.gitignore') {
            @unlink($file);
            $cleared++;
        }
    }
}
$log[] = "[INFO] Berhasil membersihkan $cleared file cache Laravel.";

echo json_encode([
    'status'  => 'success',
    'message' => "Deploy berhasil! {$successCount} file diperbarui dari GitHub.",
    'log'     => $log
]);
