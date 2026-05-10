<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function show()
    {
        $settings = $this->firebase->getValue('settings') ?? [];
        $packages = $this->firebase->getValue('packages') ?? [];
        
        return view('register', compact('packages', 'settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:150',
            'nik' => 'required|numeric|digits:16',
            'hp' => 'required|string|max:20',
            'ttl' => 'required|string|max:100',
            'tgl' => 'required|date',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'required|string',
            'paket' => 'required|string',
            'kamar' => 'required|string',
            'catatan' => 'nullable|string',
            'wali' => 'required|string|max:150',
            
            'hubungan' => 'required|string|max:100',
            'hp_darurat' => 'required|string|max:20',
        ], [
            'required' => ':attribute wajib diisi.',
            'nik.digits' => 'NIK harus persis 16 digit angka.'
        ]);

        $data = [
            'nama_lengkap' => $request->nama,
            'nik' => $request->nik,
            'no_hp' => $request->hp,
            'ttl' => $request->ttl,
            'tgl' => $request->tgl,
            'gender' => $request->gender,
            'alamat' => $request->alamat,
            'paket' => $request->paket,
            'kamar' => $request->kamar,
            'catatan' => $request->catatan ?? '-',
            'wali' => $request->wali,
            'hubungan' => $request->hubungan,
            'hp_darurat' => $request->hp_darurat,
            'status' => 'Menunggu Verifikasi',
            'created_at' => now()->toDateTimeString(),
            'ref_id' => 'REG-' . strtoupper(Str::random(8))
        ];

        // Format dynamic fields for backward compatibility with the admin registrations view
        $formattedDynamicFields = [];
        $labels = [
            'nik' => 'NIK (16 Digit)',
            'ttl' => 'Tempat Lahir',
            'tgl' => 'Tanggal Lahir',
            'gender' => 'Jenis Kelamin',
            'alamat' => 'Alamat Lengkap',
            'paket' => 'Paket Dipilih',
            'kamar' => 'Tipe Kamar',
            'catatan' => 'Catatan Khusus',
            'wali' => 'Nama Keluarga/Wali',
            'hubungan' => 'Hubungan Wali',
            'hp_darurat' => 'No. HP Darurat'
        ];

        foreach($labels as $key => $label) {
            $formattedDynamicFields[] = [
                'label' => $label,
                'value' => $data[$key],
                'type' => 'text'
            ];
        }
        $data['dynamic_fields'] = $formattedDynamicFields;

        // Simpan ke Firebase node 'registrations' (bukan pendaftaran agar sinkron dgn Admin Panel)
        $this->firebase->getReference('registrations')->push($data);

        return redirect()->route('register.success')
            ->with('nama', $request->nama)
            ->with('ref_id', $data['ref_id']);
    }

    public function success()
    {
        $settings = $this->firebase->getValue('settings') ?? [];
        return view('register-success', compact('settings'));
    }

    public function checkStatus(Request $request)
    {
        $refId = strtoupper($request->ref_id);
        if (!$refId) {
            return response()->json(['success' => false, 'message' => 'Masukkan nomor referensi.']);
        }

        $all = $this->firebase->getValue('registrations') ?? [];
        $found = null;
        foreach ($all as $data) {
            if (isset($data['ref_id']) && strtoupper($data['ref_id']) === $refId) {
                $found = $data;
                break;
            }
        }

        if ($found) {
            return response()->json([
                'success' => true,
                'status' => $found['status'] ?? 'Menunggu Verifikasi',
                'nama' => $found['nama_lengkap'] ?? 'Jemaah',
                'tgl' => isset($found['created_at']) ? date('d M Y', strtotime($found['created_at'])) : '-'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Nomor referensi tidak ditemukan.']);
    }
}
