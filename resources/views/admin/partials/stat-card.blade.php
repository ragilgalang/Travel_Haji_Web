{{--
    Stat Card Partial
    Usage: @include('admin.partials.stat-card', ['icon' => '🕋', 'label' => 'Paket Aktif', 'value' => $stats['packages_count']])
--}}
<div class="card stat-card">
    <span class="stat-icon">{{ $icon }}</span>
    <h3 class="stat-label">{{ $label }}</h3>
    <p class="stat-value">{{ $value }}</p>
</div>
