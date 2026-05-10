<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

use Kreait\Laravel\Firebase\Facades\Firebase;
$db = Firebase::database();

$packages = $db->getReference('packages')->getValue();
$registrations = $db->getReference('registrations')->getValue();

echo "PACKAGES COUNT: " . (is_array($packages) ? count($packages) : "NOT AN ARRAY (" . gettype($packages) . ")") . "\n";
echo "REGISTRATIONS COUNT: " . (is_array($registrations) ? count($registrations) : "NOT AN ARRAY (" . gettype($registrations) . ")") . "\n";

if (is_array($registrations)) {
    echo "SAMPLE REGISTRATION:\n";
    print_r(array_slice($registrations, 0, 1, true));
}
