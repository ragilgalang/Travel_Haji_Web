@extends('admin.layout')

@section('page_title', 'Dashboard Overview')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
@endpush

@section('content')
<!-- Welcome Banner -->
<div class="welcome-banner animate-fade-in-up">
  <div class="welcome-text">
    <div class="greeting-tag">✦ &nbsp;Portal Admin</div>
    <h2>Selamat Datang,<br><em>{{ Auth::user()->name ?? 'Administrator' }}!</em></h2>
    <p>Melalui dashboard ini, Anda dapat mengelola seluruh konten website PT. UMI MUTHMAINAH BERKAH — paket, testimoni, dan pendaftaran jemaah secara langsung.</p>
  </div>
  <div class="welcome-actions">
    <a href="{{ route('admin.guide') }}" class="action-btn secondary" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"></path><path d="M6.5 20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"></path></svg>
      Buku Panduan
    </a>
    <a href="{{ route('admin.packages.create') }}" class="action-btn primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14" stroke-linecap="round"/></svg>
      Tambah Paket Baru
    </a>
    <a href="/" target="_blank" class="action-btn secondary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
      Lihat Website
    </a>
    <form action="{{ route('admin.clearCache') }}" method="POST" class="d-block w-100 mt-2">
      @csrf
      <button type="submit" class="action-btn secondary w-100" style="background: rgba(255,255,255,0.05); border: 1px dashed rgba(255,255,255,0.2); font-size: 0.85rem; padding: 8px 16px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
        Hapus Cache Website
      </button>
    </form>
  </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
  <!-- Card 1: Paket -->
  <a href="{{ route('admin.packages.index') }}" class="stat-card green stat-card-link animate-fade-in-up" style="animation-delay: 0.1s;">
    <div class="stat-card-top">
      <div class="stat-icon green">
        <svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>
      </div>
      <div class="stat-trend up">↑ Aktif</div>
    </div>
    <div class="stat-number green" id="stat-packages">{{ $stats['packages_count'] ?? 0 }}</div>
    <div class="stat-label">Paket Aktif</div>
    <div class="stat-sub">Umrah & Haji Reguler</div>
    <div class="mini-bars">
      <div class="mini-bar green height-40"></div>
      <div class="mini-bar green height-60"></div>
      <div class="mini-bar green active height-100"></div>
    </div>
  </a>

  <!-- Card 2: Testimoni -->
  <a href="{{ route('admin.testimonials.index') }}" class="stat-card gold stat-card-link animate-fade-in-up" style="animation-delay: 0.2s;">
    <div class="stat-card-top">
      <div class="stat-icon gold">
        <svg viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
      </div>
      <div class="stat-trend gold-t">★ Baru</div>
    </div>
    <div class="stat-number gold" id="stat-testimonials">{{ $stats['testimonials_count'] ?? 0 }}</div>
    <div class="stat-label">Testimoni Jemaah</div>
    <div class="stat-sub">Rating rata-rata jemaah</div>
    <div class="mini-bars">
      <div class="mini-bar gold height-30"></div>
      <div class="mini-bar gold height-55"></div>
      <div class="mini-bar gold active height-95"></div>
    </div>
  </a>

  <!-- Card 3: Pendaftar -->
  <a href="{{ route('admin.registrations.index') }}" class="stat-card blue stat-card-link animate-fade-in-up" style="animation-delay: 0.3s;">
    <div class="stat-card-top">
      <div class="stat-icon blue">
        <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
      </div>
      <div class="stat-trend blue-t">● Terbaru</div>
    </div>
    <div class="stat-number blue" id="stat-registrations">{{ $stats['registrations_count'] ?? 0 }}</div>
    <div class="stat-label">Total Pendaftar</div>
    <div class="stat-sub">Data jemaah masuk</div>
    <div class="mini-bars">
      <div class="mini-bar blue height-60"></div>
      <div class="mini-bar blue height-80"></div>
      <div class="mini-bar blue active height-100"></div>
    </div>
  </a>

  <!-- Card 4: Pengunjung -->
  <a href="{{ route('admin.visitors.index') }}" class="stat-card red stat-card-link animate-fade-in-up" style="animation-delay: 0.4s;">
    <div class="stat-card-top">
      <div class="stat-icon stat-icon-red-bg">
        <svg viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
      </div>
      <div class="stat-trend stat-trend-red">Live</div>
    </div>
    <div class="stat-number stat-number-red" id="stat-visitors">{{ number_format($stats['page_views'] ?? 0) }}</div>
    <div class="stat-label">Total Pengunjung</div>
    <div class="stat-sub">Klik halaman website</div>
    <div class="mini-bar-red-wrapper">
      <div class="mini-bar-red height-45"></div>
      <div class="mini-bar-red height-75"></div>
      <div class="mini-bar-red active height-90"></div>
    </div>
  </a>
</div>

<script>
/**
 * Real-time Stats Auto-update System 🚀
 * Memperbarui angka statistik setiap 10 detik tanpa refresh halaman.
 */
