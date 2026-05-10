<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\FirebaseService;
$firebase = app(FirebaseService::class);

$data = [
    'name' => 'Paket Tes Sistem',
    'type' => 'umrah',
    'price' => '10 jt',
    'duration' => '9 hari',
    'created_at' => now()->toDateTimeString()
];

$id = $firebase->push('packages', $data);
echo "PAKET TES BERHASIL DIBUAT DENGAN ID: $id\n";
