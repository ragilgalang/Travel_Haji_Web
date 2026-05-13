@php
    function optUrl($url, $w = 800) {
        if (strpos($url, 'images.unsplash.com') !== false) {
            $base = explode('?', $url)[0];
            return $base . "?w={$w}&q=70&auto=format,compress&fm=webp&fit=crop";
        }
        return $url;
    }

    /**
     * Otomatis ganti URL localhost & Physical Path → URL production dinamis
     */
    function fixUrl($url) {
        if (!$url || !is_string($url)) return $url;
        
        $currentHost = rtrim(request()->getSchemeAndHttpHost(), '/');

        // Jika jalur sudah relatif (mulai dengan /uploads), gabungkan dengan host saat ini
        if (str_starts_with($url, '/uploads')) {
            return $currentHost . $url;
        }
        
        // 1. Tangani Physical Path (jika ada sisa-sisa path Windows di DB)
        if (str_contains($url, 'xampp\htdocs')) {
            $parts = explode('public\\', $url);
            if (count($parts) > 1) {
                $url = $currentHost . '/' . str_replace('\\', '/', $parts[1]);
            }
        }
        
        // 2. Tangani Localhost URL
        $localhosts = [
            'http://127.0.0.1:8000', 
            'http://127.0.0.1', 
            'http://localhost:8000', 
            'http://localhost',
            'https://127.0.0.1:8000',
            'https://localhost:8000'
        ];
        
        return str_replace($localhosts, $currentHost, $url);
    }

    // Terapkan fixUrl ke seluruh array $settings & $packages
    if (!empty($settings) && is_array($settings)) {
        array_walk_recursive($settings, function(&$value) {
            if (is_string($value)) $value = fixUrl($value);
        });
    }
    if (!empty($packages) && is_array($packages)) {
        array_walk_recursive($packages, function(&$value) {
            if (is_string($value)) $value = fixUrl($value);
        });
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script>
/** ── AUTO-HEAL MEDIA URL ── **/
window.addEventListener('error', function(e) {
    if (e.target.tagName === 'IMG' || e.target.tagName === 'VIDEO') {
        var src = e.target.src;
        if (src.indexOf('127.0.0.1') !== -1 || src.indexOf('localhost') !== -1) {
            var newSrc = src.replace(/https?:\/\/(127\.0\.0\.1|localhost)(:8000)?/, window.location.origin);
            if (newSrc !== src) e.target.src = newSrc;
        }
    }
}, true);

/** ── DESKTOP MODE FIX ── **/
(function() {
    try {
        var ua = navigator.userAgent || '';
        var isMobilePhone = /Android|iPhone|iPod/i.test(ua) && !/iPad|Tablet/i.test(ua);
        if (!isMobilePhone) return;

        var ratio = screen.width / window.innerWidth;
        if (ratio < 0.55) {
            var zoom = Math.min(2.5, Math.max(1.5, 1 / ratio * 0.9));
            // Gunakan transform agar lebih stabil di berbagai browser
            document.documentElement.style.setProperty('--dm-zoom', zoom);
            document.documentElement.classList.add('mobile-desktop-mode');
            
            // Tambahkan style langsung untuk mencegah gap kanan
            var style = document.createElement('style');
            style.textContent = 'html.mobile-desktop-mode { zoom: ' + zoom + '; width: 100%; overflow-x: hidden; } ' +
                                'html.mobile-desktop-mode body { width: 100%; overflow-x: hidden; }';
            document.head.appendChild(style);
        }
    } catch(e) {}
})();
</script>

    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://images.unsplash.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Preload LCP Image (Slide 1) -->
    @php
        $lcpImg = optUrl(($settings['hero_bg_1'] ?? 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa'), 1200);
    @endphp
    <link rel="preload" as="image" href="{{ $lcpImg }}" fetchpriority="high">
    <title>{{ $settings['site_name'] ?? 'PT. UMI MUTHMAINAH BERKAH' }} — Paket Haji & Umrah Premium</title>
    
    <!-- SEO & Metadata -->
    <meta name="description" content="{{ $settings['site_description'] ?? 'Penyelenggara Perjalanan Ibadah Haji & Umrah Resmi Kemenag RI. Amanah, Nyaman, dan Berpengalaman bersama PT. UMI MUTHMAINAH BERKAH' }}">
    <meta name="keywords" content="{{ $settings['site_keywords'] ?? 'haji, umrah, travel haji, umrah premium, travelhaji' }}">
    <meta name="author" content="{{ $settings['site_name'] ?? 'PT. UMI MUTHMAINAH BERKAH' }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $settings['site_name'] ?? 'PT. UMI MUTHMAINAH BERKAH' }} — Paket Haji & Umrah Premium">
    <meta property="og:description" content="{{ $settings['site_description'] ?? 'Penyelenggara Perjalanan Ibadah Haji & Umrah Resmi Kemenag RI.' }}">
    <meta property="og:image" content="{{ $settings['og_image'] ?? asset('logo/logo.png') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $settings['site_name'] ?? 'PT. UMI MUTHMAINAH BERKAH' }} — Paket Haji & Umrah Premium">
    <meta property="twitter:description" content="{{ $settings['site_description'] ?? 'Penyelenggara Perjalanan Ibadah Haji & Umrah Resmi Kemenag RI.' }}">
    <meta property="twitter:image" content="{{ $settings['og_image'] ?? asset('logo/logo.png') }}">
    <link rel="icon" type="image/png" href="{{ $settings['site_logo'] ?? asset('logo/logo.png') }}">
<link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600;700;800&family=Lora:ital,wght@0,500;0,600;1,400;1,500&display=swap" rel="stylesheet">
    <style>
        /* DYNAMIC BRANDING - Keep this in Blade for real-time customization */
        :root {
            --green: {{ $settings['primary_color'] ?? '#1a5c3a' }};
            --green2: {{ ($settings['primary_color'] ?? '#1a5c3a') . 'ee' }};
            --green3: {{ ($settings['primary_color'] ?? '#1a5c3a') . 'cc' }};
            --green-light: {{ ($settings['primary_color'] ?? '#1a5c3a') . '15' }};
            
            --gold: {{ $settings['secondary_color'] ?? '#d4a843' }};
            --gold2: {{ ($settings['secondary_color'] ?? '#d4a843') . 'ee' }};
            --gold-light: {{ ($settings['secondary_color'] ?? '#d4a843') . '15' }};
            
            --about-badge: var(--green);
        }
    </style>
    
    <!-- STYLESHEETS -->
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/welcome_legacy.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/welcome_modern.css') }}">
    <link rel="stylesheet" href="{{ asset('css/welcome_components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/welcome_extra.css') }}">
    <link rel="stylesheet" href="{{ asset('css/welcome_responsive.css') }}?v=1.2">
    <noscript>
        <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
        <link rel="stylesheet" href="{{ asset('css/welcome_extra.css') }}">
    </noscript>

</head>
<body>

@include('layouts.header')

<main id="main-content">

<!-- HERO with Slideshow -->
<section class="hero" id="sync-hero">
  <!-- Panggilan file background yang sudah dipisah -->
  @include('layouts.hero_background')

  <div class="hero-content">
    <div class="hero-badges-wrapper">
      <div id="sync-hero_badge" class="hero-badge"><span></span> {{ $settings['hero_badge'] ?? 'TERDAFTAR RESMI KEMENAG RI · IZIN PPIU NO. U - 207/2021' }}</div>
      <div id="sync-hero_badge_2" class="hero-badge"><span></span> {{ $settings['hero_badge_2'] ?? 'PIHK 81200009510360001' }}</div>
    </div>
    <h1 id="sync-hero_title">{!! $settings['hero_title'] ?? 'Wujudkan Perjalanan<br><em>Suci ke Baitullah</em>' !!}</h1>
    <p id="sync-hero_description" class="hero-desc">{{ $settings['hero_description'] ?? 'Lebih dari 12.000 jemaah telah kami antarkan ke Tanah Suci dengan aman, nyaman, dan penuh keberkahan.' }}</p>
    <div class="hero-cta">
      <a href="#paket" class="btn btn-gold btn-lg">Lihat Paket Haji</a>
      <button class="btn-play" onclick="openModal()">
        <div class="play-circle">▶</div>
        Tonton Video Profil
      </button>
    </div>
  </div>

  <div class="slide-dots" id="slideDots"></div>

  <div class="scroll-hint">
    <span>Scroll</span>
    <div class="scroll-hint-line"></div>
  </div>
</section>


<!-- STATS -->
<div id="sync-stats" class="stats-bar">
  <div class="stats-inner">
    <div class="stat-item reveal" id="sync-stat1_num"><div class="stat-num">{{ number_format($registrationsCount, 0, ',', '.') }}+</div><div class="stat-label" id="sync-stat1_label">{{ $settings['stat1_label'] ?? 'Jemaah Diberangkatkan' }}</div></div>
    <div class="stat-item reveal delay-100" id="sync-stat2_num"><div class="stat-num">{{ $settings['stat2_num'] ?? '20+' }}</div><div class="stat-label" id="sync-stat2_label">{{ $settings['stat2_label'] ?? 'Tahun Pengalaman' }}</div></div>
    <div class="stat-item reveal delay-200" id="sync-stat3_num"><div class="stat-num">{{ $satisfactionRate }}%</div><div class="stat-label" id="sync-stat3_label">{{ $settings['stat3_label'] ?? 'Kepuasan Jemaah' }}</div></div>
    <div class="stat-item reveal delay-300" id="sync-stat4_num"><div class="stat-num">{{ $settings['stat4_num'] ?? '15+' }}</div><div class="stat-label" id="sync-stat4_label">{{ $settings['stat4_label'] ?? 'Kota Keberangkatan' }}</div></div>
  </div>
</div>

<!-- ABOUT -->
<section class="section about-section" id="tentang">
  <div class="section-inner">
    <div class="about-grid">
      <!-- Panggilan file background/gambar animasi Tentang Kami -->
      @include('layouts.about_background')

      <div class="reveal-right">
        <div id="sync-about-badge" class="sec-eyebrow">Tentang {{ $settings['site_name'] ?? 'PT. UMI MUTHMAINAH BERKAH' }}</div>
        <h2 id="sync-about_title" class="sec-title">{!! $settings['about_title'] ?? 'Melayani Sepenuh Hati Sejak <em>2014</em>' !!}</h2>
        <p id="sync-about_description" class="sec-sub about-desc-no-mb">{{ $settings['about_description'] ?? 'PT. Umi Muthmainah Berkah hadir untuk memberikan pengalaman ibadah terbaik bagi Anda. Dengan komitmen pada kualitas pelayanan dan bimbingan ibadah sesuai sunnah, kami telah mendampingi ribuan jemaah mewujudkan impian mereka ke Tanah Suci.' }}</p>
        <div class="about-list" id="sync-about-points">
          <div class="about-item">
            <div class="about-item-icon">
              @if(!empty($settings['about_item1_img']))
                <img src="{{ optUrl($settings['about_item1_img'], 100) }}" alt="" class="about-point-img" loading="lazy" width="64" height="64">
              @else
                {{ $settings['about_item1_icon'] ?? '🕋' }}
              @endif
            </div>
            <div class="about-item-body">
              <div class="about-item-title">{{ $settings['about_item1_title'] ?? 'Resmi & Terpercaya' }}</div>
              <p class="about-item-text">{{ $settings['about_item1_text'] ?? 'Izin PPIU No. U - 207 / 2021 & PIHK 81200009510360001.' }}</p>
            </div>
          </div>
          <div class="about-item">
            <div class="about-item-icon">
              @if(!empty($settings['about_item2_img']))
                <img src="{{ optUrl($settings['about_item2_img'], 100) }}" alt="" class="about-point-img" loading="lazy" width="64" height="64">
              @else
                {{ $settings['about_item2_icon'] ?? '💎' }}
              @endif
            </div>
            <div class="about-item-body">
              <div class="about-item-title">{{ $settings['about_item2_title'] ?? 'Pembimbing Berpengalaman' }}</div>
              <p class="about-item-text">{{ $settings['about_item2_text'] ?? 'Didampingi Mutawwif & Ustaz bersertifikasi selama proses ibadah.' }}</p>
            </div>
          </div>
          <div class="about-item">
            <div class="about-item-icon">
              @if(!empty($settings['about_item3_img']))
                <img src="{{ optUrl($settings['about_item3_img'], 100) }}" alt="" class="about-point-img" loading="lazy" width="64" height="64">
              @else
                {{ $settings['about_item3_icon'] ?? '🌟' }}
              @endif
            </div>
            <div>
              <div class="about-item-title">{{ $settings['about_item3_title'] ?? 'Layanan Terpercaya' }}</div>
              <div class="about-item-desc">{{ $settings['about_item3_desc'] ?? 'Rating 4.9/5 dari ribuan jemaah yang telah diberangkatkan ke Tanah Suci.' }}</div>
            </div>
          </div>
        </div>
        <a href="#paket" class="btn btn-solid">Lihat Semua Paket →</a>
      </div>
    </div>
  </div>
</section>

<!-- PACKAGES -->
<section class="section pkg-section" id="paket">
  <div class="section-inner">
    <div class="section-header centered reveal">
      <div id="sync-sec_pkg_eye" class="sec-eyebrow">{{ $settings['sec_pkg_eye'] ?? 'Pilihan Paket' }}</div>
      <h2 id="sync-sec_pkg_title" class="sec-title">{!! $settings['sec_pkg_title'] ?? 'Paket <em>Haji & Umrah</em> Terbaik' !!}</h2>
      <p id="sync-sec_pkg_desc" class="sec-sub">Temukan paket yang sesuai dengan kebutuhan dan anggaran Anda.</p>
    </div>

    {{-- ── TAB FILTER KATEGORI ── --}}
    @php
      $allCategories = collect($packages)->pluck('category')->filter()->unique()->values();
    @endphp
    @if($allCategories->count() > 1)
    <div class="pkg-filter-tabs" id="pkgFilterTabs">
      <button class="pkg-filter-btn active" data-cat="semua" onclick="filterPaket('semua', this)">Semua</button>
      @foreach($allCategories as $cat)
      <button class="pkg-filter-btn" data-cat="{{ $cat }}" onclick="filterPaket('{{ $cat }}', this)">{{ $cat }}</button>
      @endforeach
    </div>
    @endif

    <div class="pkg-slider-wrapper">
      <div class="pkg-slider-controls">
        <button class="pkg-slider-btn prev" onclick="scrollPkg(-1)">◀</button>
        <button class="pkg-slider-btn next" onclick="scrollPkg(1)">▶</button>
      </div>
      
      <div class="pkg-grid-slider" id="pkgSlider">
        @foreach($packages as $package)
        <div class="pkg-card-wrapper" data-category="{{ $package['category'] ?? '' }}">
          <div class="pkg-card reveal">
            <div class="pkg-img">
              <img src="{{ $package['image_url'] ?? 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?w=600&q=80&fit=crop' }}" alt="{{ $package['name'] }}">
              <div class="pkg-img-overlay"></div>
              <div class="pkg-img-label">{{ $package['category'] ?? 'Paket' }}</div>
            </div>
            <div class="pkg-body">
              <div class="pkg-name">{{ $package['name'] }}</div>
              <div class="pkg-days">{{ $package['duration'] ?? '9 Hari' }} · {{ $package['departure'] ?? 'Keberangkatan' }}</div>
              <div class="pkg-price">Rp {{ number_format((float) preg_replace('/[^0-9]/', '', $package['price'] ?? 0), 0, ',', '.') }} <small>/orang</small></div>
              <div class="pkg-divider"></div>
              <div class="pkg-actions">
                <a href="{{ route('register.show', ['package' => $package['id']]) }}" class="pkg-btn btn-daftar">Daftar</a>
                <button type="button" class="pkg-btn btn-detail-outline" onclick='openPkgModal({!! json_encode([
                  "name" => $package["name"],
                  "duration" => $package["duration"] ?? "9 Hari",
                  "hotel" => $package["hotel"] ?? "Bintang 5",
                  "airline" => $package["airline"] ?? "Saudia Airlines",
                  "price" => "Rp " . number_format((float) preg_replace("/[^0-9]/", "", $package["price"] ?? 0), 0, ",", "."),
                  "features" => isset($package["features"]) ? (is_array($package["features"]) ? $package["features"] : explode(",", $package["features"])) : [],
                  "hotel_facilities" => isset($package["hotel_facilities"]) ? (is_array($package["hotel_facilities"]) ? $package["hotel_facilities"] : explode(",", $package["hotel_facilities"])) : []
                ]) !!})'>Detail</button>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</section>

