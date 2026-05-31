<?php
/**
 * SCRIPT KHUSUS UNTUK GIT CLONE PERTAMA KALI
 * Upload ke public_html/clone.php
 */

header('Content-Type: text/plain');

$repoUrl = 'https://github.com/ragilgalang/Travel_Haji_Web.git';
$branch = 'main';

$publicDir = __DIR__; 
$repoDir   = dirname($publicDir); 

echo "Memulai proses Git Clone...\n";
echo "Target Repo: $repoUrl\n\n";

// Coba setup git di parent directory (root hosting)
echo "Mencoba setup Git di: " . $repoDir . "\n";
exec("cd " . escapeshellarg($repoDir) . " && git init 2>&1", $out1, $code1);
exec("cd " . escapeshellarg($repoDir) . " && git remote add origin " . escapeshellarg($repoUrl) . " 2>&1", $out2, $code2);
exec("cd " . escapeshellarg($repoDir) . " && git pull origin " . escapeshellarg($branch) . " --force 2>&1", $out3, $code3);

echo implode("\n", $out1) . "\n";
echo implode("\n", $out2) . "\n";
echo implode("\n", $out3) . "\n\n";

if ($code3 === 0) {
    echo "✅ SUCCESS! Repository berhasil dihubungkan dan ditarik (pull) ke root hosting.\n";
} else {
    echo "❌ Gagal pull di root. Coba paksa reset...\n";
    exec("cd " . escapeshellarg($repoDir) . " && git fetch origin 2>&1", $fout);
    exec("cd " . escapeshellarg($repoDir) . " && git reset --hard origin/" . escapeshellarg($branch) . " 2>&1", $rout, $rcode);
    echo implode("\n", $rout) . "\n";
    
    if ($rcode === 0) {
        echo "✅ SUCCESS! Berhasil memaksa sinkronisasi dengan git reset --hard.\n";
    } else {
        echo "❌ ERROR FATAL! Git gagal dijalankan di server Anda. Kemungkinan fitur Git diblokir oleh Hostinger.\n";
    }
}
echo "\nSelesai.";
