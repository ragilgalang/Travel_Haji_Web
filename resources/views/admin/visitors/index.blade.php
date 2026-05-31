@extends('admin.layout')

@section('page_title', 'Detail Pengunjung')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/visitors/index.css') }}">
@endpush

@section('content')
<div class="visitor-container">
    <div class="welcome-banner">
        <div class="welcome-text">
            <div class="greeting-tag">Analisis Website</div>
            <h2>Detail <em>Pengunjung Website</em></h2>
            <p>Pantau siapa saja yang melihat website Anda, lengkap dengan informasi perangkat and alamat IP mereka.</p>
        </div>
        <div class="welcome-actions welcome-actions-flex">
            <form action="{{ route('admin.visitors.sync') }}" method="POST">
                @csrf
                <button type="submit" class="action-btn secondary btn-sync">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 4v6h-6M1 20v-6h6"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>
                    Sinkronisasi Angka
                </button>
            </form>
 
            <form action="{{ route('admin.visitors.clear') }}" method="POST" onsubmit="return confirm('Hapus semua riwayat kunjungan?')">
                @csrf
                <button type="submit" class="action-btn secondary btn-clear">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18m-2 0v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6m3 0V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                    Bersihkan Riwayat
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="info-bar visitor-success-bar">
        <div class="info-icon visitor-success-icon">✅</div>
        <p>{{ session('success') }}</p>
    </div>
    @endif

    {{-- ========== FILTER BAR ========== --}}
    <div class="filter-bar">
        <form method="GET" class="filter-form">
            <span class="filter-label">📅 Periode:</span>
            <a href="{{ route('admin.visitors.index', ['time' => 'all']) }}" class="filter-chip {{ request('time') == 'all' ? 'active' : '' }}">Semua</a>
            <a href="{{ route('admin.visitors.index', ['time' => 'today']) }}" class="filter-chip {{ request('time', 'today') == 'today' ? 'active' : '' }}">Hari Ini</a>
            <a href="{{ route('admin.visitors.index', ['time' => 'week']) }}" class="filter-chip {{ request('time') == 'week' ? 'active' : '' }}">7 Hari</a>
            <a href="{{ route('admin.visitors.index', ['time' => 'month']) }}" class="filter-chip {{ request('time') == 'month' ? 'active' : '' }}">30 Hari</a>
        </form>
    </div>

    {{-- ========== CHART SECTION ========== --}}
    <!-- ========================================== -->
    <!-- [TANDA: GRAFIK PENGUNJUNG - CANVAS HTML] -->
    <!-- ========================================== -->
    <div class="chart-grid">
        {{-- Line Chart: Kunjungan Harian --}}
        <div class="chart-card chart-card-wide">
            <div class="chart-card-header">
                <h3>📈 Tren Kunjungan</h3>
            </div>
            <div class="chart-body">
                <canvas id="dailyChart" height="280"></canvas>
            </div>
        </div>
    </div>

    {{-- ========== TABEL LOG ========== --}}
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div>
                <h3 class="card-title">Log Aktivitas Terbaru</h3>
                <span class="text-muted visitor-count-text">Menampilkan {!! count($visitors) !!} kunjungan terakhir</span>
            </div>
            <form method="GET" style="display: flex; gap: 10px; align-items: center;">
                <label style="font-size: 13px; font-weight: 600; color: #4b5563;">Filter Waktu:</label>
                <select name="time" class="form-control" onchange="this.form.submit()" style="min-width: 160px; padding: 8px 12px; border-radius: 10px; border: 1px solid #d1d5db;">
                    <option value="all" {{ request('time') == 'all' ? 'selected' : '' }}>— Semua Waktu —</option>
                    <option value="today" {{ request('time', 'today') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="week" {{ request('time') == 'week' ? 'selected' : '' }}>7 Hari Terakhir</option>
                    <option value="month" {{ request('time') == 'month' ? 'selected' : '' }}>1 Bulan Terakhir</option>
                </select>
                @if(request('time'))
                    <a href="{{ route('admin.visitors.index') }}" class="action-btn secondary" style="padding: 8px 14px; text-decoration: none;">Reset</a>
                @endif
            </form>
        </div>
        <div class="card-body visitor-card-body">
            <div class="table-responsive">
                <table class="data-table visitor-table-full">
                    <thead>
                        <tr>
                            <th>Perangkat / Browser</th>
                            <th>Alamat IP</th>
                            <th>Waktu Kunjungan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($visitors as $visitor)
                            @php
                                $ua = $visitor['user_agent'] ?? '';

                                // ===== DETEKSI TIPE PERANGKAT =====
                                $isBot = preg_match('/bot|crawl|spider|slurp|facebookexternalhit|Googlebot|bingbot|Baiduspider|YandexBot/i', $ua);
                                $isTablet = preg_match('/iPad|Tablet|tablet|playbook/i', $ua) && !preg_match('/Mobile/i', $ua);
                                $isMobile = !$isTablet && preg_match('/Mobile|Android|iPhone|iPod|BlackBerry|Windows Phone|webOS/i', $ua);
                                $isDesktop = !$isMobile && !$isTablet && !$isBot;

                                if ($isBot) $deviceType = 'Bot / Crawler';
                                elseif ($isTablet) $deviceType = 'Tablet';
                                elseif ($isMobile) $deviceType = 'HP / Ponsel';
                                else $deviceType = 'Laptop / PC';

                                // ===== DETEKSI MEREK / MODEL =====
                                $brand = '';
                                if (preg_match('/iPhone/i', $ua)) $brand = 'iPhone';
                                elseif (preg_match('/iPad/i', $ua)) $brand = 'iPad';
                                elseif (preg_match('/SM-[A-Z0-9]+/i', $ua, $m)) $brand = 'Samsung ' . $m[0];  // Samsung Galaxy
                                elseif (preg_match('/Samsung/i', $ua)) $brand = 'Samsung';
                                elseif (preg_match('/Xiaomi|MI\s|Redmi|POCO/i', $ua)) $brand = 'Xiaomi';
                                elseif (preg_match('/Oppo|CPH[0-9]+/i', $ua)) $brand = 'OPPO';
                                elseif (preg_match('/Vivo|V[0-9]{4}/i', $ua)) $brand = 'Vivo';
                                elseif (preg_match('/Realme/i', $ua)) $brand = 'Realme';
                                elseif (preg_match('/Huawei|Honor/i', $ua)) $brand = 'Huawei';
                                elseif (preg_match('/Nokia/i', $ua)) $brand = 'Nokia';
                                elseif (preg_match('/Motorola|moto\s/i', $ua)) $brand = 'Motorola';
                                elseif (preg_match('/OnePlus/i', $ua)) $brand = 'OnePlus';
                                elseif (preg_match('/Asus|ZenFone/i', $ua)) $brand = 'ASUS';
                                elseif (preg_match('/LG-[A-Z0-9]+/i', $ua, $m)) $brand = 'LG ' . $m[0];
                                elseif (preg_match('/Googlebot/i', $ua)) $brand = 'Google';
                                elseif (preg_match('/facebookexternalhit/i', $ua)) $brand = 'Facebook';

                                // ===== DETEKSI SISTEM OPERASI =====
                                if (preg_match('/Windows NT 10\.0/i', $ua)) $os = 'Windows 10/11';
                                elseif (preg_match('/Windows NT 6\.3/i', $ua)) $os = 'Windows 8.1';
                                elseif (preg_match('/Windows NT 6\.1/i', $ua)) $os = 'Windows 7';
                                elseif (preg_match('/Windows/i', $ua)) $os = 'Windows';
                                elseif (preg_match('/Mac OS X ([\d_]+)/i', $ua, $m)) {
                                    $osVer = str_replace('_', '.', $m[1]);
                                    $os = "macOS $osVer";
                                } elseif (preg_match('/Android ([\d.]+)/i', $ua, $m)) $os = 'Android ' . $m[1];
                                elseif (preg_match('/iPhone OS ([\d_]+)/i', $ua, $m)) $os = 'iOS ' . str_replace('_', '.', $m[1]);
                                elseif (preg_match('/iPad.*OS ([\d_]+)/i', $ua, $m)) $os = 'iPadOS ' . str_replace('_', '.', $m[1]);
                                elseif (preg_match('/Linux/i', $ua)) $os = 'Linux';
                                elseif (preg_match('/CrOS/i', $ua)) $os = 'Chrome OS';
                                else $os = 'Unknown OS';

                                // ===== DETEKSI BROWSER =====
                                if (preg_match('/Edg\//i', $ua)) $browser = 'Edge';
                                elseif (preg_match('/OPR\/|Opera/i', $ua)) $browser = 'Opera';
                                elseif (preg_match('/SamsungBrowser\/([\d.]+)/i', $ua, $m)) $browser = 'Samsung Browser ' . $m[1];
                                elseif (preg_match('/UCBrowser/i', $ua)) $browser = 'UC Browser';
                                elseif (preg_match('/MIUI Browser|MiuiBrowser/i', $ua)) $browser = 'MIUI Browser';
                                elseif (preg_match('/Chrome\/([\d]+)/i', $ua, $m)) $browser = 'Chrome ' . $m[1];
                                elseif (preg_match('/Firefox\/([\d]+)/i', $ua, $m)) $browser = 'Firefox ' . $m[1];
                                elseif (preg_match('/Safari\/([\d]+)/i', $ua, $m) && !preg_match('/Chrome/i', $ua)) $browser = 'Safari';
                                elseif ($isBot) $browser = 'Bot';
                                else $browser = 'Browser Lain';

                                // ===== ICON PERANGKAT =====
                                $deviceIcon = $isBot
                                    ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><circle cx="12" cy="5" r="2"/><path d="M12 7v4"/><path d="M8 14h.01M12 14h.01M16 14h.01"/></svg>'
                                    : ($isTablet
                                        ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>'
                                        : ($isMobile
                                            ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>'
                                            : '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>'
                                        )
                                    );

                                // Warna badge
                                $badgeColor = $isBot ? '#f59e0b' : ($isMobile ? '#10b981' : ($isTablet ? '#3b82f6' : '#6366f1'));
                            @endphp
                             <tr>
                                <td>
                                    <div class="visitor-device-row">
                                        <div class="device-icon-wrapper" style="color: {{ $badgeColor }}">
                                            {!! $deviceIcon !!}
                                        </div>
                                        <div>
                                            <div class="visitor-device-name" style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                                                <span style="background:{{ $badgeColor }}20; color:{{ $badgeColor }}; border-radius:6px; padding:2px 8px; font-size:11px; font-weight:700;">
                                                    {{ $deviceType }}
                                                </span>
                                                @if($brand)
                                                    <span style="font-weight:600; font-size:13px;">{{ $brand }}</span>
                                                @endif
                                            </div>
                                            <div style="font-size:12px; color:#6b7280; margin-top:2px;">
                                                {{ $os }} &bull; {{ $browser }}
                                            </div>
                                            <div class="visitor-ua-text" title="{{ $ua }}" style="font-size:10px; color:#9ca3af; margin-top:1px; max-width:280px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                                {{ Str::limit($ua, 60) }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                 <td class="visitor-ip-text">
                                    {{ $visitor['ip'] ?? '-' }}
                                </td>
                                <td class="visitor-time-text">
                                    <span class="visitor-time-main">
                                        {{ isset($visitor['timestamp']) ? \Carbon\Carbon::parse($visitor['timestamp'])->diffForHumans() : '-' }}
                                    </span>
                                    <span class="visitor-time-sub">{{ $visitor['timestamp'] ?? '-' }}</span>
                                </td>
                            </tr>
                        @empty
                             <tr>
                                <td colspan="3" class="visitor-empty-state">
                                    <div class="visitor-empty-icon">📉</div>
                                    <p>Belum ada rekaman kunjungan masuk.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartFont = { family: "'Inter', 'Segoe UI', sans-serif" };

    // ==========================================
    // [TANDA: GRAFIK PENGUNJUNG - SKRIP CHART.JS]
    // ==========================================
    // ===== 1. LINE CHART: Kunjungan Harian =====
    new Chart(document.getElementById('dailyChart'), {
        type: 'line',
        data: {
            labels: @json($chartData['dailyLabels']),
            datasets: [{
                label: 'Kunjungan',
                data: @json($chartData['dailyCounts']),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.08)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleFont: { ...chartFont, size: 13 },
                    bodyFont: { ...chartFont, size: 12 },
                    padding: 12,
                    cornerRadius: 10,
                    displayColors: false,
                    callbacks: {
                        label: ctx => ctx.parsed.y + ' pengunjung'
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { ...chartFont, size: 11 }, color: '#94a3b8' }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: {
                        font: { ...chartFont, size: 11 },
                        color: '#94a3b8',
                        stepSize: 1,
                        precision: 0
                    }
                }
            }
        }
    });

});
</script>
@endpush