<script>
function filterPaket(cat, btn) {
  // Update active tab
  document.querySelectorAll('.pkg-filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');

  // Filter cards
  document.querySelectorAll('#pkgSlider .pkg-card-wrapper').forEach(card => {
    if (cat === 'semua' || card.dataset.category === cat) {
      card.style.display = '';
    } else {
      card.style.display = 'none';
    }
  });
}
</script>

<!-- GALLERY MARQUEE -->
<div class="gallery-section" id="sync-galeri">
  <div class="gallery-inner">
    <div class="section-header gallery-header reveal">
      <div class="sec-eyebrow gallery-eyebrow-gold">{{ $settings['sec_gal_eye'] ?? 'Galeri Perjalanan' }}</div>
      <h2 class="sec-title">{!! $settings['sec_gal_title'] ?? 'Momen <em>Berkesan</em> Jemaah Kami' !!}</h2>
      <p class="sec-sub">Ribuan momen penuh makna tertangkap dalam setiap perjalanan suci bersama {{ $settings['site_name'] ?? 'PT. UMI MUTHMAINAH BERKAH' }}.</p>
    </div>
    <div class="marquee-wrap">
      <div class="marquee-row" id="row1"></div>
      <div class="marquee-row rev" id="row2"></div>
    </div>
  </div>
