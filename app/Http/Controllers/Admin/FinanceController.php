<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Cache;

class FinanceController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function index(Request $request)
    {
        $registrations = collect($this->firebase->getValue('registrations') ?? []);
        $packages = collect($this->firebase->getValue('packages') ?? []);

        // Filter pendaftaran yang sudah dikonfirmasi/lunas
        $confirmedRegs = $registrations->filter(function($reg) {
            $status = $reg['status'] ?? '';
            return in_array($status, ['Confirmed', 'Selesai', 'Berhasil']);
        });

        // Filter Tipe Paket jika ada request
        if ($request->filled('type')) {
            $typeFilter = strtolower($request->type);
            $confirmedRegs = $confirmedRegs->filter(function($reg) use ($packages, $typeFilter) {
                $packageName = $reg['paket'] ?? '';
                $package = $packages->firstWhere('name', $packageName);
                if ($package) {
                    return strtolower($package['type'] ?? 'umrah') === $typeFilter;
                }
                return false;
            });
        }

        // Filter Waktu
        if ($request->filled('time')) {
            $time = $request->time;
            $now = now();
            
            $confirmedRegs = $confirmedRegs->filter(function($reg) use ($time, $now) {
                $createdAt = \Carbon\Carbon::parse($reg['created_at'] ?? now());
                
                switch($time) {
                    case '7days': return $createdAt->greaterThanOrEqualTo($now->copy()->subDays(7));
                    case '1month': return $createdAt->greaterThanOrEqualTo($now->copy()->subMonth());
                    case '3months': return $createdAt->greaterThanOrEqualTo($now->copy()->subMonths(3));
                    case '6months': return $createdAt->greaterThanOrEqualTo($now->copy()->subMonths(6));
                    case '1year': return $createdAt->greaterThanOrEqualTo($now->copy()->subYear());
                    default: return true;
                }
            });
        }

        $totalIncome = 0;
        $incomeByCategory = [
            'haji' => 0,
            'umrah' => 0
        ];

        foreach ($confirmedRegs as $reg) {
            // Cari harga dari paket yang dipilih
            $packageName = $reg['paket'] ?? '';
            $package = $packages->firstWhere('name', $packageName);
            
            if ($package) {
                $price = $this->parsePrice($package['price'] ?? '0');
                $totalIncome += $price;
                
                $type = strtolower($package['type'] ?? 'umrah');
                if (isset($incomeByCategory[$type])) {
                    $incomeByCategory[$type] += $price;
                }
            }
        }

        return view('admin.finance.index', compact('totalIncome', 'incomeByCategory', 'confirmedRegs', 'packages'));
    }

    /**
     * Mengubah string harga (misal: "25 jt" atau "35.000.000") menjadi angka murni
     */
    private function parsePrice($priceStr)
    {
        // Bersihkan karakter non-angka kecuali titik/koma/jt
        $priceStr = strtolower($priceStr);
        $clean = preg_replace('/[^0-9jt]/', '', $priceStr);
        
        if (str_contains($clean, 'jt')) {
            return (float)str_replace('jt', '', $clean) * 1000000;
        }
        
        return (float)$clean;
    }
}
