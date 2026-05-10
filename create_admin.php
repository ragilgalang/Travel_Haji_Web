<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$auth = app('firebase.auth');

$email = 'admin123@gmail.com';
$password = 'admin123';

try {
    $user = $auth->getUserByEmail($email);
    echo "Akun admin sudah ada di Firebase Auth! \nEmail: $email \nPassword: $password (default)\n";
} catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
    $userProperties = [
        'email' => $email,
        'emailVerified' => true,
        'password' => $password,
        'displayName' => 'Admin TravelHaji',
        'disabled' => false,
    ];

    $createdUser = $auth->createUser($userProperties);
    echo "Akun admin berhasil dibuat di Firebase Auth!\nMembuat admin:\nEmail: $email\nPassword: $password\n";
} catch (\Exception $e) {
    echo "Terjadi kesalahan: " . $e->getMessage() . "\n";
}
