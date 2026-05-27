<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();
$firebase = app(\App\Services\FirebaseService::class);
$settings = $firebase->getValue('settings');
echo "HERO BG 1: " . $settings['hero_bg_1'] . "\n";
