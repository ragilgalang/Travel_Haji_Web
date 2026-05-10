<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Portal — PT. UMI MUTHMAINAH BERKAH</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin-login.css') }}">
</head>
<body>

<div class="wrapper">

  <!-- LEFT PANEL -->
  <div class="panel-left">
    <div class="badge-top">Admin Portal</div>

    <div class="panel-left-body">
      <!-- Mosque SVG Illustration -->
      <svg class="mosque-illustration" viewBox="0 0 320 180" xmlns="http://www.w3.org/2000/svg">
        <!-- Sky / atmosphere -->
        <defs>
          <radialGradient id="skyGrad" cx="50%" cy="60%" r="70%">
            <stop offset="0%" stop-color="#1a7a4a" stop-opacity="0.3"/>
            <stop offset="100%" stop-color="#0a3522" stop-opacity="0"/>
          </radialGradient>
          <linearGradient id="domeGrad" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#2db366"/>
            <stop offset="100%" stop-color="#0d4a2f"/>
          </linearGradient>
          <linearGradient id="goldGrad" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#f5e6b8"/>
            <stop offset="100%" stop-color="#c9a84c"/>
          </linearGradient>
          <filter id="glow">
            <feGaussianBlur stdDeviation="2" result="coloredBlur"/>
            <feMerge><feMergeNode in="coloredBlur"/><feMergeNode in="SourceGraphic"/></feMerge>
          </filter>
        </defs>

        <ellipse cx="160" cy="160" rx="160" ry="40" fill="url(#skyGrad)"/>

        <!-- Ground -->
        <rect x="0" y="155" width="320" height="30" fill="#061a10" opacity="0.5"/>

        <!-- Main building body -->
        <rect x="80" y="110" width="160" height="50" fill="#0d4a2f" rx="2"/>
        <!-- Arched windows -->
        <ellipse cx="110" cy="120" rx="10" ry="14" fill="rgba(201,168,76,0.2)" stroke="#c9a84c" stroke-width="1"/>
        <rect x="100" y="120" width="20" height="14" fill="rgba(201,168,76,0.2)"/>
        <ellipse cx="160" cy="118" rx="12" ry="16" fill="rgba(201,168,76,0.25)" stroke="#c9a84c" stroke-width="1"/>
        <rect x="148" y="118" width="24" height="16" fill="rgba(201,168,76,0.25)"/>
        <ellipse cx="210" cy="120" rx="10" ry="14" fill="rgba(201,168,76,0.2)" stroke="#c9a84c" stroke-width="1"/>
        <rect x="200" y="120" width="20" height="14" fill="rgba(201,168,76,0.2)"/>

        <!-- Main dome -->
        <ellipse cx="160" cy="110" rx="44" ry="8" fill="#0a3522"/>
        <path d="M116 110 Q116 68 160 62 Q204 68 204 110 Z" fill="url(#domeGrad)"/>
        <!-- Dome highlight -->
        <path d="M128 100 Q135 75 160 68" stroke="rgba(255,255,255,0.15)" stroke-width="2" fill="none"/>
        <!-- Dome finial -->
        <rect x="157" y="52" width="6" height="14" fill="url(#goldGrad)" rx="1"/>
        <!-- Crescent on top -->
        <g filter="url(#glow)">
          <path d="M160 48 A8 8 0 1 1 174 48 A5 5 0 1 0 160 48 Z" fill="#c9a84c"/>
          <line x1="160" y1="44" x2="160" y2="38" stroke="#c9a84c" stroke-width="1.5"/>
          <circle cx="160" cy="36" r="2.5" fill="#c9a84c"/>
        </g>

        <!-- Left minaret -->
        <rect x="88" y="75" width="16" height="80" fill="#0a3522" rx="2"/>
        <ellipse cx="96" cy="75" rx="8" ry="4" fill="#1a7a4a"/>
        <path d="M88 75 Q96 55 104 75 Z" fill="url(#domeGrad)"/>
        <rect x="94" y="49" width="4" height="10" fill="url(#goldGrad)" rx="1"/>
        <path d="M96 46 A5 5 0 1 1 104 46 A3 3 0 1 0 96 46 Z" fill="#c9a84c" transform="scale(0.7) translate(40, 18)"/>

        <!-- Right minaret -->
        <rect x="216" y="75" width="16" height="80" fill="#0a3522" rx="2"/>
        <ellipse cx="224" cy="75" rx="8" ry="4" fill="#1a7a4a"/>
        <path d="M216 75 Q224 55 232 75 Z" fill="url(#domeGrad)"/>
        <rect x="222" y="49" width="4" height="10" fill="url(#goldGrad)" rx="1"/>

        <!-- Small side domes -->
        <path d="M80 110 Q80 96 95 93 Q110 96 110 110 Z" fill="#1a4a30"/>
        <path d="M210 110 Q210 96 225 93 Q240 96 240 110 Z" fill="#1a4a30"/>

        <!-- Lanterns / lights -->
        <circle cx="96" cy="105" r="2" fill="#f5e6b8" opacity="0.8"/>
        <circle cx="224" cy="105" r="2" fill="#f5e6b8" opacity="0.8"/>
        <circle cx="160" cy="108" r="3" fill="#f5e6b8" opacity="0.6"/>

        <!-- Pillars -->
        <rect x="82" y="110" width="4" height="50" fill="#0a3522"/>
        <rect x="234" y="110" width="4" height="50" fill="#0a3522"/>
      </svg>

      <h1>Manajemen PT. UMI<br>MUTHMAINAH BERKAH</h1>
      <p>Kelola paket, fasilitas, dan testimoni jemaah dengan mudah melalui portal admin terpadu kami.</p>
    </div>

    <div class="stats-row">
      <div class="stat-item">
        <span class="num">1.2K+</span>
        <span class="lbl">Jemaah Aktif</span>
      </div>
      <div class="stat-item">
        <span class="num">48</span>
        <span class="lbl">Paket Tersedia</span>
      </div>
      <div class="stat-item">
        <span class="num">98%</span>
        <span class="lbl">Kepuasan</span>
      </div>
    </div>

    <!-- Decorative crescent -->
    <svg class="deco-crescent" viewBox="0 0 52 52" xmlns="http://www.w3.org/2000/svg">
      <path d="M26 4 A22 22 0 1 1 48 26 A16 16 0 1 0 26 4 Z" fill="#c9a84c"/>
    </svg>
  </div>

  <!-- RIGHT PANEL -->
  <div class="panel-right">
    <div class="logo-area">
      <div class="logo-icon">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 2L4 7v10l8 5 8-5V7L12 2Z" stroke="white" stroke-width="1.5" stroke-linejoin="round"/>
          <path d="M12 8v8M8 10l4-2 4 2" stroke="#c9a84c" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <div class="logo-text">
        <div class="company">{{ $settings['site_name'] ?? 'PT. UMI MUTHMAINAH BERKAH' }}</div>
        <div class="tagline">Travel Haji & Umrah Terpercaya</div>
      </div>
    </div>

    <div class="greeting">
      <h2>Selamat Datang<em>!</em></h2>
      <p>Silakan masuk untuk mengelola sistem travel haji dan umrah Anda.</p>
    </div>

    <div class="divider"></div>

    <form action="{{ route('login.authenticate') }}" method="POST" id="loginForm">
        @csrf
        <div class="form-group">
          <label>
            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
              <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
            </svg>
            Username atau Email
          </label>
          <div class="input-wrap @error('login') has-error @enderror">
            <input type="text" id="login" name="login" value="{{ old('login') }}" placeholder="Masukkan username atau email" required autofocus autocomplete="off">
          </div>
          @error('login')
            <div class="error-text">{{ $message }}</div>
          @enderror
        </div>
    
        <div class="form-group">
          <label>
            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
            </svg>
            Kata Sandi
          </label>
          <div class="input-wrap @error('password') has-error @enderror">
            <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="new-password">
            <button class="eye-btn" type="button" onclick="togglePwd()" id="eyeBtn" title="Tampilkan/sembunyikan">
              <svg id="eyeIcon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
            </button>
          </div>
          @error('password')
            <div class="error-text">{{ $message }}</div>
          @enderror
        </div>
    
        <div class="row-options">
          <label class="remember">
            <input type="checkbox" id="rememberMe" name="remember" {{ old('remember') ? 'checked' : '' }}>
            <span class="check-box"></span>
            <span>Ingat saya</span>
          </label>
          <a href="javascript:void(0)" onclick="showForgotAlert()" class="forgot">Lupa kata sandi?</a>
        </div>
    
        <button type="submit" class="btn-login" id="loginBtn">
          <span class="btn-text">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Masuk ke Dashboard
          </span>
        </button>
    </form>

    <div class="footer-note">
      <span>
        &copy; {{ date('Y') }} PT. UMI MUTHMAINAH BERKAH, SIDOKARE, SIDOARJO. Seluruh Hak Cipta Dilindungi.
      </span>
    </div>
  </div>

</div>

<script>
  function togglePwd() {
    const pwd = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    const isText = pwd.type === 'text';
    pwd.type = isText ? 'password' : 'text';
    icon.innerHTML = isText
      ? '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>'
      : '<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
  }

  const loginForm = document.getElementById('loginForm');
  loginForm.addEventListener('submit', function(e) {

    const btn = document.getElementById('loginBtn');
    btn.classList.add('loading');
    btn.style.pointerEvents = 'none';
  });

  // Gentle input focus animations
  document.querySelectorAll('input[type="email"], input[type="password"]').forEach(el => {
    el.addEventListener('focus', () => el.parentElement.classList.add('input-focus-zoom'));
    el.addEventListener('blur', () => el.parentElement.classList.remove('input-focus-zoom'));
  });

  function showForgotAlert() {
    alert("Demi keamanan akun, silakan hubungi Administrator Sistem secara langsung di kantor untuk melakukan reset kata sandi Anda. Terima kasih.");
  }
</script>
</body>
</html>