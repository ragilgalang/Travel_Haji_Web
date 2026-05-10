<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function index()
    {
        $packages = collect($this->firebase->getValue('packages') ?? []);
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        // Main Home Page
        $xml .= '<url>';
        $xml .= '<loc>' . url('/') . '</loc>';
        $xml .= '<changefreq>daily</changefreq>';
        $xml .= '<priority>1.0</priority>';
        $xml .= '</url>';
        
        // Register Page
        $xml .= '<url>';
        $xml .= '<loc>' . route('register.show') . '</loc>';
        $xml .= '<changefreq>monthly</changefreq>';
        $xml .= '<priority>0.8</priority>';
        $xml .= '</url>';
        
        // Loop through packages if they had individual pages
        // Currently, packages are links on the home page opening modals.
        // We can still add them as unique URLs if we implement single pages later.
        
        $xml .= '</urlset>';

        return Response::make($xml, 200, [
            'Content-Type' => 'application/xml'
        ]);
    }
}
