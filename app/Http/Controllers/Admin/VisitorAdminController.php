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
        $allLogData = $this->firebase->getValue('visitor_log') ?? [];
        
        // Urutkan berdasarkan waktu (terbaru di atas)
        usort($allLogData, function($a, $b) {
            return strtotime($b['timestamp'] ?? '') - strtotime($a['timestamp'] ?? '');
        });

        $now = \Carbon\Carbon::now();
        $timeFilter = $request->input('time', '');

        // ========== FILTER WAKTU ==========
        if ($timeFilter) {
            $logData = array_values(array_filter($allLogData, function($r) use ($timeFilter, $now) {
                if (empty($r['timestamp'])) return false;
                try {
                    $date = \Carbon\Carbon::parse($r['timestamp']);
                    if ($timeFilter == 'today') return $date->isToday();
                    if ($timeFilter == 'week') return $date->greaterThanOrEqualTo($now->copy()->subDays(7));
                    if ($timeFilter == 'month') return $date->greaterThanOrEqualTo($now->copy()->subMonth());
                } catch (\Exception $e) {
                    return true;
                }
                return true;
            }));
        } else {
            $logData = $allLogData;
        }

        // ========== DATA CHART (dari data YANG SUDAH DIFILTER) ==========
        
        // Tentukan rentang hari berdasarkan filter
        $daysBack = match($timeFilter) {
            'today' => 1,
            'week'  => 7,
            'month' => 30,
            default => 14,
        };

        // 1. Kunjungan per hari atau per jam (jika today)
        $dailyLabels = [];
        $dailyCounts = [];

        if ($timeFilter == 'today') {
            // Breakdown per JAM (00:00 sampai jam sekarang)
            $currentHour = (int)$now->format('H');
            for ($i = 0; $i <= $currentHour; $i++) {
                $hourLabel = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                $dailyLabels[] = $hourLabel;
                
                $count = 0;
                foreach ($logData as $v) {
                    if (!empty($v['timestamp'])) {
                        try {
                            $ts = \Carbon\Carbon::parse($v['timestamp']);
                            if ($ts->isToday() && (int)$ts->format('H') == $i) {
                                $count++;
                            }
                        } catch (\Exception $e) {}
                    }
                }
                $dailyCounts[] = $count;
            }
        } else {
            // Breakdown per HARI
            for ($i = $daysBack - 1; $i >= 0; $i--) {
                $date = $now->copy()->subDays($i);
                $dailyLabels[] = $date->format('d M');
                $count = 0;
                foreach ($logData as $v) {
                    if (!empty($v['timestamp'])) {
                        try {
                            if (\Carbon\Carbon::parse($v['timestamp'])->isSameDay($date)) $count++;
                        } catch (\Exception $e) {}
                    }
                }
                $dailyCounts[] = $count;
            }
        }

        // 2. Breakdown perangkat (Mobile vs Desktop)
        $mobileCount = 0;
        $desktopCount = 0;
        foreach ($logData as $v) {
            $ua = $v['user_agent'] ?? '';
            if (preg_match('/Mobile|Android|iPhone/i', $ua)) {
                $mobileCount++;
            } else {
                $desktopCount++;
            }
        }

        // 3. Distribusi browser
        $browsers = ['Chrome' => 0, 'Safari' => 0, 'Firefox' => 0, 'Edge' => 0, 'Lainnya' => 0];
        foreach ($logData as $v) {
            $ua = $v['user_agent'] ?? '';
            if (strpos($ua, 'Edg') !== false) $browsers['Edge']++;
            elseif (strpos($ua, 'Chrome') !== false) $browsers['Chrome']++;
            elseif (strpos($ua, 'Safari') !== false) $browsers['Safari']++;
            elseif (strpos($ua, 'Firefox') !== false) $browsers['Firefox']++;
            else $browsers['Lainnya']++;
        }

        // 4. Statistik ringkas (dari data terfilter)
        $totalVisitors = count($logData);
        $todayCount = 0;
        $weekCount = 0;
        foreach ($logData as $v) {
            if (!empty($v['timestamp'])) {
                try {
                    $ts = \Carbon\Carbon::parse($v['timestamp']);
                    if ($ts->isToday()) $todayCount++;
                    if ($ts->greaterThanOrEqualTo($now->copy()->subDays(7))) $weekCount++;
                } catch (\Exception $e) {}
            }
        }

        // 5. Unique IPs
        $uniqueIPs = count(array_unique(array_filter(array_column($logData, 'ip'))));

        // Label filter aktif
        $filterLabel = match($timeFilter) {
            'today' => 'Hari Ini',
            'week'  => '7 Hari Terakhir',
            'month' => '30 Hari Terakhir',
            default => '14 Hari Terakhir',
        };

        $chartData = [
            'dailyLabels'   => $dailyLabels,
            'dailyCounts'   => $dailyCounts,
            'mobileCount'   => $mobileCount,
            'desktopCount'  => $desktopCount,
            'browsers'      => $browsers,
            'totalVisitors'  => $totalVisitors,
            'todayCount'    => $todayCount,
            'weekCount'     => $weekCount,
            'uniqueIPs'     => $uniqueIPs,
            'filterLabel'   => $filterLabel,
        ];

        // Tampilkan 100 kunjungan terakhir saja
        $visitors = array_slice($logData, 0, 100);

        return view('admin.visitors.index', compact('visitors', 'chartData'));
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
