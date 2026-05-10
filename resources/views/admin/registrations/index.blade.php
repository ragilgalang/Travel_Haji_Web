@extends('admin.layout')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/registrations/index.css') }}">
@endpush

@section('content')
    <div class="card-header registrations-header"
        style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <h1 class="card-title" style="margin: 0;">📋 Data Pendaftaran</h1>
        <div class="header-actions" style="display: flex; gap: 0.75rem;">
            @if(request('archived') != '1')
                @php
                    $hasFinished = collect($registrations)->contains('status', 'Selesai');
                @endphp
                @if($hasFinished)
                    <form action="{{ route('admin.registrations.archiveAllFinished') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-primary"
                            style="background: #fbbf24; color: #92400e; padding: 10px 20px; border-radius: 10px; border: none; font-weight: 700;">
                            📦 Pindahkan Semua ke Arsip
                        </button>
                    </form>
                @endif
            @endif

            <a href="{{ route('register.show') }}" target="_blank" class="public-form-link"
                style="background: #1a5c3a; padding: 10px 20px; border-radius: 10px;">
                🔗 Lihat Form Pendaftaran
            </a>
            @if(request('archived') == '1')
                <a href="{{ route('admin.registrations.index') }}" class="btn-primary"
                    style="background: #10b981; padding: 10px 20px; border-radius: 10px; border: none;">
                    🔙 Kembali
                </a>
            @else
                <a href="{{ route('admin.registrations.index', ['archived' => 1]) }}" class="btn-primary"
                    style="background: #475569; padding: 10px 20px; border-radius: 10px; border: none;">
                    📦 Lihat Arsip
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success mt-4">✅ {{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert-danger">❌ {{ session('error') }}</div>
    @endif

    <!-- FILTER BAR -->
    <div class="card filter-card">
        <form method="GET" class="filter-form">
            @if(request('archived') == '1')
                <input type="hidden" name="archived" value="1">
            @endif
            <div class="filter-group">
                <label class="filter-label">Cari Nama / NIK / No. Ref</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control filter-input"
                    placeholder="Ketik untuk mencari...">
            </div>
            <div>
                <label class="filter-label">Filter Status</label>
                <select name="status" class="form-control filter-select" onchange="this.form.submit()">
                    <option value="">— Semua Status —</option>
                    @foreach(['Menunggu Verifikasi', 'Sedang Diproses', 'Sudah Dikonfirmasi', 'Selesai', 'Berangkat', 'Dibatalkan'] as $st)
                        <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="filter-label">Filter Paket</label>
                <select name="type" class="form-control filter-select" onchange="this.form.submit()">
                    <option value="">— Semua Paket —</option>
                    <option value="haji" {{ request('type') == 'haji' ? 'selected' : '' }}>Haji</option>
                    <option value="umrah" {{ request('type') == 'umrah' ? 'selected' : '' }}>Umrah</option>
                </select>
            </div>
            <div>
                <label class="filter-label">Filter Waktu</label>
                <select name="time" class="form-control filter-select" onchange="this.form.submit()">
                    <option value="">— Semua Waktu —</option>
                    <option value="today" {{ request('time') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="week" {{ request('time') == 'week' ? 'selected' : '' }}>7 Hari Terakhir</option>
                    <option value="month" {{ request('time') == 'month' ? 'selected' : '' }}>1 Bulan Terakhir</option>
                    <option value="3months" {{ request('time') == '3months' ? 'selected' : '' }}>3 Bulan Terakhir</option>
                    <option value="6months" {{ request('time') == '6months' ? 'selected' : '' }}>6 Bulan Terakhir</option>
                    <option value="year" {{ request('time') == 'year' ? 'selected' : '' }}>Tahun Ini</option>
                </select>
            </div>
            <button type="submit" class="btn-primary filter-btn">🔍 Cari</button>
            @if(request('q') || request('status') || request('time'))
                <a href="{{ route('admin.registrations.index') }}" class="reset-btn">✕ Reset</a>
            @endif
        </form>
    </div>

    <!-- TABLE -->
    <div class="card p-0 overflow-hidden relative">
        <!-- Bulk Action Toolbar -->
        <div id="bulk-action-bar" class="bulk-action-bar-modern d-none">
            <div class="bulk-count-text">
                <span id="selected-count">0</span> data dipilih
            </div>

            <div class="bulk-actions-group">
                <!-- Bulk Update Status Form - HIDE IF ARCHIVED -->
                @if(request('archived') != '1')
                    <form id="bulk-status-form" action="{{ route('admin.registrations.bulkStatus') }}" method="POST"
                        class="bulk-status-form-modern">
                        @csrf
                        <div class="selected-ids-container"></div>
                        <select name="status" class="form-control bulk-status-select" required onchange="this.form.submit()">
                            <option value="">— Ubah Status —</option>
                            @foreach(['Menunggu Verifikasi', 'Sedang Diproses', 'Sudah Dikonfirmasi', 'Selesai', 'Berangkat', 'Dibatalkan'] as $st)
                                <option value="{{ $st }}">{{ $st }}</option>
                            @endforeach
                        </select>
                    </form>
                @endif

                <!-- Bulk Delete Form -->
                <form id="bulk-delete-form" action="{{ route('admin.registrations.bulkDestroy') }}" method="POST"
                    onsubmit="return confirm('Hapus semua data pendaftaran yang dipilih?')">
                    @csrf
                    <div class="selected-ids-container"></div>
                    <button type="submit" class="btn-bulk-delete">
                        🗑 Hapus
                    </button>
                </form>
            </div>
        </div>
        @if(count($registrations) === 0)
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <p>Belum ada data pendaftaran.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="registrations-table">
                    <thead>
                        <tr>
                            <th class="checkbox-col">
                                <input type="checkbox" id="select-all-checkbox" class="checkbox-input">
                            </th>
                            <th>DATA PENDAFTAR</th>
                            <th>NO. REF</th>
                            <th>NIK</th>
                            <th>KONTAK</th>
                            <th>STATUS</th>
                            <th>TANGGAL</th>
                            <th class="center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($registrations as $reg)
                            @php
                                $statusColors = [
                                    'Menunggu Verifikasi' => ['bg' => '#dbeafe', 'color' => '#1d4ed8'],
                                    'Sedang Diproses' => ['bg' => '#fef3c7', 'color' => '#d97706'],
                                    'Sudah Dikonfirmasi' => ['bg' => '#d1fae5', 'color' => '#065f46'],
                                    'Selesai' => ['bg' => '#ede9fe', 'color' => '#6d28d9'],
                                    'Berangkat' => ['bg' => '#1a5c3a', 'color' => '#ffffff'],
                                    'Dibatalkan' => ['bg' => '#fee2e2', 'color' => '#b91c1c'],
                                ];
                                $sc = $statusColors[$reg['status'] ?? 'Menunggu Verifikasi'] ?? ['bg' => '#f1f5f9', 'color' => '#64748b'];

                                $displayName = $reg['nama_lengkap'] ?? '-';

                                // Cek apakah pendaftaran baru (24 jam terakhir)
                                $isNewRegistration = false;
                                if (isset($reg['created_at'])) {
                                    $diff = \Carbon\Carbon::parse($reg['created_at'])->diffInHours(now());
                                    $isNewRegistration = $diff < 24;
                                }

                                // Ambil info paket
                                $packageName = $reg['paket'] ?? ($reg['dynamic_fields'][5]['value'] ?? '-');
                                $roomType = $reg['kamar'] ?? ($reg['dynamic_fields'][6]['value'] ?? '-');
                                $isHaji = str_contains(strtolower($packageName), 'haji');
                            @endphp
                            @php
                                $isNew = in_array(($reg['status'] ?? ''), ['Menunggu Verifikasi', 'Baru']);
                            @endphp
                            <tr class="registration-row {{ $isNew ? 'is-new' : '' }}">
                                <td class="checkbox-cell">
                                    <input type="checkbox" class="registration-checkbox checkbox-input" value="{{ $reg['id'] }}">
                                </td>
                                <td class="data-cell">
                                    <div class="registrant-info">
                                        <div class="reg-icon-box {{ $isHaji ? 'haji' : 'umrah' }}"
                                            title="{{ $isHaji ? 'Haji' : 'Umrah' }}">
                                            @if($isHaji)
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2">
                                                    <path d="M12 22s-8-4.5-8-11.8A8 8 0 0112 2a8 8 0 018 8.2c0 7.3-8 11.8-8 11.8z" />
                                                    <circle cx="12" cy="10" r="3" />
                                                </svg>
                                            @else
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2">
                                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" />
                                                    <circle cx="12" cy="10" r="3" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="registrant-name">
                                                {{ $displayName }}
                                                @if($isNewRegistration)
                                                    <span class="badge-new">Terbaru</span>
                                                @endif
                                            </div>
                                            <div class="reg-package-tag">
                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2.5">
                                                    <path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z" />
                                                    <path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16" />
                                                </svg>
                                                {{ $packageName }} · {{ $roomType }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="ref-code-cell">
                                    <code>{{ $reg['ref_id'] ?? '-' }}</code>
                                </td>
                                <td class="nik-cell">
                                    <span
                                        style="font-family: monospace; font-size: 0.85rem; color: #475569;">{{ $reg['nik'] ?? '-' }}</span>
                                </td>
                                <td class="contact-cell">
                                    @php
                                        $phone = $reg['no_hp'] ?? null;
                                        if (!$phone && isset($reg['dynamic_fields'])) {
                                            foreach ($reg['dynamic_fields'] as $df) {
                                                $l = Str::slug($df['label']);
                                                if (str_contains($l, 'hp') || str_contains($l, 'whatsapp') || str_contains($l, 'tel')) {
                                                    $phone = $df['value'];
                                                    break;
                                                }
                                            }
                                        }
                                    @endphp
                                    {{ $phone ?? 'N/A' }}
                                </td>
                                <td class="status-cell">
                                    <span class="status-badge" style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};">
                                        {{ $reg['status'] ?? 'Menunggu Verifikasi' }}
                                    </span>
                                </td>
                                <td class="date-cell">
                                    {{ isset($reg['created_at']) ? \Carbon\Carbon::parse($reg['created_at'])->format('d M Y, H:i') : '-' }}
                                </td>
                                <td class="action-cell">
                                    @php
                                        $wa = $reg['no_hp'] ?? ($reg['telepon'] ?? '');
                                        $wa = preg_replace('/[^0-9]/', '', $wa);
                                        $nama = $reg['nama_lengkap'] ?? 'Jemaah';
                                        $paket = $reg['paket'] ?? 'paket ibadah';
                                        $msg = rawurlencode("Assalamu'alaikum Bapak/Ibu *$nama*,\n\nSaya Admin dari *PT. UMI MUTHMAINAH BERKAH*. Terima kasih telah mendaftar untuk *$paket*. Kami ingin mengonfirmasi pendaftaran Anda, apakah ada dokumen atau informasi yang perlu kami bantu? 🙏");
                                    @endphp

                                    @if($wa)
                                        <a href="https://wa.me/62{{ ltrim($wa, '0') }}?text={{ $msg }}" target="_blank"
                                            class="btn-detail" style="background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0;"
                                            title="Chat WhatsApp">
                                            💬 Chat
                                        </a>
                                    @endif

                                    <a href="{{ route('admin.registrations.show', $reg['id']) }}" class="btn-detail">
                                        👁 Detail
                                    </a>

                                    @if(request('archived') == '1')
                                        <form action="{{ route('admin.registrations.unarchive', $reg['id']) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn-detail"
                                                style="background: #ecfdf5; color: #059669; border: 1px solid #d1fae5;">
                                                🔓 Buka Arsip
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.registrations.archive', $reg['id']) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Pindahkan ke arsip? Data tetap tercatat di laporan keuangan.')">
                                            @csrf
                                            <button type="submit" class="btn-detail"
                                                style="background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;">
                                                📦 Arsipkan
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.registrations.destroy', $reg['id']) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Hapus data pendaftaran?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-delete">
                                            🗑 Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // LIVE SEARCH LOGIC
            const searchInput = document.querySelector('.filter-input');
            const tableRows = document.querySelectorAll('.registration-row');

            searchInput.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();

                tableRows.forEach(row => {
                    const text = row.innerText.toLowerCase();
                    if (text.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            const selectAll = document.getElementById('select-all-checkbox');
            const checkboxes = document.querySelectorAll('.registration-checkbox');
            const bulkBar = document.getElementById('bulk-action-bar');
            const countSpan = document.getElementById('selected-count');
            const idContainers = document.querySelectorAll('.selected-ids-container');

            function updateActionToolbar() {
                const checked = document.querySelectorAll('.registration-checkbox:checked');
                const count = checked.length;

                if (count > 0) {
                    bulkBar.classList.add('d-flex');
                    bulkBar.classList.remove('d-none');
                    countSpan.textContent = count;

                    // Update hidden inputs for all forms
                    idContainers.forEach(container => {
                        container.innerHTML = '';
                        checked.forEach(cb => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'ids[]';
                            input.value = cb.value;
                            container.appendChild(input);
                        });
                    });
                } else {
                    bulkBar.classList.add('d-none');
                    bulkBar.classList.remove('d-flex');
                }
            }

            selectAll.addEventListener('change', function () {
                checkboxes.forEach(cb => {
                    cb.checked = selectAll.checked;
                });
                updateActionToolbar();
            });

            checkboxes.forEach(cb => {
                cb.addEventListener('change', function () {
                    if (!this.checked) selectAll.checked = false;
                    if (document.querySelectorAll('.registration-checkbox:checked').length === checkboxes.length) {
                        selectAll.checked = true;
                    }
                    updateActionToolbar();
                });
            });
        });
    </script>
@endpush