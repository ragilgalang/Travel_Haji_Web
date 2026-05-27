@extends('admin.layout')

@section('page_title', 'Kelola Testimoni')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/testimonials/kelola_testimoni.css') }}">
@endpush

@section('content')
@php
    $settings = \Illuminate\Support\Facades\Cache::get('site_settings', []);
    $hasGmaps = !empty($settings['google_maps_api_key']) && !empty($settings['google_place_id']);
@endphp

<div class="testi-container">
    {{-- ── TOP HEADER SECTION ── --}}
    <div class="testi-header-premium">
        <div class="header-info">
            <h1 class="header-title">Ulasan Jemaah</h1>
            <p class="header-subtitle">Kelola testimoni dan umpan balik dari tamu Allah</p>
        </div>
        <div class="header-actions">
            @if($hasGmaps)
                <button type="button" id="syncGmapsBtn" onclick="syncGmapsReviews()" class="btn-gmaps-premium">
                    <div class="icon-wrap">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M23 4v6h-6"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
                    </div>
                    <span>Sinkron Google Maps</span>
                </button>
            @else
                <button type="button" class="btn-setup-gmaps-disabled" title="API Google Maps belum dikonfigurasi" disabled style="opacity: 0.7; cursor: not-allowed; background: #f1f5f9; color: #64748b; border: 1px dashed #cbd5e1; padding: 10px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-exclamation-triangle"></i> Google Maps Belum Terkonfigurasi
                </button>
            @endif
        </div>
    </div>

    {{-- ── FILTER & SEARCH BAR ── --}}
    <div class="testi-filter-premium shadow-sm">
        <form method="GET" class="filter-flex">
            <div class="search-input-group">
                <div class="search-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" class="search-input-premium testi-search-input" placeholder="Cari nama jemaah atau isi testimoni...">
            </div>
            
            <div class="filter-actions">
                <div class="select-premium-wrap">
                    <select name="status" class="select-premium" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>🟢 Live di Web</option>
                        <option value="hidden" {{ request('status') == 'hidden' ? 'selected' : '' }}>🔴 Disembunyikan</option>
                    </select>
                </div>
                
                @if(request('q') || request('status'))
                    <a href="{{ route('admin.testimonials.index') }}" class="btn-reset-premium">Reset</a>
                @endif
            </div>
        </form>
    </div>
    
    {{-- ── BULK ACTION BAR (FLOATING) ── --}}
    <div class="bulk-bar-premium" id="bulkActionsBarTestimonials">
        <div class="bulk-inner">
            <div class="selected-indicator">
                <span class="count-badge" id="selectedCountTestimonials">0</span>
                <span class="label">Baris terpilih</span>
            </div>
            <div class="bulk-buttons">
                <button type="button" class="btn-bulk-premium btn-b-publish" onclick="executeBulkAction('publish')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg> Tampilkan
                </button>
                <button type="button" class="btn-bulk-premium btn-b-unpublish" onclick="executeBulkAction('unpublish')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg> Sembunyikan
                </button>
                <div class="bulk-divider"></div>
                <button type="button" class="btn-bulk-premium btn-b-delete" onclick="executeBulkAction('delete')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg> Hapus
                </button>
            </div>
        </div>
    </div>

    {{-- ── DATA TABLE ── --}}
    <div class="testi-table-container shadow-sm">
        <table class="table-premium" id="testimonialsTable">
            <thead>
                <tr>
                    <th class="col-cb"><div class="custom-cb"><input type="checkbox" class="select-all-cb" data-target="testi-checkbox" id="selectAll"></div></th>
                    <th class="col-jemaah">Informasi Jemaah</th>
                    <th class="col-content">Isi Testimoni</th>
                    <th class="col-date text-center">Waktu Kirim</th>
                    <th class="col-status text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($testimonials ?? [] as $id => $testi)
                <tr class="row-hover">
                    <td>
                        <div class="custom-cb">
                            <input type="checkbox" class="row-checkbox testi-checkbox" value="{{ $id }}">
                        </div>
                    </td>
                    <td>
                        <div class="jemaah-flex">
                            <div class="avatar-premium-wrap {{ !empty($testi['avatar_url']) ? 'has-img' : '' }}">
                                @if(!empty($testi['avatar_url']))
                                    <img src="{{ $testi['avatar_url'] }}" alt="Avatar">
                                @else
                                    <span class="avatar-initial">{{ substr($testi['name'] ?? 'J', 0, 1) }}</span>
                                @endif
                                <div class="status-indicator {{ (isset($testi['is_published']) ? $testi['is_published'] : true) ? 'online' : 'offline' }}"></div>
                            </div>
                            <div class="jemaah-info">
                                <div class="j-name">{{ $testi['name'] ?? 'Anonymous' }}</div>
                                <div class="j-meta">
                                    <span class="j-loc"><i class="fas fa-map-marker-alt"></i> {{ $testi['location'] ?? 'Indonesia' }}</span>
                                    <div class="j-rating">
                                        @for($i=1; $i<=5; $i++)
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="{{ $i <= ($testi['rating'] ?? 5) ? '#eab308' : '#e2e8f0' }}"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="testi-quote-wrap">
                            <span class="quote-icon">“</span>
                            <div class="quote-text">{{ \Illuminate\Support\Str::limit($testi['text'] ?? '', 120) }}</div>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="date-premium">
                            @if(!empty($testi['created_at']))
                                <span class="d-time">{{ \Carbon\Carbon::parse($testi['created_at'])->format('H:i') }}</span>
                                <span class="d-day">{{ \Carbon\Carbon::parse($testi['created_at'])->format('d M Y') }}</span>
                            @else
                                <span class="d-none-val">-</span>
                            @endif
                        </div>
                    </td>
                    <td class="text-center">
                        @php $isPublished = isset($testi['is_published']) ? $testi['is_published'] : true; @endphp
                        <span class="badge-premium {{ $isPublished ? 'bg-live' : 'bg-hidden' }}">
                            {{ $isPublished ? 'Live di Web' : 'Disembunyikan' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state-premium">
                            <div class="empty-icon">📂</div>
                            <h3>Belum ada testimoni</h3>
                            <p>Testimoni dari landing page atau Google Maps akan muncul di sini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // LIVE SEARCH LOGIC
        const searchInput = document.querySelector('.testi-search-input');
        const testimonialsTable = document.getElementById('testimonialsTable');
        const rows = testimonialsTable.querySelectorAll('tbody tr:not(.empty-row)');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                rows.forEach(row => {
                    const text = row.innerText.toLowerCase();
                    row.style.display = text.includes(query) ? '' : 'none';
                });
            });
        }

        // Select All Logic
        const selectAllCbs = document.querySelectorAll('.select-all-cb');
        selectAllCbs.forEach(cb => {
            cb.addEventListener('change', function() {
                const targetClass = this.getAttribute('data-target');
                const rowCheckboxes = document.querySelectorAll('.' + targetClass);
                rowCheckboxes.forEach(rowCb => rowCb.checked = this.checked);
                updateBulkActionsVisibility(targetClass);
            });
        });

        // Individual Checkbox Logic
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('row-checkbox')) {
                const classes = e.target.className.split(' ');
                const targetClass = classes.find(c => c.endsWith('-checkbox'));
                if (targetClass) updateBulkActionsVisibility(targetClass);
            }
        });

        function updateBulkActionsVisibility(targetClass) {
            const checkedCount = document.querySelectorAll('.' + targetClass + ':checked').length;
            const bar = document.getElementById('bulkActionsBarTestimonials');
            const countLabel = document.getElementById('selectedCountTestimonials');

            if (bar && countLabel) {
                if (checkedCount > 0) {
                    bar.classList.add('active');
                    countLabel.innerText = checkedCount + ' baris dipilih';
                } else {
                    bar.classList.remove('active');
                    const selectAll = document.querySelector(`.select-all-cb[data-target="${targetClass}"]`);
                    if (selectAll) selectAll.checked = false;
                }
            }
        }

        window.executeBulkAction = function(actionType) {
            const targetClass = 'testi-checkbox';
            let confirmMsg = '';

            if(actionType === 'publish') confirmMsg = 'Tampilkan testimoni terpilih ke Landing Page?';
            if(actionType === 'unpublish') confirmMsg = 'Sembunyikan testimoni terpilih dari Landing Page?';
            if(actionType === 'delete') confirmMsg = 'Hapus permanen testimoni yang terpilih?';

            const checkedRows = document.querySelectorAll('.' + targetClass + ':checked');
            if(checkedRows.length === 0) return;
            if(!confirm(confirmMsg)) return;

            const ids = Array.from(checkedRows).map(cb => cb.value);
            checkedRows.forEach(cb => cb.closest('tr').classList.add('opacity-50'));

            fetch(`/admin/testimonials/bulk-action`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ action: actionType, ids: ids })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                    checkedRows.forEach(cb => cb.closest('tr').classList.remove('opacity-50'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan koneksi.');
                checkedRows.forEach(cb => cb.closest('tr').classList.remove('opacity-50'));
            });
        };

        window.syncGmapsReviews = function() {
            const btn = document.getElementById('syncGmapsBtn');
            const originalContent = btn.innerHTML;
            
            if(!confirm('Ambil ulasan terbaru dari Google Maps sekarang?')) return;

            btn.disabled = true;
            btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="anim-spin"><path d="M23 4v6h-6"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg> Menarik Data...';

            fetch(`{{ route('admin.testimonials.syncGmaps') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Gagal: ' + data.message);
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                }
            })
            .catch(error => {
                console.error('Sync Error:', error);
                alert('Terjadi kesalahan koneksi saat sinkronisasi.');
                btn.disabled = false;
                btn.innerHTML = originalContent;
            });
        };
    });
</script>
@endpush
