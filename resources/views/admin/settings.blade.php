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
            <button type="button" onclick="doSave()" class="save-btn-floating">
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

        // ── KAMUS PERUBAHAN INLINE dari IFRAME ──
        // Menyimpan semua perubahan teks yang diedit langsung di editor
        const inlineChanges = {};

        window.addEventListener('message', function (event) {
            const data = event.data;

            if (data.type === 'INLINE_CHANGE') {
                // 1. Simpan ke kamus (pasti tersimpan, tidak bergantung DOM)
                inlineChanges[data.target] = data.value;

                // 2. Coba update form input secara real-time (best effort)
                const input = document.querySelector(`[data-sync-target="${data.target}"]`)
                           || document.querySelector(data.target);
                if (input) {
                    input.value = data.value;
                }

                // 3. Tandai bahwa ada perubahan yang belum disimpan
                const saveBtn = document.querySelector('.save-btn-floating');
                if (saveBtn && !saveBtn.dataset.dirty) {
                    saveBtn.dataset.dirty = '1';
                    saveBtn.style.background = '#d97706'; // Warna orange = ada perubahan
                    saveBtn.title = 'Ada perubahan yang belum disimpan';
                }
            }

            if (data.type === 'PICK_IMAGE') {
                openGlobalSettings();

                const mappings = {
                    'logo': { tab: 'tab-umum', input: 'logo' },
                    'hero_image': { tab: 'tab-hero', input: 'hero_image' },
                    'about_image': { tab: 'tab-about', input: 'about_image' }
                };

                const map = mappings[data.target];
                if (map) {
                    openTabM({ currentTarget: document.querySelector('.pill-btn-m') }, map.tab);
                    const fileInput = document.querySelector(`input[name="${map.input}"]`);
                    if (fileInput) {
                        fileInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        fileInput.style.outline = '4px solid #10b981';
                        setTimeout(() => fileInput.style.outline = 'none', 2000);
                        fileInput.click();
                    }
                }
            }

            if (data.type === 'SYNC_COLOR' || data.type === 'SYNC_FONT_SIZE' || data.type === 'SYNC_SCROLL') {
                // Forward to iframe jika diperlukan
                const iframe = document.getElementById('mainIframe');
                if (iframe && iframe.contentWindow) {
                    iframe.contentWindow.postMessage(data, '*');
                }
            }
        });

        // ── FUNGSI SIMPAN: Inject hidden inputs SEBELUM submit ──
        function doSave() {
            const form = document.getElementById('settingsForm');
            if (!form) return;

            // Hapus hidden input inline changes lama (kalau ada)
            form.querySelectorAll('input[data-inline-injected]').forEach(el => el.remove());

            // Untuk setiap perubahan inline, update textarea jika ada,
            // atau tambahkan hidden input agar nilai ikut terkirim ke server
            Object.entries(inlineChanges).forEach(([target, value]) => {
                // Cari form input yang punya data-sync-target cocok
                const formField = form.querySelector(`[data-sync-target="${target}"]`);
                if (formField) {
                    formField.value = value;
                } else {
                    // Tidak ada form field → buat hidden input
                    // target biasanya "#sync-hero_title" → ubah ke "hero_title"
                    const fieldName = target.replace(/^#sync-/, '');
                    // Hindari duplikat dengan input yang sudah ada
                    if (!form.querySelector(`[name="${fieldName}"]`)) {
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = fieldName;
                        hidden.value = value;
                        hidden.dataset.inlineInjected = '1';
                        form.appendChild(hidden);
                    } else {
                        // Update nilai input yang sudah ada
                        const existing = form.querySelector(`[name="${fieldName}"]`);
                        if (existing && existing.tagName !== 'INPUT' || existing.type !== 'file') {
                            existing.value = value;
                        }
                    }
                }
            });

            // Submit form
            document.getElementById('updateBtn').click();
        }

        // Close modal on click outside
        window.onclick = function (event) {
            const modal = document.getElementById('globalSettingsModal');
            if (event.target == modal) closeGlobalSettings();
        }
    </script>
@endsection