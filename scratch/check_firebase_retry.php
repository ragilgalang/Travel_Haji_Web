<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\FirebaseService;
$firebase = app(FirebaseService::class);
$packages = $firebase->getValue('packages');

echo "PACKAGES DATA:\n";
print_r($packages);
