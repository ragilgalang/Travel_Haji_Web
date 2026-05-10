<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "FIREBASE_DATABASE_URL: " . env('FIREBASE_DATABASE_URL') . "\n";
echo "CONFIG FIREBASE URL: " . config('firebase.projects.app.database.url') . "\n";
