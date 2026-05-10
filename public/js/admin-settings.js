/**
 * ╔════════════════════════════════════════════════════════════════════╗
 * ║  FILE: public/js/admin-settings.js                                 ║
 * ║  FUNGSI: Semua JavaScript untuk Halaman Pengaturan Admin           ║
 * ║                                                                    ║
 * ║  KONSEP PENTING untuk Pemula:                                      ║
 * ║  File ini dipisah dari Blade agar kode JavaScript bisa dibaca,    ║
 * ║  diedit, dan di-debug dengan editor kode (VS Code, dll)            ║
 * ║  tanpa harus menggulir ribuan baris HTML.                          ║
 * ║                                                                    ║
 * ║  CARA FILE INI DIMUAT:                                             ║
 * ║  Di settings.blade.php dengan: @push('scripts')                   ║
 * ║      <script src="{{ asset('js/admin-settings.js') }}"></script>   ║
 * ║  @endpush                                                          ║
 * ║                                                                    ║
 * ║  DATA DARI BLADE: Dibaca dari window.AdminSettingsConfig           ║
 * ║  (diisi oleh settings.blade.php, dibaca di sini)                  ║
 * ║                                                                    ║
 * ║  FUNGSI-FUNGSI UTAMA:                                              ║
 * ║  - openTab()             → Pindah antar tab                        ║
 * ║  - sendToPreview()       → Kirim pesan ke iframe preview           ║
 * ║  - syncToPreview()       → Scroll preview ke bagian yang relevan   ║
 * ║  - openPackageForm()     → Tampilkan form tambah/edit paket        ║
 * ║  - openFasilitasForm()   → Tampilkan form tambah/edit fasilitas    ║
 * ║  - executeBulkAction()   → Hapus/publish banyak data sekaligus     ║
 * ║  - addRegField()         → Tambah kolom baru di form pendaftaran   ║
 * ╚════════════════════════════════════════════════════════════════════╝
 */

/**
 * IIFE (Immediately Invoked Function Expression)
 * Fungsi yang langsung dijalankan begitu file selesai dimuat browser.
 *
 * MENGAPA IIFE?
 * Semua variabel dan fungsi di dalam () {} hanya hidup di sini (local scope).
 * Tidak akan konflik dengan JS dari library lain atau dari file JS lain.
 *
 * Namun ada pengecualian: fungsi yang perlu dipanggil dari HTML
 * (onclick="openTab(...)") HARUS ditempelkan ke window agar bisa diakses global.
 * Itulah mengapa: window.openTab = function() {...}  (bukan hanya: function openTab() {...})
 */
