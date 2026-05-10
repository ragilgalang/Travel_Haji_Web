<?php
require_once __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;

// Load .env manually if needed
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

$factory = (new Factory)
    ->withServiceAccount(__DIR__ . '/' . ($_ENV['FIREBASE_CREDENTIALS'] ?? 'firebase_credentials.json'))
    ->withDatabaseUri($_ENV['FIREBASE_DATABASE_URL'] ?? '');

$database = $factory->createDatabase();

$updates = [
    'settings/site_name' => 'PT. UMI MUTHMAINAH BERKAH',
    'settings/office_address' => 'SIDOKARE, SIDOARJO',
];

try {
    $database->getReference()->update($updates);
    echo "SUCCESS: Site name updated to PT. UMI MUTHMAINAH BERKAH, SIDOKARE, SIDOARJO\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