</div>

<!-- ITINERARY -->
<section class="section itin-section" id="jadwal">
  <div class="section-inner">
    <div id="sync-jadwal" class="section-header reveal">
      <div class="sec-eyebrow">{{ $settings['sec_itin_eye'] ?? 'Jadwal Perjalanan' }}</div>
      <h2 class="sec-title">{!! $settings['sec_itin_title'] ?? 'Alur <em>Ibadah Haji</em>' !!}</h2>
      <p class="sec-sub">Setiap langkah dirancang agar ibadah Anda khusyu' dan tertib.</p>
    </div>
    <div class="itin-grid">
      <div class="timeline reveal-left" id="sync-itin-list">
        @for($i=1; $i<=5; $i++)
        <div class="tl-item" id="sync-itin-item-{{ $i }}">
          <div class="tl-dot"></div>
          <div class="tl-day">{{ $settings['itin' . $i . '_day'] ?? ($i==1 ? 'Hari 1-3' : ($i==2 ? 'Hari 4-8' : ($i==3 ? 'Hari 9-12' : ($i==4 ? 'Hari 13-16' : 'Hari 17-21')))) }}</div>
          <div class="tl-title">{{ $settings['itin' . $i . '_title'] ?? ($i==1 ? 'Keberangkatan & Tiba di Madinah' : ($i==2 ? 'Sholat Arbain di Madinah' : ($i==3 ? 'Makkah & Umrah Wajib' : ($i==4 ? 'Puncak Haji — Arafah, Muzdalifah, Mina' : 'Tawaf Wada & Kepulangan')))) }}</div>
          <div class="tl-desc">{{ $settings['itin' . $i . '_desc'] ?? ($i==1 ? 'Kumpul di embarkasi, penerbangan ke Madinah, sambutan, check-in hotel.' : ($i==2 ? '40 waktu sholat berturut-turut di Masjid Nabawi. Ziarah Jabal Uhud, Masjid Quba.' : ($i==3 ? 'Berihram dari Miqat, perjalanan ke Makkah. Tawaf Qudum, Sa\'i, Tahallul.' : ($i==4 ? 'Wukuf di Arafah, mabit di Muzdalifah, lempar Jumroh.' : 'Tawaf Wada\' sebagai perpisahan dengan Baitullah.')))) }}</div>
        </div>
        @endfor
      </div>

      <div class="itin-aside reveal-right">
        <div class="aside-card" id="sync-jadwal-card">
          <div class="aside-card-img" 
               @if(!request()->has('preview'))
                 onclick="{{ !empty($settings['itin_aside_video']) ? 'openItinMedia(\''.$settings['itin_aside_video'].'\', \'video\')' : 'openItinMedia(\''.optUrl(($settings['itin_aside_img'] ?? 'https://images.unsplash.com/photo-1609950547341-a9e24bfeece9'), 800).'\', \'image\')' }}" 
                 style="cursor:pointer;"
               @endif>
            @if(!empty($settings['itin_aside_video']))
              <video src="{{ $settings['itin_aside_video'] }}" autoplay muted loop playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
              <div class="video-play-overlay"><span>▶</span></div>
            @else
              <img src="{{ optUrl(($settings['itin_aside_img'] ?? 'https://images.unsplash.com/photo-1609950547341-a9e24bfeece9'), 600) }}" 
                   alt="Ka'bah" loading="lazy" width="400" height="600" style="width: 100%; height: 100%; object-fit: cover;">
            @endif
            <div class="aside-card-title">{{ $settings['itin_aside_title'] ?? 'Baitullah, Makkah Al-Mukarramah' }}</div>
          </div>
          <div class="aside-card-body">
            <div class="aside-info">
              @for($i=1; $i<=3; $i++)
              <div class="aside-info-item">
                <div class="ai-icon">{{ $settings['itin_aside_i' . $i . '_icon'] ?? ($i==1 ? '📋' : ($i==2 ? '💉' : '📅')) }}</div>
                <div>
                    <div class="ai-title">{{ $settings['itin_aside_i' . $i . '_title'] ?? ($i==1 ? 'Dokumen Wajib' : ($i==2 ? 'Pemeriksaan Kesehatan' : 'Pendaftaran Awal')) }}</div>
                    <div class="ai-desc">{{ $settings['itin_aside_i' . $i . '_desc'] ?? ($i==1 ? 'Paspor berlaku min. 18 bulan, KTP, KK.' : ($i==2 ? 'Dilakukan minimal 1 bulan sebelum keberangkatan.' : 'Daftar minimal 6 bulan sebelumnya.')) }}</div>
                </div>
              </div>
              @endfor
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FACILITIES LENGKAP -->
<section class="section fac-section" id="fasilitas">
  <div class="section-inner">
    <div class="section-header centered reveal">
      <div id="sync-sec_fac_eye" class="sec-eyebrow">{{ $settings['sec_fac_eye'] ?? 'Fasilitas Lengkap' }}</div>
      <h2 id="sync-sec_fac_title" class="sec-title">{!! $settings['sec_fac_title'] ?? 'Layanan <em>Terbaik</em> Untuk Anda' !!}</h2>
      <p id="sync-sec_fac_desc" class="sec-sub">Setiap detail layanan dirancang untuk kenyamanan dan kekhusyu'an ibadah Anda.</p>
    </div>

    <div class="fac-slider-wrapper-outer">
      <div class="fac-slider-controls">
        <button class="fac-nav-btn prev no-edit" onclick="scrollFac(-1)" aria-label="Previous">
          <svg viewBox="0 0 24 24" fill="currentColor" class="no-edit"><path d="M15.41 16.59L10.83 12l4.58-4.59L14 6l-6 6 6 6 1.41-1.41z" class="no-edit"/></svg>
        </button>
        <button class="fac-nav-btn next no-edit" onclick="scrollFac(1)" aria-label="Next">
          <svg viewBox="0 0 24 24" fill="currentColor" class="no-edit"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z" class="no-edit"/></svg>
        </button>
      </div>

      <div class="fac-slider-wrapper" id="facSliderWrapper">
        <div class="fac-grid-slider" id="facSlider">
        
        @forelse($facilities as $item)
          @php 
            $finalTitle = $item['title'] ?? ($item['name'] ?? 'Fasilitas');
            $finalIcon = $item['icon'] ?? '✨';
            $finalDesc = $item['description'] ?? '';
            $finalLong = $item['description'] ?? '';
          @endphp
          <div class="fac-card-wrapper">
            <div class="fac-card reveal no-edit" onclick="showFacModal('{{ $finalIcon }}', '{{ $finalTitle }}', '{{ addslashes($finalLong) }}')">
              <div class="fac-card-icon">{{ $finalIcon }}</div>
              <div class="fac-title">{{ $finalTitle }}</div>
              <p class="fac-desc">{{ \Illuminate\Support\Str::limit($finalDesc, 80) }}</p>
            </div>
          </div>
        @empty
          <div class="empty-fac-msg" style="width: 100%; text-align: center; color: #94a3b8; padding: 40px 0;">
             Belum ada fasilitas yang ditambahkan.
          </div>
        @endforelse

        </div>

      </div>
    </div>
  </div>
