@extends('admin.layout')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/registrations/index.css') }}">
    <style>
        /* ===== TRASH PAGE STYLES ===== */
        .trash-header-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 1.5px solid #fca5a5;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.78rem;
            font-weight: 700;
            color: #991b1b;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .trash-empty-state {
            text-align: center;
            padding: 80px 20px;
        }

        .trash-empty-state .trash-empty-icon {
            font-size: 5rem;
            opacity: 0.25;
            margin-bottom: 16px;
        }

        .trash-empty-state p {
            color: #94a3b8;
            font-size: 1rem;
        }

        .btn-restore {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 14px;
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .btn-restore:hover {
            background: #059669;
            color: #fff;
            border-color: #059669;
        }

        .btn-force-delete {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 14px;
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #fecdd3;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .btn-force-delete:hover {
            background: #be123c;
            color: #fff;
            border-color: #be123c;
        }

        .trash-warning-bar {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 1px solid #fca5a5;
            border-radius: 12px;
            padding: 14px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.9rem;
            color: #991b1b;
            font-weight: 500;
        }

        .trash-table .trashed-at-cell {
            font-size: 0.78rem;
            color: #94a3b8;
            font-family: monospace;
        }

        .trash-table .reason-badge {
            display: inline-block;
            background: #fee2e2;
            color: #be123c;
            border-radius: 6px;
            padding: 2px 10px;
            font-size: 0.75rem;
            font-weight: 700;
        }
    </style>
@endpush

@section('content')
    <!-- HEADER -->
    <div class="card-header registrations-header"
        style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 14px;">
            <h1 class="card-title" style="margin: 0;">🗑️ Sampah</h1>
            <span class="trash-header-badge">Dibatalkan</span>
            @if(count($trashed) > 0)
                <span style="background: #dc2626; color: white; border-radius: 50px; padding: 2px 10px; font-size: 0.75rem; font-weight: 800;">
                    {{ count($trashed) }}
                </span>
            @endif
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            <a href="{{ route('admin.registrations.index') }}" class="btn-primary"
                style="background: #475569; padding: 10px 20px; border-radius: 10px; border: none; font-weight: 700; text-decoration: none; display: inline-block;">
                ← Kembali ke Pendaftaran
            </a>
            @if(count($trashed) > 0)
                <form action="{{ route('admin.registrations.emptyTrash') }}" method="POST"
                    onsubmit="return confirm('⚠️ Kosongkan semua Sampah? Tindakan ini tidak dapat dibatalkan!')">
                    @csrf
                    <button type="submit" class="btn-primary"
                        style="background: #dc2626; padding: 10px 20px; border-radius: 10px; border: none; font-weight: 700; cursor: pointer;">
                        🔥 Kosongkan Semua Sampah
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success mt-4">✅ {{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert-danger">❌ {{ session('error') }}</div>
    @endif

    <!-- WARNING BAR -->
    <div class="trash-warning-bar">
        <span style="font-size: 1.3rem;">⚠️</span>
        <span>Data di halaman ini adalah pendaftar dengan status <strong>Dibatalkan</strong>. Data dapat dipulihkan atau dihapus permanen. <strong style="color: #be123c;">Peringatan: Data yang berada di dalam Sampah lebih dari 1 bulan akan dihapus secara permanen secara otomatis oleh sistem.</strong></span>
    </div>

    <!-- SEARCH -->
    <div class="card filter-card">
        <form method="GET" class="filter-form">
            <div class="filter-group">
                <label class="filter-label">Cari Nama / No. HP / No. Ref</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control filter-input"
                    placeholder="Ketik untuk mencari...">
            </div>
            <button type="submit" class="btn-primary filter-btn">🔍 Cari</button>
            @if(request('q'))
                <a href="{{ route('admin.registrations.trash') }}" class="reset-btn">✕ Reset</a>
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
                <!-- Bulk Restore Form -->
                <form id="bulk-restore-form" action="{{ route('admin.registrations.bulkRestore') }}" method="POST">
                    @csrf
                    <div class="selected-ids-container"></div>
                    <button type="submit" class="btn-bulk-restore">
                        🔄 Pulihkan Terpilih
                    </button>
                </form>

                <!-- Bulk Delete Form -->
                <form id="bulk-delete-form" action="{{ route('admin.registrations.bulkDestroy') }}" method="POST"
                    onsubmit="return confirm('Hapus permanen semua data sampah yang dipilih? Tindakan ini tidak dapat dibatalkan!')">
                    @csrf
                    <div class="selected-ids-container"></div>
                    <button type="submit" class="btn-bulk-delete">
                        🔥 Hapus Permanen
                    </button>
                </form>
            </div>
        </div>

        @if(count($trashed) === 0)
            <div class="trash-empty-state">
                <div class="trash-empty-icon">🗑️</div>
                <p style="font-size: 1.1rem; font-weight: 600; color: #64748b;">Sampah Kosong</p>
                <p>Tidak ada data pendaftar yang dibatalkan.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="registrations-table trash-table">
                    <thead>
                        <tr>
                            <th class="checkbox-col">
                                <input type="checkbox" id="select-all-checkbox" class="checkbox-input">
                            </th>
                            <th>DATA PENDAFTAR</th>
                            <th>NO. REF</th>
                            <th>KONTAK</th>
                            <th>STATUS</th>
                            <th>DIBATALKAN PADA</th>
                            <th class="center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trashed as $reg)
                            @php
                                $displayName = $reg['nama_lengkap'] ?? '-';
                                $packageName = $reg['paket'] ?? ($reg['dynamic_fields'][5]['value'] ?? '-');
                                $isHaji = str_contains(strtolower($packageName), 'haji');
                                $phone = $reg['no_hp'] ?? null;
                                $trashedAt = isset($reg['trashed_at'])
                                    ? \Carbon\Carbon::parse($reg['trashed_at'])->format('d M Y, H:i')
                                    : (isset($reg['created_at']) ? \Carbon\Carbon::parse($reg['created_at'])->format('d M Y, H:i') : '-');
                            @endphp
                            <tr class="registration-row" style="opacity: 0.85;">
                                <td class="checkbox-cell">
                                    <input type="checkbox" class="registration-checkbox checkbox-input" value="{{ $reg['id'] }}">
                                </td>
                                <td class="data-cell">
                                    <div class="registrant-info">
                                        <div class="reg-icon-box {{ $isHaji ? 'haji' : 'umrah' }}" title="{{ $isHaji ? 'Haji' : 'Umrah' }}">
                                            @if($isHaji)
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M12 22s-8-4.5-8-11.8A8 8 0 0112 2a8 8 0 018 8.2c0 7.3-8 11.8-8 11.8z" />
                                                    <circle cx="12" cy="10" r="3" />
                                                </svg>
                                            @else
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" />
                                                    <circle cx="12" cy="10" r="3" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="registrant-name">{{ $displayName }}</div>
                                            <div class="reg-package-tag">
                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                    <path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z" />
                                                    <path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16" />
                                                </svg>
                                                {{ $packageName }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="ref-code-cell">
                                    <code>{{ $reg['ref_id'] ?? '-' }}</code>
                                </td>
                                <td class="contact-cell">{{ $phone ?? 'N/A' }}</td>
                                <td class="status-cell">
                                    <span class="reason-badge">Dibatalkan</span>
                                </td>
                                <td class="trashed-at-cell">{{ $trashedAt }}</td>
                                <td class="action-cell">
                                    <!-- Restore -->
                                    <form action="{{ route('admin.registrations.restore', $reg['id']) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn-restore"
                                            onclick="return confirm('Pulihkan data ini? Status akan dikembalikan ke Menunggu Verifikasi.')">
                                            🔄 Pulihkan
                                        </button>
                                    </form>

                                    <!-- Permanent Delete -->
                                    <form action="{{ route('admin.registrations.forceDelete', $reg['id']) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-force-delete"
                                            onclick="return confirm('⚠️ Hapus permanen data ini? Tindakan ini TIDAK dapat dibatalkan!')">
                                            🔥 Hapus Permanen
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

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    checkboxes.forEach(cb => { cb.checked = selectAll.checked; });
                    updateActionToolbar();
                });
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', function () {
                    if (!this.checked && selectAll) selectAll.checked = false;
                    if (document.querySelectorAll('.registration-checkbox:checked').length === checkboxes.length && selectAll) {
                        selectAll.checked = true;
                    }
                    updateActionToolbar();
                });
            });
        });
    </script>
@endpush
