<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

use App\Services\FirebaseService;
$firebase = app(FirebaseService::class);
$registrations = $firebase->getValue('registrations');

if (!$registrations) {
    echo "DATA PENDAFTARAN KOSONG DI FIREBASE.\n";
} else {
    echo "SAMPLE REGISTRATION DATA:\n";
    print_r(array_slice($registrations, 0, 1, true));
}
