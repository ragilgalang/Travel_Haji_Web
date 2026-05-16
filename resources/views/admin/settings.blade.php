@extends('admin.layout')

@section('page_title', 'Visual Editor — Travel Haji')

@section('content')
    <style>
        /* ABSOLUTE KILLER FOR ADMIN UI */
        .main-sidebar,
        .main-header,
        .content-header,
        .main-footer,
        aside.main-sidebar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }

        .content-wrapper,
        .wrapper,
        body,
        html {
            margin-left: 0 !important;
            padding: 0 !important;
            margin-top: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            overflow: hidden !important;
            background: #0f172a !important;
        }

        .content {
            padding: 0 !important;
            margin: 0 !important;
        }

        .visual-editor-container {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            z-index: 9999 !important;
        }
    </style>

    <script>
        // JS Fallback to remove elements that CSS might miss
        document.addEventListener('DOMContentLoaded', function () {
            const toKill = ['.main-sidebar', '.main-header', '.content-header', '.main-footer', 'aside.main-sidebar'];
            toKill.forEach(s => {
                const el = document.querySelector(s);
                if (el) el.remove();
            });
            document.body.classList.remove('sidebar-mini', 'sidebar-open');
            document.body.classList.add('sidebar-collapse');
        });
    </script>

    <link rel="stylesheet" href="{{ asset('css/admin-pengaturan.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <div class="visual-editor-container">
        <!-- 1. TOP FLOATING BAR (THE ONLY CONTROLS) -->
        <div class="editor-top-bar">
            <button class="exit-btn" onclick="window.location.href='{{ route('admin.dashboard') }}'"
                title="Kembali ke Dashboard">
                <i class="fas fa-times"></i>
            </button>
            <div class="v-divider"></div>
            <div class="device-switcher">
                <button class="device-btn active" onclick="setDevice('desktop')"><i class="fas fa-desktop"></i></button>
                <button class="device-btn" onclick="setDevice('tablet')"><i class="fas fa-tablet-alt"></i></button>
                <button class="device-btn" onclick="setDevice('mobile')"><i class="fas fa-mobile-alt"></i></button>
            </div>
            <div class="v-divider"></div>
            <button type="button" class="settings-btn"
                onclick="document.getElementById('globalSettingsModal').style.setProperty('display', 'flex', 'important'); console.log('Settings clicked');"
                title="Pengaturan Detail (Logo, WA, SEO)">
                <i class="fas fa-cog"></i>
            </button>
            <div class="v-divider"></div>
            <button type="button" onclick="document.getElementById('updateBtn').click()" class="save-btn-floating">
                <i class="fas fa-check"></i> Simpan
            </button>
        </div>

        <!-- 2. MAIN CANVAS (FULL SCREEN) -->
        <div class="editor-canvas">
            <div id="iframe-wrapper" class="iframe-wrapper desktop">
                <iframe id="mainIframe" src="{{ route('admin.settings.preview') }}?preview=1" class="main-iframe"></iframe>
            </div>
        </div>
    </div>

    <!-- 3. GLOBAL SETTINGS MODAL (HIDDEN BY DEFAULT) -->
    <div id="globalSettingsModal" class="editor-modal-overlay" style="display:none;">
        <div class="editor-modal-card">
            <div class="modal-header-minimal">
                <h3>Pengaturan Detail</h3>
                <button type="button" onclick="closeGlobalSettings()"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body-minimal">
                <div class="nav-pills-minimal">
                    <button class="pill-btn-m active" onclick="openTabM(event, 'tab-umum')">Umum</button>
                    <button class="pill-btn-m" onclick="openTabM(event, 'tab-hero')">🖼️ Gambar</button>
                    <button class="pill-btn-m" onclick="openTabM(event, 'tab-about')">📖 Tentang</button>
                    <button class="pill-btn-m" onclick="openTabM(event, 'tab-footer')">Footer</button>

                </div>
                <form id="settingsForm" action="{{ route('admin.settings.update') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="submit" id="updateBtn" style="display: none;">

                    @include('admin.settings.partials.general')
                    @include('admin.settings.partials.hero')
                    @include('admin.settings.partials.stats')
                    @include('admin.settings.partials.sections')
                    @include('admin.settings.partials.about')
                    @include('admin.settings.partials.jadwal')

                    @include('admin.settings.partials.regform')
                    @include('admin.settings.partials.footer')

                </form>
            </div>
        </div>
    </div>

    <script>
        function openGlobalSettings() {
            console.log('Opening settings...');
            const modal = document.getElementById('globalSettingsModal');
            if (modal) {
                modal.style.display = 'flex';
            } else {
                alert('Modal settings tidak ditemukan!');
            }
        }
        function closeGlobalSettings() { document.getElementById('globalSettingsModal').style.display = 'none'; }

        function setDevice(device) {
            const wrapper = document.getElementById('iframe-wrapper');
            if (!wrapper) return;
            wrapper.className = 'iframe-wrapper ' + device;
            document.querySelectorAll('.device-btn').forEach(btn => btn.classList.remove('active'));
            const iconClass = device === 'desktop' ? 'fa-desktop' : (device === 'tablet' ? 'fa-tablet-alt' : 'fa-mobile-alt');
            const targetBtn = document.querySelector(`.device-btn i.${iconClass}`);
            if (targetBtn) targetBtn.parentElement.classList.add('active');
        }

        function openTabM(evt, tabName) {
            document.querySelectorAll(".tab-pane").forEach(p => p.classList.remove("active"));
            document.querySelectorAll(".pill-btn-m").forEach(b => b.classList.remove("active"));
            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.classList.add("active");
        }

        window.addEventListener('message', function (event) {
            const data = event.data;

            if (data.type === 'INLINE_CHANGE') {
                const input = document.querySelector(`[data-sync-target="${data.target}"]`) || document.querySelector(data.target);
                if (input) {
                    input.value = data.value;
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }

            if (data.type === 'PICK_IMAGE') {
                openGlobalSettings();

                // Map the target to a tab and an input
                const mappings = {
                    'logo': { tab: 'tab-umum', input: 'logo' },
                    'hero_image': { tab: 'tab-hero', input: 'hero_image' },
                    'about_image': { tab: 'tab-about', input: 'about_image' }
                };

                const map = mappings[data.target];
                if (map) {
                    // Open the correct tab
                    openTabM(null, map.tab);

                    // Trigger the file input
                    const fileInput = document.querySelector(`input[name="${map.input}"]`);
                    if (fileInput) {
                        fileInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        fileInput.style.outline = '4px solid #10b981';
                        setTimeout(() => fileInput.style.outline = 'none', 2000);
                        fileInput.click(); // Open file dialog!
                    }
                }
            }
        });

        // Close modal on click outside
        window.onclick = function (event) {
            const modal = document.getElementById('globalSettingsModal');
            if (event.target == modal) closeGlobalSettings();
        }
    </script>
@endsection