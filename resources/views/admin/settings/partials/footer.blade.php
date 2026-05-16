<!-- TAB: FOOTER -->
<div id="tab-footer" class="tab-pane card">
    <h2 class="card-title mb-4">Pengaturan Footer (Menu Bawah)</h2>
    <p class="mb-4 text-sm text-muted">Atur judul kolom dan daftar tautan di bagian
        paling bawah website Anda. Pisahkan setiap tautan dengan <b>baris baru (Enter)</b>.</p>

    <div class="form-group mb-4 footer-section-card">
        <label class="form-label">🖼️ Logo Footer</label>
        <div class="logo-upload-container">
            @if(!empty($settings['footer_logo']))
                <div class="logo-preview-bg" style="margin-bottom:12px; position:relative; background:#0b1a10 !important;">
                    <img src="{{ $settings['footer_logo'] }}" alt="Footer Logo" class="logo-preview-img">
                    <label class="logo-delete-btn" title="Hapus Logo Footer">
                        <input type="checkbox" name="delete_footer_logo" value="1" style="display:none">
                        <span onclick="if(confirm('Hapus logo footer?')){ this.previousElementSibling.checked=true; document.getElementById('updateBtn').click(); }">🗑️ Hapus Logo</span>
                    </label>
                </div>
            @endif
            <input type="file" name="footer_logo" class="form-control" accept="image/*">
            <span class="help-block">Disarankan logo PNG transparan. Tampil di kolom kiri footer.</span>
        </div>
    </div>

    <div class="form-group mb-4 footer-section-light">
        <label class="form-label text-sm font-semibold mb-3 block text-primary">📍 Lokasi & Kontak
            Kantor</label>
        <div class="form-group mb-3">
            <label class="form-label text-xs font-semibold mb-1 block">Alamat Lengkap</label>
            <input type="text" name="office_address" value="{{ $settings['office_address'] ?? '' }}"
                class="form-control" placeholder="Jl. Contoh Raya No. 1, Kec. Sidoarjo, Jawa Timur"
                data-sync-target="#sync-footer-desc">
        </div>
        <div class="form-group mb-3">
            <label class="form-label text-xs font-semibold mb-1 block">Link Google Maps <span
                    class="text-muted font-normal">(opsional)</span></label>
            <input type="url" name="office_map_url" value="{{ $settings['office_map_url'] ?? '' }}"
                class="form-control" placeholder="https://maps.google.com/?q=..."
                data-sync-target="#sync-footer-desc">
            <span class="help-block">Salin URL dari Google Maps. Alamat kantor akan menjadi tautan yang bisa
                diklik pengunjung.</span>
        </div>
        <div class="stats-grid-2">
            <div class="form-group">
                <label class="form-label text-xs font-semibold mb-1 block">Nomor Telepon</label>
                <input type="text" name="office_phone" value="{{ $settings['office_phone'] ?? '' }}"
                    class="form-control text-sm" placeholder="031-XXXXXXX"
                    data-sync-target="#sync-footer-desc">
            </div>
            <div class="form-group">
                <label class="form-label text-xs font-semibold mb-1 block">Email Kantor</label>
                <input type="text" name="office_email" value="{{ $settings['office_email'] ?? '' }}"
                    class="form-control text-sm" placeholder="info@perusahaan.com"
                    data-sync-target="#sync-footer-desc">
            </div>
        </div>
        <span class="help-block mt-2">Informasi ini akan muncul di kolom kiri footer website menggantikan
            teks deskripsi.</span>
    </div>

    <div class="footer-section-card">
        <h3 class="footer-card-title">Tautan Media Sosial (Icon Footer)
        </h3>
        <div class="stats-grid-2">
            <div class="form-group">
                <label class="form-label text-xs font-semibold mb-1 block">Instagram URL</label>
                <input type="text" name="site_instagram" value="{{ $settings['site_instagram'] ?? '' }}"
                    class="form-control text-sm" placeholder="https://instagram.com/akunanda"
                    data-sync-target="#sync-footer-social">
            </div>
            <div class="form-group">
                <label class="form-label text-xs font-semibold mb-1 block">Facebook URL</label>
                <input type="text" name="site_facebook" value="{{ $settings['site_facebook'] ?? '' }}"
                    class="form-control text-sm" placeholder="https://facebook.com/halamananda"
                    data-sync-target="#sync-footer-social">
            </div>
            <div class="form-group">
                <label class="form-label text-xs font-semibold mb-1 block">YouTube URL</label>
                <input type="text" name="site_youtube" value="{{ $settings['site_youtube'] ?? '' }}"
                    class="form-control text-sm" placeholder="https://youtube.com/c/channelanda"
                    data-sync-target="#sync-footer-social">
            </div>
            <div class="form-group">
                <label class="form-label text-xs font-semibold mb-1 block">X (Twitter) URL</label>
                <input type="text" name="site_twitter" value="{{ $settings['site_twitter'] ?? '' }}"
                    class="form-control text-sm" placeholder="https://x.com/akunanda"
                    data-sync-target="#sync-footer-social">
            </div>
            <div class="form-group">
                <label class="form-label text-xs font-semibold mb-1 block">TikTok URL</label>
                <input type="text" name="site_tiktok" value="{{ $settings['site_tiktok'] ?? '' }}"
                    class="form-control text-sm" placeholder="https://tiktok.com/@akunanda"
                    data-sync-target="#sync-footer-social">
            </div>
        </div>
        <span class="help-block mt-3">Kosongkan kolom jika tidak ingin menampilkan ikon media sosial
            tersebut.</span>
    </div>

    <div class="stats-grid-2">
        <!-- Kolom 1 -->
        <div class="form-group footer-section-light">
            <label class="form-label text-sm font-semibold mb-2 block text-primary">Kolom 1</label>
            <input type="text" name="footer_col1_title"
                value="{{ $settings['footer_col1_title'] ?? 'Layanan' }}" class="form-control mb-2"
                placeholder="Judul Kolom" data-sync-target="#sync-footer-col1">
            <textarea name="footer_col1_links" class="form-control" rows="5"
                placeholder="Haji Reguler&#10;Haji Plus&#10;..."
                data-sync-target="#sync-footer-col1">{{ $settings['footer_col1_links'] ?? "Haji Reguler\nHaji Plus\nUmrah Reguler\nUmrah Ramadhan\nUmrah Keluarga" }}</textarea>
        </div>

        <!-- Kolom 2 -->
        <div class="form-group footer-section-light">
            <label class="form-label text-sm font-semibold mb-2 block text-primary">Kolom 2</label>
            <input type="text" name="footer_col2_title"
                value="{{ $settings['footer_col2_title'] ?? 'Informasi' }}" class="form-control mb-2"
                placeholder="Judul Kolom" data-sync-target="#sync-footer-col2">
            <textarea name="footer_col2_links" class="form-control" rows="5"
                placeholder="Tentang Kami&#10;FAQ&#10;..."
                data-sync-target="#sync-footer-col2">{{ $settings['footer_col2_links'] ?? "Tentang Kami\nSyarat & Ketentuan\nKebijakan Privasi\nFAQ\nBlog Ibadah" }}</textarea>
        </div>

        <!-- Kolom 3 -->
        <div class="form-group footer-section-light">
            <label class="form-label text-sm font-semibold mb-2 block text-primary">Kolom 3</label>
            <input type="text" name="footer_col3_title"
                value="{{ $settings['footer_col3_title'] ?? 'Kantor Cabang' }}" class="form-control mb-2"
                placeholder="Judul Kolom" data-sync-target="#sync-footer-col3">
            <textarea name="footer_col3_links" class="form-control" rows="5"
                placeholder="Jakarta Pusat&#10;Surabaya&#10;..."
                data-sync-target="#sync-footer-col3">{{ $settings['footer_col3_links'] ?? "Jakarta Pusat\nSurabaya\nBandung\nYogyakarta\nMakassar" }}</textarea>
        </div>
    </div>

    {{-- LEGALITAS --}}
    <div class="footer-section-card mt-6">
        <h3 class="footer-legal-title">
            🏅 Legalitas & Sertifikasi
        </h3>
        <div class="form-group mb-3">
            <label class="form-label text-xs font-semibold mb-1 block">Teks Label (contoh: LEGALITAS
                RESMI:)</label>
            <input type="text" name="footer_legalitas_label"
                value="{{ $settings['footer_legalitas_label'] ?? 'LEGALITAS RESMI:' }}" class="form-control"
                placeholder="LEGALITAS RESMI:" data-sync-target="#sync-footer-legalitas">
            <span class="help-block">Teks yang muncul di sebelah kiri badge sertifikasi.</span>
        </div>
        <div class="stats-grid-2">
            <div class="form-group">
                <label class="form-label text-xs font-semibold mb-1 block">Badge 1</label>
                <input type="text" name="footer_badge_1"
                    value="{{ $settings['footer_badge_1'] ?? 'KEMENAG RI' }}" class="form-control text-sm"
                    placeholder="KEMENAG RI" data-sync-target="#sync-footer-legalitas">
            </div>
            <div class="form-group">
                <label class="form-label text-xs font-semibold mb-1 block">Badge 2</label>
                <input type="text" name="footer_badge_2" value="{{ $settings['footer_badge_2'] ?? 'IATA' }}"
                    class="form-control text-sm" placeholder="IATA"
                    data-sync-target="#sync-footer-legalitas">
            </div>
            <div class="form-group">
                <label class="form-label text-xs font-semibold mb-1 block">Badge 3</label>
                <input type="text" name="footer_badge_3"
                    value="{{ $settings['footer_badge_3'] ?? 'ISO 9001' }}" class="form-control text-sm"
                    placeholder="ISO 9001" data-sync-target="#sync-footer-legalitas">
            </div>
            <div class="form-group">
                <label class="form-label text-xs font-semibold mb-1 block">Badge 4 <span
                        class="text-muted font-normal">(opsional)</span></label>
                <input type="text" name="footer_badge_4" value="{{ $settings['footer_badge_4'] ?? '' }}"
                    class="form-control text-sm" placeholder="Opsional..."
                    data-sync-target="#sync-footer-legalitas">
            </div>
        </div>
        <span class="help-block mt-2">Badge 1–3 memiliki nilai cadangan jika dikosongkan. Kosongkan Badge 4
            jika tidak diperlukan.</span>
    </div>

</div>
