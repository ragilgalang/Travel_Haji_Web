<?php
/**
 * Script Debugging Performa & Path Gambar untuk Laravel Hosting
 * Lokasi: public_html/cek_gambar.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<body style='font-family: sans-serif; padding: 20px; line-height: 1.6; background: #f4f7f6;'>";
echo "<div style='max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);'>";
echo "<h2 style='color: #1a5c3a; border-bottom: 2px solid #eee; padding-bottom: 10px;'>🔍 Diagnosa Path Gambar & Folder</h2>";

// 1. Cek Informasi Server Dasar
echo "<h3>1. Informasi Server</h3>";
echo "<b>Document Root:</b> " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "<b>Current Directory:</b> " . __DIR__ . "<br>";
echo "<b>PHP Version:</b> " . phpversion() . "<br>";

// 2. Cek Keberadaan Folder Kunci
echo "<h3>2. Pengecekan Folder (Case Sensitive)</h3>";
$folders = [
    'public',
    'public/logo',
    'public/uploads',
    'public/img',
    'storage',
    'vendor'
];

echo "<table style='width: 100%; border-collapse: collapse;'>";
echo "<tr style='background: #eee;'><th style='padding: 8px; text-align: left;'>Nama Folder</th><th style='padding: 8px;'>Status</th><th style='padding: 8px;'>Izin (Perms)</th></tr>";
foreach ($folders as $f) {
    $exists = is_dir($f);
    $perms = $exists ? substr(sprintf('%o', fileperms($f)), -3) : '-';
    $status = $exists ? "<span style='color: green;'>✅ Ditemukan</span>" : "<span style='color: red;'>❌ Hilang</span>";
    echo "<tr><td style='padding: 8px; border-bottom: 1px solid #eee;'>$f</td><td style='padding: 8px; border-bottom: 1px solid #eee; text-align: center;'>$status</td><td style='padding: 8px; border-bottom: 1px solid #eee; text-align: center;'>$perms</td></tr>";
}
echo "</table>";

// 3. Cek File Gambar Spesifik
echo "<h3>3. Pengecekan File Logo</h3>";
$logoPath = 'public/logo/logo.png';
if (file_exists($logoPath)) {
    echo "<div style='background: #f0fdf4; padding: 10px; border-radius: 8px; color: #16a34a;'>✅ File <b>$logoPath</b> Ditemukan.</div>";
    echo "<p>Coba buka langsung: <a href='/$logoPath' target='_blank'>Klik di sini</a></p>";
} else {
    echo "<div style='background: #fef2f2; padding: 10px; border-radius: 8px; color: #dc2626;'>❌ File <b>$logoPath</b> TIDAK ditemukan.</div>";
    echo "<p>Pastikan Anda sudah mengupload folder 'logo' ke dalam folder 'public'.</p>";
}

// 4. Analisa .htaccess
echo "<h3>4. Analisa .htaccess</h3>";
if (file_exists('.htaccess')) {
    echo "<div style='background: #f0fdf4; padding: 10px; border-radius: 8px; color: #16a34a;'>✅ File <b>.htaccess</b> Ditemukan.</div>";
} else {
    echo "<div style='background: #fff7ed; padding: 10px; border-radius: 8px; color: #9a3412;'>⚠️ File <b>.htaccess</b> tidak ada di root.</div>";
}

// 5. Daftar File Root (Top 20)
echo "<h3>5. Daftar Isi Root (Top 20)</h3>";
$files = scandir('.');
echo "<ul style='columns: 2;'>";
foreach (array_slice($files, 0, 22) as $file) {
    if ($file != '.' && $file != '..') echo "<li>$file</li>";
}
echo "</ul>";

echo "<hr style='border: 0; border-top: 1px solid #eee; margin-top: 30px;'>";
echo "<p style='color: #64748b; font-size: 0.85rem;'>Jika folder 'public' ADA tapi gambar tetap pecah, masalahnya kemungkinan besar ada di <b>APP_URL</b> dalam file <b>.env</b>.</p>";
echo "</div></body>";
