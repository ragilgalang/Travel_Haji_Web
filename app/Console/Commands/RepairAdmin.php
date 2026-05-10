<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Hash;

class RepairAdmin extends Command
{
    protected $signature = 'admin:repair';
    protected $description = 'Repair admin password in Firebase Realtime Database';

    public function handle()
    {
        $firebase = app(FirebaseService::class);
        $users = $firebase->getValue('users') ?? [];
        $targetEmail = 'admin123@gmail.com';
        
        $this->info("Mencari user: " . $targetEmail);
        
        $found = false;
        foreach ($users as $key => $u) {
            if (isset($u['email']) && $u['email'] === $targetEmail) {
                // Set password baru: admin123
                $u['password'] = Hash::make('admin123');
                
                // Simpan kembali ke Firebase
                $firebase->setValue('users/' . $key, $u);
                
                $this->info("--------------------------------------------------");
                $this->info("BERHASIL! Akun Anda telah diperbaiki.");
                $this->info("Email: " . $targetEmail);
                $this->info("Password Baru: admin123");
                $this->info("--------------------------------------------------");
                $found = true;
                break;
            }
        }

        if (!$found) {
            $this->error("GAGAL: Email $targetEmail tidak ditemukan di database.");
        }
        
        return 0;
    }
}
