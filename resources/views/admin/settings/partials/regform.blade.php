<!-- TAB: FORM REGISTER -->
<div id="tab-regform" class="tab-pane card" onfocus="syncToPreview('.reg-card')">
    <h2 class="card-title mb-1">📋 Pengaturan Halaman Pendaftaran</h2>
    <p class="mb-5 text-sm text-muted">Atur tampilan visual dan kolom formulir untuk calon
        jamaah Anda.</p>

    <!-- HEADER IMAGE SETTINGS REMOVED -->

    <div class="card-header reg-fields-header">
        <div>
            <h3 class="card-title reg-fields-title">Daftar Kolom Formulir</h3>
        </div>
        <button type="button" onclick="addRegField()" class="btn-primary btn-add-reg-field">+ Tambah Kolom</button>
    </div>

    <div id="reg-fields-container">
        @php
            $regFields = $settings['registration_fields'] ?? [
                ['label' => 'Nama Lengkap (sesuai KTP)', 'type' => 'text', 'required' => '1'],
                ['label' => 'NIK (16 digit)', 'type' => 'number', 'required' => '1'],
                ['label' => 'No. HP / WhatsApp', 'type' => 'tel', 'required' => '1'],
                ['label' => 'Foto KTP', 'type' => 'file', 'required' => '1'],
                ['label' => 'Surat Keterangan Sehat', 'type' => 'file', 'required' => '0'],
                ['label' => 'Tempat Lahir', 'type' => 'text', 'required' => '1'],
                ['label' => 'Tanggal Lahir', 'type' => 'date', 'required' => '1'],
                ['label' => 'Jenis Kelamin', 'type' => 'text', 'required' => '1', 'placeholder' => 'Laki-laki / Perempuan'],
                ['label' => 'Status Pernikahan', 'type' => 'text', 'required' => '0'],
                ['label' => 'Email (opsional)', 'type' => 'email', 'required' => '0'],
                ['label' => 'Alamat Lengkap', 'type' => 'textarea', 'required' => '1'],
                ['label' => 'Paket yang Diminati', 'type' => 'text', 'required' => '1'],
                ['label' => 'Nama Mahram / Keluarga', 'type' => 'text', 'required' => '0'],
                ['label' => 'Catatan Tambahan', 'type' => 'textarea', 'required' => '0'],
            ];
        @endphp

        @foreach($regFields as $index => $field)
            @php
                $typeIcons = [
                    'text' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.828 2.828 0 114 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>',
                    'number' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg>',
                    'tel' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>',
                    'date' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
                    'email' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
                    'textarea' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>',
                    'file' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>',
                    'select' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>',
                ];
            @endphp
            <div class="reg-field-card">
                <div class="field-actions">
                    <button type="button" onclick="this.closest('.reg-field-card').remove()"
                        class="btn-icon-delete" title="Hapus Kolom">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <polyline points="3 6 5 6 21 6" />
                            <path
                                d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" />
                            <line x1="10" y1="11" x2="10" y2="17" />
                            <line x1="14" y1="11" x2="14" y2="17" />
                        </svg>
                    </button>
                </div>

                <div class="field-header">
                    <div class="field-icon">
                        {!! $typeIcons[$field['type'] ?? 'text'] ?? $typeIcons['text'] !!}
                    </div>
                    <span class="reg-field-index">Kolom Baru (#{{ $index + 1 }})</span>
                </div>

                <div class="field-grid">
                    <div class="form-group mb-0">
                        <label class="form-label text-xs">Label Kolom</label>
                        <input type="text" name="registration_fields[{{ $index }}][label]"
                            value="{{ $field['label'] }}" class="form-control"
                            placeholder="Contoh: Nama Lengkap">
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label text-xs">Tipe Input</label>
                        <select name="registration_fields[{{ $index }}][type]" class="form-control"
                            onchange="updateFieldIcon(this)">
                            @foreach(['text' => 'Teks', 'number' => 'Angka', 'tel' => 'Telepon', 'date' => 'Tanggal', 'email' => 'Email', 'textarea' => 'Paragraf', 'file' => 'Upload File/Foto', 'select' => 'Pilihan (Dropdown)'] as $k => $v)
                                <option value="{{ $k }}" {{ ($field['type'] ?? 'text') == $k ? 'selected' : '' }}>
                                    {{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-0 text-center">
                        <label class="form-label text-xs">Wajib?</label>
                        <div class="reg-field-required-box">
                            <input type="checkbox" name="registration_fields[{{ $index }}][required]" value="1"
                                {{ ($field['required'] ?? '') == '1' ? 'checked' : '' }}
                                class="reg-field-checkbox">
                        </div>
                    </div>
                </div>

                <div class="opt-group reg-field-opt-group {{ ($field['type'] ?? '') == 'select' ? 'd-block' : 'd-none' }}">
                    <label class="form-label reg-field-opt-label">⚙️ Pilihan Jawaban
                        (Pisahkan dengan koma)</label>
                    <input type="text" name="registration_fields[{{ $index }}][options]"
                        value="{{ $field['options'] ?? '' }}" class="form-control"
                        placeholder="Contoh: Sudah Kawin, Belum Kawin, Cerai">
                    <span class="reg-field-opt-help">Masukkan
                        semua pilihan yang ingin ditampilkan di dropdown.</span>
                </div>
            </div>
        @endforeach
    </div>
</div>
