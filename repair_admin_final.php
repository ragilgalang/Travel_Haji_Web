<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\FirebaseService;
use Illuminate\Support\Facades\Hash;
use Kreait\Firebase\Contract\Auth;

$auth = app(Auth::class);
$firebase = app(FirebaseService::class);

$email = 'admin123@gmail.com';
$password = 'admin123';
$name = 'Administrator';

echo "--- STARTING ADMIN REPAIR ---\n";

// 1. Sync with Firebase Auth
try {
    try {
        $user = $auth->getUserByEmail($email);
        $auth->updateUser($user->uid, [
            'password' => $password,
            'displayName' => $name
        ]);
        echo "[1/2] Firebase Auth updated for: $email\n";
        $uid = $user->uid;
    } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
        $user = $auth->createUser([
            'email' => $email,
            'password' => $password,
            'displayName' => $name,
            'emailVerified' => true
        ]);
        echo "[1/2] Firebase Auth user created for: $email\n";
        $uid = $user->uid;
    }
} catch (\Exception $e) {
    die("FATAL ERROR (Auth): " . $e->getMessage() . "\n");
}

// 2. Sync with Realtime Database (RTDB)
try {
    // We use a predictable key for admin, OR the UID. 
    // In our provider, it iterates over all users or uses ID if known.
    // Let's use the UID as the key to stay consistent with Auth, or 'admin_...' as used in seeder.
    // The provider retrieveByCredentials iterates, so key name doesn't matter much for login.
    $userKey = 'admin_' . str_replace(['@', '.'], '_', $email);
    
    $adminData = [
        'email' => $email,
        'password' => Hash::make($password), // Laravel Hash for validateCredentials
        'name' => $name,
        'role' => 'admin',
        'updated_at' => now()->toDateTimeString()
    ];

    $firebase->setValue('users/' . $userKey, $adminData);
    echo "[2/2] Realtime Database 'users' node updated for: $email\n";

} catch (\Exception $e) {
    die("FATAL ERROR (RTDB): " . $e->getMessage() . "\n");
}

echo "--- REPAIR COMPLETE ---\n";
echo "Email: $email\n";
echo "Password: $password\n";
echo "-----------------------\n";
