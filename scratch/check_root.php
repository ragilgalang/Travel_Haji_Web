<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

use App\Services\FirebaseService;
$firebase = app(FirebaseService::class);
$root = $firebase->getValue('/');

echo "ROOT NODES:\n";
print_r(array_keys($root ?? []));
