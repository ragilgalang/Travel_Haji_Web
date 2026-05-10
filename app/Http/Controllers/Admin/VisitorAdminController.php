<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirebaseService;

class VisitorAdminController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function index(Request $request)
    {
        // Ambil data log pengunjung
        $logData = $this->firebase->getValue('visitor_log') ?? [];
        
        // Urutkan berdasarkan waktu (terbaru di atas)
        usort($logData, function($a, $b) {
            return strtotime($b['timestamp'] ?? '') - strtotime($a['timestamp'] ?? '');
        });

        // Filter waktu
        if ($request->filled('time')) {
            $time = $request->time;
            $now = \Carbon\Carbon::now();
            $logData = array_filter($logData, function($r) use ($time, $now) {
                if (empty($r['timestamp'])) return false;
                try {
                    $date = \Carbon\Carbon::parse($r['timestamp']);
                    if ($time == 'today') return $date->isToday();
                    if ($time == 'week') return $date->greaterThanOrEqualTo($now->copy()->subDays(7));
                    if ($time == 'month') return $date->greaterThanOrEqualTo($now->copy()->subMonth());
                } catch (\Exception $e) {
                    return true;
                }
                return true;
            });
        }

        // Tampilkan 100 kunjungan terakhir saja
        $visitors = array_slice($logData, 0, 100);

        return view('admin.visitors.index', compact('visitors'));
    }

    /**
     * Hapus semua log pengunjung
     */
    public function clear()
    {
        $this->firebase->deleteValue('visitor_log');
        $this->firebase->setValue('metrics/page_views', 0);
        
        // Bersihkan cache dashboard agar angka 0 langsung muncul
        \Illuminate\Support\Facades\Cache::flush();

        return redirect()->route('admin.visitors.index')->with('success', 'Seluruh riwayat pengunjung telah dihapus.');
    }
    /**
     * Sinkronkan angka total pengunjung di dashboard dengan jumlah di log detail
     */
    public function syncCounter()
    {
        $logData = $this->firebase->getValue('visitor_log') ?? [];
        $actualCount = count($logData);
        
        // Update angka di metrics
        $this->firebase->setValue('metrics/page_views', $actualCount);
        
        // Hapus cache dashboard agar langsung terupdate
        \Illuminate\Support\Facades\Cache::forget('dashboard_stats');

        return back()->with('success', "Angka pengunjung berhasil disinkronkan menjadi $actualCount.");
    }
}
