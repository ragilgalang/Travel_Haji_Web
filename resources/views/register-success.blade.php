<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pendaftaran Berhasil — {{ $settings['site_name'] ?? 'PT. UMI MUTHMAINAH BERKAH' }}</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/register_success.css') }}">
<link rel="stylesheet" href="{{ asset('css/welcome_extra.css') }}">
<script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
</head>
<body>

<!-- TOPBAR -->
<header class="topbar">
  <a href="{{ url('/') }}" class="brand">
    <div class="brand-mark">
      @if(!empty($settings['site_logo']))
        <img src="{{ $settings['site_logo'] }}" alt="Logo">
      @else
        <svg viewBox="0 0 24 24" fill="none">
          <path d="M12 2L4 7v10l8 5 8-5V7L12 2Z" stroke="white" stroke-width="1.5" stroke-linejoin="round"/>
          <path d="M12 8v8M8 10l4-2 4 2" stroke="#eab308" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      @endif
    </div>
    <div>
      <div class="brand-name">{{ $settings['site_name'] ?? 'PT. UMI MUTHMAINAH BERKAH' }}</div>
      <div class="brand-loc">{{ $settings['office_address'] ?? 'SIDOKARE, SIDOARJO' }}</div>
    </div>
  </a>
</header>

<!-- Confetti -->
<div class="confetti-wrap" id="confetti"></div>