</section>

<!-- MODAL FASILITAS PREMIUM -->
<div class="fac-modal-overlay" id="facModal" onclick="closeFacModal(event)">
  <div class="fac-modal-card no-edit">
    <button class="fac-modal-close" onclick="closeFacModal(event)">&times;</button>
    <div class="fac-modal-icon" id="modalIcon">✨</div>
    <h3 class="fac-modal-title" id="modalTitle">Judul Fasilitas</h3>
    <div class="fac-modal-line"></div>
    <p class="fac-modal-desc" id="modalDesc">Deskripsi lengkap fasilitas akan muncul di sini.</p>
    <button class="btn btn-solid no-edit" style="width: 100%; margin-top: 2rem;" onclick="closeFacModal(event)">Tutup</button>
  </div>
</div>

<script>
function scrollFac(dir) {
    const slider = document.getElementById('facSlider');
    const scrollAmount = 350; // Lebar kartu + gap
    slider.scrollBy({
        left: dir * scrollAmount,
        behavior: 'smooth'
    });
}

function showFacModal(icon, title, desc) {
    const modal = document.getElementById('facModal');
    if (!modal) return;
    document.getElementById('modalIcon').innerText = icon;
    document.getElementById('modalTitle').innerText = title;
    document.getElementById('modalDesc').innerText = desc;
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeFacModal(e) {
    const modal = document.getElementById('facModal');
    if (!modal) return;
    if (e.target.id === 'facModal' || e.target.classList.contains('fac-modal-close') || e.target.closest('.btn')) {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
}
</script>


<!-- TESTIMONIALS -->
<section class="section testi-section" id="testimoni">
  <div class="section-inner">
    <div class="section-header reveal centered">
      <div class="sec-eyebrow">Testimoni</div>
      <h2 class="sec-title">{!! $settings['sec_testi_title'] ?? 'Kata <em>Jemaah Kami</em>' !!}</h2>
      <p class="sec-sub">Kepuasan Anda adalah kebahagiaan kami dalam melayani tamu Allah.</p>
    </div>

    <div class="testi-marquee-wrapper">
      <div class="testi-marquee-row" id="testiRow1">
        <!-- JS will populate this -->
      </div>
      <div class="testi-marquee-row rev" id="testiRow2" style="--dur: 50s;">
        <!-- JS will populate this -->
      </div>
    </div>
  </div>
</section>

<!-- CTA BANNER -->
<section class="cta-section" id="kontak">
  <div class="cta-bg">
    <img src="{{ optUrl('https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa', 1000) }}" alt="Makkah" loading="lazy" width="1000" height="400">
  </div>
  <div class="cta-inner reveal">
    <div id="sync-kontak-eye" class="sec-eyebrow cta-eyebrow-gold">{{ $settings['sec_cta_eye'] ?? 'Mulai Perjalanan Suci' }}</div>
    <h2 id="sync-cta-title">{!! $settings['sec_cta_title'] ?? 'Siap Berangkat ke <em>Tanah Suci?</em>' !!}</h2>
    <div class="cta-simple">
      <p id="sync-cta-desc-simple" class="cta-desc-simple">Hubungi tim kami sekarang juga untuk konsultasi gratis dan informasi ketersediaan kuota. Kami siap membantu merencanakan perjalanan suci Anda.</p>
    </div>

    @php
      $wa_raw = $settings['contact_wa'] ?? '081234567890';
      $wa_clean = preg_replace('/[^0-9]/', '', $wa_raw);
      if (str_starts_with($wa_clean, '0')) {
          $wa_number = '62' . substr($wa_clean, 1);
      } elseif (str_starts_with($wa_clean, '8')) {
          $wa_number = '62' . $wa_clean;
      } else {
          $wa_number = $wa_clean;
      }
    @endphp

    <div class="cta-buttons">
      <a id="sync-sec_cta_btn_text" href="https://wa.me/{{ $wa_number }}?text={{ urlencode($settings['wa_msg_default'] ?? "Assalamu'alaikum Admin, saya ingin bertanya mengenai layanan di PT. Umi Muthmainah.") }}" target="_blank" class="btn btn-lg" style="display:inline-flex; align-items:center; gap:8px; background:transparent; border:2px solid rgba(255,255,255,0.8); color:#fff; font-weight:600;">
        <svg style="width:20px; height:20px;" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg> {{ $settings['sec_cta_btn_text'] ?? 'Hubungi Kami' }}
      </a>
    </div>
    <div id="sync-cta-contact" class="cta-contact-grid">
      <div class="cta-contact-item no-edit">
        <div class="cta-icon-box">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l2.27-2.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
        </div>
        <span style="pointer-events: none !important;">{{ $settings['contact_phone'] ?? $settings['office_phone'] ?? '0800-123-4567' }}</span>
      </div>
      <div class="cta-contact-item no-edit">
        <div class="cta-icon-box">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1-.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
        </div>
        <span style="pointer-events: none !important;">{{ $settings['contact_email'] ?? $settings['office_email'] ?? 'info@travelhaji.co.id' }}</span>
      </div>
      <div class="cta-contact-item no-edit">
        <div class="cta-icon-box">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
        </div>
        <span style="pointer-events: none !important;">WA: {{ $settings['contact_wa'] ?? '0812-3456-7890' }}</span>
      </div>
    </div>

  </div>
</section>

</main>

@include('layouts.footer')

<!-- PACKAGE DETAIL MODAL -->
<div class="modal-overlay" id="pkgModal" onclick="if(event.target===this)closePkgModal()">
  <div class="modal-box pkg-modal">
    <div class="modal-header">
        <h3 id="m-title">Detail Paket</h3>
        <button class="modal-close" onclick="closePkgModal()">✕</button>
    </div>
    <div class="modal-body">
        <div class="m-info-grid">
            <div class="m-info-item"><strong>Durasi:</strong> <span id="m-duration"></span></div>
            <div class="m-info-item"><strong>Hotel:</strong> <span id="m-hotel"></span></div>
            <div class="m-info-item"><strong>Maskapai:</strong> <span id="m-airline"></span></div>
            <div class="m-info-item"><strong>Harga:</strong> <span id="m-price" class="text-gold"></span></div>
        </div>
        <div class="m-features-list">
            <h4>📋 Fasilitas & Perlengkapan:</h4>
            <ul id="m-features"></ul>
        </div>
        <div class="m-features-list" id="m-hotel-fac-section" style="margin-top: 1.5rem; display: none;">
            <h4>🏨 Fasilitas Hotel:</h4>
            <ul id="m-hotel-fac"></ul>
        </div>
    </div>
    <div class="modal-footer">
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['contact_wa'] ?? '081234567890') }}" id="m-wa-link" target="_blank" class="btn btn-gold pkg-modal-wa-btn" style="width:100%; justify-content:center;">Tanya Admin via WhatsApp</a>

    </div>
  </div>
