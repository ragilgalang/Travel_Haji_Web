<?php
$file = '../storage/logs/laravel.log';
if (file_exists($file)) {
    $size = filesize($file);
    $offset = max(0, $size - 5000);
    echo "<pre>" . htmlspecialchars(file_get_contents($file, false, null, $offset)) . "</pre>";
} else {
    echo "No logs.";
}
