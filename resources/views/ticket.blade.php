<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>E-Ticket Perjalanan Ibadah — {{ $data['ref_id'] }}</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Jost:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- Libraries for QR Code -->
<script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
<link rel="stylesheet" href="{{ asset('css/ticket.css') }}">
</head>
<body>

@php
  $st = strtolower($data['status'] ?? 'menunggu verifikasi');
  $badgeClass = '';
  if (str_contains($st, 'selesai') || str_contains($st, 'lunas') || str_contains($st, 'terverifikasi')) {
      $badgeClass = 'verified';
  } elseif (str_contains($st, 'tolak') || str_contains($st, 'batal')) {
      $badgeClass = 'canceled';
  }
  
  $nik = $data['nik'] ?? '';
  $maskedNik = strlen($nik) >= 16 ? substr($nik, 0, 4) . ' ' . str_repeat('•', 8) . ' ' . substr($nik, 12, 4) : $nik;

  // SVG barcode scales naturally to container width — best for fixed-width stub panel
  $barcodeSvg = DNS1D::getBarcodeSVG($data['ref_id'], 'C39', 1, 40, '#1A1410');
@endphp

<!-- TOP BAR -->
<div class="topbar">
  <a href="{{ url('/') }}" class="btn-back">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    Kembali ke Beranda
  </a>
  <button class="btn-print" onclick="window.print()">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
    Cetak E-Ticket
  </button>
</div>