</div>

<!-- VIDEO MODAL -->
<div class="modal-overlay" id="videoModal" onclick="closeModal(event)">
  <div class="modal-box">
    @if(!empty($settings['hero_video_url']))
      {{-- Video sudah diupload dari admin --}}
      <div class="modal-video-placeholder" id="videoModalContent">
        <video
          id="heroVideoPlayer"
          controls
          playsinline
          preload="auto"
          style="width:100%; border-radius:12px; box-shadow:0 20px 50px rgba(0,0,0,0.5); max-height:80vh;"
        >
          <source src="{{ url($settings['hero_video_url']) }}?t={{ time() }}" type="video/mp4">
          Browser Anda tidak mendukung tag video.
        </video>
      </div>
    @else
      {{-- Belum ada video, tampilkan placeholder --}}
      <div class="modal-video-placeholder" id="videoModalContent">
        <span>🎬</span>
        <p>Video profil {{ $settings['site_name'] ?? 'PT. UMI MUTHMAINAH BERKAH' }} belum tersedia.</p>
        <p class="video-modal-help">Silakan upload video di menu Admin → Pengaturan → Video Profil.</p>
      </div>
    @endif
    <button class="modal-close" onclick="closeModalDirect()">✕</button>
  </div>
</div>

<script>
/* ── HERO SLIDESHOW ── */
const slides = document.querySelectorAll('.slide');
const dotsContainer = document.getElementById('slideDots');
let current = 0, timer;

slides.forEach((_,i)=>{
  const dot = document.createElement('div');
  dot.className = 'dot' + (i===0?' active':'');
  dot.onclick = () => goTo(i);
  dotsContainer.appendChild(dot);
});

function goTo(n){
  slides[current].classList.remove('active');
  dotsContainer.children[current].classList.remove('active');
  current = n;
  slides[current].classList.add('active');
  dotsContainer.children[current].classList.add('active');
}
function next(){ goTo((current+1) % slides.length); }
timer = setInterval(next, 5000);
document.getElementById('heroSlides').onmouseenter = () => clearInterval(timer);
document.getElementById('heroSlides').onmouseleave = () => { timer = setInterval(next, 5000); };

/* ── PARTICLES ── */
const pc = document.getElementById('particles');
for(let i=0;i<18;i++){
  const p = document.createElement('div');
  p.className = 'particle';
  const sz = Math.random()*6+3;
  p.style.width = sz + 'px';
  p.style.height = sz + 'px';
  p.style.left = Math.random() * 100 + '%';
  p.style.top = Math.random() * 80 + '%';
  p.style.setProperty('--dur', (Math.random() * 5 + 5) + 's');
  p.style.setProperty('--del', '-' + Math.random() * 8 + 's');
  pc.appendChild(p);
}

/* ── MARQUEE GALLERY ── */
@php
  $defaultGallery = [
    optUrl('https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa', 300),
    optUrl('https://images.unsplash.com/photo-1564769625092-b6df1b3e13f0', 300),
    optUrl('https://images.unsplash.com/photo-1609950547341-a9e24bfeece9', 300),
    optUrl('https://images.unsplash.com/photo-1466442929976-97f336a657be', 300),
    optUrl('https://images.unsplash.com/photo-1574120240282-60c4da46edaf', 300),
    optUrl('https://images.unsplash.com/photo-1604594849809-dfedbc827105', 300),
    optUrl('https://images.unsplash.com/photo-1515091943-9d5c0ad475af', 300),
    optUrl('https://images.unsplash.com/photo-1521295121783-8a321d551ad2', 300),
    optUrl('https://images.unsplash.com/photo-1513836279014-a89f7a76ae86', 300),
    optUrl('https://images.unsplash.com/photo-1506905925346-21bda4d32df4', 300),
  ];
  $galleryUrls = [];
  
  // Ambil FOTO (1-20)
  for($gi = 1; $gi <= 20; $gi++) {
    if(!empty($settings['gallery_img_'.$gi])) {
      $galleryUrls[] = optUrl($settings['gallery_img_'.$gi], 300);
    } elseif ($gi <= 10) {
      $galleryUrls[] = $defaultGallery[$gi - 1]; // Fallback ke default hanya untuk 10 pertama
    }
  }

  // VIDEO GALERI DINONAKTIFKAN - hanya foto yang ditampilkan
@endphp
const galleryImgs = {!! json_encode($galleryUrls) !!};

const row1 = document.getElementById('row1');
const row2 = document.getElementById('row2');

// Filter hanya foto (tanpa video)
const photoOnly = galleryImgs.filter(src => {
    const isVid = src.toLowerCase().match(/\.(mp4|webm|ogg|mov|m4v|avi|wmv|flv)/) || src.includes('videos');
    return !isVid;
});

// Pastikan minimal 10 foto untuk marquee yang mulus
let displayPhotos = photoOnly.length > 0 ? [...photoOnly] : [...galleryImgs];
while (displayPhotos.length < 10) { displayPhotos = [...displayPhotos, ...displayPhotos]; }

// Baris 1: foto arah kiri
displayPhotos.forEach(src => {
    const d = document.createElement('div');
    d.className = 'marquee-item';
    d.innerHTML = `<img src="${src}" alt="Gallery Photo" loading="lazy" width="300" height="200" onclick="openAboutLightbox('${src}')" style="cursor:zoom-in;">`;
    row1.appendChild(d);
});

