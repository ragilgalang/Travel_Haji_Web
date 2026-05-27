<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$fs = app(App\Services\FirebaseService::class);
$settings = $fs->getValue('settings') ?? [];
$settings['hero_bg_1'] = '/uploads/images/test_hero.jpg';

// Simulate a view render to verify the asset path!
$view = view('admin.settings', ['settings' => $settings])->render();

// Check if the asset() helper correctly prefixed the URL with the local domain
$success = strpos($view, 'src="http://localhost/Travel-Haji-lama/public/uploads/images/test_hero.jpg"') !== false;

echo $success ? "SUCCESS: The view is rendering the full XAMPP asset path properly!\n" : "FAILED: Could not find the expected asset path in the view.\n";
