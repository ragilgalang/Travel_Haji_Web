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
  <style>
    /* Animasi Global */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .animate-fade-in-up {
      animation: fadeInUp 0.6s ease-out forwards;
    }

    .nav-item {
      transition: all 0.2s ease;
      padding: 10px 16px !important;
      margin-bottom: 2px !important;
      font-size: 0.85rem !important;
    }

    .nav-item svg {
      width: 18px !important;
      height: 18px !important;
    }

    .sidebar {
      height: 100vh !important;
      display: flex !important;
      flex-direction: column !important;
      overflow: hidden !important;
      z-index: 1001 !important;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    .nav-section {
      flex: 1 !important;
      overflow-y: auto !important;
      padding-right: 4px !important;
    }

    /* --- RESPONSIVE OPTIMIZATIONS --- */
    @media (max-width: 991px) {
      .sidebar {
        transform: translateX(-100%);
        position: fixed !important;
        width: 280px !important;
      }

      .sidebar.active {
        transform: translateX(0);
        box-shadow: 20px 0 50px rgba(0, 0, 0, 0.3);
      }

      .main {
        margin-left: 0 !important;
        width: 100% !important;
      }

      .topbar {
        padding: 0 15px !important;
      }

      .topbar-left h1 {
        font-size: 1.1rem !important;
      }

      .topbar-date {
        display: none !important;
      }

      .breadcrumb {
        display: none !important;
      }
    }

    .sidebar-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(4px);
      z-index: 1000;
      opacity: 0;
      pointer-events: none;
      transition: all 0.3s ease;
    }

    .sidebar-overlay.active {
      opacity: 1;
      pointer-events: auto;
    }

    .mobile-toggle {
      display: none;
      background: #f1f5f9;
      border: none;
      padding: 8px;
      border-radius: 8px;
      margin-right: 12px;
      cursor: pointer;
      color: #1e293b;
    }

    @media (max-width: 991px) {
      .mobile-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
      }
    }

    /* Scrollbar for Sidebar */
    .nav-section::-webkit-scrollbar {
      width: 4px;
    }

    .nav-section::-webkit-scrollbar-track {
      background: rgba(0, 0, 0, 0.05);
    }

    .nav-section::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 10px;
    }

    .nav-item:hover {
      transform: translateX(5px);
    }

    /* Efek Hover Kartu */
    .stat-card,
    .card {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    .stat-card:hover,
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .topbar-date {
      padding: 6px 12px;
      background: #f1f5f9;
      border-radius: 8px;
      color: #64748b;
      font-size: 0.8rem;
      font-weight: 600;
    }

    /* Scrollbar for Changelog Modal */
    .changelog-scroll-container::-webkit-scrollbar {
      width: 6px;
    }
    .changelog-scroll-container::-webkit-scrollbar-track {
      background: #f8fafc;
      border-radius: 10px;
    }
    .changelog-scroll-container::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 10px;
    }
    .changelog-scroll-container::-webkit-scrollbar-thumb:hover {
      background: #94a3b8;
    }
  </style>
  @stack('styles')
</head>

