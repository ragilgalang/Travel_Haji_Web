<!-- TAB: UMUM -->
<div id="tab-umum" class="tab-pane active card">
    <h2 class="card-title mb-4">Pengaturan Dasar & Kontak</h2>
    <div class="form-group mb-4">
        <label class="form-label">Logo Website</label>
        <div class="logo-preview-container">
            @if(!empty($settings['site_logo']))
                <div class="logo-preview-bg" style="margin-bottom:12px; position:relative;">
                    <img src="{{ $settings['site_logo'] }}" alt="Logo" class="logo-preview-img">
                    <label class="logo-delete-btn" title="Hapus Logo">
                        <input type="checkbox" name="delete_site_logo" value="1" style="display:none" onchange="this.closest('form').querySelector('#updateBtn').click()">
                        <span onclick="if(confirm('Hapus logo website?')){ this.previousElementSibling.checked=true; this.previousElementSibling.dispatchEvent(new Event('change')); }">🗑️ Hapus Logo</span>
                    </label>
                </div>
            @endif
            <input type="file" name="site_logo" class="form-control" accept="image/*"
                data-sync-target="#sync-site-logo">
        </div>
        <span class="help-block">Biarkan kosong jika tidak ingin mengubah logo. (Disarankan PNG transparan)</span>
    </div>

    <hr class="my-4" style="border-top: 1px solid #eee;">

    <div class="grid-2 mt-4">
        <div class="form-group">
            <label class="admin-label">Nomor WhatsApp CS (Aktif)</label>
            <input type="text" name="contact_wa" value="{{ $settings['contact_wa'] ?? '0812-3456-7890' }}" 
                class="form-control" placeholder="Contoh: 08123456789" data-sync-target="#sync-footer">
            <span class="help-block">Digunakan untuk tombol "Hubungi Kami" di seluruh website.</span>
        </div>
        <div class="form-group">
            <label class="admin-label">Nomor Telepon CS (Display)</label>
            <input type="text" name="contact_phone" value="{{ $settings['contact_phone'] ?? '0800-123-4567' }}" 
                class="form-control" placeholder="Contoh: 031-123456" data-sync-target="#sync-footer">
            <span class="help-block">Muncul di bagian banner kontak website.</span>
        </div>
        <div class="form-group">
            <label class="admin-label">Email Layanan Jemaah</label>
            <input type="email" name="contact_email" value="{{ $settings['contact_email'] ?? 'info@perusahaan.com' }}" 
                class="form-control" placeholder="Contoh: info@travelhaji.com" data-sync-target="#sync-footer">
        </div>
    </div>

    <div class="admin-input-group mt-3">
        <label class="admin-label">Pesan WhatsApp Otomatis (Default)</label>
        <textarea name="wa_msg_default" class="form-control"
            rows="2" data-sync-target="#sync-footer">{{ $settings['wa_msg_default'] ?? "Assalamu'alaikum Admin, saya ingin bertanya mengenai layanan di PT. Umi Muthmainah." }}</textarea>
        <span class="help-block">Pesan ini akan otomatis terisi saat jemaah mengklik tombol Hubungi Kami
            secara umum.</span>
    </div>

    <div class="admin-input-group mt-3">
        <label class="admin-label">Tanya Paket (Otomatis sebut nama paket)</label>
        <textarea name="wa_msg_package" class="form-control"
            rows="2" data-sync-target="#paket">{{ $settings['wa_msg_package'] ?? "Assalamu'alaikum Admin, saya tertarik dengan paket [NAMA_PAKET]. Mohon info detail pendaftarannya. Syukron." }}</textarea>
        <span class="help-block">Tanda <b>[NAMA_PAKET]</b> akan diganti otomatis dengan nama paket yang
            sedang dilihat jemaah.</span>
    </div>
    <div class="admin-input-group mt-3">
        <label class="admin-label">Tanya Haji Khusus (CTA Bawah)</label>
        <textarea name="wa_msg_haji" class="form-control"
            rows="2" data-sync-target="#sync-kontak">{{ $settings['wa_msg_haji'] ?? "Bismillah, saya ingin berkonsultasi mengenai rencana pendaftaran Haji khusus di PT. Umi Muthmainah. Terima kasih." }}</textarea>
    </div>
</div>
