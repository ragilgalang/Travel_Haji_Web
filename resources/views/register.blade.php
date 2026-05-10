<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Formulir Pendaftaran — {{ $settings['site_name'] ?? 'PT. Umi Muthmainah Berkah' }}</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
/* DYNAMIC BRANDING - Keep this in Blade */
:root {
  --green: {{ $settings['primary_color'] ?? '#1a5c3a' }};
  --green2: {{ ($settings['primary_color'] ?? '#1a5c3a') . 'ee' }};
  --green3: {{ ($settings['primary_color'] ?? '#1a5c3a') . 'cc' }};
  --green-light: {{ ($settings['primary_color'] ?? '#1a5c3a') . '15' }};
  
  --gold: {{ $settings['secondary_color'] ?? '#ca8a04' }};
  --gold2: {{ ($settings['secondary_color'] ?? '#ca8a04') . 'ee' }};
  --gold-light: {{ ($settings['secondary_color'] ?? '#ca8a04') . '15' }};
  --gold-bright: var(--gold);

  /* Legacy Mapping for compatibility */
  --g900: #052e16;
  --g800: var(--green);
  --g700: var(--green2);
  --g600: var(--green);
  --g500: var(--green);
  --g100: var(--green-light);
  --g50:  var(--green-light);
  
  --cream: #fafaf7;
  --white: #ffffff;
  --gray-900: #111827;
  --gray-700: #374151;
  --gray-500: #6b7280;
  --gray-300: #d1d5db;
  --gray-100: #f3f4f6;
  --red: #ef4444;
  --red-light: #fef2f2;
}
</style>
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
<link rel="stylesheet" href="{{ asset('css/welcome_extra.css') }}">
</head>
<body>

<!-- TOPBAR -->
<header class="topbar">
  <div class="brand">
    <div class="brand-mark">
      <svg viewBox="0 0 24 24" fill="none">
        <path d="M12 2L4 7v10l8 5 8-5V7L12 2Z" stroke="white" stroke-width="1.5" stroke-linejoin="round"/>
        <path d="M12 8v8M8 10l4-2 4 2" stroke="#eab308" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>
    <div>
      <div class="brand-name">{{ $settings['site_name'] ?? 'PT. Umi Muthmainah Berkah' }}</div>
      <div class="brand-loc">{{ $settings['site_address'] ?? 'Sidokare, Sidoarjo' }}</div>
    </div>
  </div>
  <a href="/" class="back-link">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    Kembali
  </a>
</header>

<!-- HERO -->
<div class="hero">
  <div class="hero-content">
    <div class="hero-badge">✦ &nbsp;Langkah Pertama Menuju Tanah Suci</div>
    <h1>Formulir<br><em>Pendaftaran</em></h1>
    <p>Lengkapi data diri Anda dengan benar untuk memulai perjalanan suci bersama kami.</p>


  </div>
</div>

