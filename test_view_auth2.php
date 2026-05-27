<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());
class DummyUser { public $role = 'admin'; public $name = 'Admin'; public function getName() { return $this->name; } }
Auth::shouldReceive('user')->andReturn(new DummyUser());
Auth::shouldReceive('check')->andReturn(true);
$fs = app(App\Services\FirebaseService::class);
$settings = $fs->getValue('settings') ?? [];
$settings['hero_bg_1'] = '/uploads/images/test_hero.jpg';
$view = view('admin.settings', ['settings' => $settings])->render();
preg_match('/<img src="([^"]+test_hero\.jpg)"/', $view, $matches);
echo isset($matches[1]) ? "FOUND: " . $matches[1] : "NOT FOUND";
