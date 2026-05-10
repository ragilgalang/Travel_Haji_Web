@extends('admin.layout')

@section('page_title', 'Visual Editor — Travel Haji')

@section('content')
/* CSS Editor sudah dipindahkan ke admin-pengaturan.css */

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

    <link rel="stylesheet" href="{{ asset('css/admin/settings.css') }}">
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
            <button type="button" onclick="submitSettingsForm()" class="save-btn-floating">
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
                    <button class="pill-btn-m" onclick="openTabM(event, 'tab-jadwal')">📅 Jadwal</button>
                    <button class="pill-btn-m" onclick="openTabM(event, 'tab-gallery')">🎞️ Galeri</button>
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
                    @include('admin.settings.partials.gallery')

                    @include('admin.settings.partials.regform')
                    @include('admin.settings.partials.footer')

                </form>
            </div>
        </div>
    </div>

    <script>
        function submitSettingsForm() {
            const saveBtn = document.querySelector('.save-btn-floating');
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            saveBtn.style.pointerEvents = 'none';
            saveBtn.style.opacity = '0.7';

            try {
                // Langsung baca DOM iframe (same-origin - tidak perlu postMessage)
                const iframe = document.getElementById('mainIframe');
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;

                const payload = {};
                iframeDoc.querySelectorAll('[id^="sync-"]').forEach(function(el) {
                    // Hanya elemen yang benar-benar bisa diedit
                    if (el.contentEditable === 'true') {
                        const field = el.id.replace('sync-', '');
                        let value;
                        if (['H1','H2','H3'].includes(el.tagName)) {
                            value = el.innerHTML.trim();
                        } else {
                            value = el.innerText.trim();
                        }
                        if (field) {
                            payload[field] = value;
                            console.log('[Save] Collected:', field, '=', value.substring(0, 50));
                        }
                    }
                });

                console.log('[Save] Total fields collected:', Object.keys(payload).length);

                // Gabungkan dengan data form existing (file uploads, dll dari modal)
                const formData = new FormData(document.getElementById('settingsForm'));

                // Override dengan data segar dari editor
                for (const [key, value] of Object.entries(payload)) {
                    formData.set(key, value);
                }

                // Kirim ke server via AJAX
                fetch('{{ route("admin.settings.update") }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function(res) {
                    // Handle redirect or JSON response
                    const contentType = res.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return res.json();
                    }
                    // Jika bukan JSON (misalnya redirect), anggap sukses
                    return { success: true };
                })
                .then(function(result) {
                    if (result.success !== false) {
                        saveBtn.innerHTML = '<i class="fas fa-check"></i> Tersimpan!';
                        saveBtn.style.background = '#10b981';
                        setTimeout(function() {
                            // Reload iframe untuk tampilkan data terbaru
                            iframe.src = iframe.src;
                            saveBtn.innerHTML = '<i class="fas fa-check"></i> Simpan';
                            saveBtn.style.background = '';
                            saveBtn.style.pointerEvents = '';
                            saveBtn.style.opacity = '';
                        }, 1500);
                    } else {
                        throw new Error(result.message || 'Gagal menyimpan');
                    }
                })
                .catch(function(err) {
                    console.error('[Save] Error:', err);
                    saveBtn.innerHTML = '<i class="fas fa-times"></i> Gagal!';
                    saveBtn.style.background = '#ef4444';
                    setTimeout(function() {
                        saveBtn.innerHTML = '<i class="fas fa-check"></i> Simpan';
                        saveBtn.style.background = '';
                        saveBtn.style.pointerEvents = '';
                        saveBtn.style.opacity = '';
                    }, 2500);
                });

            } catch(e) {
                console.error('[Save] Exception:', e);
                saveBtn.innerHTML = '<i class="fas fa-times"></i> Error!';
                setTimeout(function() {
                    saveBtn.innerHTML = '<i class="fas fa-check"></i> Simpan';
                    saveBtn.style.pointerEvents = '';
                    saveBtn.style.opacity = '';
                }, 2500);
            }
        }

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

            // ── TERIMA DATA DARI IFRAME DAN SIMPAN KE FIREBASE ──
            if (data.type === 'SAVE_DATA') {
                const editorPayload = data.payload;
                console.log('[Admin] Menerima data dari iframe:', editorPayload);

                // Ambil data form existing (file uploads, dll)
                const formData = new FormData(document.getElementById('settingsForm'));

                // Timpa dengan data segar dari editor
                for (const [key, value] of Object.entries(editorPayload)) {
                    formData.set(key, value);
                    console.log('[Admin] Set field:', key, '=', value);
                }

                // Kirim ke server via AJAX
                fetch('{{ route("admin.settings.update") }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(result => {
                    const saveBtn = document.querySelector('.save-btn-floating');
                    if (result.success) {
                        saveBtn.innerHTML = '<i class="fas fa-check"></i> Tersimpan!';
                        saveBtn.style.background = '#10b981';
                        // Reload iframe untuk tampilkan data terbaru
                        setTimeout(() => {
                            document.getElementById('mainIframe').src = document.getElementById('mainIframe').src;
                            saveBtn.innerHTML = '<i class="fas fa-check"></i> Simpan';
                            saveBtn.style.background = '';
                            saveBtn.style.pointerEvents = '';
                            saveBtn.style.opacity = '';
                        }, 1500);
                    } else {
                        saveBtn.innerHTML = '<i class="fas fa-times"></i> Gagal!';
                        saveBtn.style.background = '#ef4444';
                        setTimeout(() => {
                            saveBtn.innerHTML = '<i class="fas fa-check"></i> Simpan';
                            saveBtn.style.background = '';
                            saveBtn.style.pointerEvents = '';
                            saveBtn.style.opacity = '';
                        }, 2000);
                    }
                })
                .catch(err => {
                    console.error('[Admin] Gagal menyimpan:', err);
                    const saveBtn = document.querySelector('.save-btn-floating');
                    saveBtn.innerHTML = '<i class="fas fa-times"></i> Error!';
                    setTimeout(() => {
                        saveBtn.innerHTML = '<i class="fas fa-check"></i> Simpan';
                        saveBtn.style.pointerEvents = '';
                        saveBtn.style.opacity = '';
                    }, 2000);
                });
                return; // Jangan proses INLINE_CHANGE
            }

            if (data.type === 'INLINE_CHANGE') {
                // Tetap simpan ke hidden input sebagai backup
                let input = null;
                if (data.field) input = document.querySelector(`[name="${data.field}"]`);
                if (input) input.value = data.value;
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