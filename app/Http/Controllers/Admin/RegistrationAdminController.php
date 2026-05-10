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
        $all = $this->firebase->getValue('registrations') ?? [];

        // Ubah jadi array dengan key sebagai id
        $registrations = [];
        foreach ($all as $id => $data) {
            // Filter Arsip: Jika tidak meminta arsip, sembunyikan yang sudah diarsipkan
            $isArchived = $data['is_archived'] ?? false;
            if ($request->query('archived') == '1') {
                if (!$isArchived) continue;
            } else {
                if ($isArchived) continue;
            }
            
            $registrations[] = array_merge(['id' => $id], $data);
        }

        // Sort berdasarkan created_at (tanggal pendaftaran)
        $sortOrder = $request->query('sort', 'desc'); // default desc (Terbaru)
        usort($registrations, function($a, $b) use ($sortOrder) {
            $timeA = isset($a['created_at']) ? strtotime($a['created_at']) : 0;
            $timeB = isset($b['created_at']) ? strtotime($b['created_at']) : 0;
            
            if ($timeA == $timeB) {
                // Jika tanggal sama persis, gunakan fallback ke ID
                return $sortOrder === 'asc' ? strcmp($a['id'], $b['id']) : strcmp($b['id'], $a['id']);
            }
            
            if ($sortOrder === 'asc') {
                return $timeA <=> $timeB; // Terlama dulu
            }
            return $timeB <=> $timeA; // Terbaru dulu
        });

        // Filter status
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

        // Filter Tipe Paket (Haji/Umrah)
        if ($request->filled('type')) {
            $type = strtolower($request->type);
            $packages = collect($this->firebase->getValue('packages') ?? []);
            
            $registrations = array_filter($registrations, function($r) use ($type, $packages) {
                $packageName = $r['paket'] ?? ($r['dynamic_fields'][5]['value'] ?? '');
                $package = $packages->firstWhere('name', $packageName);
                
                if ($package) {
                    return strtolower($package['type'] ?? 'umrah') === $type;
                }
                
                // Fallback: cek keyword di nama paket jika data paket tidak ditemukan
                return str_contains(strtolower($packageName), $type);
            });
        }

        // Filter Waktu
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

        // Search
        if ($request->filled('q')) {
            $q = strtolower($request->q);
            $registrations = array_filter($registrations, function($r) use ($q) {
                return str_contains(strtolower($r['nama_lengkap'] ?? ''), $q)
                    || str_contains(strtolower($r['no_hp'] ?? ''), $q)
                    || str_contains(strtolower($r['ref_id'] ?? ''), $q)
                    || str_contains(strtolower($r['nik'] ?? ''), $q);
            });
        }

        $settings = $this->firebase->getValue('settings') ?? [];
        return view('admin.registrations.index', compact('registrations', 'settings'));
    }

    public function show($id)
    {
        $data = $this->firebase->getValue("registrations/{$id}");
        if (!$data) abort(404);
        $data['id'] = $id;
        $settings = $this->firebase->getValue('settings') ?? [];
        return view('admin.registrations.show', compact('data', 'settings'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|string']);
        $this->firebase->setValue("registrations/{$id}/status", $request->status);
        return back()->with('success', 'Status pendaftaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->firebase->getReference("registrations/{$id}")->remove();
        return redirect()->route('admin.registrations.index')->with('success', 'Data pendaftaran berhasil dihapus.');
    }

    public function archive($id)
    {
        $this->firebase->setValue("registrations/{$id}/is_archived", true);
        return back()->with('success', 'Data pendaftaran berhasil dipindahkan ke Arsip.');
    }

    public function unarchive($id)
    {
        $this->firebase->setValue("registrations/{$id}/is_archived", false);
        return back()->with('success', 'Data pendaftaran berhasil dikembalikan dari Arsip.');
    }

    public function archiveAllFinished()
    {
        $all = $this->firebase->getValue('registrations') ?? [];
        $count = 0;
        foreach ($all as $id => $data) {
            if (($data['status'] ?? '') === 'Selesai' && !($data['is_archived'] ?? false)) {
                $this->firebase->setValue("registrations/{$id}/is_archived", true);
                $count++;
            }
        }
        return back()->with('success', $count . ' data jemaah yang sudah Selesai telah dipindahkan ke Arsip.');
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

        return redirect()->route('admin.registrations.index')->with('success', count($ids) . ' data pendaftaran berhasil dihapus.');
    }

    public function bulkUpdateStatus(Request $request)
    {
        $ids = $request->ids;
        $status = $request->status;

        if (!$ids || !is_array($ids)) {
            return back()->with('error', 'Tidak ada data yang dipilih.');
        }

        if (!$status) {
            return back()->with('error', 'Status belum dipilih.');
        }

        foreach ($ids as $id) {
            $this->firebase->setValue("registrations/{$id}/status", $status);
        }

        return redirect()->route('admin.registrations.index')->with('success', count($ids) . ' status pendaftaran berhasil diperbarui.');
    }

    public function checkNew(Request $request)
    {
        $all = $this->firebase->getValue('registrations') ?? [];
        $lastId = $request->query('last_id');
        $newOnes = [];

        foreach ($all as $id => $data) {
            // Jika lastId kosong, kita ambil yang statusnya 'Menunggu Verifikasi'
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
