<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirebaseService;

class RegistrationAdminController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function index(Request $request)
    {
        // ==========================================
        // AUTO CLEANUP: Hapus permanen data di sampah jika sudah > 1 bulan
        // ==========================================
        $all = $this->getFirebaseData('admin_registrations_list', 10, function() {
            return $this->firebase->getValue('registrations') ?? [];
        });
        $oneMonthAgo = now()->subMonth();
        $cleanedAny = false;
        foreach ($all as $id => $data) {
            if ($data['is_trashed'] ?? false) {
                $trashedAt = isset($data['trashed_at']) ? \Carbon\Carbon::parse($data['trashed_at']) : null;
                if ($trashedAt && $trashedAt->lessThan($oneMonthAgo)) {
                    $this->firebase->getReference("registrations/{$id}")->remove();
                    $cleanedAny = true;
                }
            }
        }
        if ($cleanedAny) {
            \Illuminate\Support\Facades\Cache::forget('admin_registrations_list');
            $all = $this->getFirebaseData('admin_registrations_list', 10, function() {
                return $this->firebase->getValue('registrations') ?? [];
            });
        }

        $registrations = [];
        foreach ($all as $id => $data) {
            $isArchived = $data['is_archived'] ?? false;
            $isTrashed = $data['is_trashed'] ?? false;

            if ($request->query('trashed') == '1') {
                if (!$isTrashed) continue;
            } elseif ($request->query('archived') == '1') {
                if (!$isArchived || $isTrashed) continue;
            } else {
                if ($isArchived || $isTrashed) continue;
            }
            
            $registrations[] = array_merge(['id' => $id], $data);
        }

        $sortOrder = $request->query('sort', 'desc');
        usort($registrations, function($a, $b) use ($sortOrder) {
            $timeA = isset($a['created_at']) ? strtotime($a['created_at']) : 0;
            $timeB = isset($b['created_at']) ? strtotime($b['created_at']) : 0;
            
            if ($timeA == $timeB) {
                return $sortOrder === 'asc' ? strcmp($a['id'], $b['id']) : strcmp($b['id'], $a['id']);
            }
            
            if ($sortOrder === 'asc') {
                return $timeA <=> $timeB;
            }
            return $timeB <=> $timeA;
        });

        if ($request->filled('status')) {
            $status = $request->status;
            $registrations = array_filter($registrations, function($r) use ($status) {
                $currentStatus = $r['status'] ?? 'Menunggu Verifikasi';
                if ($status === 'Menunggu Verifikasi') {
                    return $currentStatus === 'Menunggu Verifikasi' || $currentStatus === 'Baru';
                }
                return $currentStatus === $status;
            });
        }

        if ($request->filled('type')) {
            $type = strtolower($request->type);
            $packages = collect($this->getFirebaseData('firebase_packages', 30, function() {
                return $this->firebase->getValue('packages') ?? [];
            }));
            
            $registrations = array_filter($registrations, function($r) use ($type, $packages) {
                $packageName = $r['paket'] ?? ($r['dynamic_fields'][5]['value'] ?? '');
                $package = $packages->firstWhere('name', $packageName);
                
                if ($package) {
                    return strtolower($package['type'] ?? 'umrah') === $type;
                }
                
                return str_contains(strtolower($packageName), $type);
            });
        }

        if ($request->filled('time')) {
            $time = $request->time;
            $now = \Carbon\Carbon::now();
            $registrations = array_filter($registrations, function($r) use ($time, $now) {
                if (empty($r['created_at'])) return false;
                try {
                    $date = \Carbon\Carbon::parse($r['created_at']);
                    if ($time == 'today') return $date->isSameDay($now);
                    if ($time == 'week') return $date->greaterThanOrEqualTo($now->copy()->subDays(7)->startOfDay());
                    if ($time == 'month') return $date->greaterThanOrEqualTo($now->copy()->subMonth()->startOfDay());
                    if ($time == '3months') return $date->greaterThanOrEqualTo($now->copy()->subMonths(3)->startOfDay());
                    if ($time == '6months') return $date->greaterThanOrEqualTo($now->copy()->subMonths(6)->startOfDay());
                    if ($time == 'year') return $date->isSameYear($now);
                } catch (\Exception $e) {
                    return true;
                }
                return true;
            });
        }

        if ($request->filled('q')) {
            $q = strtolower($request->q);
            $registrations = array_filter($registrations, function($r) use ($q) {
                return str_contains(strtolower($r['nama_lengkap'] ?? ''), $q)
                    || str_contains(strtolower($r['no_hp'] ?? ''), $q)
                    || str_contains(strtolower($r['ref_id'] ?? ''), $q)
                    || str_contains(strtolower($r['nik'] ?? ''), $q);
            });
        }

        $settings = $this->getFirebaseData('site_settings', 300, function() {
            return $this->firebase->getValue('settings') ?? [];
        });
        return view('admin.registrations.index', compact('registrations', 'settings'));
    }

    public function show($id)
    {
        $registrations = $this->getFirebaseData('admin_registrations_list', 10, function() {
            return $this->firebase->getValue('registrations') ?? [];
        });
        $data = $registrations[$id] ?? null;
        if (!$data) {
            $data = $this->firebase->getValue("registrations/{$id}");
            if (!$data) abort(404);
        }
        $data['id'] = $id;
        $settings = $this->getFirebaseData('site_settings', 300, function() {
            return $this->firebase->getValue('settings') ?? [];
        });
        return view('admin.registrations.show', compact('data', 'settings'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:Menunggu Verifikasi,Sedang Diproses,Sudah Dikonfirmasi,Selesai,Berangkat,Dibatalkan,Baru,Diproses,Ditolak'
        ]);
        
        $this->firebase->setValue("registrations/{$id}/status", $request->status);
        \Illuminate\Support\Facades\Cache::forget('admin_registrations_list');
        
        if ($request->status === 'Dibatalkan') {
            $this->firebase->setValue("registrations/{$id}/is_trashed", true);
            return back()->with('success', 'Status diubah ke Dibatalkan dan data dipindahkan ke sampah.');
        }
        
        return back()->with('success', 'Status pendaftaran berhasil diperbarui.');
    }

    public function archive($id)
    {
        $this->firebase->setValue("registrations/{$id}/is_archived", true);
        \Illuminate\Support\Facades\Cache::forget('admin_registrations_list');
        return back()->with('success', 'Data pendaftaran berhasil dipindahkan ke Arsip.');
    }

    public function unarchive($id)
    {
        $this->firebase->setValue("registrations/{$id}/is_archived", false);
        \Illuminate\Support\Facades\Cache::forget('admin_registrations_list');
        return back()->with('success', 'Data pendaftaran berhasil dikembalikan dari Arsip.');
    }

    public function moveToTrash($id)
    {
        $this->firebase->setValue("registrations/{$id}/is_trashed", true);
        $this->firebase->setValue("registrations/{$id}/trashed_at", now()->toDateTimeString());
        \Illuminate\Support\Facades\Cache::forget('admin_registrations_list');
        return back()->with('success', 'Data berhasil dipindahkan ke Sampah.');
    }

    public function trashIndex(Request $request)
    {
        // ==========================================
        // AUTO CLEANUP: Hapus permanen data di sampah jika sudah > 1 bulan
        // ==========================================
        $all = $this->getFirebaseData('admin_registrations_list', 10, function() {
            return $this->firebase->getValue('registrations') ?? [];
        });
        $oneMonthAgo = now()->subMonth();
        $cleanedAny = false;
        foreach ($all as $id => $data) {
            if ($data['is_trashed'] ?? false) {
                $trashedAt = isset($data['trashed_at']) ? \Carbon\Carbon::parse($data['trashed_at']) : null;
                if ($trashedAt && $trashedAt->lessThan($oneMonthAgo)) {
                    $this->firebase->getReference("registrations/{$id}")->remove();
                    $cleanedAny = true;
                }
            }
        }
        if ($cleanedAny) {
            \Illuminate\Support\Facades\Cache::forget('admin_registrations_list');
            $all = $this->getFirebaseData('admin_registrations_list', 10, function() {
                return $this->firebase->getValue('registrations') ?? [];
            });
        }

        $trashed = [];
        foreach ($all as $id => $data) {
            if (!($data['is_trashed'] ?? false)) continue;
            $trashed[] = array_merge(['id' => $id], $data);
        }

        // Sort by trashed_at desc
        usort($trashed, function($a, $b) {
            $timeA = isset($a['trashed_at']) ? strtotime($a['trashed_at']) : 0;
            $timeB = isset($b['trashed_at']) ? strtotime($b['trashed_at']) : 0;
            return $timeB <=> $timeA;
        });

        // Search
        if ($request->filled('q')) {
            $q = strtolower($request->q);
            $trashed = array_filter($trashed, function($r) use ($q) {
                return str_contains(strtolower($r['nama_lengkap'] ?? ''), $q)
                    || str_contains(strtolower($r['no_hp'] ?? ''), $q)
                    || str_contains(strtolower($r['ref_id'] ?? ''), $q);
            });
        }

        $settings = $this->getFirebaseData('site_settings', 300, function() {
            return $this->firebase->getValue('settings') ?? [];
        });
        return view('admin.registrations.trash', compact('trashed', 'settings'));
    }

    public function restore($id)
    {
        $this->firebase->setValue("registrations/{$id}/is_trashed", false);
        $this->firebase->setValue("registrations/{$id}/trashed_at", null);
        $this->firebase->setValue("registrations/{$id}/status", 'Menunggu Verifikasi');
        \Illuminate\Support\Facades\Cache::forget('admin_registrations_list');
        return back()->with('success', 'Data berhasil dipulihkan ke daftar pendaftaran.');
    }

    public function bulkRestore(Request $request)
    {
        $ids = $request->ids;
        if (!$ids || !is_array($ids)) {
            return back()->with('error', 'Tidak ada data yang dipilih.');
        }
        foreach ($ids as $id) {
            $this->firebase->setValue("registrations/{$id}/is_trashed", false);
            $this->firebase->setValue("registrations/{$id}/trashed_at", null);
            $this->firebase->setValue("registrations/{$id}/status", 'Menunggu Verifikasi');
        }
        \Illuminate\Support\Facades\Cache::forget('admin_registrations_list');
        return back()->with('success', count($ids) . ' data berhasil dipulihkan ke daftar pendaftaran.');
    }

    public function forceDelete($id)
    {
        $data = $this->firebase->getValue("registrations/{$id}");
        if (!$data) return back()->with('error', 'Data tidak ditemukan.');
        $this->firebase->getReference("registrations/{$id}")->remove();
        \Illuminate\Support\Facades\Cache::forget('admin_registrations_list');
        return back()->with('success', 'Data berhasil dihapus permanen.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        if (!$ids || !is_array($ids)) {
            return back()->with('error', 'Tidak ada data yang dipilih.');
        }
        foreach ($ids as $id) {
            $this->firebase->getReference("registrations/{$id}")->remove();
        }
        \Illuminate\Support\Facades\Cache::forget('admin_registrations_list');
        return back()->with('success', count($ids) . ' data berhasil dihapus permanen dari Sampah.');
    }

    public function emptyTrash()
    {
        $all = $this->getFirebaseData('admin_registrations_list', 10, function() {
            return $this->firebase->getValue('registrations') ?? [];
        });
        $count = 0;
        foreach ($all as $id => $data) {
            if ($data['is_trashed'] ?? false) {
                $this->firebase->getReference("registrations/{$id}")->remove();
                $count++;
            }
        }
        \Illuminate\Support\Facades\Cache::forget('admin_registrations_list');
        return redirect()->route('admin.registrations.trash')->with('success', $count . ' data Sampah berhasil dikosongkan permanen.');
    }

    public function archiveAllFinished()
    {
        $all = $this->getFirebaseData('admin_registrations_list', 10, function() {
            return $this->firebase->getValue('registrations') ?? [];
        });
        $count = 0;
        foreach ($all as $id => $data) {
            if (($data['status'] ?? '') === 'Selesai' && !($data['is_archived'] ?? false)) {
                $this->firebase->setValue("registrations/{$id}/is_archived", true);
                $count++;
            }
        }
        \Illuminate\Support\Facades\Cache::forget('admin_registrations_list');
        return back()->with('success', $count . ' data jemaah yang sudah Selesai telah dipindahkan ke Arsip.');
    }

    public function bulkUpdateStatus(Request $request)
    {
        $ids = $request->ids;
        $status = $request->status;

        if (!$ids || !is_array($ids)) {
            return back()->with('error', 'Tidak ada data yang dipilih.');
        }

        $allowedStatus = ['Menunggu Verifikasi', 'Sedang Diproses', 'Sudah Dikonfirmasi', 'Selesai', 'Berangkat', 'Dibatalkan', 'Baru', 'Diproses', 'Ditolak'];
        if (!$status || !in_array($status, $allowedStatus)) {
            return back()->with('error', 'Status tidak valid.');
        }

        foreach ($ids as $id) {
            $this->firebase->setValue("registrations/{$id}/status", $status);
            if ($status === 'Dibatalkan') {
                $this->firebase->setValue("registrations/{$id}/is_trashed", true);
            }
        }

        \Illuminate\Support\Facades\Cache::forget('admin_registrations_list');
        return redirect()->route('admin.registrations.index')->with('success', count($ids) . ' status pendaftaran berhasil diperbarui.');
    }

    public function checkNew(Request $request)
    {
        $all = $this->getFirebaseData('admin_registrations_list', 10, function() {
            return $this->firebase->getValue('registrations') ?? [];
        });
        $lastId = $request->query('last_id');
        $newOnes = [];

        foreach ($all as $id => $data) {
            if (!$lastId) {
                if (($data['status'] ?? '') === 'Menunggu Verifikasi') {
                    $newOnes[] = array_merge(['id' => $id], $data);
                }
            } else {
                if ($id > $lastId) {
                    $newOnes[] = array_merge(['id' => $id], $data);
                }
            }
        }

        $latestId = $lastId;
        if (!empty($all)) {
            $keys = array_keys($all);
            sort($keys);
            $latestId = end($keys);
        }

        return response()->json([
            'new_count' => count($newOnes),
            'latest_data' => count($newOnes) > 0 ? $newOnes[0] : null,
            'latest_id' => $latestId
        ]);
    }
}
