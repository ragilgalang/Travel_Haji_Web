<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Dashboard Admin — {{ $settings['site_name'] ?? 'PT. Umi Muthmainah Berkah' }}</title>
  <link rel="icon" type="image/png" href="{{ $settings['site_logo'] ?? asset('logo/logo.png') }}">
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin-tampilan.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin-responsive-global.css') }}">
  @stack('styles')
</head>

<body>

  <!-- SIDEBAR OVERLAY (Mobile) -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- SIDEBAR -->

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="brand-icon">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 2L4 7v10l8 5 8-5V7L12 2Z" stroke="white" stroke-width="1.5" stroke-linejoin="round" />
          <path d="M12 8v8M8 10l4-2 4 2" stroke="#eab308" stroke-width="1.5" stroke-linecap="round"
            stroke-linejoin="round" />
        </svg>
      </div>
      <div class="brand-text">
        <div class="name">PT. UMI MUTHMAINAH<br>BERKAH</div>
        <div class="sub">Portal Administrator</div>
      </div>
    </div>


    <nav class="nav-section">
      <div class="nav-label">Utama</div>

      <a class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
        href="{{ route('admin.dashboard') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="3" width="7" height="7" rx="1" />
          <rect x="14" y="3" width="7" height="7" rx="1" />
          <rect x="3" y="14" width="7" height="7" rx="1" />
          <rect x="14" y="14" width="7" height="7" rx="1" />
        </svg>
        Dashboard
      </a>

      @if(in_array(Auth::user()->role, ['admin', 'manager']))
        <a class="nav-item {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}"
          href="{{ route('admin.packages.index') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z" />
            <path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16" />
          </svg>
          Kelola Paket
          @if(isset($stats['packages_count']) && $stats['packages_count'] > 0) <span
          class="nav-badge">{{ $stats['packages_count'] }}</span> @endif
        </a>

        <a class="nav-item {{ request()->routeIs('admin.facilities.*') ? 'active' : '' }}"
          href="{{ route('admin.facilities.index') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
            <polyline points="9 22 9 12 15 12 15 22" />
          </svg>
          Kelola Fasilitas
        </a>
      @endif

      @if(in_array(Auth::user()->role, ['admin', 'manager', 'cs']))
        <a class="nav-item {{ request()->routeIs('admin.registrations.*') ? 'active' : '' }}"
          href="{{ route('admin.registrations.index') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
            <circle cx="9" cy="7" r="4" />
            <path d="M23 21v-2a4 4 0 00-3-3.87" />
            <path d="M16 3.13a4 4 0 010 7.75" />
          </svg>
          Pendaftaran
        </a>

        <a class="nav-item {{ request()->routeIs('admin.chat.index') ? 'active' : '' }}"
          href="{{ route('admin.chat.index') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 8V4H8" />
            <rect x="4" y="8" width="16" height="12" rx="2" />
            <path d="M2 14h2M20 14h2M15 13a3 3 0 10-6 0" />
          </svg>
          Asisten Chat
        </a>
      @endif

      @if(in_array(Auth::user()->role, ['admin', 'manager']))
        <a class="nav-item {{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}"
          href="{{ route('admin.testimonials.index') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
          </svg>
          Testimoni
          @if(isset($stats['testimonials_count']) && $stats['testimonials_count'] > 0) <span
          class="nav-badge">{{ $stats['testimonials_count'] }}</span> @endif
        </a>
      @endif

      @if(in_array(Auth::user()->role, ['admin', 'manager', 'finance']))
        <a class="nav-item {{ request()->routeIs('admin.finance.*') ? 'active' : '' }}"
          href="{{ route('admin.finance.index') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="2" y="4" width="20" height="16" rx="2" />
            <path d="M7 15h0M2 9.5h20M12 15h0M17 15h0" />
          </svg>
          Laporan Keuangan
        </a>
      @endif

      @if(in_array(Auth::user()->role, ['admin', 'manager', 'staff']))
        <a class="nav-item {{ request()->routeIs('admin.visitors.*') ? 'active' : '' }}"
          href="{{ route('admin.visitors.index') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
            <circle cx="12" cy="12" r="3" />
          </svg>
          Detail Pengunjung
        </a>
      @endif

      <a class="nav-item {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}"
        href="{{ route('admin.profile.index') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
          <circle cx="12" cy="7" r="4" />
        </svg>
        Kelola Akun
      </a>

      @if(Auth::user()->role == 'admin')
        <div class="nav-label mt-3">Pengaturan</div>

        <a class="nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}"
          href="{{ route('admin.settings') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="3" />
            <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z" />
          </svg>
          Pengaturan Web
        </a>

        <a class="nav-item {{ request()->routeIs('admin.audit.logs') ? 'active' : '' }}"
          href="{{ route('admin.audit.logs') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
          </svg>
          Monitoring Login
        </a>
      @endif

      <a class="nav-item" href="/" target="_blank">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10" />
          <line x1="2" y1="12" x2="22" y2="12" />
          <path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z" />
        </svg>
        Lihat Website
      </a>
      <div class="sidebar-bottom" style="padding: 1.5rem; margin-top: auto;">
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        <button class="logout-btn-premium" onclick="document.getElementById('logout-form').submit();" style="
          width: 100%; 
          padding: 12px; 
          background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.15)); 
          color: #f87171; 
          border: 1px solid rgba(239, 68, 68, 0.2); 
          border-radius: 12px; 
          display: flex; 
          align-items: center; 
          justify-content: center; 
          gap: 10px; 
          font-weight: 600; 
          font-size: 0.85rem; 
          cursor: pointer; 
          transition: all 0.3s ease;
      " onmouseover="this.style.background='rgba(239, 68, 68, 0.2)'; this.style.transform='translateY(-2px)';"
          onmouseout="this.style.background='rgba(239, 68, 68, 0.1)'; this.style.transform='translateY(0)';">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" />
            <polyline points="16 17 21 12 16 7" />
            <line x1="21" y1="12" x2="9" y2="12" />
          </svg>
          Keluar
        </button>
      </div>

  </aside>

  <!-- MAIN CONTENT -->
  <main class="main">

    <!-- Topbar -->
    <header class="topbar">
      <div class="topbar-left" style="display: flex; align-items: center;">
        <button class="hamburger-btn" id="sidebarToggle">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
          </svg>
        </button>
        <div>
          <h1>@yield('page_title', 'Dashboard Overview')</h1>

          <div class="breadcrumb">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
            </svg>
            Beranda &rsaquo; <span>@yield('page_title', 'Dashboard')</span>
          </div>
        </div>
      </div>

      <div class="topbar-right">
        <div class="topbar-date">{{ date('d M Y') }}</div>
        <button class="topbar-btn" title="Notifikasi Baru" style="position: relative;" id="notifBell">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9" />
            <path d="M13.73 21a2 2 0 01-3.46 0" />
          </svg>
          <span id="notifBadge" class="d-none" style="position: absolute; top: -2px; right: -2px; background: #ef4444; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 0.65rem; font-weight: 800; display: flex; align-items: center; justify-content: center; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">0</span>
        </button>
        <div class="user-profile-wrapper" style="
          display: flex; 
          align-items: center; 
          gap: 12px; 
          padding: 6px 6px 6px 16px; 
          background: rgba(255, 255, 255, 0.9); 
          border: 1px solid #e2e8f0; 
          border-radius: 50px; 
          box-shadow: 0 4px 12px rgba(0,0,0,0.03);
          cursor: pointer;
          transition: all 0.3s ease;
      " onmouseover="this.style.boxShadow='0 6px 20px rgba(0,0,0,0.08)';"
          onmouseout="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.03)';">
          <div class="text-right" style="text-align: right;">
            <div style="color: #1e293b; font-weight: 700; font-size: 0.85rem; line-height: 1.1;">
              {{ Auth::user()->name ?? 'Administrator' }}</div>
            <div
              style="color: #eab308; font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; margin-top: 2px;">
              {{ Auth::user()->role ?? 'Admin' }}</div>
          </div>
          <div class="avatar-header" style="
            width: 36px; 
            height: 36px; 
            background: linear-gradient(135deg, #eab308, #ca8a04); 
            color: white; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-weight: 700; 
            font-size: 0.95rem;
            box-shadow: 0 2px 6px rgba(234, 179, 8, 0.3);
        ">
            {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
          </div>
        </div>
      </div>
    </header>

    <!-- Content -->
    <div class="content">
      @if(session('success'))
        <div class="alert-success-modern">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 11.08V12a10 10 0 11-5.93-9.14" />
            <polyline points="22 4 12 14.01 9 11.01" />
          </svg>
          {{ session('success') }}
        </div>
      @endif

      @yield('content')
    </div>

    <footer class="admin-footer">
      <div>© {{ date('Y') }} {{ $settings['site_name'] ?? 'PT. Umi Muthmainah Berkah' }}. Seluruh Hak Cipta Dilindungi.
      </div>
      <div class="footer-support">Support by : PT UMB</div>
    </footer>

  </main>

  <script>
    // --- REAL-TIME REGISTRATION NOTIFICATIONS ---
    let lastKnownRegistrationId = localStorage.getItem('last_reg_id');

    // Request permission on load
    if (Notification.permission !== "granted" && Notification.permission !== "denied") {
      Notification.requestPermission();
    }

    const notificationSound = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
    const notifDot = document.getElementById('globalNotificationDot');

    const notifBadge = document.getElementById('notifBadge');

    function checkNewRegistrations() {
      fetch(`{{ route('admin.registrations.checkNew') }}?last_id=${lastKnownRegistrationId || ''}`)
        .then(response => response.json())
        .then(data => {
          if (data.new_count > 0 && lastKnownRegistrationId) {
            playNotification(data.new_count, data.latest_data);
            if (notifBadge) {
              notifBadge.textContent = data.new_count;
              notifBadge.classList.remove('d-none');
              notifBadge.style.display = 'flex'; // Paksa tampil
            }
          }

          if (data.latest_id) {
            lastKnownRegistrationId = data.latest_id;
            localStorage.setItem('last_reg_id', data.latest_id);
          }
        })
        .catch(err => console.error('Notification check failed:', err));
    }

    function playNotification(count, data) {
      notificationSound.play().catch(e => console.warn('Sound play blocked'));
      if (Notification.permission === "granted") {
        const name = data ? (data.nama_lengkap || 'Customer') : 'Jemaah';
        const n = new Notification("Pendaftaran Baru", {
          body: `${name} baru saja mendaftar. Total ${count} pendaftaran baru menunggu.`,
          icon: "{{ $settings['site_logo'] ?? asset('logo/logo.png') }}"
        });
        n.onclick = () => {
          window.focus();
          window.location.href = "{{ route('admin.registrations.index') }}";
        };
      }
    }

    if (typeof lastKnownRegistrationId === 'undefined' || !lastKnownRegistrationId) {
      checkNewRegistrations();
    }
    setInterval(checkNewRegistrations, 30000);

    // Hide notif badge when clicking bell
    document.getElementById('notifBell').addEventListener('click', function () {
      if (notifBadge) {
          notifBadge.classList.add('d-none');
          notifBadge.style.display = 'none';
      }
      window.location.href = "{{ route('admin.registrations.index') }}";
    });
  </script>
  <!-- Instant Navigation & Loading Bar -->
  <!-- NProgress bar styles dipindahkan ke admin-tampilan.css -->
  <div id="nprogress-bar"></div>
  <script src="//instant.page/5.2.0" type="module" integrity="sha384-jnZyxPjiipS0KnLyWTP30STJ4XumNYuNqsXk1nadqh68E2YKe46p7yL" crossorigin="anonymous"></script>
  <script>
    // Simple top progress bar simulator
    window.addEventListener('beforeunload', function() {
        const bar = document.getElementById('nprogress-bar');
        bar.style.width = '30%';
        let width = 30;
        const interval = setInterval(() => {
            if (width >= 90) {
                clearInterval(interval);
            } else {
                width += (95 - width) * 0.1;
                bar.style.width = width + '%';
            }
        }, 100);
    });
  </script>
  <script>
    // --- MOBILE SIDEBAR TOGGLE ---
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');

    function toggleSidebar() {
      if (!sidebar) return;
      sidebar.classList.toggle('open');
      overlay.classList.toggle('active');
      document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
    }

    if(toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
    if(overlay) overlay.addEventListener('click', toggleSidebar);

    // Close sidebar on link click (mobile)
    document.querySelectorAll('.nav-item').forEach(link => {
      link.addEventListener('click', () => {
        if (window.innerWidth <= 768 && sidebar.classList.contains('open')) toggleSidebar();
      });
    });
  </script>

  @stack('scripts')

</body>

</html>