// Baris 2: foto arah kanan (urutan terbalik)
[...displayPhotos].reverse().forEach(src => {
    const d = document.createElement('div');
    d.className = 'marquee-item';
    d.innerHTML = `<img src="${src}" alt="Gallery Photo" loading="lazy" width="300" height="200" onclick="openAboutLightbox('${src}')" style="cursor:zoom-in;">`;
    row2.appendChild(d);
});

/* ── TESTIMONIAL MARQUEE ── */
const testiData = {!! json_encode($testimonials->values()->toArray()) !!};
const testiRow1 = document.getElementById('testiRow1');
const testiRow2 = document.getElementById('testiRow2');

function createTestiCard(testi) {
    const card = document.createElement('div');
    card.className = 'testi-marquee-item';
    const stars = '★'.repeat(testi.rating || 5) + '☆'.repeat(5 - (testi.rating || 5));
    const firstChar = (testi.name || 'J').charAt(0);
    
    // Jika ada image, tampilkan sebagai foto utama di atas
    let imageHtml = '';
    if (testi.avatar_url) {
        imageHtml = `<img src="${testi.avatar_url}" class="testi-img-main" alt="Foto Jemaah">`;
    }

    card.innerHTML = `
        ${imageHtml}
        <div class="testi-stars" style="color: #d4a843; font-size: 0.875rem;">${stars}</div>
        <p class="testi-text">"${testi.message || testi.text || ''}"</p>
        <div class="testi-author" style="display: flex; align-items: center; gap: 0.75rem; padding-top: 1rem; border-top: 1px solid #f1f5f9; margin-top: auto;">
            <div style="overflow: hidden;">
                <div class="testi-name" style="font-weight: 700; color: #111827; font-size: 0.875rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${testi.name || 'Jemaah'}</div>
                <div class="testi-loc" style="font-size: 0.75rem; color: #6b7280;">${testi.location || testi.category || 'Pelanggan'}</div>
            </div>
        </div>
    `;
    return card;
}

// Logic pengulangan agar marquee penuh
let displayData = [...testiData];
if (displayData.length > 0) {
    while (displayData.length < 10) {
        displayData = [...displayData, ...testiData];
    }
}

// Populate rows
displayData.forEach(testi => {
    testiRow1.appendChild(createTestiCard(testi));
});
[...displayData].reverse().forEach(testi => {
    testiRow2.appendChild(createTestiCard(testi));
});

/* ── NAV SCROLL ── */
window.addEventListener('scroll',()=>{
  document.getElementById('mainNav').classList.toggle('scrolled', scrollY > 60);
});

/* ── SCROLL REVEAL ── */
const revealEls = document.querySelectorAll('.reveal,.reveal-left,.reveal-right');
const ro = new IntersectionObserver((entries)=>{
  entries.forEach((e,i)=>{
    if(e.isIntersecting){
      setTimeout(()=>e.target.classList.add('revealed'), i*60);
      ro.unobserve(e.target);
    }
  });
},{threshold:0.1});
revealEls.forEach(el=>ro.observe(el));

/* ── TESTIMONIAL SLIDER ── */
const slider = document.getElementById('testiSlider');
document.getElementById('prevBtn').onclick = ()=>{ slider.scrollBy({left:-340,behavior:'smooth'}); };
document.getElementById('nextBtn').onclick = ()=>{ slider.scrollBy({left:340,behavior:'smooth'}); };
// drag to scroll
let isDragging=false,startX,scrollLeft;
slider.addEventListener('mousedown',e=>{isDragging=true;startX=e.pageX-slider.offsetLeft;scrollLeft=slider.scrollLeft;slider.classList.add('cursor-grabbing');slider.classList.remove('cursor-grab');});
slider.addEventListener('mouseleave',()=>{isDragging=false;slider.classList.add('cursor-grab');slider.classList.remove('cursor-grabbing');});
slider.addEventListener('mouseup',()=>{isDragging=false;slider.classList.add('cursor-grab');slider.classList.remove('cursor-grabbing');});
slider.addEventListener('mousemove',e=>{if(!isDragging)return;e.preventDefault();const x=e.pageX-slider.offsetLeft;slider.scrollLeft=scrollLeft-(x-startX)*1.2;});

/* ── PACKAGE MODAL ── */
function openPkgModal(pkg) {
    const modal = document.getElementById('pkgModal');
    document.getElementById('m-title').innerText = pkg.name;
    document.getElementById('m-duration').innerText = pkg.duration || '-';
    document.getElementById('m-hotel').innerText = pkg.hotel || '-';
    document.getElementById('m-airline').innerText = pkg.airline || '-';
    document.getElementById('m-price').innerText = pkg.price || 'Hubungi Kami';
    
    // Update WA Link with Package Name
    const baseTpl = `{!! $settings['wa_msg_package'] ?? 'Assalamu\'alaikum Admin, saya tertarik dengan paket [NAMA_PAKET]. Mohon info detail pendaftarannya. Syukron.' !!}`;
    const finalMsg = baseTpl.replace('[NAMA_PAKET]', pkg.name);
    const waNum = '{{ $wa_number }}';
    document.getElementById('m-wa-link').href = `https://wa.me/${waNum}?text=${encodeURIComponent(finalMsg)}`;

    const list = document.getElementById('m-features');
    list.innerHTML = '';
    (pkg.features || []).forEach(f => {
        const li = document.createElement('li');
        li.innerText = f;
        list.appendChild(li);
    });

    const hotelFacSection = document.getElementById('m-hotel-fac-section');
    const hotelFacList = document.getElementById('m-hotel-fac');
    hotelFacList.innerHTML = '';
    
    if (pkg.hotel_facilities && pkg.hotel_facilities.length > 0) {
        hotelFacSection.style.display = 'block';
        pkg.hotel_facilities.forEach(f => {
            const li = document.createElement('li');
            li.innerText = f;
            hotelFacList.appendChild(li);
        });
    } else {
        hotelFacSection.style.display = 'none';
    }
    
    modal.classList.add('open');
}
function closePkgModal() { document.getElementById('pkgModal').classList.remove('open'); }

/* ── PAKET DROPDOWN TOGGLE ── */
function togglePaketMenu(e) {
    e.stopPropagation();
    const menu = document.getElementById('paketMenu');
    const chevron = document.getElementById('paketChevron');
    const isOpen = menu.classList.contains('open');
    if (isOpen) {
        menu.classList.remove('open');
        chevron.classList.remove('chevron-open');
    } else {
        menu.classList.add('open');
        chevron.classList.add('chevron-open');
    }
}
function closePaketMenu() {
    const menu = document.getElementById('paketMenu');
    const chevron = document.getElementById('paketChevron');
    if (menu) { menu.classList.remove('open'); }
    if (chevron) { chevron.classList.remove('chevron-open'); }
}
// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('#paketDropdown')) { closePaketMenu(); }
});

/* ── VIDEO MODAL ── */
function openModal(){
    document.getElementById('videoModal').classList.add('open');
    // Paksa video muat ulang & play
    const vid = document.getElementById('heroVideoPlayer');
    if (vid) { 
        vid.load(); // Paksa reload source terbaru
        vid.play().catch(() => {}); 
    }
}
function closeModal(e){if(e.target===document.getElementById('videoModal'))closeModalDirect();}
function closeModalDirect(){
    document.getElementById('videoModal').classList.remove('open');
    // Pause & reset video saat modal ditutup
    const vid = document.getElementById('heroVideoPlayer');
    if (vid) { vid.pause(); vid.currentTime = 0; }
}
document.addEventListener('keydown',e=>{
    if(e.key==='Escape') {
        closeModalDirect();
        closePkgModal();
        closeMobileNav();
    }
});