<body class="admin-body">
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



      <!-- FITUR GALERI (RESTORED) -->
      <a class="nav-item {{ request()->routeIs('admin.gallery.index') ? 'active' : '' }}"
        href="{{ route('admin.gallery.index') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="3" width="18" height="18" rx="2" />
          <circle cx="8.5" cy="8.5" r="1.5" />
          <path d="M21 15l-5-5L5 21" />
        </svg>
        Kelola Galeri
      </a>

      <a class="nav-item {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}"
        href="{{ route('admin.profile.index') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
          <circle cx="12" cy="7" r="4" />
        </svg>
        Kelola Akun
      </a>

      <div class="nav-label mt-3">Pengaturan</div>

      <a class="nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}"
        href="{{ route('admin.settings') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="3" />
          <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z" />
        </svg>
        Pengaturan Web
      </a>

      <a class="nav-item {{ request()->routeIs('admin.audit.logs') ? 'active' : '' }}"
        href="{{ route('admin.audit.logs') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
          <rect x="9" y="3" width="6" height="4" rx="1" />
          <path d="M9 12h6M9 16h4" />
        </svg>
        Monitoring Login
      </a>

      <a class="nav-item" href="/" target="_blank">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10" />
          <line x1="2" y1="12" x2="22" y2="12" />
          <path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z" />
        </svg>
        Lihat Website
      </a>
      <div class="sidebar-bottom"
        style="padding: 1rem 1.5rem; margin-top: auto; border-top: 1px solid rgba(255,255,255,0.05); background: rgba(0,0,0,0.1);">
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        <button class="logout-btn-premium" onclick="document.getElementById('logout-form').submit();" style="
          width: 100%; 
          padding: 10px; 
          background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.15)); 
          color: #f87171; 
          border: 1px solid rgba(239, 68, 68, 0.2); 
          border-radius: 10px; 
          display: flex; 
          align-items: center; 
          justify-content: center; 
          gap: 10px; 
          font-weight: 600; 
          font-size: 0.8rem; 
          cursor: pointer; 
          transition: all 0.3s ease;
      " onmouseover="this.style.background='rgba(239, 68, 68, 0.2)'; this.style.transform='translateY(-2px)';"
          onmouseout="this.style.background='rgba(239, 68, 68, 0.1)'; this.style.transform='translateY(0)';">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
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
      <div class="topbar-left">
        <button class="mobile-toggle" id="sidebarToggle">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
          </svg>
        </button>
        <h1>@yield('page_title', 'Dashboard Overview')</h1>
        <div class="breadcrumb">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
          </svg>
          Beranda &rsaquo; <span>@yield('page_title', 'Dashboard')</span>
        </div>
      </div>
      <div class="topbar-right">
        <!-- Upload ke GitHub -->
        <a href="javascript:void(0)" onclick="document.getElementById('githubUploadModal').style.display='flex'" style="display: flex; align-items: center; gap: 6px; background: #faf5ff; color: #7c3aed; padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; text-decoration: none; border: 1px solid #e9d5ff; transition: all 0.2s; cursor: pointer; margin-right: 4px;" onmouseover="this.style.background='#f3e8ff'" onmouseout="this.style.background='#faf5ff'">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path>
          </svg>
          Upload ke GitHub
        </a>
        <!-- Notifikasi Pembaruan -->
        <a href="javascript:void(0)" onclick="document.getElementById('deployModal').style.display='flex'" style="display: flex; align-items: center; gap: 6px; background: #eff6ff; color: #2563eb; padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; text-decoration: none; border: 1px solid #bfdbfe; transition: all 0.2s; cursor: pointer; margin-right: 4px;" onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.59-9.21l-5.4 5.4"></path>
          </svg>
          Deploy ke Hosting
        </a>
        <a href="javascript:void(0)" onclick="document.getElementById('changelogModal').style.display='flex'" style="display: flex; align-items: center; gap: 6px; background: #f0fdf4; color: #16a34a; padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; text-decoration: none; border: 1px solid #bbf7d0; transition: all 0.2s; cursor: pointer;" onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
          </svg>
          Update
        </a>
        <div class="topbar-date">{{ date('d M Y') }}</div>
        <button class="topbar-btn" title="Notifikasi Baru" style="position: relative;" id="notifBell">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9" />
            <path d="M13.73 21a2 2 0 01-3.46 0" />
          </svg>
          <span id="notifBadge" class="d-none"
            style="position: absolute; top: -2px; right: -2px; background: #ef4444; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 0.65rem; font-weight: 800; display: flex; align-items: center; justify-content: center; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">0</span>
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
              {{ Auth::user()->name ?? 'Administrator' }}
            </div>
            <div
              style="color: #eab308; font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; margin-top: 2px;">
              {{ Auth::user()->role ?? 'Admin' }}
            </div>
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

      @if(session('error'))
        <div class="alert-danger-modern">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
            <circle cx="12" cy="12" r="10" />
            <line x1="15" y1="9" x2="9" y2="15" />
            <line x1="9" y1="9" x2="15" y2="15" />
          </svg>
          {{ session('error') }}
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

  <!-- MODAL CHANGELOG -->
  <div id="changelogModal" style="display: none; position: fixed; inset: 0; z-index: 99999; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; border-radius: 20px; width: 100%; max-width: 600px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; animation: fadeInUp 0.3s ease-out;">
      <div style="padding: 24px 30px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="width: 40px; height: 40px; background: #dcfce7; color: #16a34a; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
              <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
          </div>
          <div>
            <h3 style="margin: 0; font-family: 'Playfair Display', serif; font-size: 1.3rem; color: #0f172a;">Log Pembaruan Sistem</h3>
            <p style="margin: 2px 0 0; font-size: 0.8rem; color: #64748b;">Catatan riwayat update website</p>
          </div>
        </div>
        <button onclick="document.getElementById('changelogModal').style.display='none'" style="background: none; border: none; color: #94a3b8; cursor: pointer; padding: 5px; transition: color 0.2s;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#94a3b8'">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
      </div>
      @php
        $path = storage_path('app/changelog.txt');
        $rawContent = file_exists($path) ? file_get_contents($path) : 'Belum ada pembaruan.';
        
        $normalized = str_replace("\r\n", "\n", $rawContent);
        $pattern = '/^(\d{1,2}\s+[A-Za-z\x7f-\xff]+\s+\d{4})\n-+/m';
        
        $entries = [];
        if (preg_match_all($pattern, $normalized, $matches, PREG_OFFSET_CAPTURE)) {
            $count = count($matches[0]);
            for ($i = 0; $i < $count; $i++) {
                $date = $matches[1][$i][0];
                $startOffset = $matches[0][$i][1] + strlen($matches[0][$i][0]);
                $endOffset = ($i < $count - 1) ? $matches[0][$i+1][1] : strlen($normalized);
                $blockContent = trim(substr($normalized, $startOffset, $endOffset - $startOffset));
                
                $entries[] = [
                    'date' => $date,
                    'content' => $blockContent
                ];
            }
        }
        
        if (empty($entries) && trim($rawContent) !== '') {
            $entries[] = [
                'date' => 'Pembaruan',
                'content' => trim($rawContent)
            ];
        }
      @endphp
      <div class="changelog-scroll-container" style="padding: 24px 30px; max-height: 60vh; overflow-y: auto; background: #f8fafc;">
        @if(empty($entries))
          <div style="text-align: center; color: #94a3b8; padding: 40px 0;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 12px; opacity: 0.6; display: inline-block;">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
              <polyline points="14 2 14 8 20 8"></polyline>
            </svg>
            <p style="margin: 0; font-size: 0.9rem;">Belum ada catatan pembaruan.</p>
          </div>
        @else
          <div style="display: flex; flex-direction: column; gap: 20px;">
            @foreach($entries as $index => $entry)
              <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03); padding: 20px; transition: transform 0.2s;">
                <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px dashed #e2e8f0; padding-bottom: 10px; margin-bottom: 12px;">
                  <span style="background: #eafaf1; color: #10b981; font-weight: 700; font-size: 0.8rem; padding: 4px 10px; border-radius: 6px; letter-spacing: 0.02em; display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display: inline-block;">
                      <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                      <line x1="16" y1="2" x2="16" y2="6"></line>
                      <line x1="8" y1="2" x2="8" y2="6"></line>
                      <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    {{ $entry['date'] }}
                  </span>
                  @if($index === 0)
                    <span style="background: #eff6ff; color: #2563eb; font-weight: 800; font-size: 0.7rem; padding: 2px 8px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.05em;">Terbaru</span>
                  @endif
                </div>
                <div style="font-size: 0.9rem; color: #334155; line-height: 1.6; white-space: pre-wrap; font-family: inherit; text-align: left;">@php
                  $escapedContent = htmlspecialchars($entry['content']);
                  $withLinks = preg_replace(
                      '/(https?:\/\/[^\s<>"]+)/',
                      '<a href="$1" target="_blank" rel="noopener noreferrer" style="color:#2563eb;font-weight:600;text-decoration:underline;text-underline-offset:3px;word-break:break-all;">$1</a>',
                      $escapedContent
                  );
                  echo $withLinks;
                @endphp</div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
      <div style="padding: 20px 30px; border-top: 1px solid #f1f5f9; background: #f8fafc; text-align: right;">
        <button onclick="document.getElementById('changelogModal').style.display='none'" style="background: #e2e8f0; color: #475569; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#cbd5e1'" onmouseout="this.style.background='#e2e8f0'">Tutup</button>
      </div>
    </div>
  </div>

  @php
    $currentRemoteUrl = '';
    exec('git remote get-url origin 2>&1', $outputRemote, $returnRemote);
    if ($returnRemote === 0 && !empty($outputRemote)) {
        $currentRemoteUrl = trim($outputRemote[0]);
    }
  @endphp

  <!-- MODAL GITHUB UPLOAD -->
  <div id="githubUploadModal" style="display: none; position: fixed; inset: 0; z-index: 99999; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; border-radius: 20px; width: 100%; max-width: 600px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; animation: fadeInUp 0.3s ease-out;">
      <div style="padding: 24px 30px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="width: 40px; height: 40px; background: #faf5ff; color: #7c3aed; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path>
            </svg>
          </div>
          <div>
            <h3 style="margin: 0; font-family: 'Playfair Display', serif; font-size: 1.3rem; color: #0f172a;">Upload ke GitHub</h3>
            <p style="margin: 2px 0 0; font-size: 0.8rem; color: #64748b;">Kirim semua pembaruan lokal Anda secara otomatis ke repositori GitHub</p>
          </div>
        </div>
        <button onclick="document.getElementById('githubUploadModal').style.display='none'" style="background: none; border: none; color: #94a3b8; cursor: pointer; padding: 5px; transition: color 0.2s;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#94a3b8'">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
      </div>
      <div style="padding: 30px; font-size: 0.95rem; color: #334155; line-height: 1.6; background: white;">
        <p style="margin-top: 0;">Sistem akan melakukan <strong>git add .</strong>, <strong>git commit</strong> (dengan catatan komit otomatis), dan <strong>git push</strong> ke GitHub repositori Anda.</p>
        
        <div style="margin-top: 20px; margin-bottom: 20px;">
          <label for="githubRepoUrl" style="display: block; font-weight: 600; font-size: 0.85rem; color: #475569; margin-bottom: 6px;">Link Repository GitHub (.git):</label>
          <input type="text" id="githubRepoUrl" value="{{ $currentRemoteUrl }}" placeholder="Contoh: https://github.com/username/repo.git" style="width: 100%; padding: 12px 16px; border: 1.5px solid #cbd5e1; border-radius: 10px; font-size: 0.9rem; color: #0f172a; outline: none; box-sizing: border-box; transition: all 0.2s;" onfocus="this.style.borderColor='#7c3aed'; this.style.boxShadow='0 0 0 3px rgba(124, 58, 237, 0.15)'" onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none'">
          <span style="display: block; font-size: 0.75rem; color: #64748b; margin-top: 4px;">Pastikan Anda menggunakan link HTTPS repository GitHub yang valid.</span>
        </div>

        <div id="githubLogContainer" style="display: none; margin-top: 20px;">
          <div style="font-weight: 600; margin-bottom: 8px; font-size: 0.85rem; color: #475569;">Log Unggahan:</div>
          <pre id="githubLog" style="background: #1e293b; color: #a5b4fc; padding: 15px; border-radius: 8px; font-size: 0.8rem; overflow-y: auto; max-height: 200px; white-space: pre-wrap; margin: 0; border: 1px solid #334155; font-family: monospace;"></pre>
        </div>
      </div>
      <div style="padding: 20px 30px; border-top: 1px solid #f1f5f9; background: #f8fafc; display: flex; justify-content: flex-end; gap: 10px;">
        <button onclick="document.getElementById('githubUploadModal').style.display='none'" id="btnCancelGithub" style="background: #e2e8f0; color: #475569; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#cbd5e1'" onmouseout="this.style.background='#e2e8f0'">Batal</button>
        <button onclick="startGithubUpload()" id="btnStartGithub" style="background: #7c3aed; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s; display: flex; align-items: center; gap: 8px;" onmouseover="this.style.background='#6d28d9'" onmouseout="this.style.background='#7c3aed'">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13"></path><path d="M22 2L15 22L11 13L2 9L22 2z"></path></svg>
          Mulai Upload
        </button>
      </div>
    </div>
  </div>

  <!-- MODAL DEPLOY -->
  <div id="deployModal" style="display: none; position: fixed; inset: 0; z-index: 99999; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; border-radius: 20px; width: 100%; max-width: 600px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; animation: fadeInUp 0.3s ease-out;">
      <div style="padding: 24px 30px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="width: 40px; height: 40px; background: #eff6ff; color: #2563eb; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.59-9.21l-5.4 5.4"></path>
            </svg>
          </div>
          <div>
            <h3 style="margin: 0; font-family: 'Playfair Display', serif; font-size: 1.3rem; color: #0f172a;">Deploy ke Hosting</h3>
            <p style="margin: 2px 0 0; font-size: 0.8rem; color: #64748b;">Perbarui website produksi dengan kode terbaru dari GitHub</p>
          </div>
        </div>
        <button onclick="document.getElementById('deployModal').style.display='none'" style="background: none; border: none; color: #94a3b8; cursor: pointer; padding: 5px; transition: color 0.2s;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#94a3b8'">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
      </div>
      <div style="padding: 30px; font-size: 0.95rem; color: #334155; line-height: 1.6; background: white;">
        <p style="margin-top: 0;">Apakah Anda yakin ingin melakukan deploy? Pastikan Anda sudah melakukan <strong>push</strong> perubahan terbaru ke repository GitHub.</p>
        
        <div id="deployLogContainer" style="display: none; margin-top: 20px;">
          <div style="font-weight: 600; margin-bottom: 8px; font-size: 0.85rem; color: #475569;">Status Deploy:</div>
          <pre id="deployLog" style="background: #1e293b; color: #a5b4fc; padding: 15px; border-radius: 8px; font-size: 0.8rem; overflow-y: auto; max-height: 200px; white-space: pre-wrap; margin: 0; border: 1px solid #334155;"></pre>
        </div>
      </div>
      <div style="padding: 20px 30px; border-top: 1px solid #f1f5f9; background: #f8fafc; display: flex; justify-content: flex-end; gap: 10px;">
        <button onclick="document.getElementById('deployModal').style.display='none'" id="btnCancelDeploy" style="background: #e2e8f0; color: #475569; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#cbd5e1'" onmouseout="this.style.background='#e2e8f0'">Batal</button>
        <button onclick="startDeploy()" id="btnStartDeploy" style="background: #2563eb; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s; display: flex; align-items: center; gap: 8px;" onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13"></path><path d="M22 2L15 22L11 13L2 9L22 2z"></path></svg>
          Mulai Deploy
        </button>
      </div>
    </div>
  </div>

  <script>
    function startDeploy() {
      const btnStart = document.getElementById('btnStartDeploy');
      const btnCancel = document.getElementById('btnCancelDeploy');
      const logContainer = document.getElementById('deployLogContainer');
      const logBox = document.getElementById('deployLog');
      
      // UI Loading state
      btnStart.disabled = true;
      btnStart.style.opacity = '0.7';
      btnStart.style.cursor = 'not-allowed';
      btnStart.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="animation: spin 1s linear infinite;"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.59-9.21l-5.4 5.4"></path></svg> Memproses...';
      btnCancel.disabled = true;
      
      logContainer.style.display = 'block';
      logBox.innerHTML = '<span style="color: #fbbf24;">[INFO]</span> Menghubungi server hosting...\n';
      
      // Inject CSS spin animation if not exists
      if (!document.getElementById('spin-keyframes')) {
        const style = document.createElement('style');
        style.id = 'spin-keyframes';
        style.innerHTML = '@keyframes spin { 100% { transform: rotate(360deg); } }';
        document.head.appendChild(style);
      }

      fetch('{{ route("admin.deploy") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
           logBox.innerHTML += '<span style="color: #4ade80;">[SUCCESS]</span> ' + data.message + '\n';
           
           if(data.response && data.response.message) {
              logBox.innerHTML += '<span style="color: #60a5fa;">[SERVER]</span> ' + data.response.message + '\n';
           }
           
           btnStart.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg> Selesai';
           btnStart.style.background = '#16a34a';
        } else {
           logBox.innerHTML += '<span style="color: #f87171;">[ERROR]</span> ' + (data.message || 'Terjadi kesalahan') + '\n';
           if(data.response) {
             logBox.innerHTML += '<span style="color: #f87171;">[DETAILS]</span> ' + JSON.stringify(data.response) + '\n';
           }
           btnStart.innerHTML = 'Gagal (Coba Lagi)';
           btnStart.style.background = '#ef4444';
           btnStart.disabled = false;
           btnStart.style.opacity = '1';
           btnStart.style.cursor = 'pointer';
        }
        btnCancel.disabled = false;
        btnCancel.innerHTML = 'Tutup';
      })
      .catch(error => {
        logBox.innerHTML += '<span style="color: #f87171;">[ERROR NETWORK]</span> ' + error.message + '\n';
        btnStart.innerHTML = 'Gagal (Coba Lagi)';
        btnStart.style.background = '#ef4444';
        btnStart.disabled = false;
        btnStart.style.opacity = '1';
        btnStart.style.cursor = 'pointer';
        btnCancel.disabled = false;
      });
    }

    function startGithubUpload() {
      const repoUrlInput = document.getElementById('githubRepoUrl');
      const repoUrl = repoUrlInput ? repoUrlInput.value.trim() : '';

      if (!repoUrl) {
        alert('Silakan masukkan link repository GitHub Anda terlebih dahulu!');
        if (repoUrlInput) repoUrlInput.focus();
        return;
      }

      const btnStart = document.getElementById('btnStartGithub');
      const btnCancel = document.getElementById('btnCancelGithub');
      const logContainer = document.getElementById('githubLogContainer');
      const logBox = document.getElementById('githubLog');
      
      // UI Loading state
      btnStart.disabled = true;
      btnStart.style.opacity = '0.7';
      btnStart.style.cursor = 'not-allowed';
      btnStart.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="animation: spin 1s linear infinite;"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.59-9.21l-5.4 5.4"></path></svg> Memproses...';
      btnCancel.disabled = true;
      if (repoUrlInput) repoUrlInput.disabled = true;
      
      logContainer.style.display = 'block';
      logBox.innerHTML = '<span style="color: #fbbf24;">[INFO]</span> Menjalankan proses Git...\n';
      
      // Inject CSS spin animation if not exists
      if (!document.getElementById('spin-keyframes')) {
        const style = document.createElement('style');
        style.id = 'spin-keyframes';
        style.innerHTML = '@keyframes spin { 100% { transform: rotate(360deg); } }';
        document.head.appendChild(style);
      }

      fetch('{{ route("admin.github.upload") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json'
        },
        body: JSON.stringify({ repo_url: repoUrl })
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
            logBox.innerHTML += '<span style="color: #4ade80;">[SUCCESS]</span> ' + data.message + '\n';
            if (data.log) {
               logBox.innerHTML += '\n' + data.log + '\n';
            }
            btnStart.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg> Selesai';
            btnStart.style.background = '#16a34a';
        } else {
            logBox.innerHTML += '<span style="color: #f87171;">[ERROR]</span> ' + (data.message || 'Terjadi kesalahan') + '\n';
            if (data.log) {
               logBox.innerHTML += '\n' + data.log + '\n';
            }
            btnStart.innerHTML = 'Gagal (Coba Lagi)';
            btnStart.style.background = '#ef4444';
            btnStart.disabled = false;
            btnStart.style.opacity = '1';
            btnStart.style.cursor = 'pointer';
            if (repoUrlInput) repoUrlInput.disabled = false;
        }
        btnCancel.disabled = false;
        btnCancel.innerHTML = 'Tutup';
      })
      .catch(error => {
        logBox.innerHTML += '<span style="color: #f87171;">[ERROR NETWORK]</span> ' + error.message + '\n';
        btnStart.innerHTML = 'Gagal (Coba Lagi)';
        btnStart.style.background = '#ef4444';
        btnStart.disabled = false;
        btnStart.style.opacity = '1';
        btnStart.style.cursor = 'pointer';
        btnCancel.disabled = false;
        btnCancel.innerHTML = 'Tutup';
      });
    }
  </script>

  <script>
    // --- REAL-TIME REGISTRATION NOTIFICATIONS ---
    let lastKnownRegistrationId = localStorage.getItem('last_reg_id');

    // Request permission on load
    if (Notification.permission !== "granted" && Notification.permission !== "denied") {
      Notification.requestPermission();
    }

    const notificationSound = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
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

    // --- SIDEBAR MOBILE TOGGLE ---
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggle = document.getElementById('sidebarToggle');

    function toggleSidebar() {
      sidebar.classList.toggle('active');
      overlay.classList.toggle('active');
      document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
    }

    if (toggle) toggle.addEventListener('click', toggleSidebar);
    if (overlay) overlay.addEventListener('click', toggleSidebar);

    // Auto close sidebar on resize if large screen
    window.addEventListener('resize', () => {
      if (window.innerWidth > 991 && sidebar.classList.contains('active')) {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
      }
    });
  </script>
  <!-- Instant Navigation & Loading Bar -->
  <style>
    #nprogress-bar {
      position: fixed;
      top: 0;
      left: 0;
      width: 0%;
      height: 3px;
      background: linear-gradient(to right, #eab308, #ca8a04);
      z-index: 99999;
      transition: width 0.4s ease;
      box-shadow: 0 0 10px rgba(234, 179, 8, 0.5);
    }
  </style>
  <div id="nprogress-bar"></div>
  <script src="//instant.page/5.2.0" type="module"
    integrity="sha384-jnZyxPjiipS0KnLyWTP30STJ4XumNYuNqsXk1nadqh68E2YKe46p7yL" crossorigin="anonymous"></script>
  <script>
    // Simple top progress bar simulator
    window.addEventListener('beforeunload', function () {
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
  @stack('scripts')
</body>

</html>