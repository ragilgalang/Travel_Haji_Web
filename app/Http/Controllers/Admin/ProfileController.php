<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function index()
    {
        // Ambil riwayat login (5 terakhir)
        $historyData = $this->firebase->getValue('login_history') ?? [];

        // Urutkan berdasarkan waktu (terbaru di atas)
        usort($historyData, function ($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        $loginHistory = array_slice($historyData, 0, 5);

        return view('admin.profile', compact('loginHistory'));
    }

    public function update(Request $request)
    {
        $userKey = Auth::user()->uuid;
        $adminData = $this->firebase->getValue('users/' . $userKey) ?? [];

        $request->validate([
            'admin_email' => 'required|email',
            'admin_username' => 'required|string|min:3',
            'current_password' => 'required',
            'admin_password' => 'nullable|min:6'
        ], [
            'admin_email.required' => 'Alamat email wajib diisi.',
            'admin_email.email' => 'Format email tidak valid.',
            'admin_username.required' => 'Username wajib diisi.',
            'admin_username.min' => 'Username minimal harus 3 karakter.',
            'current_password.required' => 'Password saat ini wajib diisi untuk verifikasi.',
            'admin_password.min' => 'Password baru minimal harus 8 karakter.'
        ]);

        // Verifikasi password lama
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.'])->withInput();
        }

        if (!empty($request->admin_email)) {
            $adminData['email'] = $request->admin_email;
        }

        if (!empty($request->admin_username)) {
            $adminData['username'] = $request->admin_username;
        }

        if (!empty($request->admin_password)) {
            $adminData['password'] = Hash::make($request->admin_password);
        }

        // Simpan ke Firebase
        $this->firebase->setValue('users/' . $userKey, $adminData);

        // Sync ke SQLite lokal juga agar login lancar
        \Illuminate\Support\Facades\DB::table('users')->updateOrInsert(
            ['email' => $request->admin_email],
            [
                'username' => $request->admin_username,
                'name' => $adminData['name'] ?? Auth::user()->name,
                'role' => Auth::user()->role,
            ]
        );

        return back()->with('success', 'Profil dan kredensial akses berhasil diperbarui.');
    }
}
