<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;

class HomeController extends Controller
{
    public function index()
    {
        $firebase = new FirebaseService();
        
        // Tracking Pengunjung Unik per Sesi
        if (!session()->has('has_viewed_home')) {
            $currentViews = $firebase->getValue('metrics/page_views') ?? 0;
            $firebase->setValue('metrics/page_views', $currentViews + 1);
            
            // CATAT LOG PENGUNJUNG DETAIL
            try {
                $firebase->push('visitor_log', [
                    'ip'         => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'timestamp'  => now()->toDateTimeString(),
                ]);
            } catch (\Exception $e) {
                \Log::error('Visitor Logging Failed: ' . $e->getMessage());
            }

            session()->put('has_viewed_home', true);
        }

        // --- CACHE IMPLEMENTATION (24 HOURS) ---
        $cacheDuration = 60 * 24;

        $settings = \Illuminate\Support\Facades\Cache::remember('site_settings', $cacheDuration, function() use ($firebase) {
            return $firebase->getValue('settings') ?? [];
        });

        $packages = \Illuminate\Support\Facades\Cache::remember('site_packages', $cacheDuration, function() use ($firebase) {
            return collect($firebase->getValue('packages') ?? []);
        });

        $testimonials = \Illuminate\Support\Facades\Cache::remember('site_testimonials', $cacheDuration, function() use ($firebase) {
            $data = collect($firebase->getValue('testimonials') ?? [])->filter(function($testi) {
                return isset($testi['is_published']) && $testi['is_published'] == true;
            });
            
            // Urutkan berdasarkan created_at DESC (Terbaru di atas)
            return $data->sortByDesc(function($item) {
                return $item['created_at'] ?? '0000-00-00 00:00:00';
            });
        });

        $facilities = \Illuminate\Support\Facades\Cache::remember('site_facilities', $cacheDuration, function() use ($firebase) {
            return collect($firebase->getValue('facilities') ?? []);
        });

        // HITUNG JEMAAH DIBERANGKATKAN (REALTIME DARI ARSIP)
        $registrationsCount = \Illuminate\Support\Facades\Cache::remember('departed_count', 60, function() use ($firebase) {
            $allReg = collect($firebase->getValue('registrations') ?? []);
            return $allReg->filter(function($reg) {
                $status = $reg['status'] ?? '';
                $isArchived = $reg['is_archived'] ?? false;
                return ($status === 'Selesai' || $status === 'Berangkat') && $isArchived == true;
            })->count();
        });

        // HITUNG KEPUASAN JEMAAH (REALTIME DARI TESTIMONI)
        $satisfactionRate = \Illuminate\Support\Facades\Cache::remember('satisfaction_rate', 60, function() use ($firebase) {
            $allTesti = collect($firebase->getValue('testimonials') ?? []);
            if ($allTesti->isEmpty()) return 100; // Default jika belum ada testimoni
            
            $goodReviews = $allTesti->filter(function($t) {
                $rating = (int) ($t['rating'] ?? 0);
                return $rating >= 4;
            })->count();
            
            return round(($goodReviews / $allTesti->count()) * 100);
        });
        // ---------------------------------------
        
        $gallery = \Illuminate\Support\Facades\Cache::remember('site_gallery', 5, function() use ($firebase) {
            return $firebase->getValue('gallery') ?? [];
        });

        $galleryVisibility = \Illuminate\Support\Facades\Cache::remember('site_gallery_visibility', 5, function() use ($firebase) {
            return $firebase->getValue('gallery_visibility') ?? [];
        });

        // Load gallery_img_X langsung dari Firebase tanpa cache (realtime)
        $gallerySettings = [];
        $allSettings = $firebase->getValue('settings') ?? [];
        for ($i = 1; $i <= 50; $i++) {
            $key = 'gallery_img_' . $i;
            if (!empty($allSettings[$key])) $gallerySettings[$key] = $allSettings[$key];
            $vkey = 'gallery_video_' . $i;
            if (!empty($allSettings[$vkey])) $gallerySettings[$vkey] = $allSettings[$vkey];
        }
        if (!empty($gallerySettings)) {
            $settings = array_merge($settings, $gallerySettings);
        }

        return view('welcome', compact('settings', 'packages', 'testimonials', 'facilities', 'registrationsCount', 'satisfactionRate', 'gallery', 'galleryVisibility'));
    }

