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

// --- Jika Belum Ada .git: Lakukan git clone ---
if ($gitDir === null) {
    $log[] = '[INFO] Repository belum di-clone. Mencoba git clone...';
    $cloneCmd = "git clone --branch " . escapeshellarg($branch) . " " . escapeshellarg($repoUrl) . " " . escapeshellarg($repoDir) . " 2>&1";
    exec($cloneCmd, $cloneOutput, $cloneCode);
    $log = array_merge($log, $cloneOutput);

    if ($cloneCode !== 0) {
        // Coba clone ke public_html jika root gagal
        $cloneCmd2 = "git clone --branch " . escapeshellarg($branch) . " " . escapeshellarg($repoUrl) . " " . escapeshellarg($publicDir . '/app_repo') . " 2>&1";
        exec($cloneCmd2, $cloneOutput2, $cloneCode2);
        $log = array_merge($log, $cloneOutput2);

        if ($cloneCode2 !== 0) {
            http_response_code(500);
            echo json_encode([
                'status'  => 'error',
                'message' => 'Gagal melakukan git clone. Coba lakukan clone manual via cPanel > Git Version Control.',
                'log'     => $log
            ]);
            exit;
        }
        $gitDir = $publicDir . '/app_repo';
    } else {
        $gitDir = $repoDir;
    }
    $log[] = '[INFO] Clone berhasil! Melanjutkan ke tahap berikutnya...';
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