<!-- CARD WRAPPER -->
<div class="card-wrapper">
  <div class="deco-top">
    <div class="deco-ring">
      <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
  </div>

  <div class="success-card">
    <div class="card-stripe"></div>
    <div class="card-body">

      <div class="bismillah">بِسْمِ اللّٰهِ الرَّحْمٰنِ الرَّحِيْمِ</div>
      <h1 class="success-title">Pendaftaran <em>Berhasil!</em></h1>
      <p class="greeting">Selamat, <strong id="custName">{{ session('nama', 'Jamaah') }}</strong> 🎉</p>
      <p class="success-desc">Data pendaftaran Anda telah kami terima dengan baik. Tim kami akan segera menghubungi Anda melalui WhatsApp dalam 1×24 jam kerja.</p>

      <!-- Reference code -->
      <div class="ref-box">
        <div class="ref-label">✦ &nbsp;Nomor Referensi Pendaftaran</div>
        <div class="ref-code-row">
          <span class="ref-code" id="refCode">{{ session('ref_id', strtoupper(Str::random(8))) }}</span>
          <button class="copy-btn" onclick="copyRef()" title="Salin kode">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
          </button>
        </div>
      </div>

      <div class="ref-note">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        Simpan nomor referensi ini sebagai bukti pendaftaran Anda
      </div>

      <!-- QR and Barcode Presentation -->
      <div class="success-ticket-stubs" style="display: flex; flex-direction: column; align-items: center; margin: 1.5rem 0; padding: 1.5rem; background: rgba(243, 244, 246, 0.5); border-radius: 12px; border: 1px dashed rgba(212, 175, 55, 0.4); gap: 1rem;">
        <p style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; color: #0f5132; margin-bottom: 0;">Verifikasi E-Ticket Digital</p>
        
        <!-- ========================================== -->
        <!-- [TANDA: QR CODE KOTAK] -->
        <!-- ========================================== -->
        <div style="background: white; padding: 0.5rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); display: inline-flex;">
          <canvas id="successQrCanvas"></canvas>
        </div>
        <p style="font-size: 0.7rem; color: #6b7280; text-align: center; margin-bottom: 0;">Scan QR Code di atas dengan handphone untuk memverifikasi keaslian E-ticket secara langsung.</p>
        
        <!-- ========================================== -->
        <!-- [TANDA: BARCODE GARIS] -->
        <!-- ========================================== -->
        <div class="barcode-wrapper" style="display: flex; flex-direction: column; align-items: center; gap: 0.25rem; width: 100%; max-width: 100%; overflow: hidden; margin-top: 0.5rem;">
          <div class="barcode-lines" style="max-width: 100%; text-align: center; display: block; width: 100%;">
            {!! DNS1D::getBarcodeSVG(session('ref_id', 'REF-ID'), 'C39', 1.2, 45, '#0f5132') !!}
          </div>
          <div style="font-family: 'Courier New', monospace; font-size: 0.75rem; font-weight: 700; color: #0d4a2f; letter-spacing: 2px;">{{ session('ref_id', 'REF-ID') }}</div>
        </div>
      </div>

      <!-- Timeline -->
      <div class="timeline">
        <div class="timeline-title">Status & Langkah Selanjutnya</div>

        <div class="tl-item">
          <div class="tl-dot-wrap">
            <div class="tl-dot done"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
            <div class="tl-line done"></div>
          </div>
          <div class="tl-content">
            <div class="tl-title">Formulir Diterima ✓</div>
            <div class="tl-desc">Data Anda telah masuk ke sistem kami</div>
            <div class="tl-time"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>Hari ini, {{ now()->format('d M Y') }}</div>
          </div>
        </div>

        <div class="tl-item">
          <div class="tl-dot-wrap">
            <div class="tl-dot next">2</div>
            <div class="tl-line"></div>
          </div>
          <div class="tl-content">
            <div class="tl-title">Konfirmasi via WhatsApp</div>
            <div class="tl-desc">Tim kami menghubungi Anda untuk verifikasi data</div>
            <div class="tl-time success-tl-time-gold"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>Dalam 1×24 jam kerja</div>
          </div>
        </div>

        <div class="tl-item">
          <div class="tl-dot-wrap">
            <div class="tl-dot later">3</div>
            <div class="tl-line"></div>
          </div>
          <div class="tl-content">
            <div class="tl-title">Pelunasan & Dokumen</div>
            <div class="tl-desc">Pembayaran & pengumpulan berkas persyaratan</div>
            <div class="tl-time success-tl-time-gray">Sesuai jadwal yang ditentukan</div>
          </div>
        </div>

        <div class="tl-item">
          <div class="tl-dot-wrap"><div class="tl-dot later">4</div></div>
          <div class="tl-content">
            <div class="tl-title">Keberangkatan 🕌</div>
            <div class="tl-desc">Menuju Tanah Suci bersama jemaah {{ $settings['site_name'] ?? 'PT. UMB' }}</div>
            <div class="tl-time success-tl-time-gray">Sesuai jadwal paket</div>
          </div>
        </div>
      </div>

      <!-- Contact -->
      <div class="contact-row">
        <div class="contact-info">
          <div class="ci-label">💬 Butuh bantuan segera?</div>
          <div class="ci-num">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="#25d366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.570-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.118.553 4.107 1.523 5.832L0 24l6.335-1.509A11.934 11.934 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.006-1.373l-.36-.214-3.76.896.942-3.667-.236-.377A9.818 9.818 0 1112 21.818z"/></svg>
            {{ $settings['office_phone'] ?? '0812-3456-7890' }}
          </div>
        </div>
        <a href="#" onclick="openWAChat(event)" class="wa-btn">
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.570-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.118.553 4.107 1.523 5.832L0 24l6.335-1.509A11.934 11.934 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.006-1.373l-.36-.214-3.76.896.942-3.667-.236-.377A9.818 9.818 0 1112 21.818z"/></svg>
          Chat WA
        </a>
      </div>

      <!-- E-Tiket Download Button -->
      <a href="{{ route('register.ticket', session('ref_id', 'REF-ID')) }}" target="_blank" class="dl-ticket-btn" style="text-decoration: none; justify-content: center; display: flex; align-items: center; gap: 0.5rem; text-align: center;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Lihat & Cetak Bukti E-Tiket (PDF)
      </a>

      <!-- Action buttons -->
      <div class="action-row">
        <a href="{{ url('/') }}" class="btn btn-primary">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          Kembali ke Beranda
        </a>
        <a href="{{ route('register.show') }}" class="btn btn-outline">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          Daftar Lagi
        </a>
      </div>
    </div>

    <div class="card-footer">
      <div class="footer-text">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
        Data dilindungi enkripsi SSL
        <span class="footer-dot"></span>
        {{ $settings['site_name'] ?? 'PT. UMI MUTHMAINAH BERKAH' }} &copy; {{ date('Y') }}
      </div>
    </div>
  </div>