<!-- PAGE LAYOUT -->
<div class="page-wrap">

  <!-- FORM CARD -->
  <div>
    @if($errors->any())
        <div class="reg-error-box">
            <strong>Mohon periksa kembali isian Anda:</strong>
            <ul class="reg-error-list">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('register.store') }}" method="POST" id="regForm">
      @csrf
      
      <!-- Section 1: Identitas -->
      <div class="form-card form-card-mb">
        <div class="form-section-header">
          <div class="section-icon g">
            <svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </div>
          <div class="section-info">
            <div class="sec-title">Data Identitas Diri</div>
            <div class="sec-sub">Isi sesuai KTP / Paspor yang berlaku</div>
          </div>
        </div>
        <div class="form-body">
          <div class="field-grid">

            <div class="field-group">
              <label class="field-label">
                Nama Lengkap (sesuai KTP / Paspor)
                <span class="required-dot"></span>
              </label>
              <div class="input-wrap has-icon">
                <div class="input-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
                <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Nama lengkap sesuai identitas resmi..." id="nama">
              </div>
            </div>

            <div class="field-grid two-col">
              <div class="field-group">
                <label class="field-label">
                  NIK (16 digit)
                  <span class="required-dot"></span>
                </label>
                <div class="input-wrap has-icon">
                  <div class="input-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg></div>
                  <input type="text" name="nik" value="{{ old('nik') }}" placeholder="1234567890123456" maxlength="16" id="nik" oninput="this.value=this.value.replace(/\D/g,'')">
                </div>
              </div>
              <div class="field-group">
                <label class="field-label">
                  No. HP / WhatsApp
                  <span class="required-dot"></span>
                </label>
                <div class="input-wrap has-icon">
                  <div class="input-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.67A2 2 0 012 .84h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 8.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 15.92z"/></svg></div>
                  <input type="tel" name="hp" value="{{ old('hp') }}" placeholder="08xx xxxx xxxx" id="hp">
                </div>
              </div>
            </div>

            <div class="field-grid two-col">
              <div class="field-group">
                <label class="field-label">Tempat Lahir <span class="required-dot"></span></label>
                <div class="input-wrap has-icon">
                  <div class="input-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
                  <input type="text" name="ttl" value="{{ old('ttl') }}" placeholder="Kota kelahiran" id="ttl">
                </div>
              </div>
              <div class="field-group">
                <label class="field-label">Tanggal Lahir <span class="required-dot"></span></label>
                <div class="input-wrap">
                  <input type="date" name="tgl" value="{{ old('tgl') }}" id="tgl">
                </div>
              </div>
            </div>

            <div class="field-group">
              <label class="field-label">Jenis Kelamin <span class="required-dot"></span></label>
              <div class="radio-group">
                <div class="radio-pill">
                  <input type="radio" name="gender" id="laki" value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'checked' : '' }}>
                  <label for="laki"><span class="pill-dot"></span> Laki-laki</label>
                </div>
                <div class="radio-pill">
                  <input type="radio" name="gender" id="perempuan" value="Perempuan" {{ old('gender') == 'Perempuan' ? 'checked' : '' }}>
                  <label for="perempuan"><span class="pill-dot"></span> Perempuan</label>
                </div>
              </div>
            </div>

            <div class="field-group">
              <label class="field-label">Alamat Lengkap <span class="required-dot"></span></label>
              <div class="input-wrap">
                <textarea name="alamat" placeholder="Jalan, kelurahan, kecamatan, kabupaten/kota, provinsi..." id="alamat">{{ old('alamat') }}</textarea>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- Section 2: Paket & Kamar -->
      <div class="form-card form-card-mb">
        <div class="form-section-header">
          <div class="section-icon gold">
            <svg viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2"><path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>
          </div>
          <div class="section-info">
            <div class="sec-title">Pilihan Paket & Akomodasi</div>
            <div class="sec-sub">Sesuaikan paket dengan kebutuhan Anda</div>
          </div>
        </div>
        <div class="form-body">
          <div class="field-grid">

            <div class="field-group">
              <label class="field-label">Paket yang Dipilih <span class="required-dot"></span></label>
              <div class="input-wrap">
                <select name="paket" id="paket">
                  <option value="" disabled selected>— Pilih paket —</option>
                  @foreach($packages ?? [] as $pkg)
                    @php
                      $pkgId = $pkg['id'] ?? '';
                      $pkgName = $pkg['name'] ?? '';
                      $isSelected = (old('paket') == $pkgName) || (request('package') == $pkgId);
                    @endphp
                    <option value="{{ $pkgName }}" {{ $isSelected ? 'selected' : '' }}>
                      {{ $pkgName }} 
                      @if(isset($pkg['price']) && $pkg['price']) — Rp {{ number_format((int)$pkg['price'], 0, ',', '.') }} @endif
                      @if(isset($pkg['duration']) && $pkg['duration']) ({{ $pkg['duration'] }} hari) @endif
                    </option>
                  @endforeach
                </select>
                <div class="select-arrow"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg></div>
              </div>
            </div>

            <div class="field-group">
              <label class="field-label">Tipe Kamar <span class="required-dot"></span></label>
              <div class="radio-group">
                <div class="radio-pill">
                  <input type="radio" name="kamar" id="quad" value="Quad (4 Orang)" {{ old('kamar') == 'Quad (4 Orang)' ? 'checked' : '' }}>
                  <label for="quad"><span class="pill-dot"></span> Quad (4 orang)</label>
                </div>
                <div class="radio-pill">
                  <input type="radio" name="kamar" id="triple" value="Triple (3 Orang)" {{ old('kamar') == 'Triple (3 Orang)' ? 'checked' : '' }}>
                  <label for="triple"><span class="pill-dot"></span> Triple (3 orang)</label>
                </div>
                <div class="radio-pill">
                  <input type="radio" name="kamar" id="double" value="Double (2 Orang)" {{ old('kamar') == 'Double (2 Orang)' ? 'checked' : '' }}>
                  <label for="double"><span class="pill-dot"></span> Double (2 orang)</label>
                </div>
              </div>
            </div>

            <div class="field-group">
              <label class="field-label">Kebutuhan Khusus / Catatan</label>
              <div class="input-wrap">
                <textarea name="catatan" placeholder="Contoh: memerlukan kursi roda, alergi makanan tertentu, dll." id="catatan" class="catatan-textarea">{{ old('catatan') }}</textarea>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- Section 3: Kontak Darurat -->
      <div class="form-card">
        <div class="form-section-header">
          <div class="section-icon g">
            <svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
          </div>
          <div class="section-info">
            <div class="sec-title">Kontak Darurat</div>
            <div class="sec-sub">Keluarga atau wali yang dapat dihubungi</div>
          </div>
        </div>
        <div class="form-body">
          <div class="field-grid">
            <div class="field-grid two-col">
              <div class="field-group">
                <label class="field-label">Nama Keluarga / Wali <span class="required-dot"></span></label>
                <div class="input-wrap has-icon">
                  <div class="input-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div>
                  <input type="text" name="wali" value="{{ old('wali') }}" placeholder="Nama lengkap" id="wali">
                </div>
              </div>
              <div class="field-group">
                <label class="field-label">Hubungan <span class="required-dot"></span></label>
                <div class="input-wrap">
                  <select name="hubungan" id="hubungan">
                    <option value="" disabled selected>— Pilih hubungan —</option>
                    <option value="Suami / Istri" {{ old('hubungan') == 'Suami / Istri' ? 'selected' : '' }}>Suami / Istri</option>
                    <option value="Orang Tua" {{ old('hubungan') == 'Orang Tua' ? 'selected' : '' }}>Orang Tua</option>
                    <option value="Anak" {{ old('hubungan') == 'Anak' ? 'selected' : '' }}>Anak</option>
                    <option value="Saudara Kandung" {{ old('hubungan') == 'Saudara Kandung' ? 'selected' : '' }}>Saudara Kandung</option>
                    <option value="Lainnya" {{ old('hubungan') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                  </select>
                  <div class="select-arrow"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg></div>
                </div>
              </div>
            </div>
            <div class="field-group">
              <label class="field-label">No. HP Darurat <span class="required-dot"></span></label>
              <div class="input-wrap has-icon">
                <div class="input-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.67A2 2 0 012 .84h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 8.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 15.92z"/></svg></div>
                <input type="tel" name="hp_darurat" value="{{ old('hp_darurat') }}" placeholder="08xx xxxx xxxx" id="hp-darurat">
              </div>
            </div>
          </div>
        </div>

        <div class="form-actions">
          <div class="form-note">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4.5 8-11.8A8 8 0 0012 2a8 8 0 00-8 8.2c0 7.3 8 11.8 8 11.8z"/></svg>
            Data Anda aman & terenkripsi
          </div>
          <button type="button" class="btn-submit" onclick="submitForm()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>Kirim Pendaftaran</span>
          </button>
        </div>
      </div>
    </form>
  </div>

  <!-- SIDEBAR -->
  <div class="sidebar">

    <!-- Package Summary -->
    <div class="pkg-summary">
      <div class="pkg-header">
        <div class="pkg-badge">Paket Terbaik</div>
        <h3>Layanan Eksklusif<br>Terpercaya</h3>
        <div class="pkg-duration">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4.5 8-11.8A8 8 0 0012 2a8 8 0 00-8 8.2c0 7.3 8 11.8 8 11.8z"/></svg>
          Sesuai Izin Kemenag RI
        </div>
      </div>
      <ul class="pkg-features pt-4">
        <li class="pkg-feature">
          <div class="feat-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
          Tiket pesawat PP
        </li>
        <li class="pkg-feature">
          <div class="feat-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
          Hotel bintang 4 & 5 terbaik
        </li>
        <li class="pkg-feature">
          <div class="feat-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
          Visa resmi
        </li>
        <li class="pkg-feature">
          <div class="feat-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
          Pembimbing ibadah berpengalaman
        </li>
        <li class="pkg-feature">
          <div class="feat-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
          Manasik & layanan penuh
        </li>
      </ul>
    </div>

    <!-- Info Card -->
    <div class="info-card">
      <div class="info-card-header">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        Proses Pendaftaran
      </div>
      <div class="info-item">
        <div class="info-item-icon g">
          <svg viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
        <div class="info-item-text">
          <div class="it-title">Isi Formulir</div>
          <div class="it-desc">Lengkapi data diri, pilih paket & kontak darurat</div>
        </div>
      </div>
      <div class="info-item">
        <div class="info-item-icon gold">
          <svg viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.67A2 2 0 012 .84h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 8.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 15.92z"/></svg>
        </div>
        <div class="info-item-text">
          <div class="it-title">Konfirmasi via WA</div>
          <div class="it-desc">Tim kami menghubungi Anda dalam 1×24 jam</div>
        </div>
      </div>
      <div class="info-item">
        <div class="info-item-icon blue">
          <svg viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2"><path d="M12 22s8-4.5 8-11.8A8 8 0 0012 2a8 8 0 00-8 8.2c0 7.3 8 11.8 8 11.8z"/><circle cx="12" cy="10" r="3"/></svg>
        </div>
        <div class="info-item-text">
          <div class="it-title">Keberangkatan</div>
          <div class="it-desc">Persiapan & keberangkatan bersama jemaah</div>
        </div>
      </div>
    </div>

    <!-- Contact -->
    <div class="contact-card">
      <h4>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.67A2 2 0 012 .84h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 8.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 15.92z"/></svg>
        Butuh Bantuan?
      </h4>
      <div class="contact-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07"/></svg>
        {{ $settings['contact_wa'] ?? '0812-3456-7890' }}
      </div>
      <div class="contact-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        Senin – Sabtu, 08.00 – 17.00
      </div>
      <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['contact_wa'] ?? '') }}" target="_blank" class="wa-btn">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.570-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.118.553 4.107 1.523 5.832L0 24l6.335-1.509A11.934 11.934 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.006-1.373l-.36-.214-3.76.896.942-3.667-.236-.377A9.818 9.818 0 1112 21.818z"/></svg>
        Chat via WhatsApp
      </a>
    </div>

    <!-- Check Status Card -->
    <div class="info-card" style="margin-top: 1.5rem; background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden;">
      <div class="info-card-header" style="color: var(--green); background: #f8fafc; padding: 12px 16px; font-weight: 700; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #e2e8f0;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        Cek Status Pendaftaran
      </div>
      <div style="padding: 1.25rem;">
        <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 1rem; line-height: 1.5;">Masukkan nomor referensi untuk melihat progres pendaftaran Anda secara real-time.</p>
        <div style="margin-bottom: 0.75rem;">
          <input type="text" id="checkRefId" placeholder="REG-ABCD1234" style="width: 100%; padding: 12px; border-radius: 10px; border: 2px solid #e2e8f0; font-size: 0.9rem; text-transform: uppercase; font-weight: 600; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--green)'" onblur="this.style.borderColor='#e2e8f0'">
        </div>
        <button type="button" onclick="checkRegStatus()" id="btnCheckStatus" style="width: 100%; background: var(--green); color: white; border: none; padding: 12px; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 0.9rem; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.3s; box-shadow: 0 4px 12px var(--green-light);">
          🔍 Cek Sekarang
        </button>
        
        <div id="statusResult" class="d-none" style="margin-top: 1.25rem; padding: 15px; border-radius: 12px; background: #f1f5f9; border: 1px solid #e2e8f0; animation: fadeInUp 0.4s ease-out;">
          <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 5px; font-weight: 500;">Nama Jemaah:</div>
          <div id="resNama" style="font-weight: 800; color: #0f172a; font-size: 1rem; margin-bottom: 12px;">-</div>
          <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 6px; font-weight: 500;">Status Saat Ini:</div>
          <div id="resStatus" style="display: inline-block; padding: 6px 14px; border-radius: 30px; font-size: 0.8rem; font-weight: 800; background: #dbeafe; color: #1e40af; box-shadow: 0 2px 5px rgba(0,0,0,0.05);"> - </div>
          <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 12px; border-top: 1px solid #e2e8f0; padding-top: 8px;">Daftar pada: <span id="resTgl" style="color: #64748b; font-weight: 600;">-</span></div>
        </div>
      </div>
    </div>

  </div>

