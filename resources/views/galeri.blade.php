@php
    /**
     * FIX URL OTOMATIS
     */
    if (!function_exists('fixUrl')) {
        function fixUrl($url)
        {
            if (!$url || !is_string($url)) return '';
            $currentHost = rtrim(request()->getSchemeAndHttpHost(), '/');

            // Jika URL mengandung /uploads/, ekstrak path saja & rebuild URL bersih
            // Mencegah double/triple port seperti :8000:8000:8000
            if (str_contains($url, '/uploads/')) {
                $pathStart = strpos($url, '/uploads/');
                $path = substr($url, $pathStart);
                return $currentHost . $path;
            }

            if (str_starts_with($url, '/')) return $currentHost . $url;
            if (str_starts_with($url, 'uploads/')) return $currentHost . '/' . $url;

            if (str_starts_with($url, 'http')) {
                $localhosts = ['http://127.0.0.1:8000', 'http://127.0.0.1', 'http://localhost:8000', 'http://localhost', 'https://127.0.0.1:8000', 'https://localhost:8000'];
                return str_replace($localhosts, $currentHost, $url);
            }

            return $url;
        }
    }

    /* NORMALIZE SETTINGS */
    if (!empty($settings) && is_array($settings)) {
        array_walk_recursive($settings, function (&$value) {
            if (is_string($value))
                $value = fixUrl($value);
        });
    }

    $allMedia = [];
    $photoCount = 0;
    $videoCount = 0;
    $vis = $galleryVisibility ?? [];

    /* 1. DYNAMIC GALLERY (Firebase) - Cek is_published per item */
    $dynamicGallery = $gallery ?? [];
    foreach ($dynamicGallery as $id => $item) {
        if (empty($item['url'])) continue;
        $isPublished = $item['is_published'] ?? true;
        if (!$isPublished) continue; // Skip jika disembunyikan
        $url = fixUrl($item['url']);
        $type = $item['type'] ?? (preg_match('/\.(mp4|webm|ogg)$/i', $url) ? 'video' : 'foto');
        $allMedia[] = ['id' => $id, 'url' => $url, 'type' => $type];
    }

    /* 2. LEGACY GALLERY - FOTO (dari settings/gallery_img_X) */
    for ($i = 1; $i <= 20; $i++) {
        $key = 'gallery_img_' . $i;
        $p = $settings[$key] ?? '';
        if (empty($p)) continue;
        $isPublished = $vis[$key] ?? true;
        if (!$isPublished) continue;
        $url = fixUrl($p);
        $allMedia[] = ['id' => $key, 'url' => $url, 'type' => 'foto'];
    }

    /* 3. LEGACY GALLERY - VIDEO (dari settings/gallery_video_X) */
    for ($i = 1; $i <= 10; $i++) {
        $key = 'gallery_video_' . $i;
        $p = $settings[$key] ?? '';
        if (empty($p)) continue;
        $isPublished = $vis[$key] ?? true;
        if (!$isPublished) continue;
        $url = fixUrl($p);
        $allMedia[] = ['id' => $key, 'url' => $url, 'type' => 'video'];
    }

    /* FILTER AKHIR: HAPUS DUPLIKAT URL */
    $allMedia = collect($allMedia)->unique('url')->values()->all();

    /* HITUNG TOTAL */
    foreach ($allMedia as $m) {
        if (($m['type'] ?? 'foto') == 'foto') $photoCount++;
        else $videoCount++;
    }
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Perjalanan — {{ $settings['site_name'] ?? 'PT. UMI MUTHMAINAH BERKAH' }}</title>

    <link rel="icon" type="image/png" href="{{ $settings['site_logo'] ?? asset('logo/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --dark-green: #0a1f16;
            --accent-green: #34d399;
            --gold: #d4a843;
            --text-gray: #94a3b8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--dark-green);
            color: #ffffff;
            overflow-x: hidden;
        }

        /* HEADER / NAVBAR */
        .gallery-nav {
            padding: 20px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            background: rgba(10, 31, 22, 0.8);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .brand img {
            width: 45px;
            height: 45px;
            object-fit: contain;
        }

        .brand-info h4 {
            font-size: 1rem;
            font-weight: 800;
            line-height: 1.2;
        }

        .brand-info p {
            font-size: 0.75rem;
            color: var(--text-gray);
        }

        .nav-right {
            font-size: 0.8rem;
            color: var(--text-gray);
            display: none;
        }

        @media (min-width: 768px) {
            .nav-right {
                display: block;
            }
        }

        /* HERO SECTION */
        .gallery-hero {
            padding: 80px 5% 40px;
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr;
            gap: 40px;
            align-items: flex-end;
        }

        @media (min-width: 992px) {
            .gallery-hero {
                grid-template-columns: 1.5fr 1fr;
            }
        }

        .hero-content .eyebrow {
            color: var(--accent-green);
            text-transform: uppercase;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 2px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .hero-content h1 {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 25px;
        }

        .hero-content p {
            color: var(--text-gray);
            font-size: 1.1rem;
            line-height: 1.6;
            max-width: 600px;
        }

        .hero-stats-wrapper {
            display: flex;
            flex-direction: column;
            gap: 30px;
            align-items: flex-start;
        }

        @media (min-width: 992px) {
            .hero-stats-wrapper {
                align-items: flex-end;
                text-align: right;
            }
        }

        .stats-grid {
            display: flex;
            gap: 40px;
        }

        .stat-item h2 {
            font-size: 3rem;
            font-weight: 800;
            line-height: 1;
        }

        .stat-item span {
            color: var(--text-gray);
            font-size: 0.9rem;
            font-weight: 600;
        }

        /* FILTERS */
        .gallery-filters {
            padding: 40px 5%;
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            padding: 12px 28px;
            border-radius: 100px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-btn.active {
            background: var(--accent-green);
            color: var(--dark-green);
            border-color: var(--accent-green);
        }

        .filter-btn:hover:not(.active) {
            background: rgba(255, 255, 255, 0.1);
        }

        /* GRID */
        .gallery-main-grid {
            padding: 20px 5% 100px;
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }

        .media-card {
            aspect-ratio: 4/5;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 24px;
            overflow: hidden;
            position: relative;
            cursor: pointer;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .media-card:hover {
            transform: scale(1.02);
            border-color: rgba(52, 211, 153, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .media-card img,
        .media-card video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.8s ease;
        }

        .media-card:hover img {
            transform: scale(1.1);
        }

        .type-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 40px;
            height: 40px;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(10px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }

        .overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(10, 31, 22, 0.8), transparent);
            opacity: 0;
            transition: opacity 0.3s;
            display: flex;
            align-items: flex-end;
            padding: 30px;
        }

        .media-card:hover .overlay {
            opacity: 1;
        }

        /* ANIMATIONS */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s cubic-bezier(0.2, 1, 0.3, 1);
        }

        .reveal.revealed {
            opacity: 1;
            transform: translateY(0);
        }

        /* BACK TO HOME FLOAT */
        .back-float {
            position: fixed;
            bottom: 30px;
            left: 30px;
            z-index: 100;
            background: white;
            color: var(--dark-green);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            transition: all 0.3s;
        }

        .back-float:hover {
            transform: scale(1.1);
        }

        /* PREMIUM LIGHTBOX */
        .lightbox-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.4s ease;
            backdrop-filter: blur(10px);
        }

        .lightbox-overlay.active {
            display: flex;
            opacity: 1;
        }

        .lightbox-content {
            position: relative;
            max-width: 90%;
            max-height: 85vh;
            transform: scale(0.9);
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .lightbox-overlay.active .lightbox-content {
            transform: scale(1);
        }

        .lightbox-content img,
        .lightbox-content video {
            width: auto;
            height: auto;
            max-width: 100%;
            max-height: 85vh;
            border-radius: 12px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        }

        .lightbox-close {
            position: absolute;
            top: -50px;
            right: 0;
            background: white;
            color: black;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .lightbox-close:hover {
            transform: rotate(90deg);
            background: var(--accent-green);
        }

        .lightbox-info {
            position: absolute;
            bottom: -40px;
            left: 0;
            width: 100%;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="gallery-nav">
        <div class="brand">
            <img src="{{ $settings['site_logo'] ?? asset('logo/logo.png') }}" alt="Logo">
            <div class="brand-info">
                <h4>{{ $settings['site_name'] ?? 'PT. UMI MUTHMAINAH BERKAH' }}</h4>
                <p>Travel Umrah & Haji Terpercaya</p>
            </div>
        </div>
        <div class="nav-right">
            {{ $settings['address'] ?? 'PD. Sidokare Indah Blok UU No. 8, Sidoarjo' }}
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="gallery-hero">
        <div class="hero-content">
            <div class="eyebrow"><i class="fas fa-sparkles"></i> Galeri Perjalanan</div>
            <h1>Momen <em>Berkesan</em><br>Jemaah Kami</h1>
            <p>Ribuan momen penuh makna tertangkap dalam setiap perjalanan suci bersama <span
                    style="color: var(--accent-green)">{{ $settings['site_name'] ?? 'PT. UMI MUTHMAINAH BERKAH' }}</span>.
            </p>
            <div style="margin-top: 12px; display: inline-flex; align-items: center; gap: 5px;
                        background: rgba(212, 168, 67, 0.08); border: 1px solid rgba(212, 168, 67, 0.25);
                        padding: 4px 12px; border-radius: 100px; font-size: 0.65rem; color: #d4a843;
                        letter-spacing: 0.04em; font-weight: 600; opacity: 0.8;">
                <svg width="9" height="9" viewBox="0 0 24 24" fill="#d4a843"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                Support by : PT UMB
            </div>
        </div>

        <div class="hero-stats-wrapper">
            <div class="stats-grid">
                <div class="stat-item">
                    <h2>{{ $photoCount }}</h2>
                    <span>Foto</span>
                </div>
                <div class="stat-item">
                    <h2>{{ $videoCount }}</h2>
                    <span>Video</span>
                </div>
            </div>
        </div>
    </section>

    <!-- FILTERS -->
    <div class="gallery-filters">
        <button class="filter-btn active" onclick="filterGallery('semua', this)">Semua</button>
        <button class="filter-btn" onclick="filterGallery('foto', this)">
            <i class="fas fa-image"></i> Foto
        </button>
        <button class="filter-btn" onclick="filterGallery('video', this)">
            <i class="fas fa-video"></i> Video
        </button>
    </div>

    <!-- MAIN GRID -->
    <div class="gallery-main-grid" id="galleryGrid">
        @foreach($allMedia as $idx => $item)
            @php $type = $item['type'] ?? 'foto'; @endphp
            <div class="media-card reveal item-{{ $type }}" 
                 style="transition-delay: {{ ($idx % 10) * 50 }}ms;"
                 onclick="openMedia('{{ $item['url'] }}', '{{ $type }}')">
                
                <div class="type-badge">
                    <i class="fas {{ $type == 'foto' ? 'fa-image' : 'fa-play' }}"></i>
                </div>

                @if($type == 'foto')
                    <img src="{{ $item['url'] }}{{ str_contains($item['url'], '?') ? '&' : '?' }}v={{ time() }}" alt="Galeri" onerror="this.closest('.media-card').remove()">
                @else
                    <video muted loop playsinline onloadeddata="this.style.opacity=1" style="opacity:0; transition: opacity 0.3s;" onerror="this.closest('.media-card').remove()">
                        <source src="{{ $item['url'] }}" type="video/mp4" onerror="this.closest('.media-card').remove()">
                    </video>
                @endif

                <div class="overlay">
                    <span>Lihat Detail <i class="fas fa-arrow-right" style="margin-left: 10px;"></i></span>
                </div>
            </div>
        @endforeach
    </div>

    <a href="{{ route('home') }}" class="back-float" title="Kembali ke Beranda">
        <i class="fas fa-home"></i>
    </a>

    <!-- LIGHTBOX MODAL -->
    <div id="lightbox" class="lightbox-overlay" onclick="closeMedia()">
        <div class="lightbox-content" onclick="event.stopPropagation()">
            <button class="lightbox-close" onclick="closeMedia()">✕</button>
            <div id="lightboxBody"></div>
            <div class="lightbox-info" id="lightboxInfo"></div>
        </div>
    </div>

    <script>
        // FILTER LOGIC
        function filterGallery(type, btn) {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const items = document.querySelectorAll('.media-card');
            items.forEach(item => {
                if (type === 'semua' || item.classList.contains('item-' + type)) {
                    item.style.display = 'block';
                    setTimeout(() => item.style.opacity = '1', 10);
                } else {
                    item.style.opacity = '0';
                    setTimeout(() => item.style.display = 'none', 300);
                }
            });
        }

        // OPEN MEDIA (PREMIUM LIGHTBOX)
        function openMedia(url, type) {
            const lightbox = document.getElementById('lightbox');
            const body = document.getElementById('lightboxBody');
            const info = document.getElementById('lightboxInfo');

            body.innerHTML = '';

            if (type === 'foto') {
                body.innerHTML = `<img src="${url}" alt="Gallery">`;
                info.innerHTML = `<i class="fas fa-image"></i> Foto Perjalanan`;
            } else {
                body.innerHTML = `<video src="${url}" controls autoplay style="width: 100%; outline: none;"></video>`;
                info.innerHTML = `<i class="fas fa-play-circle"></i> Video Perjalanan`;
            }

            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeMedia() {
            const lightbox = document.getElementById('lightbox');
            const body = document.getElementById('lightboxBody');

            // Stop video if playing
            const video = body.querySelector('video');
            if (video) video.pause();

            lightbox.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // REVEAL ANIMATION
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

        // VIDEO HOVER PLAY
        document.querySelectorAll('.media-card.item-video').forEach(card => {
            const video = card.querySelector('video');
            if (video) {
                card.addEventListener('mouseenter', () => video.play().catch(() => {}));
                card.addEventListener('mouseleave', () => {
                    video.pause();
                    video.currentTime = 0;
                });
            }
        });

        // VIDEO HOVER PLAY
        document.querySelectorAll('.media-card.item-video').forEach(card => {
            const video = card.querySelector('video');
            card.addEventListener('mouseenter', () => video.play());
            card.addEventListener('mouseleave', () => {
                video.pause();
                video.currentTime = 0;
            });
        });
    </script>

</body>

</html>