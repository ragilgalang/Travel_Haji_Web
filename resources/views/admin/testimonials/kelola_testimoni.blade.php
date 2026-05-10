@extends('admin.layout')

@section('page_title', 'Kelola Testimoni')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/testimonials/kelola_testimoni.css') }}">
@endpush

@section('content')
<div class="card">
    <div class="card-header testimonials-header">
        <h2 class="card-title">Ulasan Jemaah</h2>
        @php
            $settings = \Illuminate\Support\Facades\Cache::get('site_settings', []);
            $hasGmaps = !empty($settings['google_maps_api_key']) && !empty($settings['google_place_id']);
        @endphp
        
        <div class="header-actions">
            @if($hasGmaps)
                <button type="button" id="syncGmapsBtn" onclick="syncGmapsReviews()" class="btn-primary" style="background: #4285F4; border: none; border-radius: 8px; padding: 10px 18px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M23 4v6h-6"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
                    Sinkron Google Maps
                </button>
            @else
                <a href="{{ route('admin.settings', ['tab' => 'integrasi']) }}" class="btn-secondary" style="font-size: 0.85rem; color: #64748b; text-decoration: none;">
                    ⚙️ Atur Google Maps API
                </a>
            @endif
        </div>
    </div>

    <div class="card filter-card mb-4">
        <form method="GET" class="filter-form">
            <div class="filter-group">
                <label class="filter-label">Cari Nama / Isi Testimoni</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control filter-input testi-search-input" placeholder="Ketik untuk mencari...">
            </div>
            <div>
                <label class="filter-label">Filter Status</label>
                <select name="status" class="form-control filter-select" onchange="this.form.submit()">
                    <option value="">— Semua Status —</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Live di Web</option>
                    <option value="hidden" {{ request('status') == 'hidden' ? 'selected' : '' }}>Disembunyikan</option>
                </select>
            </div>
            <button type="submit" class="btn-primary filter-btn">🔍 Cari</button>
            @if(request('q') || request('status'))
            <a href="{{ route('admin.testimonials.index') }}" class="reset-btn">✕ Reset</a>
            @endif
        </form>
    </div>
    
    <div class="bulk-actions" id="bulkActionsBarTestimonials">
        <span id="selectedCountTestimonials">0 baris dipilih</span>
        <button type="button" class="btn-bulk btn-bulk-publish" onclick="executeBulkAction('publish')">✅ Tampilkan Info Web</button>
        <button type="button" class="btn-bulk btn-bulk-unpublish" onclick="executeBulkAction('unpublish')">🚫 Sembunyikan Info</button>
        <button type="button" class="btn-bulk btn-bulk-delete" onclick="executeBulkAction('delete')">🗑️ Hapus</button>
    </div>

    <div class="testimonials-table-wrapper">
        <table class="testimonials-table" id="testimonialsTable">
            <thead>
                <tr>
                    <th class="checkbox-col"><input type="checkbox" class="select-all-cb" data-target="testi-checkbox"></th>
                    <th class="label-col">Foto</th>
                    <th class="label-col">Jemaah</th>
                    <th class="label-col">Isi Testimoni</th>
                    <th class="label-col">Waktu Kirim</th>
                    <th class="label-col">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($testimonials ?? [] as $id => $testi)
                <tr>
                    <td><input type="checkbox" class="row-checkbox testi-checkbox" value="{{ $id }}"></td>
                    <td>
                        <div class="jemaah-avatar-wrapper">
                            @if(!empty($testi['avatar_url']))
                                <img src="{{ $testi['avatar_url'] }}">
                            @else
                                <span>{{ substr($testi['name'] ?? 'J', 0, 1) }}</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="jemaah-name">{{ $testi['name'] ?? 'Anonymous' }}</div>
                        <div class="jemaah-location">{{ $testi['location'] ?? '' }}</div>
                        <div class="jemaah-rating">{{ str_repeat('★', $testi['rating'] ?? 5) }}</div>
                    </td>
                    <td class="testi-content">"{{ \Illuminate\Support\Str::limit($testi['text'] ?? '', 80) }}"</td>
                    <td class="date-col">
                        <div class="date-box">
                            @if(!empty($testi['created_at']))
                                <div class="time">{{ \Carbon\Carbon::parse($testi['created_at'])->format('H:i') }}</div>
                                <div>{{ \Carbon\Carbon::parse($testi['created_at'])->format('d M Y') }}</div>
                            @else
                                <span style="color: #cbd5e1;">-</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        @php
                            $isPublished = isset($testi['is_published']) ? $testi['is_published'] : true;
                        @endphp
                        @if($isPublished)
                            <span class="status-badge yes">Live di Web</span>
                        @else
                            <span class="status-badge no">Disembunyikan</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="empty-testi">Belum ada ulasan yang masuk.</td></tr>
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
