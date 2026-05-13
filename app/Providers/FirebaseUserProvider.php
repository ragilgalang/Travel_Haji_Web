<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Models\User;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;

class FirebaseUserProvider implements UserProvider
{
    protected $auth;

    public function __construct(FirebaseAuth $auth)
    {
        $this->auth = $auth;
    }

    public function retrieveById($identifier)
    {
        try {
            $firebase = app(\App\Services\FirebaseService::class);
            $user_data = $firebase->getValue('users/' . $identifier);
            
            if ($user_data) {
                $dbUser = \Illuminate\Support\Facades\DB::table('users')->where('email', $user_data['email'] ?? null)->first();
                return new User([
                    'uuid' => $identifier,
                    'email' => $user_data['email'] ?? null,
                    'username' => $user_data['username'] ?? null,
                    'name' => $user_data['name'] ?? 'Administrator',
                    'password' => $user_data['password'] ?? null,
                    'role' => $dbUser ? $dbUser->role : ($user_data['role'] ?? 'user'),
                ]);
            }
        } catch (\Exception $e) {
            // Fallback to Firebase Auth if needed
        }

        try {
            $firebaseUser = $this->auth->getUser($identifier);
            $dbUser = \Illuminate\Support\Facades\DB::table('users')->where('email', $firebaseUser->email)->first();
            return new User([
                'uuid' => $firebaseUser->uid,
                'email' => $firebaseUser->email,
                'name' => $firebaseUser->displayName,
                'role' => $dbUser ? $dbUser->role : 'user',
            ]);
        } catch (\Exception $e) {
            // Ignore Firebase Auth error
        }

        // Fallback: Cari di database SQLite lokal
        $dbUser = \Illuminate\Support\Facades\DB::table('users')->where('id', $identifier)->orWhere('email', $identifier)->first();
        if ($dbUser) {
            return new User([
                'uuid' => $dbUser->id,
                'email' => $dbUser->email,
                'name' => $dbUser->name,
                'password' => $dbUser->password,
                'role' => $dbUser->role ?? 'user',
            ]);
        }

        return null;
    }

    public function retrieveByToken($identifier, $token) { return null; }
    public function updateRememberToken(Authenticatable $user, $token) {}
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials)) {
            return null;
        }

        // Ambil nilai identifier (bisa dari key 'email' atau 'username')
        $loginValue = $credentials['email'] ?? $credentials['username'] ?? null;
        
        if (!$loginValue) {
            return null;
        }

        try {
            $firebase = app(\App\Services\FirebaseService::class);
            $users = $firebase->getValue('users') ?? [];

            // Cari user berdasarkan email atau username di Firebase
            foreach ($users as $key => $data) {
                $matchEmail = isset($data['email']) && strtolower($data['email']) === strtolower($loginValue);
                $matchUser  = isset($data['username']) && strtolower($data['username']) === strtolower($loginValue);

                if ($matchEmail || $matchUser) {
                    $email = $data['email'] ?? null;
                    $dbUser = \Illuminate\Support\Facades\DB::table('users')->where('email', $email)->first();
                    
                    return new User([
                        'uuid' => $key,
                        'email' => $email,
                        'username' => $data['username'] ?? null,
                        'name' => $data['name'] ?? 'Administrator',
                        'password' => $data['password'] ?? null,
                        'role' => $dbUser ? $dbUser->role : ($data['role'] ?? 'user'),
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Abaikan error Firebase dan lanjut cek ke SQLite
        }

        // Fallback: Cari di database SQLite lokal
        $dbUser = \Illuminate\Support\Facades\DB::table('users')
            ->where('email', $loginValue)
            ->orWhere('username', $loginValue)
            ->first();

        if ($dbUser) {
            return new User([
                'uuid' => $dbUser->id,
                'email' => $dbUser->email,
                'username' => $dbUser->username ?? null,
                'name' => $dbUser->name,
                'password' => $dbUser->password,
                'role' => $dbUser->role ?? 'user',
            ]);
        }

        return null;
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        if (!$user->getAuthPassword()) {
            return false;
        }

        try {
            return \Illuminate\Support\Facades\Hash::check($credentials['password'], $user->getAuthPassword());
        } catch (\RuntimeException $e) {
            // Password di database bukan format hash (mungkin plain text atau format lama)
            // Cek apakah password persis sama dengan ketikan (untuk backward compatibility)
            return $credentials['password'] === $user->getAuthPassword();
        }
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false) {}
}
