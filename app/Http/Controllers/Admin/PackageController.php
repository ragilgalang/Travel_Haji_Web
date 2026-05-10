<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function index(Request $request)
    {
        // Paksa hapus cache
        \Illuminate\Support\Facades\Cache::forget('firebase_packages');
        
        $search = $request->input('search');
        $type = $request->input('type');
        
        $allData = $this->firebase->getValue('packages') ?? [];
        
        // Ubah ke collection dan pastikan kita bekerja dengan array data murni
        $packages = collect($allData)->map(function($item, $key) {
            if (is_array($item)) {
                $item['id'] = $key;
                return $item;
            }
            return null;
        })->filter()->reverse();

        // Terapkan Filter Pencarian Nama
        if (!empty($search)) {
            $searchLower = strtolower(trim($search));
            $packages = $packages->filter(function($pkg) use ($searchLower) {
                $name = strtolower($pkg['name'] ?? '');
                return str_contains($name, $searchLower);
            });
        }

        // Terapkan Filter Tipe (Haji/Umrah) - PENGECEKAN EKSTREM
        if (!empty($type) && $type !== 'all') {
            $typeTarget = strtolower(trim($type));
            $packages = $packages->filter(function($pkg) use ($typeTarget) {
                $rawType = $pkg['type'] ?? '';
                // Cek apakah tipenya benar-benar cocok
                return strtolower(trim($rawType)) === $typeTarget;
            });
        }
        
        return view('admin.packages.kelola_paket', [
            'pkg_list' => $packages,
            'search' => $search,
            'type' => $type
        ]);
    }

    public function create()
    {
        return view('admin.packages.create');
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'type' => 'required|in:haji,umrah',
                'category' => 'nullable|string',
                'duration' => 'required|string',
                'price' => 'required|string',
                'hotel' => 'nullable|string',
                'image_url' => 'nullable',
                'contact_phone' => 'nullable|string',
                'promo_until' => 'nullable|string',
                'hotel_facilities_text' => 'nullable|string',
            ]);

            // Proses Fitur: Ambil dari textarea (string) dan pecah jadi array berdasarkan baris
            $featuresRaw = $request->input('features_text', '');
            $data['features'] = array_values(array_filter(explode("\n", str_replace("\r", "", $featuresRaw))));
            
            // Proses Fasilitas Hotel
            $hotelFacRaw = $request->input('hotel_facilities_text', '');
            $data['hotel_facilities'] = array_values(array_filter(explode("\n", str_replace("\r", "", $hotelFacRaw))));
            
            $data['is_featured'] = $request->has('is_featured');
            $data['created_at'] = now()->toDateTimeString();

            // Handle Image Upload
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                if ($file->isValid()) {
                    $folder = 'uploads/images';
                    $filename = time() . '_' . Str::slug($data['name']) . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path($folder), $filename);
                    $data['image_url'] = url($folder . '/' . $filename);
                }
            }

            $this->firebase->push('packages', $data);
            
            // Bersihkan cache terkait agar perubahan langsung terlihat
            $this->clearPackageCaches();

            return redirect()->route('admin.packages.index')->with([
                'success' => 'Paket "' . $data['name'] . '" berhasil ditambahkan!'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['msg' => 'Gagal menyimpan: ' . $e->getMessage()])
                ->with('paket_form_submission', true);
        }
    }

    public function edit(string $id)
    {
        $package = (object) $this->firebase->getValue('packages/' . $id);
        if (!$package) abort(404);
        
        $package->id = $id; // Ensure ID is present for the form
        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, string $id)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'type' => 'required|in:haji,umrah',
                'category' => 'nullable|string',
                'duration' => 'required|string',
                'price' => 'required|string',
                'hotel' => 'nullable|string',
                'image_url' => 'nullable',
                'contact_phone' => 'nullable|string',
                'promo_until' => 'nullable|string',
                'hotel_facilities_text' => 'nullable|string',
            ]);

            // Proses Fitur Umum
            $featuresRaw = $request->input('features_text', '');
            $data['features'] = array_values(array_filter(explode("\n", str_replace("\r", "", $featuresRaw))));

            // Proses Fasilitas Hotel
            $hotelFacRaw = $request->input('hotel_facilities_text', '');
            $data['hotel_facilities'] = array_values(array_filter(explode("\n", str_replace("\r", "", $hotelFacRaw))));

            $data['is_featured'] = $request->has('is_featured');
            $data['updated_at'] = now()->toDateTimeString();

            // Handle Image Upload
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                if ($file->isValid()) {
                    $folder = 'uploads/images';
                    $filename = time() . '_' . Str::slug($data['name']) . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path($folder), $filename);
                    $data['image_url'] = url($folder . '/' . $filename);
                }
            }

            $this->firebase->updateValue('packages/' . $id, $data);
            
            // Bersihkan cache agar perubahan langsung terlihat
            $this->clearPackageCaches();

            return redirect()->route('admin.packages.index')->with([
                'success' => 'Paket "' . $data['name'] . '" berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['msg' => 'Gagal memperbarui: ' . $e->getMessage()])
                ->with('paket_form_submission', true);
        }
    }

    public function destroy(string $id)
    {
        $this->firebase->deleteValue('packages/' . $id);
        
        // Bersihkan semua cache terkait agar item benar-benar hilang dari semua tampilan
        $this->clearPackageCaches();

        return redirect()->route('admin.packages.index')->with([
            'success' => 'Paket berhasil dihapus dari Firebase!'
        ]);
    }

    public function bulkAction(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            $action = $request->input('action');

            if (empty($ids)) {
                return response()->json(['success' => false, 'message' => 'Tidak ada paket yang dipilih.'], 400);
            }

            $count = 0;
            foreach ($ids as $id) {
                if ($action === 'delete') {
                    // Log untuk memastikan ID dan path benar
                    \Log::info("Mencoba menghapus paket ID: " . $id . " di path: packages/" . $id);
                    
                    $this->firebase->deleteValue('packages/' . $id);
                    $count++;
                }
            }

            // Bersihkan semua cache terkait
            $this->clearPackageCaches();

            return response()->json([
                'success' => true, 
                'message' => $count . ' paket berhasil dihapus. IDs: ' . implode(', ', $ids)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bersihkan semua cache agar perubahan langsung terlihat di semua halaman
     */
    private function clearPackageCaches()
    {
        \Illuminate\Support\Facades\Cache::flush();
    }
}
