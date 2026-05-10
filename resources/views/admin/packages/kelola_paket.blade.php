@extends('admin.layout')

@section('page_title', 'Kelola Paket')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/packages/kelola_paket.css') }}">
@endpush

@section('content')
    <!-- MAIN TABLE -->
    <div class="card">
        <div class="card-header packages-header">
            <h2 class="card-title">Daftar Paket Perjalanan (Total: {{ count($pkg_list ?? []) }})</h2>
            <button type="button" onclick="openPackageForm('create')" class="btn-primary btn-add-package">+ Tambah Paket
                Baru</button>
        </div>

        @if(session('success'))
            <div class="success-alert">
                {{ session('success') }}
            </div>
        @endif

        <!-- FILTER BAR (Style ala Pendaftaran) -->
        <div class="card filter-card"
            style="margin-bottom: 20px; padding: 20px; background: white; border-radius: 12px; border: 1px solid #e2e8f0;">
            <form method="GET" class="filter-form" id="filterFormPackages"
                style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
                <div class="filter-group" style="flex: 1; min-width: 250px;">
                    <label class="filter-label"
                        style="display: block; font-size: 0.85rem; font-weight: 600; color: #64748b; margin-bottom: 8px;">Cari
                        Nama Paket / Hotel</label>
                    <input type="text" name="search" id="liveSearchInput" value="{{ $search }}"
                        class="form-control filter-input" placeholder="Ketik untuk mencari langsung..."
                        style="width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 10px;">
                </div>
                <div style="min-width: 150px;">
                    <label class="filter-label"
                        style="display: block; font-size: 0.85rem; font-weight: 600; color: #64748b; margin-bottom: 8px;">Filter
                        Tipe</label>
                    <select name="type" class="form-control filter-select" onchange="this.form.submit()"
                        style="width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 10px; background: white;">
                        <option value="all" {{ ($type == 'all' || !$type) ? 'selected' : '' }}>— Semua Tipe —</option>
                        <option value="umrah" {{ $type == 'umrah' ? 'selected' : '' }}>Umrah</option>
                        <option value="haji" {{ $type == 'haji' ? 'selected' : '' }}>Haji</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary"
                    style="background: #10b981; color: white; border: none; padding: 11px 25px; border-radius: 10px; font-weight: 600; cursor: pointer;">🔍
                    Cari</button>
                @if(!empty($search) || (!empty($type) && $type !== 'all'))
                    <a href="{{ route('admin.packages.index') }}" class="btn-reset"
                        style="padding: 11px 15px; color: #64748b; text-decoration: none; font-size: 0.9rem;">✕ Reset</a>
                @endif
            </form>

            @if(!empty($search) || (!empty($type) && $type !== 'all'))
                <div style="margin-top: 15px; font-size: 0.85rem; color: #10b981; font-weight: 500;">
                    ✓ Menampilkan filter aktif
                    @if($search) : "{{ $search }}" @endif
                    @if($type && $type !== 'all') : {{ strtoupper($type) }} @endif
                    <span style="color: #94a3b8; font-weight: 400; margin-left: 5px;">(Ditemukan {{ count($pkg_list ?? []) }}
                        paket)</span>
                </div>
            @endif
        </div>

        <div class="bulk-actions" id="bulkActionsBarPackages">
            <span id="selectedCountPackages">0 paket dipilih</span>
            <button type="button" class="btn-bulk btn-bulk-delete" onclick="executeBulkAction('packages', 'delete')">🗑️
                Hapus Paket</button>
        </div>

        <div class="table-wrapper">
            <table class="packages-table" id="packagesTable">
                <thead>
                    <tr>
                        <th class="checkbox-col"><input type="checkbox" class="select-all-cb" data-target="paket-checkbox">
                        </th>
                        <th class="label-col">Paket</th>
                        <th class="label-col">Hotel</th>
                        <th class="label-col">Hotel Fac</th>
                        <th class="label-col">Tipe</th>
                        <th class="label-col">Durasi</th>
                        <th class="label-col">Harga</th>
                        <th class="label-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pkg_list ?? [] as $id => $package)
                        <tr>
                            <td><input type="checkbox" class="row-checkbox paket-checkbox" value="{{ $id }}"></td>
                            <td>
                                <div class="pkg-name-box">{{ $package['name'] ?? 'Untitled' }}</div>
                                @if($package['is_featured'] ?? false)
                                    <div class="featured-tag">⭐ FEATURED</div>
                                @endif
                            </td>
                            <td class="pkg-hotel-text">{{ $package['hotel'] ?? '-' }}</td>
                            <td class="pkg-hotel-fac-text">
                                @if(isset($package['hotel_facilities']) && is_array($package['hotel_facilities']))
                                    <span style="font-size: 0.8rem; color: #64748b;">{{ count($package['hotel_facilities']) }} item</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td><span class="type-badge">{{ $package['type'] ?? 'N/A' }}</span></td>
                            <td class="pkg-duration-text">{{ $package['duration'] ?? '-' }}</td>
                            <td class="pkg-price-text">{{ $package['price'] ?? '-' }}</td>
                            <td class="action-btns">
                                <button type="button"
                                    onclick="openPackageForm('edit', '{{ $id }}', {{ json_encode($package) }})"
                                    class="btn-edit-inline">Edit</button>
                                <form action="{{ route('admin.packages.destroy', $id) }}" method="POST"
                                    onsubmit="return confirm('Hapus paket ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-delete-inline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-packages">Belum ada paket perjalanan yang sesuai filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- OVERLAY: FORM EDITOR PAKET HAJI & UMRAH -->
    <div id="paketFormOverlay" class="modal-overlay">
        <div class="modal-content card pkg-modal-card">
            <div class="card-header modal-header pkg-modal-header">
                <div class="header-content">
                    <h2 class="card-title" id="paketFormTitle">Tambah Paket Baru</h2>
                    <p class="card-subtitle">Lengkapi detail paket perjalanan haji atau umrah</p>
                </div>
                <button type="button" onclick="closePackageForm()" class="btn-close-modal">&times;</button>
            </div>

            @if($errors->any() && old('paket_form_submission'))
                <div class="error-alert-box">
                    <div class="error-icon">⚠️</div>
                    <div class="error-text">
                        <strong>Gagal Menyimpan Paket</strong>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form id="paketCrudForm" action="{{ route('admin.packages.store') }}" method="POST"
                enctype="multipart/form-data" class="pkg-form">
                @csrf
                <input type="hidden" name="_method" id="paketMethod" value="POST">
                <input type="hidden" name="paket_form_submission" value="1">

                <div class="form-body">
                    <!-- SECTION 1: INFORMASI UTAMA -->
                    <div class="form-section">
                        <h3 class="section-title">Informasi Utama</h3>
                        <div class="form-grid-3">
                            <div class="form-group mb-4">
                                <label class="form-label">Nama Paket</label>
                                <input type="text" name="name" id="pkg_name" class="form-control" required
                                    placeholder="Umrah VIP Ramadhan" value="{{ old('name') }}">
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label">Kategori</label>
                                <select name="category" id="pkg_category" class="form-control">
                                    <option value="Umroh Ekonomis" {{ old('category') == 'Umroh Ekonomis' ? 'selected' : '' }}>Umroh Ekonomis</option>
                                    <option value="Umroh Reguler" {{ old('category') == 'Umroh Reguler' ? 'selected' : '' }}>
                                        Umroh Reguler</option>
                                    <option value="Umroh Bisnis" {{ old('category') == 'Umroh Bisnis' ? 'selected' : '' }}>
                                        Umroh Bisnis</option>
                                    <option value="Umroh VIP" {{ old('category') == 'Umroh VIP' ? 'selected' : '' }}>Umroh VIP
                                    </option>
                                    <option value="Haji Furoda" {{ old('category') == 'Haji Furoda' ? 'selected' : '' }}>Haji
                                        Furoda</option>
                                </select>
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label">Tipe Ibadah</label>
                                <select name="type" id="pkg_type" class="form-control">
                                    <option value="umrah" {{ old('type') == 'umrah' ? 'selected' : '' }}>Umrah</option>
                                    <option value="haji" {{ old('type') == 'haji' ? 'selected' : '' }}>Haji</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: AKOMODASI & HARGA -->
                    <div class="form-section">
                        <h3 class="section-title">Akomodasi & Harga</h3>
                        <div class="form-grid-3">
                            <div class="form-group mb-4">
                                <label class="form-label">Durasi Perjalanan</label>
                                <input type="text" name="duration" id="pkg_duration" class="form-control" required
                                    placeholder="9 Hari" value="{{ old('duration') }}">
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label">Nama Hotel</label>
                                <input type="text" name="hotel" id="pkg_hotel" class="form-control"
                                    placeholder="Hotel Hilton" value="{{ old('hotel') }}">
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label">Harga (Tampilan)</label>
                                <input type="text" name="price" id="pkg_price" class="form-control highlight-input" required
                                    placeholder="Rp 35.000.000" value="{{ old('price') }}">
                            </div>
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label">Fasilitas Hotel (Pisahkan tiap baris)</label>
                            <textarea name="hotel_facilities_text" id="pkg_hotel_facilities" class="form-control" rows="3" placeholder="Contoh:&#10;WiFi Gratis&#10;Dekat Masjidil Haram&#10;Sarapan Buffet"></textarea>
                        </div>
                    </div>

                    <!-- SECTION 3: MEDIA & PROMOSI -->
                    <div class="form-section">
                        <h3 class="section-title">Media & Promosi</h3>
                        <div class="form-group mb-4">
                            <label class="form-label">Gambar Cover</label>
                            <div class="media-input-group">
                                <div class="file-input-wrapper">
                                    <input type="file" name="image" id="pkg_image_file" class="form-control"
                                        accept="image/*">
                                </div>
                                <div class="url-input-wrapper">
                                    <input type="url" name="image_url" id="pkg_image_url" class="form-control"
                                        placeholder="Atau paste URL gambar (https://...)" value="{{ old('image_url') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-grid-2">
                            <div class="form-group mb-4">
                                <label class="form-label">Kontak Khusus (WhatsApp)</label>
                                <input type="text" name="contact_phone" id="pkg_contact" class="form-control"
                                    placeholder="08123456789 (opsional)" value="{{ old('contact_phone') }}">
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label">Tenggat Promo / Label</label>
                                <input type="text" name="promo_until" id="pkg_promo" class="form-control"
                                    placeholder="Contoh: Hemat 2 Juta" value="{{ old('promo_until') }}">
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 4: DETAIL FITUR -->
                    <div class="form-section last">
                        <h3 class="section-title">Fasilitas</h3>
                        <div class="form-group mb-4">
                            <textarea name="features_text" id="pkg_features" class="form-control features-textarea" rows="4"
                                placeholder="Tuliskan fitur paket, pisahkan dengan baris baru (Enter)...&#10;Contoh:&#10;Tiket Pesawat PP&#10;Visa Umroh&#10;Hotel Dekat Masjid"></textarea>
                        </div>

                        <div class="featured-toggle-box">
                            <div class="toggle-control">
                                <input type="checkbox" name="is_featured" id="pkg_is_featured" value="1"
                                    class="featured-checkbox">
                                <label for="pkg_is_featured" class="toggle-label">
                                    <span class="toggle-icon">🌟</span>
                                    <div class="toggle-text">
                                        <span class="label-main">Jadikan Paket Populer</span>
                                        <span class="label-sub">Paket akan ditampilkan sebagai sorotan utama di halaman
                                            depan</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer pkg-modal-footer">
                    <button type="button" onclick="closePackageForm()" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary btn-save-pkg">Simpan Data Paket</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // LIVE SEARCH LOGIC (Mirip Pendaftaran)
            const liveSearchInput = document.getElementById('liveSearchInput');
            const packageRows = document.querySelectorAll('#packagesTable tbody tr');

            if (liveSearchInput) {
                liveSearchInput.addEventListener('input', function () {
                    const query = this.value.toLowerCase().trim();
                    let foundAny = false;

                    packageRows.forEach(row => {
                        const text = row.innerText.toLowerCase();
                        if (text.includes(query)) {
                            row.style.display = '';
                            foundAny = true;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    // Handle empty state if needed
                    const emptyMsg = document.querySelector('.empty-packages');
                    if (!foundAny && !emptyMsg) {
                        // bisa tambahkan row baru temporarily jika mau
                    }
                });
            }

            // Form Overlay Error handling
            @if($errors->any() && old('paket_form_submission'))
                openPackageForm('create');
            @endif

            // Select All Logic
            const selectAllCbs = document.querySelectorAll('.select-all-cb');
            selectAllCbs.forEach(cb => {
                cb.addEventListener('change', function () {
                    const targetClass = this.getAttribute('data-target');
                    const rowCheckboxes = document.querySelectorAll('.' + targetClass);
                    rowCheckboxes.forEach(rowCb => rowCb.checked = this.checked);
                    updateBulkActionsVisibility(targetClass);
                });
            });

            // Individual Checkbox Logic
            document.addEventListener('change', function (e) {
                if (e.target.classList.contains('row-checkbox')) {
                    const classes = e.target.className.split(' ');
                    const targetClass = classes.find(c => c.endsWith('-checkbox'));
                    if (targetClass) updateBulkActionsVisibility(targetClass);
                }
            });

            function updateBulkActionsVisibility(targetClass) {
                const checkedCount = document.querySelectorAll('.' + targetClass + ':checked').length;
                const bar = document.getElementById('bulkActionsBarPackages');
                const countLabel = document.getElementById('selectedCountPackages');

                if (bar && countLabel) {
                    if (checkedCount > 0) {
                        bar.classList.add('active');
                        countLabel.innerText = checkedCount + ' paket dipilih';
                    } else {
                        bar.classList.remove('active');
                        const selectAll = document.querySelector(`.select-all-cb[data-target="${targetClass}"]`);
                        if (selectAll) selectAll.checked = false;
                    }
                }
            }

            window.executeBulkAction = function (module, actionType) {
                const targetClass = 'paket-checkbox';
                const confirmMsg = 'Hapus permanen paket perjalanan yang terpilih?';

                const checkedRows = document.querySelectorAll('.' + targetClass + ':checked');
                if (checkedRows.length === 0) return;
                if (!confirm(confirmMsg)) return;

                const ids = Array.from(checkedRows).map(cb => cb.value);
                checkedRows.forEach(cb => cb.closest('tr').classList.add('opacity-50'));

                fetch("{{ route('admin.packages.bulkAction') }}", {
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
                            alert(data.message); // Tampilkan pesan sukses
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                            checkedRows.forEach(cb => cb.closest('tr').classList.remove('opacity-50'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan koneksi atau server (419/500).');
                        checkedRows.forEach(cb => cb.closest('tr').classList.remove('opacity-50'));
                    });
            };
        });

        function openPackageForm(mode, id = null, data = null) {
            const formOverlay = document.getElementById('paketFormOverlay');
            const formTitle = document.getElementById('paketFormTitle');
            const crudForm = document.getElementById('paketCrudForm');
            const methodInput = document.getElementById('paketMethod');

            // References to inputs
            const inputs = {
                name: document.getElementById('pkg_name'),
                type: document.getElementById('pkg_type'),
                category: document.getElementById('pkg_category'),
                duration: document.getElementById('pkg_duration'),
                hotel: document.getElementById('pkg_hotel'),
                price: document.getElementById('pkg_price'),
                contact: document.getElementById('pkg_contact'),
                promo: document.getElementById('pkg_promo'),
                imageUrl: document.getElementById('pkg_image_url'),
                features: document.getElementById('pkg_features'),
                hotelFeatures: document.getElementById('pkg_hotel_facilities'),
                isFeatured: document.getElementById('pkg_is_featured')
            };

            if (mode === 'create') {
                formTitle.innerText = 'Tambah Paket Baru';
                crudForm.action = "{{ route('admin.packages.store') }}";
                methodInput.value = 'POST';
                crudForm.reset();
            } else if (mode === 'edit' && data) {
                formTitle.innerText = 'Edit Paket: ' + (data.name || '');
                crudForm.action = `/admin/packages/${id}`;
                methodInput.value = 'PUT';

                inputs.name.value = data.name || '';
                inputs.type.value = data.type || 'umrah';
                inputs.category.value = data.category || '';
                inputs.duration.value = data.duration || '';
                inputs.hotel.value = data.hotel || '';
                inputs.price.value = data.price || '';
                inputs.contact.value = data.contact_phone || '';
                inputs.promo.value = data.promo_until || '';
                inputs.imageUrl.value = data.image_url || '';
                inputs.isFeatured.checked = data.is_featured ? true : false;

                if (data.features && Array.isArray(data.features)) {
                    inputs.features.value = data.features.join('\n');
                } else if (data.features) {
                    // handle object or string parsing
                    try {
                        let f = Object.values(data.features);
                        inputs.features.value = f.join('\n');
                    } catch (e) {
                        inputs.features.value = '';
                    }
                } else {
                    inputs.features.value = '';
                }

                if (data.hotel_facilities && Array.isArray(data.hotel_facilities)) {
                    inputs.hotelFeatures.value = data.hotel_facilities.join('\n');
                } else {
                    inputs.hotelFeatures.value = '';
                }
            }

            formOverlay.classList.add('active');
        }

        // Auto Format Rupiah for Price Input
        const priceInput = document.getElementById('pkg_price');
        if (priceInput) {
            priceInput.addEventListener('keyup', function (e) {
                this.value = formatRupiah(this.value, 'Rp ');
            });
        }

        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
        }

        function closePackageForm() {
            document.getElementById('paketFormOverlay').classList.remove('active');
        }
    </script>
@endpush