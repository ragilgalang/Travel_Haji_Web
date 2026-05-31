<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Helper caching yang tahan banting (resilient).
     * Jika Firebase gagal (misal SSL error / timeout), akan me-return fallback 
     * TANPA menyimpan state gagal/kosong tersebut ke dalam cache.
     */
    protected function getFirebaseData($cacheKey, $ttl, $callback, $fallback = [])
    {
        $data = \Illuminate\Support\Facades\Cache::get($cacheKey);
        if ($data !== null) {
            return $data;
        }

        try {
            $data = $callback();
            \Illuminate\Support\Facades\Cache::put($cacheKey, $data, $ttl);
            return $data;
        } catch (\Exception $e) {
            \Log::error("Firebase Error for {$cacheKey}: " . $e->getMessage());
            return is_callable($fallback) ? $fallback() : $fallback;
        }
    }
}