(function () {
    'use strict'; // Mode ketat — mencegah kesalahan umum JS (pakai variabel tanpa deklarasi, dll)

    /**
     * Baca konfigurasi dari jembatan data yang diisi Blade.
     * window.AdminSettingsConfig diisi di settings.blade.php sebelum file ini dimuat.
     * Fallback ke {} kosong agar tidak error jika config tidak tersedia.
     */
    const cfg = window.AdminSettingsConfig || {};

    // ================================================================
    // TOGGLE PASSWORD — Tombol lihat/sembunyikan password
    // ================================================================
    /**
     * window.togglePassword — dipanggil dari HTML: onclick="togglePassword('admin_password', this)"
     * Mengubah tipe input antara 'password' (disembunyikan) dan 'text' (terlihat).
     *
     * @param {string} inputId - ID elemen input password
     * @param {HTMLElement} btn - Tombol yang diklik (agar bisa ganti ikon)
     */
    window.togglePassword = function (inputId, btn) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type    = 'text';
            btn.innerText = '👓'; // Ikon saat password terlihat
        } else {
            input.type    = 'password';
            btn.innerText = '👁️'; // Ikon saat password disembunyikan
        }
    };

    // ================================================================
    // PERPINDAHAN TAB
    // ================================================================
    /**
     * window.openTab — Dipanggil dari onclick di setiap tombol tab.
     *
     * CARA KERJA:
     * 1. Semua .tab-pane disembunyikan (hapus class 'active')
     * 2. Semua .tab-btn dikembalikan normal (hapus class 'active')
     * 3. Tab yang diklik ditampilkan (tambah class 'active')
     * 4. Preview iframe disesuaikan (regform/branding → iframe daftar, sisanya → iframe home)
     * 5. Scroll preview ke bagian yang relevan dengan tab ini
     *
     * @param {Event|null} evt  - Event klik tombol (null jika dipanggil secara programatik)
     * @param {string} tabName  - ID tab yang ingin dibuka (misal: 'tab-hero')
     */
    window.openTab = function (evt, tabName) {
        if (evt) evt.preventDefault(); // Cegah browser refresh/navigate

        // Tutup overlay yang mungkin terbuka saat pindah tab
        if (typeof closePackageForm  === 'function') closePackageForm();
        if (typeof closeFasilitasForm === 'function') closeFasilitasForm();

        // Sembunyikan SEMUA tab-pane
        document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
        // Reset SEMUA tombol tab ke state normal
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        // Tampilkan tab yang dipilih
        document.getElementById(tabName).classList.add('active');
        // Tandai tombol tab yang diklik sebagai aktif
        if (evt) evt.currentTarget.classList.add('active');

        // Pilih iframe yang sesuai — beberapa tab preview halaman daftar, sisanya halaman home
        const frameHome     = document.getElementById('previewHome');
        const frameRegister = document.getElementById('previewRegister');

        if (tabName === 'tab-regform' || tabName === 'tab-branding') {
            // Tab Form Daftar & Branding → tampilkan iframe halaman /daftar
            frameHome.style.opacity       = '0';
            frameHome.style.pointerEvents = 'none';
            frameRegister.style.opacity       = '1';
            frameRegister.style.pointerEvents = 'auto';
        } else {
            // Tab lainnya → tampilkan iframe halaman utama (/)
            frameRegister.style.opacity       = '0';
            frameRegister.style.pointerEvents = 'none';
            frameHome.style.opacity       = '1';
            frameHome.style.pointerEvents = 'auto';
        }

        /**
         * Mapping tab → ID elemen di halaman preview untuk auto-scroll.
         * Saat tab diganti, preview otomatis scroll ke bagian yang relevan.
         * Nilai (#sync-...) adalah ID elemen di welcome.blade.php atau halaman lain.
         */
        const tabTargets = {
            'tab-hero':             '#sync-hero',
            'tab-stats':            '#sync-stats',
            'tab-sections':         '#sync-fasilitas',
            'tab-paket':            '#sync-paket',
            'tab-kelola-fasilitas': '#sync-fasilitas',
            'tab-about':            '#sync-about-title',
            'tab-jadwal':           '#sync-jadwal',
            'tab-footer':           '#sync-footer',
            'tab-gallery':          '#sync-galeri',
            'tab-testimoni':        '#sync-testimoni',
        };
        if (tabTargets[tabName]) {
            // setTimeout 300ms — beri waktu animasi tab selesai sebelum scroll
            setTimeout(() => syncToPreview(tabTargets[tabName]), 300);
        }
    };

    // ================================================================
    // KOMUNIKASI DENGAN IFRAME PREVIEW
    // ================================================================
    /**
     * window.sendToPreview — Kirim "pesan" ke kedua iframe preview.
     *
     * postMessage() adalah cara aman untuk berkomunikasi antara
     * halaman induk (admin) dan iframe (website yang dimuat di dalamnya).
     * Ini bekerja bahkan jika keduanya di domain yang berbeda.
     *
     * Contoh penggunaan:
     *   sendToPreview({ type: 'SYNC_SCROLL', target: '#sync-hero' })
     *   → iframe menerima pesan ini dan scroll ke elemen #sync-hero
     *
     * @param {object} data - Objek pesan yang dikirim ke iframe
     */
    window.sendToPreview = function (data) {
        ['previewHome', 'previewRegister'].forEach(id => {
            const f = document.getElementById(id);
            if (f && f.contentWindow) f.contentWindow.postMessage(data, '*');
        });
    };

    /**
     * window.syncToPreview — Scroll preview ke elemen tertentu.
     * Hanya kirim ke iframe yang SEDANG AKTIF (terlihat).
     *
     * @param {string} targetId - CSS selector elemen tujuan scroll (misal: '#sync-hero')
     */
    window.syncToPreview = function (targetId) {
        // Cek apakah tab yang aktif adalah regform atau branding
        const tabPaneReg      = document.getElementById('tab-regform');
        const tabPaneBranding = document.getElementById('tab-branding');
        let activeFrame = document.getElementById('previewHome'); // default: iframe home

        // Jika tab regform atau branding aktif, kirim ke iframe daftar
        if ((tabPaneReg      && tabPaneReg.classList.contains('active')) ||
            (tabPaneBranding && tabPaneBranding.classList.contains('active'))) {
            activeFrame = document.getElementById('previewRegister');
        }

        if (activeFrame && activeFrame.contentWindow) {
            try {
                activeFrame.contentWindow.postMessage({ type: 'SYNC_SCROLL', target: targetId }, '*');
            } catch (e) {
                // Jika iframe belum siap (masih loading), coba lagi setelah 0.5 detik
                setTimeout(() => syncToPreview(targetId), 500);
            }
        }
    };

    // ================================================================
    // SUBMIT FORM PENGATURAN (AJAX — tanpa reload halaman penuh)
    // ================================================================
    /**
     * DOMContentLoaded — event ini menunggu seluruh HTML halaman selesai diparsing.
     * Saat dipanggil di awal file (sebelum DOM siap), kita TIDAK BISA querySelector().
     * Dengan event ini, kita pastikan DOM sudah ada sebelum JS dijalankan.
     */
    document.addEventListener('DOMContentLoaded', function () {

        const settingsForm = document.getElementById('settingsForm');
        if (settingsForm) {
            settingsForm.addEventListener('submit', function (e) {
                /**
                 * e.preventDefault() — Hentikan perilaku default form (reload halaman).
                 * Kita akan kirim data secara AJAX (fetch) sehingga halaman tidak reload.
                 * Keuntungan: preview iframe tetap ter-scroll di posisi yang sama.
                 */
                e.preventDefault();
                const btn          = document.getElementById('updateBtn');
                const originalText = btn.innerHTML;
                btn.innerHTML      = '⏳ Proses...'; // Ubah teks tombol jadi loading
                btn.disabled       = true;           // Nonaktifkan tombol agar tidak diklik 2x

                /**
                 * fetch() mengirim data form ke server secara asinkron.
                 * FormData(this) mengumpulkan semua input dalam <form> ini, termasuk file upload.
                 * Header 'X-Requested-With': 'XMLHttpRequest' memberitahu Laravel ini adalah AJAX request.
                 * Header 'Accept': 'application/json' meminta respons dalam format JSON.
                 */
                fetch(this.action, {
                    method: this.method,
                    body:   new FormData(this),
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(r => {
                    if (!r.ok) throw new Error('Server error: ' + r.status); // Lempar error jika HTTP 4xx/5xx
                    return r.json(); // Ubah respons menjadi objek JavaScript
                })
                .then(data => {
                    if (data.success) {
                        // Tunggu 400ms lalu refresh kedua iframe agar tampil data terbaru
                        setTimeout(() => {
                            document.getElementById('previewHome').contentWindow.location.reload();
                            document.getElementById('previewRegister').contentWindow.location.reload();
                        }, 400);

                        // Perbarui nama brand di sidebar secara langsung tanpa reload halaman
                        const newSiteName = document.querySelector('input[name="site_name"]')?.value;
                        if (newSiteName) {
                            const brand = document.querySelector('.sidebar-brand');
                            if (brand) brand.innerText = newSiteName;
                            document.title = 'Admin Dashboard — ' + newSiteName;
                        }
                        btn.innerHTML = '✔ Sukses!';
                        setTimeout(() => { btn.innerHTML = originalText; btn.disabled = false; }, 1500);
                    } else {
                        btn.innerHTML = '❌ Gagal';
                        setTimeout(() => { btn.innerHTML = originalText; btn.disabled = false; }, 2000);
                    }
                })
                .catch(err => {
                    console.error('Settings save error:', err);
                    btn.innerHTML = '❌ Gagal';
                    setTimeout(() => { btn.innerHTML = originalText; btn.disabled = false; }, 2000);
                });
            });
        }

        // ================================================================
        // BULK ACTIONS — Pilih banyak baris sekaligus (checkbox)
        // ================================================================
        /**
         * Tombol "Select All" di header tabel.
         * Saat dicentang: centang semua baris dengan class yang sesuai.
         * data-target="testi-checkbox" menentukan baris mana yang ikut terseleksi.
         */
        document.querySelectorAll('.select-all-cb').forEach(cb => {
            cb.addEventListener('change', function () {
                const cls = this.getAttribute('data-target');
                document.querySelectorAll('.' + cls).forEach(row => row.checked = this.checked);
                _updateBulkBar(cls); // Perbarui tampilan action bar
            });
        });

        /**
         * Event delegation — satu event listener di document yang menangkap
         * klik dari SEMUA checkbox individual (row-checkbox).
         * Lebih efisien daripada memasang listener di setiap checkbox.
         */
        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('row-checkbox')) {
                const cls = [...e.target.classList].find(c => c.endsWith('-checkbox'));
                if (cls) _updateBulkBar(cls);
            }
        });

        /**
         * _updateBulkBar — Tampilkan/sembunyikan bar aksi bulk.
         * Bar muncul (class 'active') hanya jika ada checkbox yang tercentang.
         * Nama dimulai _ (konvensi private — hanya untuk dipakai di dalam IIFE ini).
         *
         * @param {string} targetClass - Class checkbox tabel yang sedang dicek
         */
        function _updateBulkBar(targetClass) {
            const count = document.querySelectorAll('.' + targetClass + ':checked').length;
            // Map: class checkbox → ID elemen bar dan teks hitungan
            const map = {
                'testi-checkbox': { barId: 'bulkActionsBarTestimonials', countId: 'selectedCountTestimonials', suffix: ' baris dipilih' },
                'paket-checkbox': { barId: 'bulkActionsBarPackages',     countId: 'selectedCountPackages',     suffix: ' paket dipilih' },
                'fac-checkbox':   { barId: 'bulkActionsBarFacilities',   countId: 'selectedCountFacilities',   suffix: ' fasilitas dipilih' },
            };
            const entry      = map[targetClass];
            if (!entry) return;
            const bar        = document.getElementById(entry.barId);
            const countLabel = document.getElementById(entry.countId);
            if (!bar || !countLabel) return;
            if (count > 0) {
                bar.classList.add('active');
                countLabel.innerText = count + entry.suffix;
            } else {
                bar.classList.remove('active');
                const selectAll = document.querySelector(`.select-all-cb[data-target="${targetClass}"]`);
                if (selectAll) selectAll.checked = false;
            }
        }

        // ================================================================
        // DATA-SYNC-TARGET — Auto scroll preview saat klik/fokus input
        // ================================================================
        /**
         * Setiap input/textarea yang punya atribut data-sync-target
         * akan otomatis men-scroll preview ke bagian yang relevan saat difokus.
         *
         * Contoh di HTML: <input data-sync-target="#sync-hero">
         * → saat diklik, preview scroll ke elemen dengan id="sync-hero"
         */
        document.querySelectorAll('[data-sync-target]').forEach(input => {
            ['focus', 'click'].forEach(evtType => {
                input.addEventListener(evtType, function () {
                    syncToPreview(this.getAttribute('data-sync-target'));
                });
            });
        });

        // ================================================================
        // PULIHKAN TAB AKTIF dari session server atau localStorage browser
        // ================================================================
        /**
         * cfg.activeTab: tab yang seharusnya aktif (dari session Laravel setelah simpan).
         * localStorage: menyimpan tab terakhir agar saat halaman reload, tab yang sama terbuka.
         * Dipakai setelah operasi bulk (delete paket, dll) yang membutuhkan page reload.
         */
        let targetTab = cfg.activeTab || '';
        const storedTab = localStorage.getItem('admin_active_tab');
        if (!targetTab && storedTab) {
            targetTab = storedTab;
            localStorage.removeItem('admin_active_tab'); // Hapus setelah dipakai
        }
        if (targetTab) {
            const btn = document.querySelector(`.tab-btn[onclick*="${targetTab}"]`);
            if (btn) btn.click(); // Klik tombol tab seperti pengguna yang klik manual
            else openTab(null, targetTab); // Atau buka langsung jika tombol tidak ketemu
        }

        // ================================================================
        // PULIHKAN FORM PAKET jika form sebelumnya gagal validasi server
        // ================================================================
        /**
         * old() di Laravel menyimpan input lama saat form gagal validasi.
         * Kita buka kembali overlay form paket agar pengguna tahu mana yang salah.
         * cfg.oldPaketFormSubmission = true jika form paket sebelumnya dikirim tapi error.
         */
        if (cfg.oldPaketFormSubmission) {
            setTimeout(() => {
                document.getElementById('paketFormOverlay').style.opacity       = '1';
                document.getElementById('paketFormOverlay').style.pointerEvents = 'auto';
                if (cfg.oldPaketMethod === 'PUT') {
                    // Jika sebelumnya edit (bukan tambah baru), ubah judul form
                    document.getElementById('paketFormTitle').innerText = 'Edit Paket Perjalanan';
                }
                // cfg.oldFeatures adalah array fitur yang sudah diisi sebelumnya
                if (cfg.oldFeatures && cfg.oldFeatures.length > 0) {
                    const el = document.getElementById('pkg_features');
                    if (el) el.value = cfg.oldFeatures.join('\n'); // join: gabung array jadi teks baris-per-baris
                }
            }, 200);
        }

        // Hal yang sama untuk form fasilitas
        if (cfg.oldFasilitasFormSubmission) {
            setTimeout(() => {
                document.getElementById('fasilitasFormOverlay').style.opacity       = '1';
                document.getElementById('fasilitasFormOverlay').style.pointerEvents = 'auto';
                if (cfg.oldPaketMethod === 'PUT') {
                    document.getElementById('fasilitasFormTitle').innerText = 'Edit Fasilitas';
                }
            }, 100);
        }
    });

    // ================================================================
    // BULK ACTION EXECUTOR — Eksekusi aksi massal ke server
    // ================================================================
    /**
     * window.executeBulkAction — Dipanggil dari tombol di action bar bulk.
     * Mengirim ID-ID yang tercentang ke server untuk diproses (hapus/publish/dll).
     *
     * @param {string} module      - Modul yang dituju ('testimonials', 'packages', 'facilities')
     * @param {string} actionType  - Jenis aksi ('delete', 'publish', 'unpublish')
     */
    window.executeBulkAction = function (module, actionType) {
        // Map modul → class checkbox & pesan konfirmasi
        const moduleMap = {
            testimonials: { cls: 'testi-checkbox', msgs: { publish: 'Tampilkan testimoni terpilih ke Landing Page?', unpublish: 'Sembunyikan testimoni terpilih dari Landing Page?', delete: 'Hapus permanen testimoni yang terpilih?' } },
            packages:     { cls: 'paket-checkbox', msgs: { delete: 'Hapus permanen paket perjalanan yang terpilih?' } },
            facilities:   { cls: 'fac-checkbox',   msgs: { delete: 'Hapus permanen fasilitas yang terpilih?' } },
        };
        const mod = moduleMap[module];
        if (!mod) return;

        const targetClass = mod.cls;
        const confirmMsg  = mod.msgs[actionType] || 'Yakin?';
        const checkedRows = document.querySelectorAll('.' + targetClass + ':checked');
        if (checkedRows.length === 0) return;
        if (!confirm(confirmMsg)) return; // Tampilkan dialog konfirmasi browser

        // Ubah semua baris terpilih menjadi semi-transparan (tanda sedang diproses)
        const ids = Array.from(checkedRows).map(cb => cb.value);
        checkedRows.forEach(cb => cb.closest('tr').style.opacity = '0.5');

        /**
         * Kirim request POST ke endpoint bulk-action.
         * Body dikirim sebagai JSON (bukan FormData) karena hanya berisi array ID.
         * JSON.stringify() mengubah objek JavaScript menjadi string JSON.
         */
        fetch(`/admin/${module}/bulk-action`, {
            method: 'POST',
            headers: {
                'Content-Type':  'application/json',
                // CSRF token diambil dari meta tag yang sudah dipasang di admin/layout.blade.php
                'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept':        'application/json',
            },
            body: JSON.stringify({ action: actionType, ids }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                if (module === 'testimonials' && (actionType === 'publish' || actionType === 'unpublish')) {
                    // Untuk publish/unpublish testimoni: update UI langsung tanpa reload
                    checkedRows.forEach(cb => {
                        const tr = cb.closest('tr');
                        tr.style.opacity = '1';
                        const statusCol  = tr.querySelector('td:last-child');
                        if (statusCol) {
                            // Template literal untuk buat HTML badge status baru
                            statusCol.innerHTML = actionType === 'publish'
                                ? '<span class="status-badge yes">Live di Web</span>'
                                : '<span class="status-badge no">Disembunyikan</span>';
                        }
                        cb.checked = false;
                    });
                    // Reset select-all dan sembunyikan action bar
                    const selectAll = document.querySelector(`.select-all-cb[data-target="${targetClass}"]`);
                    if (selectAll) selectAll.checked = false;
                    document.getElementById('bulkActionsBarTestimonials').classList.remove('active');
                    // Refresh iframe agar tampilan testimoni di landing page update
                    const frame = document.getElementById('previewHome');
                    frame.contentWindow.location.reload();
                    frame.onload = () => setTimeout(() => syncToPreview('#sync-testimoni'), 500);
                } else {
                    // Untuk operasi hapus: reload halaman agar hitungan/pagination akurat
                    const tabMap = { testimonials: 'tab-testimoni', packages: 'tab-paket', facilities: 'tab-kelola-fasilitas' };
                    const tabKey = tabMap[module];
                    // Simpan tab aktif ke localStorage agar setelah reload, tab yang sama terbuka
                    if (tabKey) localStorage.setItem('admin_active_tab', tabKey);
                    location.reload();
                }
            } else {
                alert('Error: ' + data.message);
                checkedRows.forEach(cb => cb.closest('tr').style.opacity = '1'); // Kembalikan opacity
            }
        })
        .catch(err => {
            console.error('Bulk action error:', err);
            alert('Terjadi kesalahan koneksi.');
            checkedRows.forEach(cb => cb.closest('tr').style.opacity = '1');
        });
    };

    // ================================================================
    // OVERLAY FORM PAKET
    // ================================================================
    /**
     * window.openPackageForm — Tampilkan form tambah/edit paket di panel kanan.
     *
     * Overlay menggunakan opacity + pointer-events untuk show/hide.
     * MENGAPA TIDAK display:none/block?
     * Karena CSS transition (animasi fade) tidak bekerja pada display.
     * opacity: 0 → 1 bisa dianimasikan dengan CSS transition.
     *
     * @param {string} mode       - 'create' (tambah baru) atau 'edit' (ubah existing)
     * @param {string|null} id    - ID paket yang diedit (null jika create)
     * @param {object|null} data  - Data paket yang diedit (dari onclick di tabel)
     */
    window.openPackageForm = function (mode, id = null, data = null) {
        const overlay     = document.getElementById('paketFormOverlay');
        const formTitle   = document.getElementById('paketFormTitle');
        const crudForm    = document.getElementById('paketCrudForm');
        const methodInput = document.getElementById('paketMethod');

        // Kumpulkan semua referensi input dalam satu objek agar mudah diakses
        const inputs = {
            name:          document.getElementById('pkg_name'),
            type:          document.getElementById('pkg_type'),
            category:      document.getElementById('pkg_category'),
            duration:      document.getElementById('pkg_duration'),
            price:         document.getElementById('pkg_price'),
            image_url:     document.getElementById('pkg_image_url'),
            contact_phone: document.getElementById('pkg_contact'),
            promo_until:   document.getElementById('pkg_promo'),
            features:      document.getElementById('pkg_features'),
            hotel:         document.getElementById('pkg_hotel'),
            is_featured:   document.getElementById('pkg_is_featured'),
        };

        if (mode === 'create') {
            formTitle.innerText = 'Tambah Paket Baru';
            crudForm.action     = cfg.packagesStoreUrl; // URL dari window.AdminSettingsConfig
            methodInput.value   = 'POST';
            // Bersihkan semua input (reset form)
            Object.values(inputs).forEach(inp => {
                if (!inp) return;
                if (inp.type === 'checkbox') inp.checked = false;
                else if (inp.tagName === 'SELECT') inp.selectedIndex = 0; // Pilih opsi pertama
                else inp.value = '';
            });
        } else if (mode === 'edit' && data) {
            formTitle.innerText         = 'Edit Paket Perjalanan';
            crudForm.action             = `/admin/packages/${id}`;
            methodInput.value           = 'PUT'; // Laravel menerima PUT/PATCH via input tersembunyi _method
            // Isi input dengan data paket yang akan diedit
            inputs.name.value           = data.name          || '';
            inputs.type.value           = data.type          || 'umrah';
            inputs.category.value       = data.category      || 'Umroh Reguler';
            inputs.duration.value       = data.duration      || '';
            inputs.price.value          = data.price         || '';
            inputs.image_url.value      = data.image_url     || '';
            inputs.contact_phone.value  = data.contact_phone || '';
            inputs.promo_until.value    = data.promo_until   || '';
            inputs.hotel.value          = data.hotel         || '';
            inputs.is_featured.checked  = !!data.is_featured; // !! mengubah nilai ke boolean (true/false)
            // data.features bisa berupa object PHP yang dikonversi JSON → ambil values-nya saja
            inputs.features.value       = data.features && typeof data.features === 'object'
                ? Object.values(data.features).join('\n')
                : '';
        }

        // Tampilkan overlay dengan animasi fade-in
        overlay.style.opacity       = '1';
        overlay.style.pointerEvents = 'auto'; // Aktifkan kembali klik pada overlay
    };

    /**
     * window.closePackageForm — Sembunyikan overlay form paket.
     */
    window.closePackageForm = function () {
        const overlay = document.getElementById('paketFormOverlay');
        overlay.style.opacity       = '0';
        overlay.style.pointerEvents = 'none'; // Cegah klik pada elemen yang tidak terlihat
    };

    // ================================================================
    // OVERLAY FORM FASILITAS
    // ================================================================
    /**
     * window.openFasilitasForm — Logika sama dengan openPackageForm,
     * tapi untuk form fasilitas (hotel, amenitas, dll).
     */
    window.openFasilitasForm = function (mode, id = null, data = null) {
        const overlay     = document.getElementById('fasilitasFormOverlay');
        const formTitle   = document.getElementById('fasilitasFormTitle');
        const crudForm    = document.getElementById('fasilitasCrudForm');
        const methodInput = document.getElementById('fasilitasMethodInput');
        const inputs = {
            title:       document.getElementById('fac_title'),
            icon:        document.getElementById('fac_icon'),
            hotel:       document.getElementById('fac_hotel'),
            image_url:   document.getElementById('fac_image_url'),
            description: document.getElementById('fac_description'),
        };

        if (mode === 'create') {
            formTitle.innerText      = 'Tambah Fasilitas Baru';
            crudForm.action          = cfg.facilitiesStoreUrl;
            methodInput.value        = 'POST';
            inputs.title.value       = '';
            inputs.icon.value        = '🏨'; // Nilai default emoji hotel
            inputs.hotel.value       = '';
            inputs.image_url.value   = '';
            inputs.description.value = '';
        } else if (mode === 'edit' && data) {
            formTitle.innerText      = 'Edit Fasilitas';
            crudForm.action          = `/admin/facilities/${id}`;
            methodInput.value        = 'PUT';
            inputs.title.value       = data.title       || '';
            inputs.icon.value        = data.icon        || '🏨';
            inputs.hotel.value       = data.hotel       || '';
            inputs.image_url.value   = data.image_url   || '';
            inputs.description.value = data.description || '';
        }

        overlay.style.opacity       = '1';
        overlay.style.pointerEvents = 'auto';
    };

    window.closeFasilitasForm = function () {
        const overlay = document.getElementById('fasilitasFormOverlay');
        overlay.style.opacity       = '0';
        overlay.style.pointerEvents = 'none';
    };

    // ================================================================
    // FORM PENDAFTARAN — Helper untuk builder kolom dinamis
    // ================================================================
    /**
     * window.toggleOpt — Tampilkan/sembunyikan grup input "pilihan jawaban"
     * hanya jika tipe kolom dipilih adalah 'select' (dropdown).
     *
     * @param {HTMLSelectElement} sel - Elemen <select> tipe input yang berubah
     */
    window.toggleOpt = function (sel) {
        const row      = sel.closest('.reg-field-row'); // Cari div induk terdekat
        const optGroup = row.querySelector('.opt-group'); // Cari grup pilihan di dalamnya
        optGroup.style.display = (sel.value === 'select') ? 'block' : 'none';
    };

    /**
     * window.addRegField — Tambahkan satu baris kolom baru ke form pendaftaran.
     *
     * Template literal (string backtick `) memungkinkan tulis HTML multi-baris
     * langsung di dalam JavaScript dengan variabel ${index} di dalamnya.
     * insertAdjacentHTML('beforeend', html) menambahkan HTML baru di akhir container.
     *
     * MENGAPA index? Agar nama input adalah:
     * registration_fields[0][label], registration_fields[1][label], dst.
     * Laravel menerima ini sebagai array di Controller.
     */
    window.addRegField = function () {
        const container = document.getElementById('reg-fields-container');
        if (!container) return;
        const index = container.querySelectorAll('.reg-field-row').length; // Hitung baris yang sudah ada
        container.insertAdjacentHTML('beforeend', `
            <div class="reg-field-row card" style="padding:15px; background:#f8fafc; margin-bottom:12px; border:1px solid #e2e8f0; position:relative;">
                <div style="display:grid; grid-template-columns:2fr 1fr 0.6fr auto; gap:12px; align-items:flex-end;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" style="font-size:0.75rem;">Label Kolom</label>
                        <input type="text" name="registration_fields[${index}][label]" class="form-control" required>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" style="font-size:0.75rem;">Tipe Input</label>
                        <select name="registration_fields[${index}][type]" class="form-control" onchange="toggleOpt(this)">
                            <option value="text">Teks</option>
                            <option value="number">Angka</option>
                            <option value="tel">Telepon</option>
                            <option value="date">Tanggal</option>
                            <option value="email">Email</option>
                            <option value="textarea">Paragraf</option>
                            <option value="file">Upload File/Foto</option>
                            <option value="select">Pilihan (Dropdown)</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0; text-align:center;">
                        <label class="form-label" style="font-size:0.75rem;">Wajib?</label>
                        <div style="display:flex; justify-content:center; padding:8px 0;">
                            <input type="checkbox" name="registration_fields[${index}][required]" value="1" style="width:18px; height:18px;">
                        </div>
                    </div>
                    <button type="button" onclick="this.parentElement.parentElement.remove()"
                        class="btn-primary" style="background:#ef4444; padding:8px 10px; height:38px; min-width:38px;">🗑</button>
                </div>
                <div class="opt-group" style="margin-top:12px; border-top:1px dashed #e2e8f0; padding-top:12px; display:none;">
                    <label class="form-label" style="font-size:0.75rem; color:var(--primary); font-weight:700;">⚙️ Pilihan Jawaban (Pisahkan dengan koma)</label>
                    <input type="text" name="registration_fields[${index}][options]" class="form-control" placeholder="Contoh: Sudah Kawin, Belum Kawin, Cerai">
                    <span style="font-size:0.7rem; color:var(--text-muted); margin-top:4px; display:block;">Masukkan semua pilihan yang ingin ditampilkan di dropdown.</span>
                </div>
            </div>
        `);
    };

})(); // Akhir IIFE — tanda kurung ini yang menjalankan fungsi secara langsung