/* ── MOBILE NAV ── */
function toggleMobileNav() {
    const drawer   = document.getElementById('mobileNavDrawer');
    const overlay  = document.getElementById('mobileNavOverlay');
    const toggle   = document.getElementById('navToggle');
    const isOpen   = drawer.classList.contains('open');
    if (isOpen) {
        closeMobileNav();
    } else {
        drawer.classList.add('open');
        overlay.style.display = 'block';
        requestAnimationFrame(() => overlay.classList.add('open'));
        toggle.classList.add('open');
        toggle.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }
}

function closeMobileNav() {
    const drawer  = document.getElementById('mobileNavDrawer');
    const overlay = document.getElementById('mobileNavOverlay');
    const toggle  = document.getElementById('navToggle');
    if (!drawer) return;
    drawer.classList.remove('open');
    overlay.classList.remove('open');
    toggle.classList.remove('open');
    toggle.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
    setTimeout(() => { overlay.style.display = 'none'; }, 300);
}

function toggleMobileSubMenu(btn) {
    const submenu = btn.nextElementSibling;
    const isOpen  = submenu.classList.contains('open');
    // Tutup semua submenu lain
    document.querySelectorAll('.mobile-nav-submenu.open').forEach(m => {
        m.classList.remove('open');
        m.previousElementSibling.classList.remove('open');
    });
    if (!isOpen) {
        submenu.classList.add('open');
        btn.classList.add('open');
    }
}

/* ── PACKAGE SLIDER ── */
function scrollPkg(dir) {
    const slider = document.getElementById('pkgSlider');
    const scrollAmount = 370; // card width + gap
    slider.scrollBy({
        left: dir * scrollAmount,
        behavior: 'smooth'
    });
}

/* ── NOTIFICATION SYSTEM ── */
function showNotification(message, type = 'success') {
    // Buat elemen notifikasi
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.innerHTML = `
        <div class="notification-content">
            <div class="notification-icon ${type}">${type === 'success' ? 'checkmark' : 'error'}</div>
            <div class="notification-message">${message}</div>
        </div>
    `;
    
    // Tambahkan ke body
    document.body.appendChild(notification);
    
    // Tampilkan dengan animasi
    setTimeout(() => notification.classList.add('show'), 100);
    
    // Hapus setelah 4 detik
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 4000);
}

/* CONTACT FORM HANDLER */
function handleContactSubmit(event) {
    event.preventDefault();
    
    const form = event.target;
    const submitBtn = form.querySelector('.form-submit-btn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnSpinner = submitBtn.querySelector('.btn-spinner');
    
    // Get form values
    const formData = {
        nama: form.nama.value.trim(),
        telepon: form.telepon.value.trim(),
        email: form.email.value.trim(),
        paket: form.paket.value,
        pesan: form.pesan.value.trim()
    };
    
    // Validasi dasar
    if (!formData.nama || !formData.telepon || !formData.email) {
        showNotification('Mohon lengkapi semua field yang wajib diisi!', 'error');
        return;
    }
    
    // Validasi email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(formData.email)) {
        showNotification('Format email tidak valid!', 'error');
        return;
    }
    
    // Validasi telepon (minimal 10 digit)
    const phoneRegex = /^[0-9]{10,}$/;
    if (!phoneRegex.test(formData.telepon.replace(/[^0-9]/g, ''))) {
        showNotification('Nomor telepon minimal 10 digit!', 'error');
        return;
    }
    
    // Tampilkan loading state
    submitBtn.disabled = true;
    btnText.style.display = 'none';
    btnSpinner.style.display = 'inline-block';
    submitBtn.style.position = 'relative';
    
    // Kirim data ke backend via AJAX
    fetch('{{ route("contact.submit") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        // Reset button state
        submitBtn.disabled = false;
        btnText.style.display = 'inline';
        btnSpinner.style.display = 'none';

        if (data.success) {
            // Reset form
            form.reset();
            
            // Tampilkan notifikasi sukses
            showNotification(data.message);
            
            // Optional: Auto fill WhatsApp dengan data formulir
            const waMessage = encodeURIComponent(`Halo, saya tertarik dengan paket ${formData.paket || 'yang tersedia'}. Nama: ${formData.nama}, Telepon: ${formData.telepon}, Email: ${formData.email}${formData.pesan ? ', Pesan: ' + formData.pesan : ''}`);
            const waNumber = '{{ preg_replace('/[^0-9]/', '', $settings['contact_wa'] ?? '081234567890') }}';
            
            // Tampilkan info tambahan untuk WhatsApp
            setTimeout(() => {
                showNotification('Atau hubungi kami langsung via WhatsApp untuk respons lebih cepat!', 'info');
            }, 2000);
        } else {
            showNotification('Gagal mengirim pesan. Silakan coba lagi nanti.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        submitBtn.disabled = false;
        btnText.style.display = 'inline';
        btnSpinner.style.display = 'none';
        showNotification('Terjadi kesalahan pada server. Silakan hubungi via WhatsApp.', 'error');
    });
}

/* FORM INPUT ENHANCEMENTS */
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Loaded - Initializing form handlers');
    
    // Get the contact form
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        console.log('Contact form found, adding submit listener');
        
        // Remove existing handler if any
        contactForm.removeEventListener('submit', handleContactSubmit);
        
        // Add submit listener
        contactForm.addEventListener('submit', handleContactSubmit);
        
        // Also keep the inline handler as backup
        contactForm.setAttribute('onsubmit', 'handleContactSubmit(event)');
    } else {
        console.error('Contact form not found!');
    }
    
    // Add focus effects to form inputs
    const formInputs = document.querySelectorAll('.form-input');
    console.log('Found form inputs:', formInputs.length);
    
    formInputs.forEach((input, index) => {
        console.log(`Setting up input ${index}:`, input.id || input.type);
        
        // Make sure input is clickable
        input.style.pointerEvents = 'auto';
        input.style.cursor = 'text';
        
        // Focus effect
        input.addEventListener('focus', function() {
            console.log('Input focused:', this.id || this.type);
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
            if (this.value.trim()) {
                this.parentElement.classList.add('filled');
            } else {
                this.parentElement.classList.remove('filled');
            }
        });
        
        // Click test
        input.addEventListener('click', function() {
            console.log('Input clicked:', this.id || this.type);
        });
        
        // Check if input has value on load
        if (input.value.trim()) {
            input.parentElement.classList.add('filled');
        }
    });
    
    // Setup submit button
    const submitBtn = document.querySelector('.form-submit-btn');
    if (submitBtn) {
        console.log('Submit button found');
        submitBtn.style.pointerEvents = 'auto';
        submitBtn.style.cursor = 'pointer';
        
        submitBtn.addEventListener('click', function(e) {
            console.log('Submit button clicked');
        });
    } else {
        console.error('Submit button not found!');
    }
    
    // Add input validation feedback
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('telepon');
    
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailRegex.test(this.value)) {
                this.classList.add('error');
                showNotification('Format email tidak valid!', 'error');
            } else {
                this.classList.remove('error');
            }
        });
    }
    
    if (phoneInput) {
        phoneInput.addEventListener('blur', function() {
            const phoneRegex = /^[0-9]{10,}$/;
            const cleanPhone = this.value.replace(/[^0-9]/g, '');
            if (this.value && !phoneRegex.test(cleanPhone)) {
                this.classList.add('error');
                showNotification('Nomor telepon minimal 10 digit!', 'error');
            } else {
                this.classList.remove('error');
            }
        });
    }
    
    // Test form interaction
    console.log('Form setup complete. Testing interaction...');
    setTimeout(() => {
        const firstInput = document.querySelector('.form-input');
        if (firstInput) {
            console.log('First input found:', firstInput.id || firstInput.type);
            firstInput.focus();
            setTimeout(() => firstInput.blur(), 100);
        }
    }, 1000);
});

