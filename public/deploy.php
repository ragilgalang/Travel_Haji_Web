<?php
/**
 * ========================================
 * SCRIPT DEPLOY OTOMATIS - TRAVEL HAJI
 * ========================================
 * Upload file ini ke ROOT hosting Anda: /public_html/deploy.php
 */

// ⚠️ WAJIB SAMA dengan nilai DEPLOY_SECRET di file .env lokal
$secretToken = 'BebasKetikKatakunciRahasiaDisini123';

// ⚠️ URL Repository GitHub (format HTTPS)
$repoUrl = 'https://github.com/ragilgalang/Travel_Haji_Web.git';

// ⚠️ Branch yang digunakan
$branch = 'main';

// --- Validasi Keamanan Token ---
header('Content-Type: application/json');

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

// --- Tentukan Direktori ---
$log = [];
$publicDir = __DIR__; // /public_html
$repoDir   = dirname($publicDir); // Parent dari public_html (root hosting)

// Cek apakah .git ada di parent (root hosting) atau di public_html
if (is_dir($repoDir . '/.git')) {
    $gitDir = $repoDir;
} elseif (is_dir($publicDir . '/.git')) {
    $gitDir = $publicDir;
} else {
    $gitDir = null;
}

// --- Jika Belum Ada .git: Setup git ---
if ($gitDir === null) {
    $log[] = '[INFO] Repository belum di-setup. Memulai inisialisasi Git...';
    $repoUrl = 'https://github.com/ragilgalang/Travel_Haji_Web.git';
    
    $cmdInit = "cd " . escapeshellarg($repoDir) . " && git init 2>&1";
    exec($cmdInit, $outInit, $codeInit);
    $log = array_merge($log, $outInit);
    
    $cmdRemote = "cd " . escapeshellarg($repoDir) . " && git remote add origin " . escapeshellarg($repoUrl) . " 2>&1";
    exec($cmdRemote, $outRemote, $codeRemote);
    $log = array_merge($log, $outRemote);
    
    $cmdFetch = "cd " . escapeshellarg($repoDir) . " && git fetch origin 2>&1";
    exec($cmdFetch, $outFetch, $codeFetch);
    $log = array_merge($log, $outFetch);
    
    $cmdReset = "cd " . escapeshellarg($repoDir) . " && git reset --hard origin/" . escapeshellarg($branch) . " 2>&1";
    exec($cmdReset, $outReset, $codeReset);
    $log = array_merge($log, $outReset);

    if ($codeReset !== 0) {
        http_response_code(500);
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal melakukan setup git awal di server.',
            'log'     => $log
        ]);
        exit;
    }
    $gitDir = $repoDir;
    $log[] = '[INFO] Setup awal Git berhasil! Melanjutkan ke tahap pembaruan...';
}

// --- Jalankan git pull ---
$log[] = '[INFO] Menjalankan git pull...';
$pullCmd = "cd " . escapeshellarg($gitDir) . " && git pull origin " . escapeshellarg($branch) . " 2>&1";
exec($pullCmd, $pullOutput, $pullCode);
$log = array_merge($log, $pullOutput);

if ($pullCode !== 0) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Gagal melakukan git pull di server hosting.',
        'log'     => $log
    ]);
    exit;
}

// --- Composer install (jika tersedia) ---
if (file_exists($gitDir . '/composer.json')) {
    $log[] = '[INFO] Menjalankan composer install...';
    exec("cd " . escapeshellarg($gitDir) . " && composer install --no-dev --optimize-autoloader 2>&1", $composerOut);
    $log = array_merge($log, $composerOut);
}

// --- Laravel artisan cache clear (jika tersedia) ---
if (file_exists($gitDir . '/artisan')) {
    $log[] = '[INFO] Membersihkan cache Laravel...';
    exec("cd " . escapeshellarg($gitDir) . " && php artisan config:clear 2>&1", $clearOut);
    exec("cd " . escapeshellarg($gitDir) . " && php artisan cache:clear 2>&1", $cacheOut);
    $log = array_merge($log, $clearOut, $cacheOut);
}

echo json_encode([
    'status'  => 'success',
    'message' => 'Deploy berhasil! Website sudah diperbarui dari GitHub.',
    'log'     => $log
]);
