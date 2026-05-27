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
                $dbUser = null;
                try {
                    $dbUser = \Illuminate\Support\Facades\DB::table('users')->where('email', $user_data['email'] ?? null)->first();
                } catch (\Exception $e) {
                    \Log::warning('SQLite retrieveById error (ignored): ' . $e->getMessage());
                }
                return new User([
                    'uuid' => $identifier,
                    'email' => $user_data['email'] ?? null,
                    'username' => $user_data['username'] ?? null,
                    'name' => $user_data['name'] ?? 'Administrator',
                    'password' => $dbUser->password ?? ($user_data['password'] ?? null),
                    'role' => $dbUser ? $dbUser->role : ($user_data['role'] ?? 'user'),
                ]);
            }
        } catch (\Exception $e) {
            // Fallback to Firebase Auth if needed
        }

        try {
            $firebaseUser = $this->auth->getUser($identifier);
            $dbUser = null;
            try {
                $dbUser = \Illuminate\Support\Facades\DB::table('users')->where('email', $firebaseUser->email)->first();
            } catch (\Exception $e) {
                \Log::warning('SQLite retrieveById fallback error (ignored): ' . $e->getMessage());
            }
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
        $dbUser = null;
        try {
            $dbUser = \Illuminate\Support\Facades\DB::table('users')->where('id', $identifier)->orWhere('email', $identifier)->first();
        } catch (\Exception $e) {
            \Log::warning('SQLite retrieveById ultimate fallback error (ignored): ' . $e->getMessage());
        }
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

        // Generate toleransi email typos (e.g. .com <-> .con)
        $alternatives = [strtolower($loginValue)];
        if (str_ends_with(strtolower($loginValue), '@gmail.com')) {
            $alternatives[] = str_replace('@gmail.com', '@gmail.con', strtolower($loginValue));
        } elseif (str_ends_with(strtolower($loginValue), '@gmail.con')) {
            $alternatives[] = str_replace('@gmail.con', '@gmail.com', strtolower($loginValue));
        }

        try {
            $firebase = app(\App\Services\FirebaseService::class);
            $users = $firebase->getValue('users') ?? [];

            // Cari user berdasarkan email atau username di Firebase
            foreach ($users as $key => $data) {
                $dbEmail = isset($data['email']) ? strtolower($data['email']) : null;
                $dbUsername = isset($data['username']) ? strtolower($data['username']) : null;

                $matchEmail = false;
                foreach ($alternatives as $alt) {
                    if ($dbEmail === $alt) {
                        $matchEmail = true;
                        break;
                    }
                }

                $matchUser = false;
                foreach ($alternatives as $alt) {
                    if ($dbUsername === $alt) {
                        $matchUser = true;
                        break;
                    }
                }

                if ($matchEmail || $matchUser) {
                    $email = $data['email'] ?? null;
                    
                    $dbUser = null;
                    try {
                        $dbUser = \Illuminate\Support\Facades\DB::table('users')->where('email', $email)->first();
                    } catch (\Exception $e) {
                        \Log::warning('SQLite retrieveByCredentials database check error (ignored): ' . $e->getMessage());
                    }
                    
                    return new User([
                        'uuid' => $key,
                        'email' => $email,
                        'username' => $data['username'] ?? null,
                        'name' => $data['name'] ?? 'Administrator',
                        'password' => $dbUser->password ?? ($data['password'] ?? null),
                        'role' => $dbUser ? $dbUser->role : ($data['role'] ?? 'user'),
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Abaikan error Firebase dan lanjut cek ke SQLite
            \Log::warning('Firebase retrieveByCredentials error (ignored): ' . $e->getMessage());
        }

        // Fallback: Cari di database SQLite lokal
        $dbUser = null;
        try {
            $dbUser = \Illuminate\Support\Facades\DB::table('users')
                ->where('email', $loginValue)
                ->orWhere('username', $loginValue)
                ->first();
        } catch (\Exception $e) {
            \Log::warning('SQLite retrieveByCredentials ultimate fallback error (ignored): ' . $e->getMessage());
        }

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
        } catch (\Throwable $e) {
            // Password di database bukan format hash (mungkin plain text atau format lama)
            // Cek apakah password persis sama dengan ketikan (untuk backward compatibility)
            return $credentials['password'] === $user->getAuthPassword();
        }
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false) {}
}