</div>

<!-- Copy toast -->
<div class="copy-toast" id="copyToast">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
  Kode berhasil disalin!
</div>

<script>
// ── Confetti ──
function spawnConfetti() {
  const colors = ['#22c55e','#16a34a','#eab308','#fbbf24','#dcfce7','#fef3c7','#34d399','#f59e0b'];
  const wrap = document.getElementById('confetti');
  for (let i = 0; i < 60; i++) {
    const el = document.createElement('div');
    el.className = 'particle';
    const size = 6 + Math.random() * 10;
    el.style.left = Math.random() * 100 + '%';
    el.style.top = (-10 - Math.random() * 40) + 'px';
    el.style.width = size + 'px';
    el.style.height = (size * (0.4 + Math.random())) + 'px';
    el.style.backgroundColor = colors[Math.floor(Math.random()*colors.length)];
    el.style.animationDuration = (2.5 + Math.random() * 3) + 's';
    el.style.animationDelay = (Math.random() * 1.8) + 's';
    el.style.borderRadius = (Math.random() > 0.5 ? '50%' : '2px');
    el.style.transform = 'rotate(' + (Math.random()*360) + 'deg)';
    wrap.appendChild(el);
    el.addEventListener('animationend', () => el.remove());
  }
}
window.addEventListener('load', () => {
  setTimeout(spawnConfetti, 400);
  setTimeout(spawnConfetti, 1400);

  // ==========================================
  // [TANDA: PROSES PENJANAAN (GENERATOR) QR CODE KOTAK]
  // ==========================================
  // Generate QR Code dynamically
  const refCode = document.getElementById('refCode').textContent.trim();
  const ticketBaseUrl = "{{ url('/tiket') }}";
  const ticketUrl = `${ticketBaseUrl}/${refCode}`;
  
  new QRious({
    element: document.getElementById('successQrCanvas'),
    value: ticketUrl,
    size: 140,
    background: '#ffffff',
    foreground: '#0f5132',
    level: 'H'
  });

  // No backend or dynamic JS required for barcode anymore
});

// ── Copy reference code ──
function copyRef() {
  const code = document.getElementById('refCode').textContent;
  navigator.clipboard.writeText(code).catch(() => {});
  const btn  = document.querySelector('.copy-btn');
  const toast = document.getElementById('copyToast');
  btn.classList.add('copy-success');
  btn.innerHTML = `<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>`;
  toast.classList.add('show');
  setTimeout(() => {
    toast.classList.remove('show');
    btn.classList.remove('copy-success');
    btn.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>`;
  }, 2200);
}

// ── WhatsApp Chat ──
function openWAChat(e) {
  e.preventDefault();
  const name = document.getElementById('custName').innerText || 'Jamaah';
  const ref = document.getElementById('refCode').innerText || '---';
  const siteName = {!! json_encode($settings['site_name'] ?? 'PT. UMB') !!};
  const phone = {!! json_encode(preg_replace('/[^0-9]/', '', $settings['office_phone'] ?? '6281234567890')) !!};
  let cleanPhone = phone;
  if(cleanPhone.startsWith('0')) cleanPhone = '62' + cleanPhone.substring(1);
  const text = `Assalamu'alaikum Admin ${siteName}. Saya *${name}*, baru saja mendaftar perjalanan dengan Nomor Referensi: *${ref}*. Mohon informasi untuk langkah selanjutnya. Terima kasih.`;
  const url = `https://wa.me/${cleanPhone}?text=${encodeURIComponent(text)}`;
  window.open(url, '_blank');
}
</script>
</body>
</html>
