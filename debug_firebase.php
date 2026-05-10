<?php

require __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$serviceAccount = $_ENV['FIREBASE_CREDENTIALS'] ?? null;
$databaseUrl = $_ENV['FIREBASE_DATABASE_URL'] ?? null;

echo "Service Account: $serviceAccount\n";
echo "Database URL: $databaseUrl\n";

try {
    $factory = (new Factory)
        ->withServiceAccount(__DIR__ . '/' . $serviceAccount)
        ->withDatabaseUri($databaseUrl);

    $database = $factory->createDatabase();
    $database->getReference('test_connection')->set(['time' => time()]);
    echo "Success! Firebase RTDB connected.\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if (method_exists($e, 'getResponse')) {
        echo "Response: " . (string)$e->getResponse()->getBody() . "\n";
    }
}
