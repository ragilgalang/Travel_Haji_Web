@php
    function optUrl($url, $w = 800) {
        if (strpos($url, 'images.unsplash.com') !== false) {
            // Remove existing query parameters
            $base = explode('?', $url)[0];
            return $base . "?w={$w}&q=70&auto=format,compress&fm=webp&fit=crop";
        }
        return $url;
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

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
    <meta name="description" content="{{ $settings['site_description'] ?? 'Penyelenggara Perjalanan Ibadah Haji & Umrah Resmi Kemenag RI. Amanah, Nyaman, dan Berpengalaman.' }}">
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
        /* DYNAMIC BRANDING */
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

        /* PACKAGE SLIDER CSS */
        .pkg-slider-wrapper {
            position: relative;
            padding: 0 1rem;
            margin-top: 2rem;
        }

        .pkg-grid-slider {
            display: flex;
            gap: 2rem;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding: 1rem 0.5rem 3rem;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE/Edge */
        }

        .pkg-grid-slider::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }

        .pkg-card-wrapper {
            flex: 0 0 350px;
            max-width: 350px;
        }

        .pkg-slider-controls {
            position: absolute;
            top: -60px;
            right: 1.5rem;
            display: flex;
            gap: 1rem;
            z-index: 10;
        }

        .pkg-slider-btn {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: white;
            border: 1px solid var(--green-light);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            color: var(--green);
        }

        .pkg-slider-btn:hover {
            background: var(--green);
            color: white;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .pkg-card-wrapper {
                flex: 0 0 300px;
            }
            .pkg-slider-controls {
                position: static;
                justify-content: center;
                margin-bottom: 1rem;
            }
        }

        /* FACILITY SLIDER CSS */
        .fac-slider-wrapper {
            position: relative;
            margin-top: 2rem;
        }

        .fac-grid-slider {
            display: flex;
            gap: 1.5rem;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding: 1rem 0.5rem 2rem;
            scrollbar-width: none;
        }

        .fac-grid-slider::-webkit-scrollbar {
            display: none;
        }

        .fac-card-wrapper {
            flex: 0 0 300px;
            max-width: 300px;
        }

        .fac-slider-controls {
            position: absolute;
            top: -60px;
            right: 0;
            display: flex;
            gap: 1rem;
            z-index: 10;
        }

        /* TESTI MARQUEE CSS */
        .testi-marquee-wrapper {
            overflow: hidden;
            padding: 2rem 0;
            position: relative;
        }

        .testi-marquee-row {
            display: flex;
            gap: 2rem;
            width: max-content;
            animation: marquee-scroll var(--dur, 40s) linear infinite;
            margin-bottom: 2rem;
        }

        .testi-marquee-row.rev {
            animation-direction: reverse;
        }

        .testi-marquee-row:hover {
            animation-play-state: paused;
        }

        .testi-marquee-item {
            flex: 0 0 350px;
            background: white;
            padding: 2rem;
            border-radius: 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.05);
        }

        @keyframes marquee-scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
    </style>
    
    <!-- STYLESHEETS -->
    <link rel="stylesheet" href="{{ asset('css/tata-letak-atas.css') }}">
    <link rel="stylesheet" href="{{ asset('css/beranda-lama.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tata-letak-bawah.css') }}">
    <link rel="stylesheet" href="{{ asset('css/beranda-modern.css') }}">
    <link rel="stylesheet" href="{{ asset('css/beranda-ekstra.css') }}">
    
    <noscript>
        <link rel="stylesheet" href="{{ asset('css/tema.css') }}">
        <link rel="stylesheet" href="{{ asset('css/beranda-ekstra.css') }}">
    </noscript>

    <style>
        /* VISUAL EDITOR STYLES */
        [contenteditable="true"] { outline: 2px dashed transparent; transition: 0.2s; }
        [contenteditable="true"]:hover { outline: 2px dashed #d4a843; background: rgba(212, 168, 67, 0.05); }
        [contenteditable="true"]:focus { outline: 2px solid #d4a843; background: rgba(212, 168, 67, 0.1); }
        .edit-image-trigger { cursor: pointer; }
        
        /* MATIKAN TOTAL ELEMEN NO-EDIT */
        .no-edit, .no-edit * {
            pointer-events: none !important;
            user-select: none !important;
            cursor: default !important;
            outline: none !important;
        }
    </style>

</head>
<body>

@include('layouts.header')

<main id="main-content">

<!-- HERO with Slideshow -->
<section class="hero" id="sync-hero">
  <!-- Panggilan file background yang sudah dipisah -->
  @include('layouts.hero_background')

  <div class="hero-content">
    <div id="sync-hero_badge" class="hero-badge" contenteditable="true"><span></span> {{ $settings['hero_badge'] ?? 'Terdaftar Resmi Kemenag RI' }}</div>
    {{-- Badge 2 --}}
    <div id="sync-hero_badge_2" class="hero-badge" contenteditable="true" style="margin-top: 10px;"><span></span> {{ $settings['hero_badge_2'] ?? 'PROVIDER VISA UMRAH & HAJI KHUSUS' }}</div>
    
    <h1 id="sync-hero_title" contenteditable="true">{!! $settings['hero_title'] ?? 'Wujudkan Perjalanan<br><em>Suci ke Baitullah</em>' !!}</h1>
    <p id="sync-hero_description" class="hero-desc" contenteditable="true">{{ $settings['hero_description'] ?? 'Lebih dari 12.000 jemaah telah kami antarkan ke Tanah Suci dengan aman, nyaman, dan penuh keberkahan.' }}</p>
    <div class="hero-cta">
      <a href="javascript:void(0)" class="btn btn-gold btn-lg">Lihat Paket Haji</a>
      <button class="btn-play" type="button">
        <div class="play-circle">▶</div>
        Tonton Video Profil
      </button>
    </div>
  </div>

  <div class="slide-dots" id="slideDots"></div>

  <div id="sync-hero-float" class="hero-float">
    <div class="nav-logo">
    <div class="nav-logo-box">
      @if(isset($settings['logo_url']) && $settings['logo_url'])
        <img src="{{ $settings['logo_url'] }}" alt="Logo" class="nav-logo-img">
      @else
        🕌
      @endif
    </div>
    <div class="nav-brand">{{ $settings['brand_name'] ?? 'Nusa' }}<span>{{ $settings['brand_tagline'] ?? 'Haji' }}</span></div>
  </div>
    <div id="sync-h_float1">
      <div class="hf-num" style="pointer-events: none;">{{ $settings['hero_float1_num'] ?? '4.9/5' }}</div>
      <div class="hf-label" style="pointer-events: none;">Rating Jemaah</div>
    </div>
    <div class="hero-divider"></div>
    <div id="sync-h_float2">
      <div class="hf-num" style="pointer-events: none;">{{ $kepuasan_jemaah ?? '99%' }}</div>
      <div class="hf-label" style="pointer-events: none;">Kepuasan</div>
    </div>
  </div>

  <div class="scroll-hint">
    <span>Scroll</span>
    <div class="scroll-hint-line"></div>
  </div>
</section>

<!-- STATS -->
<div id="sync-stats" class="stats-bar">
  <div class="stats-inner">
    <div class="stat-item reveal" id="sync-stat1_num">
      <div class="stat-num" style="pointer-events: none;">{{ $total_jemaah_arsip ?? '12.000+' }}</div>
      <div class="stat-label" style="pointer-events: none;">Jemaah Diberangkatkan</div>
    </div>
    <div class="stat-item reveal delay-100" id="sync-stat2_num">
      <div class="stat-num" style="pointer-events: none;">{{ $settings['stat2_num'] ?? '20+' }}</div>
      <div class="stat-label" style="pointer-events: none;">{{ $settings['stat2_label'] ?? 'Tahun Pengalaman' }}</div>
    </div>
    <div class="stat-item reveal delay-200" id="sync-stat3_num">
      <div class="stat-num" style="pointer-events: none;">{{ $kepuasan_jemaah ?? '99%' }}</div>
      <div class="stat-label" style="pointer-events: none;">Kepuasan Jemaah</div>
    </div>
    <div class="stat-item reveal delay-300" id="sync-stat4_num">
      <div class="stat-num" style="pointer-events: none;">{{ $settings['stat4_num'] ?? '15+' }}</div>
      <div class="stat-label" style="pointer-events: none;">{{ $settings['stat4_label'] ?? 'Kota Keberangkatan' }}</div>
    </div>
  </div>
</div>

<!-- ABOUT -->
<section class="section about-section" id="tentang">
  <div class="section-inner">
    <div class="about-grid">
      <!-- Panggilan file background/gambar animasi Tentang Kami -->
      @include('layouts.about_background')

      <div class="reveal-right">
        <div id="sync-about_badge" class="sec-eyebrow" contenteditable="true">Tentang {{ $settings['site_name'] ?? 'PT. UMI MUTHMAINAH BERKAH' }}</div>
        <h2 id="sync-about_title" class="sec-title" contenteditable="true">{!! $settings['about_title'] ?? 'Melayani Sepenuh Hati Sejak <em>2014</em>' !!}</h2>
        <p id="sync-about_description" class="sec-sub about-desc-no-mb" contenteditable="true">{{ $settings['about_description'] ?? 'PT. Umi Muthmainah Berkah hadir untuk memberikan pengalaman ibadah terbaik bagi Anda. Dengan komitmen pada kualitas pelayanan dan bimbingan ibadah sesuai sunnah, kami telah mendampingi ribuan jemaah mewujudkan impian mereka ke Tanah Suci.' }}</p>
        <div class="about-list" id="sync-about_points">
          <div class="about-item">
            <div class="about-item-icon">
              @if(!empty($settings['about_item1_img']))
                <img src="{{ optUrl($settings['about_item1_img'], 100) }}" alt="" class="about-point-img" loading="lazy" width="64" height="64">
              @else
                <span id="sync-about_item1_icon" contenteditable="true">{{ $settings['about_item1_icon'] ?? '🕋' }}</span>
              @endif
            </div>
            <div class="about-item-body">
              <div class="about-item-title" id="sync-about_item1_title" contenteditable="true">{{ $settings['about_item1_title'] ?? 'Resmi & Terpercaya' }}</div>
              <p class="about-item-text" id="sync-about_item1_text" contenteditable="true">{{ $settings['about_item1_text'] ?? 'Izin PPIU No. U - 207 / 2021 & PIHK 81200009510360001.' }}</p>
            </div>
          </div>
          <div class="about-item">
            <div class="about-item-icon">
              @if(!empty($settings['about_item2_img']))
                <img src="{{ optUrl($settings['about_item2_img'], 100) }}" alt="" class="about-point-img" loading="lazy" width="64" height="64">
              @else
                <span id="sync-about_item2_icon" contenteditable="true">{{ $settings['about_item2_icon'] ?? '💎' }}</span>
              @endif
            </div>
            <div class="about-item-body">
              <div class="about-item-title" id="sync-about_item2_title" contenteditable="true">{{ $settings['about_item2_title'] ?? 'Pembimbing Berpengalaman' }}</div>
              <p class="about-item-text" id="sync-about_item2_text" contenteditable="true">{{ $settings['about_item2_text'] ?? 'Didampingi Mutawwif & Ustaz bersertifikasi selama proses ibadah.' }}</p>
            </div>
          </div>
          <div class="about-item">
            <div class="about-item-icon">
              @if(!empty($settings['about_item3_img']))
                <img src="{{ optUrl($settings['about_item3_img'], 100) }}" alt="" class="about-point-img" loading="lazy" width="64" height="64">
              @else
                <span id="sync-about_item3_icon" contenteditable="true">{{ $settings['about_item3_icon'] ?? '🌟' }}</span>
              @endif
            </div>
            <div>
              <div class="about-item-title" id="sync-about_item3_title" contenteditable="true">{{ $settings['about_item3_title'] ?? 'Layanan Terpercaya' }}</div>
              <div class="about-item-desc" id="sync-about_item3_desc" contenteditable="true">{{ $settings['about_item3_desc'] ?? 'Rating 4.9/5 dari ribuan jemaah yang telah diberangkatkan ke Tanah Suci.' }}</div>
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
      <div id="sync-sec-pkg-eye" class="sec-eyebrow" contenteditable="true">{{ $settings['sec_pkg_eye'] ?? 'Pilihan Paket' }}</div>
      <h2 id="sync-sec-pkg-title" class="sec-title" contenteditable="true">{!! $settings['sec_pkg_title'] ?? 'Paket <em>Haji & Umrah</em> Terbaik' !!}</h2>
      <p id="sync-sec-pkg-sub" class="sec-sub" contenteditable="true">Temukan paket yang sesuai dengan kebutuhan dan anggaran Anda.</p>
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
      <div id="sync-sec-gal-eye" class="sec-eyebrow gallery-eyebrow-gold" contenteditable="true">{{ $settings['sec_gal_eye'] ?? 'Galeri Perjalanan' }}</div>
      <h2 id="sync-sec-gal-title" class="sec-title" contenteditable="true">{!! $settings['sec_gal_title'] ?? 'Momen <em>Berkesan</em> Jemaah Kami' !!}</h2>
      <p id="sync-sec-gal-sub" class="sec-sub" contenteditable="true">Ribuan momen penuh makna tertangkap dalam setiap perjalanan suci bersama {{ $settings['site_name'] ?? 'PT. UMI MUTHMAINAH BERKAH' }}.</p>
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
    <div id="sync-jadwal-header" class="section-header reveal">
      <div id="sync-sec-itin-eye" class="sec-eyebrow" contenteditable="true">{{ $settings['sec_itin_eye'] ?? 'Jadwal Perjalanan' }}</div>
      <h2 id="sync-sec-itin-title" class="sec-title" contenteditable="true">{!! $settings['sec_itin_title'] ?? 'Alur <em>Ibadah Haji</em>' !!}</h2>
      <p id="sync-sec-itin-sub" class="sec-sub" contenteditable="true">Setiap langkah dirancang agar ibadah Anda khusyu' dan tertib.</p>
    </div>
    <div class="itin-grid">
      <div class="timeline reveal-left" id="sync-itin-list">
        @for($i=1; $i<=5; $i++)
        <div class="tl-item" id="sync-itin-item-{{ $i }}">
          <div class="tl-dot"></div>
          <div class="tl-day" id="sync-itin{{ $i }}-day" contenteditable="true">{{ $settings['itin' . $i . '_day'] ?? ($i==1 ? 'Hari 1-3' : ($i==2 ? 'Hari 4-8' : ($i==3 ? 'Hari 9-12' : ($i==4 ? 'Hari 13-16' : 'Hari 17-21')))) }}</div>
          <div class="tl-title" id="sync-itin{{ $i }}-title" contenteditable="true">{{ $settings['itin' . $i . '_title'] ?? ($i==1 ? 'Keberangkatan & Tiba di Madinah' : ($i==2 ? 'Sholat Arbain di Madinah' : ($i==3 ? 'Makkah & Umrah Wajib' : ($i==4 ? 'Puncak Haji — Arafah, Muzdalifah, Mina' : 'Tawaf Wada & Kepulangan')))) }}</div>
          <div class="tl-desc" id="sync-itin{{ $i }}-desc" contenteditable="true">{{ $settings['itin' . $i . '_desc'] ?? ($i==1 ? 'Kumpul di embarkasi, penerbangan ke Madinah, sambutan, check-in hotel.' : ($i==2 ? '40 waktu sholat berturut-turut di Masjid Nabawi. Ziarah Jabal Uhud, Masjid Quba.' : ($i==3 ? 'Berihram dari Miqat, perjalanan ke Makkah. Tawaf Qudum, Sa\'i, Tahallul.' : ($i==4 ? 'Wukuf di Arafah, mabit di Muzdalifah, lempar Jumroh.' : 'Tawaf Wada\' sebagai perpisahan dengan Baitullah.')))) }}</div>
        </div>
        @endfor
      </div>

      <div class="itin-aside reveal-right">
        <div class="aside-card" id="sync-jadwal-card">
          <div class="aside-card-img">
            <img src="{{ optUrl(($settings['itin_aside_img'] ?? 'https://images.unsplash.com/photo-1609950547341-a9e24bfeece9'), 600) }}" alt="Ka'bah" loading="lazy" width="400" height="600">
            <div class="aside-card-title">{{ $settings['itin_aside_title'] ?? 'Baitullah, Makkah Al-Mukarramah' }}</div>
          </div>
          <div class="aside-card-body">
            <div class="aside-info">
              @for($i=1; $i<=3; $i++)
              <div class="aside-info-item">
                <div class="ai-icon" id="sync-itin-aside-i{{ $i }}-icon" contenteditable="true">{{ $settings['itin_aside_i' . $i . '_icon'] ?? ($i==1 ? '📋' : ($i==2 ? '💉' : '📅')) }}</div>
                <div>
                    <div class="ai-title" id="sync-itin-aside-i{{ $i }}-title" contenteditable="true">{{ $settings['itin_aside_i' . $i . '_title'] ?? ($i==1 ? 'Dokumen Wajib' : ($i==2 ? 'Pemeriksaan Kesehatan' : 'Pendaftaran Awal')) }}</div>
                    <div class="ai-desc" id="sync-itin-aside-i{{ $i }}-desc" contenteditable="true">{{ $settings['itin_aside_i' . $i . '_desc'] ?? ($i==1 ? 'Paspor berlaku min. 18 bulan, KTP, KK.' : ($i==2 ? 'Dilakukan minimal 1 bulan sebelum keberangkatan.' : 'Daftar minimal 6 bulan sebelumnya.')) }}</div>
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

<!-- FACILITIES -->
<section class="section fac-section" id="fasilitas">
  <div class="section-inner">
    <div class="section-header centered reveal">
      <div id="sync-sec-fac-eye" class="sec-eyebrow" contenteditable="true">{{ $settings['sec_fac_eye'] ?? 'Fasilitas Lengkap' }}</div>
      <h2 id="sync-sec-fac-title" class="sec-title" contenteditable="true">{!! $settings['sec_fac_title'] ?? 'Yang Anda <em>Dapatkan</em>' !!}</h2>
      <p id="sync-sec-fac-sub" class="sec-sub" contenteditable="true">{{ $settings['sec_fac_sub'] ?? 'Setiap detail layanan dirancang untuk kenyamanan dan kekhusyu\'an ibadah Anda.' }}</p>
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
        
        {{-- 1. Fasilitas Penerbangan (Statis) --}}
        <div class="fac-card-wrapper">
          <div class="fac-card reveal no-edit" onclick="showFacModal('✈️', 'Penerbangan', 'Nikmati perjalanan ibadah yang nyaman dengan maskapai kelas premium. Kami menyediakan tiket pesawat pulang-pergi (PP) dengan rute langsung menuju Jeddah atau Madinah untuk meminimalisir kelelahan jemaah selama di perjalanan.')">
            <div class="fac-card-icon">✈️</div>
            <div class="fac-title">Penerbangan</div>
            <p class="fac-desc">Tiket pesawat PP dengan maskapai terpercaya, kursi nyaman kelas ekonomi hingga bisnis.</p>
          </div>
        </div>

        {{-- 2. Fasilitas Hotel (Statis) --}}
        <div class="fac-card-wrapper">
          <div class="fac-card reveal no-edit" onclick="showFacModal('🏨', 'Hotel', 'Kami menjamin kenyamanan istirahat Anda dengan akomodasi hotel bintang 5/4 pilihan. Lokasi hotel sangat strategis, hanya berjarak 50 hingga 300 meter dari halaman Masjidil Haram di Makkah dan Masjid Nabawi di Madinah.')">
            <div class="fac-card-icon">🏨</div>
            <div class="fac-title">Hotel</div>
            <p class="fac-desc">Akomodasi premium berjarak 50–300 meter dari Masjidil Haram dan Masjid Nabawi.</p>
          </div>
        </div>

        {{-- 3. Fasilitas Katering (Statis) --}}
        <div class="fac-card-wrapper">
          <div class="fac-card reveal no-edit" onclick="showFacModal('🍽️', 'Katering', 'Kami memahami pentingnya asupan nutrisi bagi jemaah. Hidangan halal, bergizi, dan higienis disajikan 3 kali sehari dengan menu khas masakan Indonesia yang lezat untuk menjaga stamina dan mengobati rasa rindu masakan tanah air selama di tanah suci.')">
            <div class="fac-card-icon">🍽️</div>
            <div class="fac-title">Katering</div>
            <p class="fac-desc">Hidangan halal berkualitas 3x sehari dengan menu masakan Indonesia yang lezat.</p>
          </div>
        </div>

        {{-- 4. Fasilitas Transportasi (Statis) --}}
        <div class="fac-card-wrapper">
          <div class="fac-card reveal no-edit" onclick="showFacModal('🚌', 'Transportasi', 'Seluruh rangkaian perjalanan ziarah dan transportasi antar kota (Makkah-Madinah) menggunakan bus kelas premium yang dilengkapi AC dingin, kursi ergonomis, dan pengemudi berpengalaman demi keamanan dan kenyamanan maksimal jemaah.')">
            <div class="fac-card-icon">🚌</div>
            <div class="fac-title">Transportasi</div>
            <p class="fac-desc">Bus full AC kelas premium untuk seluruh perjalanan di Makkah dan Madinah.</p>
          </div>
        </div>

        {{-- 5. Dokumen & Visa (Statis) --}}
        <div class="fac-card-wrapper">
          <div class="fac-card reveal no-edit" onclick="showFacModal('🛂', 'Dokumen & Visa', 'Kami membantu pengurusan seluruh dokumen perjalanan Anda, mulai dari paspor, visa haji/umrah, hingga asuransi perjalanan, memastikan semua persyaratan administratif terpenuhi dengan aman dan cepat.')">
            <div class="fac-card-icon">🛂</div>
            <div class="fac-title">Dokumen & Visa</div>
            <p class="fac-desc">Pengurusan paspor, visa haji/umrah, dan asuransi perjalanan yang aman dan cepat.</p>
          </div>
        </div>

        {{-- 6. Bimbingan & Perlengkapan (Statis) --}}
        <div class="fac-card-wrapper">
          <div class="fac-card reveal no-edit" onclick="showFacModal('📚', 'Bimbingan', 'Setiap jemaah mendapatkan bimbingan manasik intensif serta perlengkapan ibadah lengkap (kain ihram/mukena, koper, seragam, dll) berkualitas untuk mendukung kekhusyu\'an ibadah Anda.')">
            <div class="fac-card-icon">📚</div>
            <div class="fac-title">Bimbingan</div>
            <p class="fac-desc">Manasik haji intensif dan paket perlengkapan ibadah lengkap berkualitas premium.</p>
          </div>
        </div>

        {{-- 7. Layanan Khusus Masyair (Statis) --}}
        <div class="fac-card-wrapper">
          <div class="fac-card reveal no-edit" onclick="showFacModal('⛺', 'Layanan Masyair', 'Layanan khusus selama fase Masyair di Arafah, Muzdalifah, dan Mina dengan tenda yang nyaman, konsumsi terjaga, serta pendampingan mutawwif yang siap membantu ibadah Anda.')">
            <div class="fac-card-icon">⛺</div>
            <div class="fac-title">Layanan Masyair</div>
            <p class="fac-desc">Tenda nyaman dan layanan khusus jemaah selama di Arafah, Muzdalifah, dan Mina.</p>
          </div>
        </div>

        {{-- 8. Identitas & Kesehatan (Statis) --}}
        <div class="fac-card-wrapper">
          <div class="fac-card reveal no-edit" onclick="showFacModal('🏥', 'Kesehatan', 'Kesehatan jemaah adalah prioritas. Kami menyediakan identitas jemaah yang jelas serta tim pendamping yang siap membantu koordinasi layanan kesehatan selama di tanah suci.')">
            <div class="fac-card-icon">🏥</div>
            <div class="fac-title">Kesehatan</div>
            <p class="fac-desc">Pendampingan layanan kesehatan dan kartu identitas jemaah untuk keamanan maksimal.</p>
          </div>
        </div>

        {{-- 9. Layanan Dalam Negeri (Statis) --}}
        <div class="fac-card-wrapper">
          <div class="fac-card reveal no-edit" onclick="showFacModal('🇮🇩', 'Layanan Domestik', 'Kami memberikan layanan penjemputan dan pengantaran jemaah dari daerah asal hingga bandara internasional, serta bantuan teknis selama proses keberangkatan di tanah air.')">
            <div class="fac-card-icon">🇮🇩</div>
            <div class="fac-title">Layanan Domestik</div>
            <p class="fac-desc">Layanan penjemputan dan pengantaran jemaah serta bantuan teknis di dalam negeri.</p>
          </div>
        </div>

        {{-- Fasilitas Dinamis dari Database (Firebase) --}}
        @foreach($facilities as $id => $fac)
        <div class="fac-card-wrapper">
          <div class="fac-card reveal no-edit" onclick="showFacModal('{{ $fac['icon'] ?? '✨' }}', '{{ $fac['title'] ?? ($fac['name'] ?? 'Fasilitas') }}', '{{ addslashes($fac['description'] ?? '') }}')">
            <div class="fac-card-icon">
                @if(!empty($fac['image_url']))
                    <img src="{{ $fac['image_url'] }}" style="width:50px; height:50px; object-fit:contain;">
                @else
                    {{ $fac['icon'] ?? '✨' }}
                @endif
            </div>
            <div class="fac-title">{{ $fac['title'] ?? ($fac['name'] ?? '') }}</div>
            <p class="fac-desc">{{ $fac['description'] ?? '' }}</p>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</section>

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
    <div id="sync-kontak-eye" class="sec-eyebrow cta-eyebrow-gold" contenteditable="true">{{ $settings['sec_cta_eye'] ?? 'Mulai Perjalanan Suci' }}</div>
    <h2 id="sync-cta-title" contenteditable="true">{!! $settings['sec_cta_title'] ?? 'Siap Berangkat ke <em>Tanah Suci?</em>' !!}</h2>
    <div class="cta-simple">
      <p id="sync-cta-desc-simple" class="cta-desc-simple" contenteditable="true">Hubungi tim kami sekarang juga untuk konsultasi gratis dan informasi ketersediaan kuota. Kami siap membantu merencanakan perjalanan suci Anda.</p>
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
      <a href="{{ route('register.show') }}" class="btn btn-lg" style="display:inline-flex; align-items:center; gap:8px; background:transparent; border:2px solid rgba(255,255,255,0.8); color:#fff; font-weight:600;">
        <svg style="width:20px; height:20px;" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg> Hubungi Kami
      </a>
    </div>
    <div id="sync-cta-contact" class="cta-contact-grid">
      <div class="cta-contact-item no-edit">
        <div class="cta-icon-box">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l2.27-2.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
        </div>
        <span contenteditable="false" style="pointer-events: none !important;">{{ $settings['contact_phone'] ?? $settings['office_phone'] ?? '0800-123-4567' }}</span>
      </div>
      <div class="cta-contact-item no-edit">
        <div class="cta-icon-box">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1-.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
        </div>
        <span contenteditable="false" style="pointer-events: none !important;">{{ $settings['contact_email'] ?? $settings['office_email'] ?? 'info@travelhaji.co.id' }}</span>
      </div>
      <div class="cta-contact-item no-edit">
        <div class="cta-icon-box">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
        </div>
        <span contenteditable="false" style="pointer-events: none !important;">WA: {{ $settings['contact_wa'] ?? '0812-3456-7890' }}</span>
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
        <a href="https://wa.me/{{ $wa_number }}" id="m-wa-link" target="_blank" class="btn btn-gold pkg-modal-wa-btn">Tanya Admin via WhatsApp</a>
    </div>
  </div>
</div>

<!-- VIDEO MODAL -->
<div class="modal-overlay" id="videoModal" onclick="closeModal(event)">
  <div class="modal-box">
    <div class="modal-video-placeholder">
      <span>🎬</span>
      <p>Video profil {{ $settings['site_name'] ?? 'PT. UMI MUTHMAINAH BERKAH' }} akan ditampilkan di sini</p>
      <p class="video-modal-help">Hubungi tim kami untuk informasi lebih lanjut</p>
    </div>
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
  for($gi = 1; $gi <= 10; $gi++) {
    $galleryUrls[] = optUrl(($settings['gallery_img_'.$gi] ?? $defaultGallery[$gi - 1]), 300);
  }
@endphp
const galleryImgs = {!! json_encode($galleryUrls) !!};
const row1 = document.getElementById('row1');
const row2 = document.getElementById('row2');
const imgs1 = [...galleryImgs, ...galleryImgs];
const imgs2 = [...galleryImgs.slice(5), ...galleryImgs.slice(5)];
imgs1.forEach(src=>{
  const d=document.createElement('div');d.className='marquee-item';
  d.innerHTML=`<img src="${src}" alt="Gallery" loading="lazy" width="300" height="200">`;
  row1.appendChild(d);
});
imgs2.forEach(src=>{
  const d=document.createElement('div');d.className='marquee-item';
  d.innerHTML=`<img src="${src}" alt="Gallery" loading="lazy" width="300" height="200">`;
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
    
    card.innerHTML = `
        <div class="testi-stars" style="color: #d4a843; margin-bottom: 1rem;">${stars}</div>
        <p class="testi-text" style="font-style: italic; color: #4b5563; margin-bottom: 1.5rem;">"${testi.message || testi.text || ''}"</p>
        <div class="testi-author" style="display: flex; align-items: center; gap: 1rem;">
            <div class="testi-av" style="width: 40px; height: 40px; background: #1a5c3a; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.875rem;">
                ${testi.avatar_url ? `<img src="${testi.avatar_url}" style="width:100%; height:100%; border-radius:50%; object-fit:cover;">` : firstChar}
            </div>
            <div>
                <div class="testi-name" style="font-weight: 700; color: #111827; font-size: 0.875rem;">${testi.name || 'Jemaah'}</div>
                <div class="testi-loc" style="font-size: 0.75rem; color: #6b7280;">${testi.location || testi.category || 'Pelanggan'}</div>
            </div>
        </div>
    `;
    return card;
}

// Populate rows with duplicated items for seamless loop
[...testiData, ...testiData].forEach(testi => {
    testiRow1.appendChild(createTestiCard(testi));
});
[...testiData.slice().reverse(), ...testiData.slice().reverse()].forEach(testi => {
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
function openModal(){document.getElementById('videoModal').classList.add('open');}
function closeModal(e){if(e.target===document.getElementById('videoModal'))closeModalDirect();}
function closeModalDirect(){document.getElementById('videoModal').classList.remove('open');}
document.addEventListener('keydown',e=>{
    if(e.key==='Escape') {
        closeModalDirect();
        closePkgModal();
    }
});

/* ── PACKAGE SLIDER ── */
function scrollPkg(dir) {
    const slider = document.getElementById('pkgSlider');
    const scrollAmount = 370; // card width + gap
    slider.scrollBy({
        left: dir * scrollAmount,
        behavior: 'smooth'
    });
}

function scrollFac(dir) {
    const slider = document.getElementById('facSlider');
    const scrollAmount = 320; // card width + gap
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

    // ── HANDLER REQUEST_SAVE: Kumpulkan semua data saat admin klik Simpan ──
    if (event.data && event.data.type === 'REQUEST_SAVE') {
        const payload = {};

        // Kumpulkan semua elemen yang punya ID sync-
        document.querySelectorAll('[id^="sync-"]').forEach(function(el) {
            if (el.hasAttribute('contenteditable') && el.contentEditable !== 'false') {
                const field = el.id.replace('sync-', '');
                // H1/H2/H3: simpan innerHTML agar tag <em> terjaga
                // Elemen lain: simpan innerText agar bersih
                let value;
                if (['H1','H2','H3'].includes(el.tagName)) {
                    value = el.innerHTML.trim();
                } else {
                    value = el.innerText.trim();
                }
                if (field && value !== undefined) {
                    payload[field] = value;
                    console.log('[Editor] Collected:', field, '=', value);
                }
            }
        });

        console.log('[Editor] Sending SAVE_DATA with', Object.keys(payload).length, 'fields');
        window.parent.postMessage({ type: 'SAVE_DATA', payload: payload }, '*');
        return;
    }


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
    // --- CLEAN & STABLE VISUAL EDITOR LOGIC ---
    // Aktif jika berada di dalam iframe (bukan tab biasa)
    const isInIframe = window !== window.top;
    if (isInIframe) {
        document.body.classList.add('editor-mode');
        
        // Add minimal but effective editor styles
        const editorStyle = document.createElement('style');
        editorStyle.textContent = `
            .editor-mode [contenteditable="true"] {
                cursor: text !important;
                outline: 1px dashed rgba(16, 185, 129, 0.5) !important;
                transition: all 0.2s !important;
            }
            .editor-mode [contenteditable="true"]:hover {
                outline: 2px dashed #10b981 !important;
                background: rgba(16, 185, 129, 0.05) !important;
            }
            .editor-mode [contenteditable="true"]:focus {
                outline: 2px solid #10b981 !important;
                background: rgba(16, 185, 129, 0.1) !important;
                box-shadow: 0 0 15px rgba(16, 185, 129, 0.2) !important;
            }
            .editor-mode img { 
                cursor: pointer !important; 
                transition: all 0.3s !important;
            }
            .editor-mode img:hover { 
                filter: brightness(0.8) !important;
                outline: 3px solid #10b981 !important;
            }
        `;
        document.head.appendChild(editorStyle);

        // Sync text to parent dashboard
        document.addEventListener('input', function(e) {
            const el = e.target;
            if (el.hasAttribute('contenteditable')) {
                const id = el.id;
                if (id && id.startsWith('sync-')) {
                    // Mapping ID: sync-hero_title -> hero_title
                    const field = id.replace('sync-', '');
                    
                    // Gunakan innerHTML untuk judul (h1, h2) agar tag <em> tetap terjaga
                    // Gunakan innerText untuk lainnya agar bersih dari tag <div> sampah saat enter
                    let value = el.innerHTML;
                    if (el.tagName !== 'H1' && el.tagName !== 'H2' && el.tagName !== 'H3') {
                        value = el.innerText.trim();
                    }

                    console.log(`[Editor] Syncing ${field}:`, value);

                    window.parent.postMessage({
                        type: 'INLINE_CHANGE',
                        field: field, // Kirim field name murni
                        value: value
                    }, '*');
                }
            }
        });

        // Trigger save on blur to be safe
        document.addEventListener('blur', function(e) {
            const el = e.target;
            if (el.hasAttribute('contenteditable')) {
                el.dispatchEvent(new Event('input', { bubbles: true }));
            }
        }, true);

        // Smart click handler: Allow focus for text, but block navigation for links
        window.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            const btn = e.target.closest('button');
            const editable = e.target.closest('[contenteditable="true"]');

            // IZINKAN KLIK UNTUK ELEMEN DENGAN CLASS no-edit (Slider Fasilitas)
            if (e.target.closest('.no-edit')) return;

            if (link || btn) {
                // If it's a regular link/button, block navigation
                if (!e.target.closest('.device-btn')) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            }
            
            // If it's an image, trigger picker
            if (e.target.tagName === 'IMG' || e.target.classList.contains('edit-image-trigger')) {
                const img = e.target.tagName === 'IMG' ? e.target : e.target.querySelector('img');
                if (img) {
                    // Find suitable target name
                    let targetName = 'site_logo';
                    if (img.id.includes('footer')) targetName = 'footer_logo';
                    if (img.closest('.hero')) targetName = 'hero_image';
                    if (img.closest('.about')) targetName = 'about_image';
                    
                    window.parent.postMessage({ type: 'PICK_IMAGE', target: targetName }, '*');
                }
            }
        }, true);
    }

    // Utility for image pick (used by footer)
    function handleImagePick(input, imgSelector, targetName) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.querySelector(imgSelector);
                if(img) img.src = e.target.result;
                window.parent.postMessage({
                    type: 'INLINE_CHANGE',
                    target: `[name="${targetName}"]`,
                    value: e.target.result
                }, '*');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    /* ── FACILITY SLIDER (DI EDITOR) ── */
    window.scrollFac = function(dir) {
        const slider = document.getElementById('facSlider');
        const scrollAmount = 350;
        if (slider) {
            slider.scrollBy({ left: dir * scrollAmount, behavior: 'smooth' });
        }
    };

    window.showFacModal = function(icon, title, desc) {
        const modal = document.getElementById('facModal');
        if (!modal) return;
        document.getElementById('modalIcon').innerText = icon;
        document.getElementById('modalTitle').innerText = title;
        document.getElementById('modalDesc').innerText = desc;
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    window.closeFacModal = function(e) {
        const modal = document.getElementById('facModal');
        if (!modal) return;
        if (!e || e.target.id === 'facModal' || e.target.classList.contains('fac-modal-close') || e.target.closest('.btn')) {
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    };
</script>
    <link rel="stylesheet" href="{{ asset('css/beranda-ekstra.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('css/beranda-modern.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('css/tata-letak-atas.css') }}" media="print" onload="this.media='all'">
</script>
</body>
</html>

