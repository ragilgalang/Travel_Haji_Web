<!-- TAB: SECTION HEADERS -->
<div id="tab-sections" class="tab-pane card">
    <h2 class="card-title mb-4">Judul Bagian (Section Titles)</h2>
    <p class="mb-4 text-sm text-muted">Ubah teks pembuka untuk masing-masing bagian
        (Paket, Galeri, Fasilitas, dll).</p>

    <div class="grid-gap-1-5">
        <div class="form-group section-config-card">
            <label class="section-config-label">Bagian
                Paket Perjalanan</label>
            <div class="stats-grid-2">
                <input type="text" name="sec_pkg_eye"
                    value="{{ $settings['sec_pkg_eye'] ?? 'Pilih Perjalanan Anda' }}" class="form-control"
                    placeholder="Teks Kecil Atas" data-sync-target="#paket">
                <input type="text" name="sec_pkg_title"
                    value="{{ $settings['sec_pkg_title'] ?? 'Paket <em>Haji & Umrah</em>' }}"
                    class="form-control" placeholder="Judul Besar" data-sync-target="#paket">
            </div>
        </div>

        <div class="form-group section-config-card">
            <label class="section-config-label">Bagian
                Itinerary (Jadwal) & Galeri</label>
            <div class="stats-grid-2">
                <input type="text" name="sec_itin_eye"
                    value="{{ $settings['sec_itin_eye'] ?? 'Jadwal Perjalanan' }}" class="form-control"
                    placeholder="Teks Kecil" data-sync-target="#sync-jadwal">
                <input type="text" name="sec_gal_eye"
                    value="{{ $settings['sec_gal_eye'] ?? 'Galeri Perjalanan' }}" class="form-control"
                    placeholder="Teks Kecil" data-sync-target="#sync-galeri">
            </div>
            <div class="stats-grid-2 mt-2">
                <input type="text" name="sec_itin_title"
                    value="{{ $settings['sec_itin_title'] ?? 'Alur <em>Ibadah Haji</em>' }}"
                    class="form-control" placeholder="Judul Jadwal" data-sync-target="#sync-jadwal">
                <input type="text" name="sec_gal_title"
                    value="{{ $settings['sec_gal_title'] ?? 'Momen <em>Berkesan</em> Jemaah Kami' }}"
                    class="form-control" placeholder="Judul Galeri" data-sync-target="#sync-galeri">
            </div>
        </div>

        <div class="form-group section-config-card">
            <label class="section-config-label">Bagian
                Fasilitas & Testimoni</label>
            <div class="stats-grid-2">
                <input type="text" name="sec_fac_title"
                    value="{{ $settings['sec_fac_title'] ?? 'Yang Anda <em>Dapatkan</em>' }}"
                    class="form-control" placeholder="Judul Fasilitas" data-sync-target="#sync-fasilitas">
                <input type="text" name="sec_testi_title"
                    value="{{ $settings['sec_testi_title'] ?? 'Kata <em>Jemaah Kami</em>' }}"
                    class="form-control" placeholder="Judul Testimoni" data-sync-target="#sync-testimoni">
            </div>
        </div>

        <div class="form-card-group">
            <div class="form-card-title">
                <span>📣 Bagian Bottom CTA (Hubungi Kami)</span>
            </div>

            <div class="grid-2">
                <div class="admin-input-group">
                    <label class="admin-label">Teks Eyebrow (Kecil Atas)</label>
                    <input type="text" name="sec_cta_eye"
                        value="{{ $settings['sec_cta_eye'] ?? 'Mulai Perjalanan Suci' }}"
                        class="form-control" data-sync-target="#sync-kontak">
                </div>
                <div class="admin-input-group">
                    <label class="admin-label">Judul Besar CTA</label>
                    <input type="text" name="sec_cta_title"
                        value="{{ $settings['sec_cta_title'] ?? 'Siap Berangkat ke <em>Tanah Suci?</em>' }}"
                        class="form-control" data-sync-target="#sync-cta-title">
                </div>
            </div>

            <div class="section-cta-inner-card">
                <p class="text-sm text-gray-500 italic">Pengaturan nomor WhatsApp, Telepon, dan Email dapat Anda kelola di tab <b>Umum</b> untuk menghindari data ganda.</p>
            </div>

            <div class="admin-input-group mt-4">
                <label class="admin-label">Teks Tombol Hubungi</label>
                <input type="text" name="sec_cta_btn_text"
                    value="{{ $settings['sec_cta_btn_text'] ?? 'Hubungi Saya' }}" class="form-control"
                    placeholder="Contoh: Chat Admin" data-sync-target="#sync-cta-title">
            </div>
        </div>
    </div>
</div>
