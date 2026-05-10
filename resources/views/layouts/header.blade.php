<!-- Header Walcome_blade -->
<header id="site-header">
  <nav class="nav" id="mainNav">
    <div class="nav-logo" id="sync-site-logo">

      <img src="{{ $settings['site_logo'] ?? asset('logo/Logo-PT-Umi-Muthmainah-Berkah.jpg') }}" alt="Logo"
        class="header-logo-img" width="auto" height="38" id="sync-header-logo-img"
        style="max-height: 38px; width: auto; object-fit: contain;" @if(request()->has('preview'))
        onclick="document.getElementById('site-logo-picker').click()" @else onclick="window.location.href='/'" @endif>

      @if(request()->has('preview'))
        <input type="file" id="site-logo-picker" style="display:none" accept="image/*"
          onchange="if(window.handleImagePick) handleImagePick(this, '#sync-header-logo-img', 'site_logo')">
      @endif

      <div class="nav-brand">
        <span class="brand-main" id="sync-site-name" @if(request()->has('preview')) contenteditable="true"
          style="min-width: 100px; display: inline-block; color: var(--green) !important; opacity: 1 !important;"
        @endif>{{ !empty(trim($settings['site_name'] ?? '')) ? $settings['site_name'] : 'PT. UMI MUTHMAINAH BERKAH' }}</span>
        <span class="brand-sub" id="sync-office-addr-header" @if(request()->has('preview')) contenteditable="true"
          style="min-width: 100px; display: inline-block; color: var(--text-darker) !important; opacity: 1 !important;"
        @endif>{{ !empty(trim($settings['office_address'] ?? '')) ? $settings['office_address'] : 'SIDOKARE, SIDOARJO' }}</span>
      </div>
    </div>
    <div class="nav-menu">
      <a href="#tentang">Tentang</a>
      <div class="nav-item-dropdown mega-menu-trigger" id="paketDropdown">
        <button class="dropdown-trigger" onclick="togglePaketMenu(event)" type="button">
          Paket <span class="chevron" id="paketChevron"></span>
        </button>
        <div class="mega-menu-content" id="paketMenu">
          <div class="mega-menu-grid">
            @php
              $grouped = collect($packages)->groupBy(fn($p) => $p['category'] ?? 'Lainnya');
            @endphp
            @foreach($grouped as $kategori => $pkgList)
              <div class="mega-category-group">
                <div class="mega-category-label">{{ $kategori }}</div>
                @foreach($pkgList as $pkg)
                  <a href="#paket" class="mega-item"
                    onclick="if(window.filterPaket){ filterPaket('{{ $pkg['category'] ?? 'semua' }}', document.querySelector('.pkg-filter-btn[data-cat=\'{{ $pkg['category'] ?? 'semua' }}\']')); } openPkgModal({!! json_encode($pkg) !!}); closePaketMenu();">
                    <span class="mega-title">{{ $pkg['name'] }}</span>
                  </a>
                @endforeach
              </div>
            @endforeach
          </div>
        </div>
      </div>
      <a href="#jadwal">Jadwal</a>
      <a href="#fasilitas">Fasilitas</a>
      <a href="#testimoni">Testimoni</a>
    </div>
    <div class="nav-actions">
      <a href="@if(request()->has('preview')) javascript:void(0) @else {{ route('register.show') }} @endif"
        class="btn btn-solid" @if(request()->has('preview')) contenteditable="true" @endif>Daftar Sekarang</a>
    </div>
    {{-- Hamburger Toggle Button (mobile only) --}}
    <button class="nav-toggle" id="navToggle" aria-label="Buka Menu" aria-expanded="false" onclick="toggleMobileNav()">
      <span class="hamburger-bar"></span>
      <span class="hamburger-bar"></span>
      <span class="hamburger-bar"></span>
    </button>
  </nav>

  {{-- Mobile Nav Overlay --}}
  <div class="mobile-nav-overlay" id="mobileNavOverlay" onclick="closeMobileNav()"></div>
  <div class="mobile-nav-drawer" id="mobileNavDrawer">
    <div class="mobile-nav-header">
      <div class="mobile-nav-logo">
        <img src="{{ $settings['site_logo'] ?? asset('logo/Logo-PT-Umi-Muthmainah-Berkah.jpg') }}" alt="Logo" height="32" style="max-height:32px; width:auto;">
        <span style="font-weight:800; color:var(--green); font-size:0.9rem;">{{ !empty(trim($settings['site_name'] ?? '')) ? $settings['site_name'] : 'PT. UMI MUTHMAINAH BERKAH' }}</span>
      </div>
      <button class="mobile-nav-close" onclick="closeMobileNav()" aria-label="Tutup Menu">✕</button>
    </div>
    <nav class="mobile-nav-links">
      <a href="#tentang" onclick="closeMobileNav()">Tentang</a>
      <div class="mobile-nav-group">
        <button class="mobile-nav-group-toggle" onclick="toggleMobileSubMenu(this)">Paket <span class="mobile-chevron">▾</span></button>
        <div class="mobile-nav-submenu">
          @php $grouped = collect($packages)->groupBy(fn($p) => $p['category'] ?? 'Lainnya'); @endphp
          @foreach($grouped as $kategori => $pkgList)
            <div class="mobile-submenu-label">{{ $kategori }}</div>
            @foreach($pkgList as $pkg)
              <a href="#paket" class="mobile-submenu-item"
                onclick="if(window.filterPaket){ filterPaket('{{ $pkg['category'] ?? 'semua' }}', document.querySelector('.pkg-filter-btn[data-cat=\'{{ $pkg['category'] ?? 'semua' }}\']') || document.querySelector('.pkg-filter-btn')); } closeMobileNav();">
                {{ $pkg['name'] }}
              </a>
            @endforeach
          @endforeach
        </div>
      </div>
      <a href="#jadwal" onclick="closeMobileNav()">Jadwal</a>
      <a href="#fasilitas" onclick="closeMobileNav()">Fasilitas</a>
      <a href="#testimoni" onclick="closeMobileNav()">Testimoni</a>
    </nav>
    <div class="mobile-nav-footer">
      <a href="@if(request()->has('preview')) javascript:void(0) @else {{ route('register.show') }} @endif"
        class="btn btn-solid mobile-daftar-btn">Daftar Sekarang</a>
    </div>
  </div>
</header>