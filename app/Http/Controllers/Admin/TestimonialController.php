<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirebaseService;

class TestimonialController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function index(Request $request)
    {
        $all = $this->firebase->getValue('testimonials') ?? [];
        
        $testimonials = collect($all)->map(function($item, $key) {
            return array_merge(['id' => $key], $item);
        })->reverse(); // Sort terbaru dulu

        // Filter Status
        if ($request->filled('status')) {
            $status = $request->status;
            $testimonials = $testimonials->filter(function($t) use ($status) {
                $isPublished = isset($t['is_published']) ? $t['is_published'] : true;
                if ($status === 'published') return $isPublished === true;
                if ($status === 'hidden') return $isPublished === false;
                return true;
            });
        }

        // Search
        if ($request->filled('q')) {
            $q = strtolower($request->q);
            $testimonials = $testimonials->filter(function($t) use ($q) {
                return str_contains(strtolower($t['name'] ?? ''), $q)
                    || str_contains(strtolower($t['text'] ?? ''), $q)
                    || str_contains(strtolower($t['location'] ?? ''), $q);
            });
        }

        return view('admin.testimonials.kelola_testimoni', compact('testimonials'));
    }

    public function create()
    {
        return view('admin.testimonials.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'location' => 'required',
            'rating' => 'required|integer|min:1|max:5',
            'text' => 'required',
            'avatar_url' => 'nullable|url',
        ]);

        $this->firebase->push('testimonials', $data);
        \Illuminate\Support\Facades\Cache::forget('site_testimonials');
        \Illuminate\Support\Facades\Cache::forget('dashboard_stats');
        return redirect()->route('admin.testimonials.index')->with('success', 'Testimoni berhasil ditambahkan ke Firebase!');
    }

    public function destroy(string $id)
    {
        $this->firebase->deleteValue('testimonials/' . $id);
        \Illuminate\Support\Facades\Cache::forget('site_testimonials');
        \Illuminate\Support\Facades\Cache::forget('dashboard_stats');
        return back()->with('success', 'Testimoni berhasil dihapus dari Firebase!');
    }

    public function toggleVisibility(Request $request, string $id)
    {
        $testi = $this->firebase->getValue('testimonials/' . $id);
        if (!$testi) {
            return response()->json(['success' => false, 'message' => 'Testimoni tidak ditemukan'], 404);
        }

        // Jika blm ada is_published, asumsikan true (untuk data lama), lalu dibalik menjadi false.
        $currentState = isset($testi['is_published']) ? $testi['is_published'] : true;
        
        $this->firebase->updateValue('testimonials/' . $id, [
            'is_published' => !$currentState
        ]);

        \Illuminate\Support\Facades\Cache::forget('site_testimonials');

        return response()->json([
            'success' => true, 
            'message' => 'Status rilis testimoni berhasil diubah',
            'new_state' => !$currentState
        ]);
    }

    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada baris yang dipilih.'], 400);
        }

        foreach ($ids as $id) {
            if ($action === 'publish') {
                $this->firebase->updateValue('testimonials/' . $id, ['is_published' => true]);
            } elseif ($action === 'unpublish') {
                $this->firebase->updateValue('testimonials/' . $id, ['is_published' => false]);
            } elseif ($action === 'delete') {
                $this->firebase->deleteValue('testimonials/' . $id);
            }
        }

        \Illuminate\Support\Facades\Cache::forget('site_testimonials');
        if ($action === 'delete') {
            \Illuminate\Support\Facades\Cache::forget('dashboard_stats');
        }

        return response()->json([
            'success' => true, 
            'message' => count($ids) . ' testimoni berhasil di-update.'
        ]);
    }

    public function syncGmaps(Request $request)
    {
        $settings = \Illuminate\Support\Facades\Cache::get('site_settings', function() {
            return $this->firebase->getValue('settings') ?? [];
        });

        $apiKey = $settings['google_maps_api_key'] ?? null;
        $placeId = $settings['google_place_id'] ?? null;

        if (!$apiKey || !$placeId) {
            return response()->json(['success' => false, 'message' => 'API Key atau Place ID belum dikonfigurasi.'], 400);
        }

        try {
            $url = "https://maps.googleapis.com/maps/api/place/details/json?place_id={$placeId}&fields=name,rating,reviews&key={$apiKey}";
            
            $response = \Illuminate\Support\Facades\Http::get($url);
            $data = $response->json();

            if (($data['status'] ?? '') !== 'OK') {
                return response()->json(['success' => false, 'message' => 'Google API Error: ' . ($data['error_message'] ?? $data['status'])], 500);
            }

            $reviews = $data['result']['reviews'] ?? [];
            $count = 0;
            
            $existing = $this->firebase->getValue('testimonials') ?? [];
            $existingTexts = collect($existing)->pluck('text')->toArray();

            foreach ($reviews as $rev) {
                if (($rev['rating'] ?? 0) < 4) continue;
                if (in_array($rev['text'], $existingTexts)) continue;

                $newTesti = [
                    'name' => $rev['author_name'] ?? 'Jemaah Google',
                    'location' => 'Ulasan Google Maps',
                    'rating' => $rev['rating'] ?? 5,
                    'text' => $rev['text'] ?? '',
                    'avatar_url' => $rev['profile_photo_url'] ?? null,
                    'is_published' => true,
                    'source' => 'google_maps',
                    'created_at' => now()->toDateTimeString()
                ];

                $this->firebase->push('testimonials', $newTesti);
                $count++;
            }

            \Illuminate\Support\Facades\Cache::forget('site_testimonials');
            \Illuminate\Support\Facades\Cache::forget('dashboard_stats');

            return response()->json([
                'success' => true, 
                'message' => "Berhasil menarik {$count} ulasan baru dari Google Maps!"
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal sinkronisasi: ' . $e->getMessage()], 500);
        }
    }
}