    public function submitReview(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'name' => 'required|string|max:100',
            'location' => 'required|string|max:100',
            'rating' => 'required|integer|min:1|max:5',
            'text' => 'required|string|max:500',
            'image' => 'nullable|image|max:3072' // Max 3MB
        ]);

        $firebase = new FirebaseService();
        
        // --- VALIDASI NOMOR REFERENSI ---
        $allTestimonials = collect($firebase->getValue('testimonials') ?? []);
        $registrations = collect($firebase->getValue('registrations') ?? []);
        
        $token = strtoupper($request->token);

        // 1. Cek apakah token valid di data pendaftaran
        $isValidRef = $registrations->contains(function($reg) use ($token) {
            return isset($reg['ref_id']) && strtoupper($reg['ref_id']) === $token;
        });

        if (!$isValidRef) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf, Nomor Referensi Pendaftaran tidak valid. Hanya jemaah terdaftar yang dapat memberikan ulasan.'
            ], 422);
        }

        // 2. Cek apakah token sudah pernah kirim ulasan
        $isAlreadyUsed = $allTestimonials->contains(function($t) use ($token) {
            return isset($t['ref_id']) && strtoupper($t['ref_id']) === $token;
        });

        if ($isAlreadyUsed) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf, nomor referensi ini sudah pernah digunakan untuk memberikan ulasan sebelumnya.'
            ], 422);
        }

        // --- HANDLE UPLOAD GAMBAR ---
        $avatarUrl = '';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $folderName = 'uploads/testimonials';
            
            // Gunakan path kustom yang tangguh untuk sistem operasi Windows/XAMPP
            $absolutePath = public_path(str_replace('/', DIRECTORY_SEPARATOR, $folderName));
            
            // Buat folder jika belum ada secara rekursif
            if (!file_exists($absolutePath)) {
                mkdir($absolutePath, 0755, true);
            }

            $filename = time() . '_' . \Illuminate\Support\Str::random(5) . '.' . $file->getClientOriginalExtension();
            $file->move($absolutePath, $filename);
            $avatarUrl = asset($folderName . '/' . $filename);
        }

        // Simpan ulasan
        $firebase->push('testimonials', [
            'name' => $request->name,
            'location' => $request->location,
            'rating' => (int)$request->rating,
            'text' => $request->text,
            'ref_id' => strtoupper($request->token),
            'avatar_url' => $avatarUrl, 
            'created_at' => date('Y-m-d H:i:s'),
            'is_published' => false 
        ]);

        \Illuminate\Support\Facades\Cache::forget('site_testimonials');
        \Illuminate\Support\Facades\Cache::forget('dashboard_stats');

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih! Ulasan dan foto Anda berhasil dikirim.'
        ]);
    }

    public function submitContact(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:150',
            'telepon' => 'required|string|max:20',
            'email' => 'required|email|max:150',
            'paket' => 'nullable|string',
            'pesan' => 'nullable|string|max:1000'
        ]);

        $firebase = new FirebaseService();
        $firebase->push('contact_inquiries', [
            'nama' => $request->nama,
            'telepon' => $request->telepon,
            'email' => $request->email,
            'paket' => $request->paket ?? 'Umum',
            'pesan' => $request->pesan ?? '-',
            'ip' => $request->ip(),
            'created_at' => now()->toDateTimeString(),
            'status' => 'Baru'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesan Anda berhasil terkirim. Tim kami akan segera menghubungi Anda.'
        ]);
    }

    public function gallery()
    {
        $firebase = new FirebaseService();
        $cacheDuration = 60 * 24;

        $settings = \Illuminate\Support\Facades\Cache::remember('site_settings', $cacheDuration, function() use ($firebase) {
            return $firebase->getValue('settings') ?? [];
        });

        $gallery = \Illuminate\Support\Facades\Cache::remember('site_gallery', 5, function() use ($firebase) {
            return $firebase->getValue('gallery') ?? [];
        });

        $galleryVisibility = \Illuminate\Support\Facades\Cache::remember('site_gallery_visibility', 5, function() use ($firebase) {
            return $firebase->getValue('gallery_visibility') ?? [];
        });

        // Load gallery_img_X langsung dari Firebase tanpa cache (realtime)
        $gallerySettings = [];
        $allSettings = $firebase->getValue('settings') ?? [];
        for ($i = 1; $i <= 50; $i++) {
            $key = 'gallery_img_' . $i;
            if (!empty($allSettings[$key])) $gallerySettings[$key] = $allSettings[$key];
            $vkey = 'gallery_video_' . $i;
            if (!empty($allSettings[$vkey])) $gallerySettings[$vkey] = $allSettings[$vkey];
        }
        if (!empty($gallerySettings)) {
            $settings = array_merge($settings, $gallerySettings);
        }

        return view('galeri', compact('settings', 'gallery', 'galleryVisibility'));
    }
}

