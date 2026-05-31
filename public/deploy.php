<?php
/**
 * ========================================
 * SCRIPT DEPLOY OTOMATIS - TRAVEL HAJI
 * ========================================
 * Upload file ini ke ROOT hosting Anda: /public_html/deploy.php
 *
 * PENTING: Ganti nilai $secretToken di bawah dengan token yang sama
 * persis seperti yang ada di file .env lokal Anda (DEPLOY_SECRET).
 */

// ⚠️ WAJIB SAMA dengan nilai DEPLOY_SECRET di file .env lokal
$secretToken = 'BebasKetikKatakunciRahasiaDisini123';

// --- Validasi Keamanan Token ---
header('Content-Type: application/json');

$incomingToken = $_SERVER['HTTP_X_DEPLOY_TOKEN'] ?? $_GET['token'] ?? '';

if ($incomingToken !== $secretToken) {
    http_response_code(403);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Akses ditolak. Token tidak valid.'
    ]);
    exit;
}

// --- Jalankan Proses Deploy ---
$log = [];
$repoDir = __DIR__; // Folder deploy.php berada (biasanya /public_html)

// Cek apakah ada folder .git di parent atau current directory
$gitDir = '';
if (is_dir($repoDir . '/../.git')) {
    $gitDir = dirname($repoDir); // Parent dari public_html
} elseif (is_dir($repoDir . '/.git')) {
    $gitDir = $repoDir;
}

if (empty($gitDir)) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Folder .git tidak ditemukan. Pastikan repository sudah di-clone di server.',
        'log'     => []
    ]);
    exit;
}

// Jalankan git pull
$command = "cd " . escapeshellarg($gitDir) . " && git pull origin main 2>&1";
exec($command, $output, $returnCode);

$log = $output;

if ($returnCode !== 0) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Gagal melakukan git pull di server hosting.',
        'log'     => $log
    ]);
    exit;
}

// Jalankan composer install jika ada
$composerPath = $gitDir . '/composer.json';
if (file_exists($composerPath)) {
    $composerCmd = "cd " . escapeshellarg($gitDir) . " && composer install --no-dev --optimize-autoloader 2>&1";
    exec($composerCmd, $composerOutput, $composerCode);
    $log = array_merge($log, ['--- composer install ---'], $composerOutput);
}

// Clear config & cache Laravel jika artisan ada
$artisanPath = $gitDir . '/artisan';
if (file_exists($artisanPath)) {
    exec("cd " . escapeshellarg($gitDir) . " && php artisan config:clear 2>&1", $clearOut);
    exec("cd " . escapeshellarg($gitDir) . " && php artisan cache:clear 2>&1", $cacheOut);
    $log = array_merge($log, ['--- artisan clear ---'], $clearOut, $cacheOut);
}

echo json_encode([
    'status'  => 'success',
    'message' => 'Deploy berhasil! Website sudah diperbarui.',
    'log'     => $log
]);
