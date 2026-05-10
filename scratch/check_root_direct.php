<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

use Kreait\Laravel\Firebase\Facades\Firebase;
$db = Firebase::database();
$root = $db->getReference('/')->getValue();

echo "ALL ROOT KEYS:\n";
if (is_array($root)) {
    print_r(array_keys($root));
} else {
    echo "ROOT IS NOT AN ARRAY (VALUE: " . json_encode($root) . ")\n";
}