</div>

<!-- Toast is preserved if needed for JS validation before real post -->

<script>
function submitForm() {
  const fields = ['nama','nik','hp','ttl','tgl','alamat','wali','hp-darurat'];
  let valid = true;

  fields.forEach(id => {
    const el = document.getElementById(id);
    if (el && !el.value.trim()) {
      el.classList.add('error');
      el.addEventListener('input', () => el.classList.remove('error'), { once: true });
      valid = false;
    }
  });

  const paket = document.getElementById('paket');
  if (!paket.value) { paket.classList.add('error'); valid = false; }

  const gender = document.querySelector('input[name="gender"]:checked');
  const kamar  = document.querySelector('input[name="kamar"]:checked');

  if (!valid || !gender || !kamar) {
    // Scroll to first error
    const firstError = document.querySelector('.error');
    if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    return;
  }

  const btn = document.querySelector('.btn-submit');
  btn.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="spin-anim"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg> <span>Mengirim...</span>`;
  btn.disabled = true;

  // Let CSS spin animate for 400ms smoothly before pushing POST requests via HTML standard form
  setTimeout(() => {
    document.getElementById('regForm').submit();
  }, 400);
}

async function checkRegStatus() {
    const refId = document.getElementById('checkRefId').value.trim();
    const btn = document.getElementById('btnCheckStatus');
    const resBox = document.getElementById('statusResult');
    
    if (!refId) {
        alert('Silakan masukkan nomor referensi Anda.');
        return;
    }

    // Loading state
    btn.disabled = true;
    btn.innerHTML = `<span style="display:inline-block; animation: spin 1s linear infinite;">⌛</span> Memeriksa...`;
    resBox.classList.add('d-none');

    try {
        const response = await fetch('{{ route('register.checkStatus') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ref_id: refId })
        });
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('resNama').textContent = data.nama;
            document.getElementById('resStatus').textContent = data.status;
            document.getElementById('resTgl').textContent = data.tgl;
            
            // Color mapping for status
            const s = data.status;
            const statusEl = document.getElementById('resStatus');
            if (s === 'Selesai') {
                statusEl.style.background = '#dcfce7'; statusEl.style.color = '#15803d';
            } else if (s === 'Sedang Diproses') {
                statusEl.style.background = '#fef3c7'; statusEl.style.color = '#b45309';
            } else if (s === 'Dibatalkan') {
                statusEl.style.background = '#fee2e2'; statusEl.style.color = '#b91c1c';
            } else {
                statusEl.style.background = '#dbeafe'; statusEl.style.color = '#1e40af';
            }

            resBox.classList.remove('d-none');
        } else {
            alert(data.message);
        }
    } catch (e) {
        alert('Terjadi kesalahan. Silakan coba lagi nanti.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = `🔍 Cek Sekarang`;
    }
}

/* ── LIVE SYNC LISTENER ── */
let syncTimeout = null;
window.addEventListener('message', function(event) {
    if (!event.data || typeof event.data !== 'object') return;

    if (event.data.type === 'SYNC_SCROLL') {
        const targetId = event.data.target;
        if (!targetId) return;

        const el = document.querySelector(targetId);
        if (el) {
            document.querySelectorAll('.sync-highlight').forEach(h => h.classList.remove('sync-highlight'));
            if (window.syncTimeout) clearTimeout(window.syncTimeout);

            try {
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } catch(e) {
                const offset = el.getBoundingClientRect().top + window.pageYOffset - (window.innerHeight / 2);
                window.scrollTo({ top: offset, behavior: 'smooth' });
            }
            
            setTimeout(() => {
                el.classList.add('sync-highlight');
                window.syncTimeout = setTimeout(() => {
                    el.classList.remove('sync-highlight');
                }, 3000);
            }, 400);
        }
    }

    if (event.data.type === 'SYNC_COLOR') {
        const d = event.data;
        if (d.primary) {
            document.documentElement.style.setProperty('--green', d.primary);
            document.documentElement.style.setProperty('--green2', d.primary + 'ee');
            document.documentElement.style.setProperty('--green3', d.primary + 'cc');
            document.documentElement.style.setProperty('--green-light', d.primary + '15');
        }
        if (d.secondary) {
            document.documentElement.style.setProperty('--gold', d.secondary);
            document.documentElement.style.setProperty('--gold2', d.secondary + 'ee');
            document.documentElement.style.setProperty('--gold-light', d.secondary + '15');
        }
    }
});

// Styles for highlighting (Inserted dynamically)
const syncStyle = document.createElement('style');
syncStyle.textContent = `
    .sync-highlight {
        outline: 5px solid #10b981 !important;
        outline-offset: -5px !important;
        box-shadow: inset 0 0 100px rgba(16, 185, 129, 0.4), 0 0 50px rgba(16, 185, 129, 0.6) !important;
        animation: pulse-sync 1.5s ease-in-out infinite !important;
        position: relative !important;
        z-index: 1000 !important;
        transition: all 0.3s ease !important;
    }
    @keyframes pulse-sync {
        0%, 100% { outline-color: rgba(16, 185, 129, 0.5); transform: scale(1); }
        50% { outline-color: rgba(16, 185, 129, 1); outline-width: 8px; transform: scale(1.002); }
    }
    @keyframes spin{to{transform:rotate(360deg)}}
`;
document.head.appendChild(syncStyle);
</script>
</body>
</html>
