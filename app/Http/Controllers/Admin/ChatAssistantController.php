<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirebaseService;

class ChatAssistantController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function index(Request $request)
    {
        $all = $this->getFirebaseData('admin_registrations_list', 10, function() {
            return $this->firebase->getValue('registrations') ?? [];
        });
        
        $registrations = [];
        foreach ($all as $id => $data) {
            // Hanya tampilkan yang tidak diarsipkan dan statusnya 'Diproses'
            if (!($data['is_archived'] ?? false) && ($data['status'] ?? '') === 'Diproses') {
                $registrations[] = array_merge(['id' => $id], $data);
            }
        }

        // Search
        if ($request->filled('q')) {
            $q = strtolower($request->q);
            $registrations = array_filter($registrations, function($r) use ($q) {
                return str_contains(strtolower($r['nama_lengkap'] ?? ''), $q) || 
                       str_contains(strtolower($r['no_hp'] ?? ''), $q);
            });
        }

        // Sort by newest
        usort($registrations, function($a, $b) {
            return ($b['created_at'] ?? '') <=> ($a['created_at'] ?? '');
        });

        return view('admin.chat.index', compact('registrations'));
    }
}
