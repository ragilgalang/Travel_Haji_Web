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
            <a href="{{ route('admin.visitors.index') }}" class="filter-chip {{ request('time') == '' ? 'active' : '' }}">Semua</a>
            <a href="{{ route('admin.visitors.index', ['time' => 'today']) }}" class="filter-chip {{ request('time') == 'today' ? 'active' : '' }}">Hari Ini</a>
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
                <h3>📈 Tren Kunjungan ({{ $chartData['filterLabel'] }})</h3>
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
                    <option value="">— Semua Waktu —</option>
                    <option value="today" {{ request('time') == 'today' ? 'selected' : '' }}>Hari Ini</option>
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
                                $isMobile = preg_match('/Mobile|Android|iPhone/i', $ua);
                                $browser = 'Unknown';
                                if(strpos($ua, 'Chrome')) $browser = 'Chrome';
                                elseif(strpos($ua, 'Safari')) $browser = 'Safari';
                                elseif(strpos($ua, 'Firefox')) $browser = 'Firefox';
                                elseif(strpos($ua, 'Edge')) $browser = 'Edge';
                            @endphp
                             <tr>
                                <td>
                                    <div class="visitor-device-row">
                                        <div class="device-icon-wrapper">
                                            @if($isMobile)
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                                            @else
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="visitor-device-name">
                                                {{ $isMobile ? 'Mobile' : 'Desktop' }} - {{ $browser }}
                                            </div>
                                            <div class="visitor-ua-text" title="{{ $ua }}">
                                                {{ $ua }}
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
