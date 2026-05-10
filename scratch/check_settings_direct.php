<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\FirebaseService;

$firebase = new FirebaseService();
$settings = $firebase->getValue('settings');

echo "CURRENT SETTINGS IN FIREBASE:\n";
echo "---------------------------\n";
print_r($settings);
echo "---------------------------\n";