document.addEventListener('DOMContentLoaded', function() {
    const statsUrl = "{{ route('admin.stats.api') }}";
    
    function formatNumber(num) {
        return new Intl.NumberFormat().format(num);
    }

    function updateStats() {
        fetch(statsUrl)
            .then(response => response.json())
            .then(data => {
                const elements = {
                    'stat-packages': data.packages_count,
                    'stat-testimonials': data.testimonials_count,
                    'stat-registrations': data.registrations_count,
                    'stat-visitors': formatNumber(data.page_views)
                };

                Object.keys(elements).forEach(id => {
                    const el = document.getElementById(id);
                    if (el && el.innerText != elements[id]) {
                        // Animasi halus saat angka berubah
                        el.style.transform = 'scale(1.1)';
                        el.style.opacity = '0.5';
                        setTimeout(() => {
                            el.innerText = elements[id];
                            el.style.transform = 'scale(1)';
                            el.style.opacity = '1';
                        }, 200);
                    }
                });
            })
            .catch(error => console.error('Gagal memuat statistik realtime:', error));
    }

    // Nonaktifkan polling otomatis sementara karena menyebabkan deadlock pada 'php artisan serve'
    // yang bersifat single-threaded. Statistik akan tetap muncul saat halaman direfresh.
    /*
    setInterval(updateStats, 10000); 
    */
    
    console.log('Polling dinonaktifkan untuk mencegah server hang.');
</script>

<!-- Bottom Grid -->
<div class="bottom-grid">
  <!-- Package List -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">Daftar Paket</div>
      <a href="{{ route('admin.packages.index') }}" class="card-action">Lihat Semua</a>
    </div>
    <div class="pkg-table">
      @forelse($packagesList as $pkg)
      <div class="pkg-row">
        <div class="pkg-icon">
          @if(str_contains(strtolower($pkg['nama'] ?? ''), 'haji'))
            <svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M12 22s-8-4.5-8-11.8A8 8 0 0112 2a8 8 0 018 8.2c0 7.3-8 11.8-8 11.8z"/><circle cx="12" cy="10" r="3"/></svg>
          @else
            <svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
          @endif
        </div>
        <div class="pkg-info">
          <div class="pkg-name">{{ $pkg['nama'] ?? 'Paket Haji/Umrah' }}</div>
          <div class="pkg-detail">{{ $pkg['hari'] ?? '-' }} Hari · {{ $pkg['kamar'] ?? 'Quad/Triple/Double' }}</div>
        </div>
        <div class="pkg-price">{{ $pkg['harga_mulai'] ?? 'TBA' }}</div>
        <div class="pkg-status active">Aktif</div>
      </div>
      @empty
      <div class="empty-state-pkg">
        Belum ada paket yang dibuat.
      </div>
      @endforelse
    </div>
  </div>

  <!-- Visitors and Actions -->
  <div class="dashboard-bottom-right">
    <!-- Visitor Details Card -->
    <div class="card">
        <div class="card-header">
          <div class="card-title">Kunjungan Terkini</div>
          <a href="{{ route('admin.visitors.index') }}" class="card-action">Detail</a>
        </div>
        <div class="visitor-list">
          @forelse($recentVisitors as $v)
          <a href="{{ route('admin.visitors.index') }}" class="visitor-row visitor-row-link">
            <div class="pkg-icon visitor-icon-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
            </div>
            <div class="visitor-info">
              <div class="visitor-ip">{{ $v['ip_address'] ?? '0.0.0.0' }}</div>
              <div class="visitor-meta">{{ $v['browser'] ?? 'Browser' }} · {{ $v['device'] ?? 'Device' }}</div>
            </div>
            <div class="visitor-time">
                {{ isset($v['timestamp']) ? \Carbon\Carbon::parse($v['timestamp'])->diffForHumans() : '-' }}
            </div>
          </a>
          @empty
          <div class="empty-state-visitor">Tidak ada log kunjungan.</div>
          @endforelse
        </div>
    </div>

    <!-- Quick Actions Card -->
    <div class="card quick-card quick-card-animated">
    <div class="card-header">
      <div class="card-title">Aksi Cepat</div>
    </div>
    <div class="quick-actions">
      <a href="{{ route('admin.packages.create') }}" class="quick-btn">
        <div class="quick-btn-icon g">
          <svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M12 5v14"/></svg>
        </div>
        <div class="quick-btn-text">
          <div class="qb-title">Tambah Paket</div>
          <div class="qb-sub">Buat paket haji atau umrah baru</div>
        </div>
        <svg class="arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
      </a>

      <a href="{{ route('admin.testimonials.index') }}" class="quick-btn">
        <div class="quick-btn-icon y">
          <svg viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
        </div>
        <div class="quick-btn-text">
          <div class="qb-title">Kelola Testimoni</div>
          <div class="qb-sub">{{ $stats['testimonials_count'] ?? 0 }} testimoni jemaah</div>
        </div>
        <svg class="arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
      </a>

      <a href="{{ route('admin.registrations.index') }}" class="quick-btn">
        <div class="quick-btn-icon b">
          <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/></svg>
        </div>
        <div class="quick-btn-text">
          <div class="qb-title">Pendaftaran Jemaah</div>
          <div class="qb-sub">Lihat daftar jemaah terdaftar</div>
        </div>
        <svg class="arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
      </a>

      <a href="{{ route('admin.settings') }}" class="quick-btn">
        <div class="quick-btn-icon g">
          <svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
        </div>
        <div class="quick-btn-text">
          <div class="qb-title">Pengaturan Website</div>
          <div class="qb-sub">Ubah tampilan & konten website</div>
        </div>
        <svg class="arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
      </a>
    </div>
  </div>
</div>

<!-- Info bar -->
<div class="info-bar" id="infoBar">
  <div class="info-icon">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
  </div>
  <p>💡 <strong>Tips:</strong> Gunakan menu "Pengaturan Website" untuk memperbarui konten halaman utama, galeri, dan informasi kontak perusahaan Anda.</p>
  <button class="info-close" onclick="document.getElementById('infoBar').classList.add('d-none')">×</button>
</div>

@endsection
