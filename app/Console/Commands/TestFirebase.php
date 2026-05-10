<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestFirebase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-firebase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test connection to Firebase Realtime Database';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\FirebaseService $firebase)
    {
        $this->info('Testing connection to Firebase...');
        try {
            $testData = [
                'status' => 'connected',
                'timestamp' => now()->toDateTimeString(),
                'app_name' => config('app.name')
            ];
            
            $this->comment('Mencoba menulis data ke path: test_connection...');
            $firebase->setValue('test_connection', $testData);
            
            $this->comment('Mencoba membaca data kembali...');
            $value = $firebase->getValue('test_connection');
            
            if ($value && $value['status'] === 'connected') {
                $this->info('✅ BERHASIL TERHUBUNG KE FIREBASE!');
                $this->line('Data terbaca: ' . json_encode($value));
                return 0;
            } else {
                $this->error('❌ Gagal memvalidasi data yang terbaca.');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ KONEKSI GAGAL!');
            $this->line('Pesan Error: ' . $e->getMessage());
            $this->line('Pastikan file JSON kredensial ada di path: ' . config('firebase.projects.app.credentials'));
            $this->line('Pastikan Database URL sudah benar: ' . config('firebase.projects.app.database.url'));
            return 1;
        }
    }
}