/* ── LIVE SYNC LISTENER ── */
let syncTimeout = null;
window.addEventListener('message', function(event) {
    if (event.data.type === 'SYNC_COLOR') {
        const d = event.data;
        if (d.primary) {
            document.documentElement.style.setProperty('--green', d.primary);
            document.documentElement.style.setProperty('--green2', d.primary + 'ee');
            document.documentElement.style.setProperty('--green3', d.primary + 'cc');
            document.documentElement.style.setProperty('--green-light', d.primary + '15');
            document.documentElement.style.setProperty('--about-badge', d.primary);
        }
        if (d.secondary) {
            document.documentElement.style.setProperty('--gold', d.secondary);
            document.documentElement.style.setProperty('--gold2', d.secondary + 'ee');
            document.documentElement.style.setProperty('--gold-light', d.secondary + '15');
        }
    }

    if (event.data.type === 'SYNC_FONT_SIZE') {
        const els = document.querySelectorAll(event.data.target);
        els.forEach(el => { el.style.fontSize = (el.tagName === 'H1') ? `clamp(1.5rem, 3.5vw, ${event.data.size})` : event.data.size; });
    }
    
    if (event.data.type === 'SYNC_SCROLL') {
        const targetId = event.data.target;
        console.log('[Preview] Scroll request received for:', targetId);
        if (!targetId) return;

        const el = document.querySelector(targetId);
        if (el) {
            console.log('[Preview] Found element, scrolling...');
            document.querySelectorAll('.sync-highlight').forEach(h => h.classList.remove('sync-highlight'));
            if (window.syncTimeout) clearTimeout(window.syncTimeout);

            try {
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } catch(e) {
                const offset = el.getBoundingClientRect().top + window.pageYOffset - (window.innerHeight / 2);
                window.scrollTo({ top: offset, behavior: 'smooth' });
            }

            el.classList.add('sync-highlight');
            window.syncTimeout = setTimeout(() => {
                el.classList.remove('sync-highlight');
            }, 5000);
        }
    }
});

// Styles for highlighting (Inserted dynamically)
const style = document.createElement('style');
style.textContent = `
    .sync-highlight {
        outline: 6px solid #10b981 !important;
        outline-offset: -2px !important;
        box-shadow: inset 0 0 100px rgba(16, 185, 129, 0.4), 0 0 80px rgba(16, 185, 129, 0.7) !important;
        animation: pulse-sync 1.5s ease-in-out infinite !important;
        background-color: rgba(16, 185, 129, 0.1) !important;
        position: relative !important;
        z-index: 1000 !important;
        transition: all 0.3s ease !important;
    }
    @keyframes pulse-sync {
        0%, 100% { outline-color: rgba(16, 185, 129, 0.5); transform: scale(1); }
        50% { outline-color: rgba(16, 185, 129, 1); outline-width: 8px; transform: scale(1.002); }
    }
`;
document.head.appendChild(style);
    // --- ENHANCED VISUAL EDITOR LOGIC ---
    if (window.location.search.includes('preview=1')) {
        document.body.classList.add('editor-mode');
        
        // Add more comprehensive editor styles
        const editorStyle = document.createElement('style');
        editorStyle.textContent = `
            .editor-mode [data-sync-target], 
            .editor-mode h1, .editor-mode h2, .editor-mode h3, .editor-mode h4, .editor-mode h5, 
            .editor-mode p:not(.no-edit), .editor-mode .btn, .editor-mode span.badge {
                cursor: pointer !important;
                position: relative !important;
                outline: 2px dashed transparent !important;
                transition: all 0.2s !important;
            }
            .editor-mode img { 
                cursor: pointer !important; 
                filter: brightness(1) !important;
                transition: all 0.3s !important;
            }
            .editor-mode img:hover { 
                filter: brightness(0.8) sepia(0.2) !important;
                outline: 3px solid #10b981 !important;
            }
            .editor-mode [data-sync-target]:hover, 
            .editor-mode h1:hover, .editor-mode h2:hover, .editor-mode h3:hover, 
            .editor-mode p:hover:not(.no-edit) {
                outline: 2px dashed #10b981 !important;
                background: rgba(16, 185, 129, 0.05) !important;
            }
            .editor-mode [contenteditable="true"]:focus {
                outline: 2px solid #10b981 !important;
                background: white !important;
                color: #111827 !important;
                cursor: text !important;
            }
            /* MATIKAN TOTAL ELEMEN NO-EDIT */
            .editor-mode .no-edit, 
            .editor-mode .no-edit * {
                pointer-events: none !important;
                user-select: none !important;
                cursor: default !important;
                outline: none !important;
                background: transparent !important;
            }
        `;
        document.head.appendChild(editorStyle);

        // Make text elements editable (INCLUDING links and buttons now that navigation is disabled)
        const textElements = document.querySelectorAll('h1, h2, h3, h4, h5, p, .btn, span.badge, [data-sync-target]');
        textElements.forEach(el => {
            // JANGAN JADIKAN EDITABLE JIKA ADA CLASS no-edit ATAU DI DALAMNYA
            if (el.closest('.no-edit')) return;

            if (!el.querySelector('img')) { // Don't make containers with images editable as text
                el.setAttribute('contenteditable', 'true');
                el.setAttribute('spellcheck', 'false');
                
                el.addEventListener('click', (e) => {
                    e.preventDefault(); // Stop navigation
                    el.focus();
                });

                el.addEventListener('input', (e) => {
                    const targetId = el.getAttribute('data-sync-target') || '#' + el.id;
                    window.parent.postMessage({ type: 'INLINE_CHANGE', target: targetId, value: el.innerText }, '*');
                });
            }
        });

        // --- AGGRESSIVE NAVIGATION BLOCKER ---
        window.addEventListener('click', function(e) {
            // Check if we clicked a link or something that acts like one
            let target = e.target.closest('a') || e.target.closest('button') || (e.target.onclick ? e.target : null);
            
            if (target) {
                // JIKA ELEMEN MEMILIKI CLASS no-edit, BIARKAN BEKERJA (Untuk Slider)
                if (target.closest('.no-edit')) return;

                // If it's an image, let it pass to PICK_IMAGE logic
                if (e.target.tagName === 'IMG') return;

                // Otherwise, KILL the event completely
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                // If it's a text element, focus it
                if (target.hasAttribute('contenteditable')) {
                    target.focus();
                }
            }
        }, true); // Use Capture phase to catch it first!

        // Make images clickable to upload
        const images = document.querySelectorAll('img, .logo-img');
        images.forEach(img => {
            img.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                let targetInput = 'logo';
                if (img.closest('.hero')) targetInput = 'hero_image';
                if (img.closest('.about')) targetInput = 'about_image';
                
                window.parent.postMessage({ type: 'PICK_IMAGE', target: targetInput }, '*');
            }, true);
        });
    }
</script>
    <link rel="stylesheet" href="{{ asset('css/beranda-ekstra.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('css/beranda-modern.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('css/tata-letak-atas.css') }}" media="print" onload="this.media='all'">
</body>
</html>