<!-- TICKET -->
<div class="ticket-wrap">

  <!-- ═══ LEFT ═══ -->
  <div class="left-panel">

    <!-- Header -->
    <div class="ticket-header">
      <div class="logo-block">
        <div class="logo-icon">
          @if(!empty($settings['site_logo']))
            <img src="{{ $settings['site_logo'] }}" alt="Logo">
          @else
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8">
              <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
              <circle cx="12" cy="9" r="2.5"/>
            </svg>
          @endif
        </div>
        <div class="logo-text">
          <strong>{{ $settings['site_name'] ?? 'PT. UMI MUTHMAINAH BERKAH' }}</strong>
          <span>Penyelenggara Haji &amp; Umrah Resmi</span>
        </div>
      </div>
      <div class="status-badge {{ $badgeClass }}">
        <span class="dot"></span>
        {{ $data['status'] ?? 'Menunggu Verifikasi' }}
      </div>
    </div>

    <!-- Tab Nav -->
    <div class="tab-nav">
      <button class="tab-btn active" onclick="switchTab('info')">Informasi Jemaah</button>
      <button class="tab-btn" onclick="switchTab('jadwal')">Jadwal Keberangkatan</button>
    </div>

    <!-- TAB: Info -->
    <div class="tab-content active" id="tab-info">
      <div class="bismillah">بِسْمِ اللهِ الرَّحْمٰنِ الرَّحِيْمِ</div>
      <div class="ticket-title">
        <h1>E-Ticket Perjalanan <em>Ibadah</em></h1>
      </div>

      <div class="divider"></div>

      <div class="fields">
        <div class="field">
          <label>Nama Lengkap Jemaah</label>
          <div class="value large">{{ $data['nama_lengkap'] ?? '-' }}</div>
        </div>
        <div class="field">
          <label>Nomor Induk Kependudukan</label>
          <div class="value">{{ $maskedNik ?: '-' }}</div>
        </div>
        <div class="field">
          <label>Paket Perjalanan</label>
          <div class="value" style="color:var(--forest);font-weight:600;">{{ $data['paket'] ?? '-' }}</div>
        </div>
        <div class="field">
          <label>Pilihan Kamar</label>
          <div class="value">{{ $data['kamar'] ?? '-' }}</div>
        </div>
        <div class="field">
          <label>Nomor Handphone</label>
          <div class="value">{{ $data['no_hp'] ?? '-' }}</div>
        </div>
        <div class="field">
          <label>Tanggal Pendaftaran</label>
          <div class="value">{{ isset($data['created_at']) ? date('d M Y, H:i', strtotime($data['created_at'])) . ' WIB' : '-' }}</div>
        </div>
        <div class="field">
          <label>Kontak Darurat / Wali</label>
          <div class="value">{{ $data['wali'] ?? '-' }} <span style="font-size:12px;color:var(--ink-soft)">({{ $data['hubungan'] ?? '-' }})</span></div>
        </div>
        <div class="field">
          <label>No. HP Darurat</label>
          <div class="value">{{ $data['hp_darurat'] ?? '-' }}</div>
        </div>
      </div>

      <div class="ticket-note">
        ✦ Tunjukkan bukti digital atau cetak E-Ticket ini saat melakukan verifikasi dokumen dan pembayaran di kantor operasional.
      </div>
    </div>

    <!-- TAB: Jadwal -->
    <div class="tab-content" id="tab-jadwal">

      <div class="schedule-section">
        <div class="schedule-title">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <span>Keberangkatan · Surabaya → Jeddah</span>
        </div>

        <div class="route">
          <div class="route-city">
            <div class="city-code">SUB</div>
            <div class="city-name">Surabaya, Indonesia</div>
            <div class="city-time">03.00 WIB</div>
            <div class="city-date">15 November 2026</div>
          </div>
          <div class="route-arrow">
            <div class="arrow-line"></div>
            <div class="duration">±9j 30m</div>
          </div>
          <div class="route-city" style="text-align:right">
            <div class="city-code">JED</div>
            <div class="city-name">Jeddah, Arab Saudi</div>
            <div class="city-time">07.30 AST</div>
            <div class="city-date">15 November 2026</div>
          </div>
        </div>

        <div class="schedule-pills">
          <div class="pill">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
            Garuda Indonesia GA-981
          </div>
          <div class="pill">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 3 L8 3 L8 7"/></svg>
            Kelas Ekonomi
          </div>
          <div class="pill">Terminal 2</div>
        </div>

        <!-- Return -->
        <div class="return-route">
          <div class="return-label">Kepulangan · Jeddah → Surabaya</div>
          <div class="route" style="margin-bottom:12px">
            <div class="route-city">
              <div class="city-code">JED</div>
              <div class="city-name">Jeddah, Arab Saudi</div>
              <div class="city-time">22.00 AST</div>
              <div class="city-date">27 November 2026</div>
            </div>
            <div class="route-arrow">
              <div class="arrow-line"></div>
              <div class="duration">±9j 45m</div>
            </div>
            <div class="route-city" style="text-align:right">
              <div class="city-code">SUB</div>
              <div class="city-name">Surabaya, Indonesia</div>
              <div class="city-time">12.45 WIB</div>
              <div class="city-date">28 November 2026</div>
            </div>
          </div>
          <div class="schedule-pills">
            <div class="pill">Garuda Indonesia GA-982</div>
            <div class="pill">12 Hari Program</div>
          </div>
        </div>
      </div>

      <!-- Itinerary -->
      <div style="margin-bottom:28px">
        <div style="font-size:11px;font-weight:600;letter-spacing:.12em;text-transform:uppercase;color:var(--gold);margin-bottom:16px;">Program Perjalanan</div>
        <div style="display:flex;flex-direction:column;gap:0">
          <!-- Day rows -->
          <div style="display:flex;gap:14px;align-items:flex-start;padding:12px 0;border-bottom:1px solid var(--cream-deep)">
            <div style="min-width:36px;height:36px;border-radius:50%;background:var(--forest);color:white;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0">H1</div>
            <div>
              <div style="font-weight:600;font-size:14px;color:var(--ink)">Keberangkatan dari Surabaya</div>
              <div style="font-size:12px;color:var(--ink-soft);margin-top:2px">Bandara Juanda → Jeddah (transit Riyadh) · Pemeriksaan dokumen & imigrasi</div>
            </div>
            <div style="margin-left:auto;font-size:11px;color:var(--ink-soft);white-space:nowrap;flex-shrink:0">15 Nov</div>
          </div>
          <div style="display:flex;gap:14px;align-items:flex-start;padding:12px 0;border-bottom:1px solid var(--cream-deep)">
            <div style="min-width:36px;height:36px;border-radius:50%;background:var(--gold);color:white;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0">H2</div>
            <div>
              <div style="font-weight:600;font-size:14px;color:var(--ink)">Tiba di Jeddah & Perjalanan ke Madinah</div>
              <div style="font-size:12px;color:var(--ink-soft);margin-top:2px">Ziarah Masjid Nabawi · Hotel Madinah 4 malam</div>
            </div>
            <div style="margin-left:auto;font-size:11px;color:var(--ink-soft);white-space:nowrap;flex-shrink:0">16 Nov</div>
          </div>
          <div style="display:flex;gap:14px;align-items:flex-start;padding:12px 0;border-bottom:1px solid var(--cream-deep)">
            <div style="min-width:36px;height:36px;border-radius:50%;background:var(--gold);color:white;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0">H6</div>
            <div>
              <div style="font-weight:600;font-size:14px;color:var(--ink)">Perjalanan ke Makkah · Umrah</div>
              <div style="font-size:12px;color:var(--ink-soft);margin-top:2px">Tawaf & Sa'i · Tahallul · Hotel Makkah 6 malam</div>
            </div>
            <div style="margin-left:auto;font-size:11px;color:var(--ink-soft);white-space:nowrap;flex-shrink:0">20 Nov</div>
          </div>
          <div style="display:flex;gap:14px;align-items:flex-start;padding:12px 0">
            <div style="min-width:36px;height:36px;border-radius:50%;background:var(--forest);color:white;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0">H12</div>
            <div>
              <div style="font-weight:600;font-size:14px;color:var(--ink)">Kepulangan ke Surabaya</div>
              <div style="font-size:12px;color:var(--ink-soft);margin-top:2px">Jeddah → Surabaya via Garuda Indonesia</div>
            </div>
            <div style="margin-left:auto;font-size:11px;color:var(--ink-soft);white-space:nowrap;flex-shrink:0">27 Nov</div>
          </div>
        </div>
      </div>

      <div class="ticket-note">
        ✦ Jadwal keberangkatan bersifat tentatif dan dapat berubah. Konfirmasi final akan dikirimkan via WhatsApp minimal 14 hari sebelum keberangkatan.
      </div>
    </div>

  </div><!-- /left-panel -->

  <!-- ═══ RIGHT (STUB) ═══ -->
  <div class="right-panel">
    <div class="notch top"></div>
    <div class="notch bottom"></div>

    <div class="stub-label">
      <h2>E-Ticket Stub</h2>
      <p>Verifikasi Digital</p>
    </div>

    <div class="divider-v"></div>

    <button class="btn-cetak" onclick="window.print()">Cetak E-Ticket</button>

    <div class="qr-frame">
      <!-- Dynamic QR Code canvas rendered using qrious.min.js -->
      <canvas id="qrCodeCanvas" style="width: 130px; height: 130px; border-radius: 8px; background: #fff; padding: 6px;"></canvas>
    </div>

    <div class="verified-badge">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
      Sistem Terverifikasi
    </div>

    <div class="divider-v"></div>

    <div class="barcode-section">
      <div class="barcode-lines">
        {!! $barcodeSvg !!}
      </div>
      <div class="barcode-id">{{ $data['ref_id'] }}</div>
    </div>
  </div><!-- /right-panel -->

</div><!-- /ticket-wrap -->

<script>
function switchTab(tab) {
  document.querySelectorAll('.tab-btn').forEach((b) => {
    b.classList.toggle('active', (b.innerText.toLowerCase().includes('jemaah') && tab==='info') || (b.innerText.toLowerCase().includes('jadwal') && tab==='jadwal'));
  });
  document.querySelectorAll('.tab-content').forEach((c) => {
    if (tab === 'info') {
      c.classList.toggle('active', c.id === 'tab-info');
    } else {
      c.classList.toggle('active', c.id === 'tab-jadwal');
    }
  });
}

// Generate QR Code dynamically pointing to the ticket page URL
const ticketUrl = window.location.href;
const qr = new QRious({
  element: document.getElementById('qrCodeCanvas'),
  value: ticketUrl,
  size: 130,
  background: '#ffffff',
  foreground: '#2C4A35', // Premium forest green color matching the ticket branding
  level: 'H'
});
</script>
</body>
</html>
