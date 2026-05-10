<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirebaseService;

class FacilityController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function index()
    {
        $facilities = \Illuminate\Support\Facades\Cache::remember('firebase_facilities', 60*24, function() {
            return collect($this->firebase->getValue('facilities') ?? []);
        });
        return view('admin.facilities.kelola_fasilitas', compact('facilities'));
    }

    public function create()
    {
        return view('admin.facilities.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'icon' => 'required',
            'hotel' => 'nullable|string',
            'image_url' => 'nullable|url',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if ($file->isValid()) {
                $folder = 'uploads/images';
                if (!file_exists(public_path($folder))) {
                    mkdir(public_path($folder), 0755, true);
                }
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path($folder), $filename);
                $data['image_url'] = url($folder . '/' . $filename);
            }
        }

        $this->firebase->push('facilities', $data);
        \Illuminate\Support\Facades\Cache::forget('firebase_facilities');
        \Illuminate\Support\Facades\Cache::forget('site_global_data');
        return redirect()->route('admin.facilities.index')->with([
            'success' => 'Fasilitas berhasil ditambahkan ke Firebase!'
        ]);
    }

    public function edit(string $id)
    {
        $facility = (object) $this->firebase->getValue('facilities/' . $id);
        if (!$facility) abort(404);
        $facility->id = $id;
        return view('admin.facilities.edit', compact('facility'));
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'icon' => 'required',
            'hotel' => 'nullable|string',
            'image_url' => 'nullable|url',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if ($file->isValid()) {
                $folder = 'uploads/images';
                if (!file_exists(public_path($folder))) {
                    mkdir(public_path($folder), 0755, true);
                }
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path($folder), $filename);
                $data['image_url'] = url($folder . '/' . $filename);
            }
        }

        $this->firebase->updateValue('facilities/' . $id, $data);
        \Illuminate\Support\Facades\Cache::forget('firebase_facilities');
        \Illuminate\Support\Facades\Cache::forget('site_global_data');
        return redirect()->route('admin.facilities.index')->with([
            'success' => 'Fasilitas berhasil diperbarui di Firebase!'
        ]);
    }

    public function destroy(string $id)
    {
        $this->firebase->deleteValue('facilities/' . $id);
        \Illuminate\Support\Facades\Cache::forget('firebase_facilities');
        \Illuminate\Support\Facades\Cache::forget('site_global_data');
        return redirect()->route('admin.facilities.index')->with([
            'success' => 'Fasilitas berhasil dihapus dari Firebase!'
        ]);
    }

    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada fasilitas yang dipilih.'], 400);
        }

        foreach ($ids as $id) {
            if ($action === 'delete') {
                $this->firebase->deleteValue('facilities/' . $id);
            }
        }

        \Illuminate\Support\Facades\Cache::forget('firebase_facilities');
        \Illuminate\Support\Facades\Cache::forget('site_global_data');

        return response()->json([
            'success' => true, 
            'message' => count($ids) . ' fasilitas berhasil dihapus.'
        ]);
    }
}
