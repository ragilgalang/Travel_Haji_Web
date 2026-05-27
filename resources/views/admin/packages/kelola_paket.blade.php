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
                <div class="error-alert-box-dark">
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
                    <!-- SECTION 1: INFORMASI PAKET -->
                    <div class="form-section">
                        <div class="form-section-header-badge">
                            📄 INFORMASI PAKET
                        </div>
                        
                        <div class="form-group mb-4">
                            <label class="form-label-gold">NAMA PAKET</label>
                            <input type="text" name="name" id="pkg_name" class="form-control-dark" required
                                placeholder="cth. Umrah Premium Awal Musim 1447H" value="{{ old('name') }}">
                        </div>

                        <div class="form-grid-2-custom">
                            <div class="form-group">
                                <label class="form-label-gold">JENIS PERJALANAN</label>
                                <select name="type" id="pkg_type" class="form-control-dark">
                                    <option value="" disabled selected>Pilih jenis...</option>
                                    <option value="umrah" {{ old('type') == 'umrah' ? 'selected' : '' }}>Umrah</option>
                                    <option value="haji" {{ old('type') == 'haji' ? 'selected' : '' }}>Haji</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label-gold">DURASI (HARI)</label>
                                <input type="text" name="duration" id="pkg_duration" class="form-control-dark" required
                                    placeholder="cth. 12" value="{{ old('duration') }}">
                            </div>
                        </div>

                        <div class="form-grid-2-custom">
                            <div class="form-group">
                                <label class="form-label-gold">KAPASITAS JEMAAH</label>
                                <input type="text" name="quota" id="pkg_quota" class="form-control-dark"
                                    placeholder="cth. 50" value="{{ old('quota') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label-gold">TIPE KAMAR</label>
                                <select name="room_type" id="pkg_room_type" class="form-control-dark">
                                    <option value="" disabled selected>Pilih tipe...</option>
                                    <option value="Quad (4 Orang)" {{ old('room_type') == 'Quad (4 Orang)' ? 'selected' : '' }}>Quad (4 Orang)</option>
                                    <option value="Triple (3 Orang)" {{ old('room_type') == 'Triple (3 Orang)' ? 'selected' : '' }}>Triple (3 Orang)</option>
                                    <option value="Double (2 Orang)" {{ old('room_type') == 'Double (2 Orang)' ? 'selected' : '' }}>Double (2 Orang)</option>
                                    <option value="Quad/Triple/Double" {{ old('room_type') == 'Quad/Triple/Double' ? 'selected' : '' }}>Quad/Triple/Double (Semua Tipe)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-grid-2-custom">
                            <div class="form-group">
                                <label class="form-label-gold">NAMA HOTEL</label>
                                <input type="text" name="hotel" id="pkg_hotel" class="form-control-dark"
                                    placeholder="cth. Hotel Hilton / Snood Ajyad" value="{{ old('hotel') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label-gold">HARGA (TAMPILAN)</label>
                                <input type="text" name="price" id="pkg_price" class="form-control-dark highlight-input-gold" required
                                    placeholder="Rp 35.000.000" value="{{ old('price') }}">
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: RUTE PENERBANGAN -->
                    <div class="form-section">
                        <div class="form-section-header-badge">
                            ✈ RUTE PENERBANGAN
                        </div>

                        <div class="form-grid-2-custom">
                            <div class="form-group">
                                <label class="form-label-gold">BANDARA ASAL</label>
                                <select name="airport_origin" id="pkg_airport_origin" class="form-control-dark">
                                    <option value="" disabled selected>Pilih bandara...</option>
                                    <option value="Surabaya (SUB)" {{ old('airport_origin') == 'Surabaya (SUB)' ? 'selected' : '' }}>Surabaya (SUB)</option>
                                    <option value="Jakarta (CGK)" {{ old('airport_origin') == 'Jakarta (CGK)' ? 'selected' : '' }}>Jakarta (CGK)</option>
                                    <option value="Solo (SOC)" {{ old('airport_origin') == 'Solo (SOC)' ? 'selected' : '' }}>Solo (SOC)</option>
                                    <option value="Medan (KNO)" {{ old('airport_origin') == 'Medan (KNO)' ? 'selected' : '' }}>Medan (KNO)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label-gold">BANDARA TUJUAN</label>
                                <select name="airport_destination" id="pkg_airport_destination" class="form-control-dark">
                                    <option value="" disabled selected>Pilih bandara...</option>
                                    <option value="Jeddah (JED)" {{ old('airport_destination') == 'Jeddah (JED)' ? 'selected' : '' }}>Jeddah (JED)</option>
                                    <option value="Madinah (MED)" {{ old('airport_destination') == 'Madinah (MED)' ? 'selected' : '' }}>Madinah (MED)</option>
                                    <option value="Riyadh (RUH)" {{ old('airport_destination') == 'Riyadh (RUH)' ? 'selected' : '' }}>Riyadh (RUH)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-grid-2-custom">
                            <div class="form-group">
                                <label class="form-label-gold">MASKAPAI</label>
                                <select name="airline" id="pkg_airline" class="form-control-dark">
                                    <option value="" disabled selected>Pilih maskapai...</option>
                                    <option value="Saudia Airlines" {{ old('airline') == 'Saudia Airlines' ? 'selected' : '' }}>Saudia Airlines</option>
                                    <option value="Garuda Indonesia" {{ old('airline') == 'Garuda Indonesia' ? 'selected' : '' }}>Garuda Indonesia</option>
                                    <option value="Lion Air" {{ old('airline') == 'Lion Air' ? 'selected' : '' }}>Lion Air</option>
                                    <option value="Batik Air" {{ old('airline') == 'Batik Air' ? 'selected' : '' }}>Batik Air</option>
                                    <option value="Qatar Airways" {{ old('airline') == 'Qatar Airways' ? 'selected' : '' }}>Qatar Airways</option>
                                    <option value="Emirates" {{ old('airline') == 'Emirates' ? 'selected' : '' }}>Emirates</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label-gold">NOMOR PENERBANGAN</label>
                                <input type="text" name="flight_number" id="pkg_flight_number" class="form-control-dark"
                                    placeholder="cth. GA-981" value="{{ old('flight_number') }}">
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3: TANGGAL & WAKTU -->
                    <div class="form-section">
                        <div class="form-section-header-badge">
                            ⏰ TANGGAL & WAKTU
                        </div>

                        <div class="form-grid-2-custom">
                            <div class="form-group">
                                <label class="form-label-gold">TANGGAL BERANGKAT</label>
                                <input type="date" name="departure_date" id="pkg_departure_date" class="form-control-dark"
                                    value="{{ old('departure_date') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label-gold">JAM BERANGKAT (WIB)</label>
                                <input type="time" name="departure_time" id="pkg_departure_time" class="form-control-dark"
                                    value="{{ old('departure_time') }}">
                            </div>
                        </div>

                        <div class="form-grid-2-custom">
                            <div class="form-group">
                                <label class="form-label-gold">TANGGAL PULANG</label>
                                <input type="date" name="return_date" id="pkg_return_date" class="form-control-dark"
                                    value="{{ old('return_date') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label-gold">JAM PULANG (WIB)</label>
                                <input type="time" name="return_time" id="pkg_return_time" class="form-control-dark"
                                    value="{{ old('return_time') }}">
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label-gold">STATUS JADWAL</label>
                            <select name="status" id="pkg_status" class="form-control-dark">
                                <option value="Aktif — Pendaftaran dibuka" {{ old('status') == 'Aktif — Pendaftaran dibuka' ? 'selected' : '' }}>Aktif — Pendaftaran dibuka</option>
                                <option value="Tidak Aktif — Pendaftaran ditutup" {{ old('status') == 'Tidak Aktif — Pendaftaran ditutup' ? 'selected' : '' }}>Tidak Aktif — Pendaftaran ditutup</option>
                                </select>
                        </div>
                    </div>

                    <!-- SECTION 4: DETAIL TAMBAHAN (MEDIA & FASILITAS) -->
                    <div class="form-section last">
                        <div class="form-section-header-badge">
                            ✨ DETAIL TAMBAHAN
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label-gold">KATEGORI PAKET</label>
                            <select name="category" id="pkg_category" class="form-control-dark">
                                <option value="Umroh Ekonomis" {{ old('category') == 'Umroh Ekonomis' ? 'selected' : '' }}>Umroh Ekonomis</option>
                                <option value="Umroh Reguler" {{ old('category') == 'Umroh Reguler' ? 'selected' : '' }}>Umroh Reguler</option>
                                <option value="Umroh Bisnis" {{ old('category') == 'Umroh Bisnis' ? 'selected' : '' }}>Umroh Bisnis</option>
                                <option value="Umroh VIP" {{ old('category') == 'Umroh VIP' ? 'selected' : '' }}>Umroh VIP</option>
                                <option value="Haji Furoda" {{ old('category') == 'Haji Furoda' ? 'selected' : '' }}>Haji Furoda</option>
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label-gold">Gambar Cover</label>
                            <div class="media-input-group-dark">
                                <div class="file-input-wrapper-dark">
                                    <input type="file" name="image" id="pkg_image_file" class="form-control-dark"
                                        accept="image/*">
                                </div>
                                <div class="url-input-wrapper">
                                    <input type="url" name="image_url" id="pkg_image_url" class="form-control-dark"
                                        placeholder="Atau paste URL gambar (https://...)" value="{{ old('image_url') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-grid-2-custom">
                            <div class="form-group">
                                <label class="form-label-gold">KONTAK KHUSUS (WHATSAPP)</label>
                                <input type="text" name="contact_phone" id="pkg_contact" class="form-control-dark"
                                    placeholder="08123456789 (opsional)" value="{{ old('contact_phone') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label-gold">TENGGAT PROMO / LABEL</label>
                                <input type="text" name="promo_until" id="pkg_promo" class="form-control-dark"
                                    placeholder="Contoh: Hemat 2 Juta" value="{{ old('promo_until') }}">
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label-gold">FASILITAS HOTEL (PISAHKAN TIAP BARIS)</label>
                            <textarea name="hotel_facilities_text" id="pkg_hotel_facilities" class="form-control-dark features-textarea-dark" rows="3" placeholder="Contoh:&#10;WiFi Gratis&#10;Dekat Masjidil Haram&#10;Sarapan Buffet"></textarea>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label-gold">FASILITAS UMUM / FITUR UTAMA (PISAHKAN TIAP BARIS)</label>
                            <textarea name="features_text" id="pkg_features" class="form-control-dark features-textarea-dark" rows="4"
                                placeholder="Tuliskan fitur paket, pisahkan dengan baris baru (Enter)...&#10;Contoh:&#10;Tiket Pesawat PP&#10;Visa Umroh&#10;Hotel Dekat Masjid"></textarea>
                        </div>

                        <div class="featured-toggle-box-dark">
                            <div class="toggle-control">
                                <input type="checkbox" name="is_featured" id="pkg_is_featured" value="1"
                                    class="featured-checkbox">
                                <label for="pkg_is_featured" class="toggle-label">
                                    <span class="toggle-icon">🌟</span>
                                    <div class="toggle-text">
                                        <span class="label-main-gold">Jadikan Paket Populer</span>
                                        <span class="label-sub-gold">Paket akan ditampilkan sebagai sorotan utama di halaman depan</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer pkg-modal-footer">
                    <button type="button" onclick="closePackageForm()" class="btn-secondary-dark">Batal</button>
                    <button type="submit" class="btn-primary btn-save-pkg-gold">Simpan Data Paket</button>
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

            // Auto open create modal if redirected from dashboard
            @if(session('open_create_modal'))
                openPackageForm('create');
            @endif

            // Auto open edit modal if redirected
            @if(session('open_edit_modal') && session('edit_id') && session('edit_data'))
                openPackageForm('edit', '{{ session('edit_id') }}', {!! json_encode(session('edit_data')) !!});
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
                isFeatured: document.getElementById('pkg_is_featured'),
                
                // Screenshot fields
                quota: document.getElementById('pkg_quota'),
                roomType: document.getElementById('pkg_room_type'),
                airportOrigin: document.getElementById('pkg_airport_origin'),
                airportDestination: document.getElementById('pkg_airport_destination'),
                airline: document.getElementById('pkg_airline'),
                flightNumber: document.getElementById('pkg_flight_number'),
                departureDate: document.getElementById('pkg_departure_date'),
                departureTime: document.getElementById('pkg_departure_time'),
                returnDate: document.getElementById('pkg_return_date'),
                returnTime: document.getElementById('pkg_return_time'),
                status: document.getElementById('pkg_status')
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
                inputs.category.value = data.category || 'Umroh Reguler';
                inputs.duration.value = data.duration || '';
                inputs.hotel.value = data.hotel || '';
                inputs.price.value = data.price || '';
                inputs.contact.value = data.contact_phone || '';
                inputs.promo.value = data.promo_until || '';
                inputs.imageUrl.value = data.image_url || '';
                inputs.isFeatured.checked = data.is_featured ? true : false;

                // Screenshot fields
                inputs.quota.value = data.quota || '';
                inputs.roomType.value = data.room_type || '';
                inputs.airportOrigin.value = data.airport_origin || '';
                inputs.airportDestination.value = data.airport_destination || '';
                inputs.airline.value = data.airline || '';
                inputs.flightNumber.value = data.flight_number || '';
                inputs.departureDate.value = data.departure_date || '';
                inputs.departureTime.value = data.departure_time || '';
                inputs.returnDate.value = data.return_date || '';
                inputs.returnTime.value = data.return_time || '';
                inputs.status.value = data.status || 'Aktif — Pendaftaran dibuka';

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