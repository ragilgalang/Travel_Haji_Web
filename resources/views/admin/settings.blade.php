@extends('admin.layout')

@section('page_title', 'Visual Editor — Travel Haji')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/settings.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Sembunyikan TOTAL semua elemen dashboard admin */
        .sidebar, .topbar, .admin-footer, .sidebar-overlay, .hamburger-btn, .breadcrumb,
        .main-header, .main-sidebar, .content-header, .main-footer, .topbar-left, .topbar-right,
        aside, header, footer { 
            display: none !important; 
            visibility: hidden !important;
            height: 0 !important;
            width: 0 !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }
        
        .main, .content-wrapper, .main-content { 
            margin: 0 !important; 
            padding: 0 !important;
            width: 100vw !important;
            min-height: 100vh !important;
            border: none !important;
            left: 0 !important;
            position: absolute !important;
            top: 0 !important;
        }
        
        body { 
            overflow: hidden !important; 
            background: #0f172a !important; 
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .visual-editor-container { 
            height: 100vh; 
            width: 100vw; 
            display: flex; 
            flex-direction: column; 
            position: fixed; 
            inset: 0; 
            z-index: 999999; 
            background: #0f172a;
        }

        /* ── BOTTOM BAR (FIXED AT BOTTOM RIGHT) ── */
        .editor-bottom-bar {
            position: absolute; bottom: 30px; right: 30px; 
            z-index: 2000000 !important; background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(25px);
            padding: 10px 15px; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.4);
            display: flex; align-items: center; gap: 15px; border: 1px solid rgba(255,255,255,0.7);
            animation: slideInRight 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            pointer-events: auto !important;
        }

        .editor-modal-overlay {
            z-index: 3000000 !important;
        }

        .settings-btn, .save-btn-floating, .device-btn, .exit-btn {
            pointer-events: auto !important;
            cursor: pointer !important;
        }

        @keyframes slideInRight { from { transform: translateX(100px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    </style>
    <script>
        // Fungsi pembersihan agresif
        function nukeAdminUI() {
            const selectors = [
                '.sidebar', '.topbar', '.admin-footer', '.sidebar-overlay', 
                '.hamburger-btn', '.breadcrumb', '.main-header', '.main-sidebar', 
                '.content-header', '.main-footer', 'aside', 'header', 'footer'
            ];
            selectors.forEach(s => {
                document.querySelectorAll(s).forEach(el => {
                    if (!el.classList.contains('visual-editor-container')) {
                        el.style.display = 'none';
                        el.remove();
                    }
                });
            });
            
            // Paksa container utama ke full width
            const main = document.querySelector('.main') || document.querySelector('.content-wrapper');
            if (main) {
                main.style.margin = '0';
                main.style.padding = '0';
                main.style.width = '100vw';
                main.style.left = '0';
            }
        }

        // Jalankan beberapa kali untuk memastikan elemen dinamis juga hilang
        nukeAdminUI();
        window.addEventListener('load', nukeAdminUI);
        setInterval(nukeAdminUI, 500); 
    </script>
@endpush

@section('content')

    <div class="visual-editor-container">
        <!-- 1. MAIN CANVAS (FULL SCREEN) -->
        <div class="editor-canvas">
            <div id="iframe-wrapper" class="iframe-wrapper desktop">
                <iframe id="mainIframe" src="{{ route('admin.settings.preview') }}?preview=1" class="main-iframe"></iframe>
            </div>
        </div>
        
        <!-- 2. EDITOR BAR (FLOATING AT BOTTOM) -->
        <div class="editor-bottom-bar">
            <button class="exit-btn" title="Keluar" onclick="window.location.href='{{ route('admin.dashboard') }}'">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="device-switcher">
                <button class="device-btn active" id="btn-desktop" onclick="setDevice('desktop')" title="Desktop View">
                    <i class="fas fa-desktop"></i>
                </button>
                <button class="device-btn" id="btn-tablet" onclick="setDevice('tablet')" title="Tablet View">
                    <i class="fas fa-tablet-alt"></i>
                </button>
                <button class="device-btn" id="btn-mobile" onclick="setDevice('mobile')" title="Mobile View">
                    <i class="fas fa-mobile-alt"></i>
                </button>
            </div>

            <button type="button" class="settings-btn" onclick="openGlobalSettings()" title="Pengaturan Lanjut">
                <i class="fas fa-cog"></i>
            </button>

            <!-- Tombol Simpan Tunggal (Cerdas) -->
            <button type="button" onclick="performFinalSave()" class="save-btn-floating">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </div>

    <script>
        function performFinalSave() {
            const saveBtn = document.querySelector('.save-btn-floating');
            const originalHtml = saveBtn.innerHTML;
            
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyiapkan...';
            saveBtn.style.pointerEvents = 'none';
            saveBtn.style.opacity = '0.7';

            try {
                // 1. Ambil data dari Editor Visual (Iframe)
                const iframe = document.getElementById('mainIframe');
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                const syncElements = iframeDoc.querySelectorAll('[id^="sync-"]');

                // Sinkronkan ke input tersembunyi
                syncElements.forEach(function(el) {
                    if (el.contentEditable === 'true') {
                        const field = el.id.replace('sync-', '');
                        if (field) {
                            let input = document.querySelector(`input[name="${field}"]`);
                            if (!input) {
                                input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = field;
                                document.getElementById('settingsForm').appendChild(input);
                            }
                            input.value = el.innerText.trim();
                        }
                    }
                });

                // 2. Gunakan XMLHttpRequest untuk upload dengan Progress Bar
                const form = document.getElementById('settingsForm');
                const formData = new FormData(form);
                const xhr = new XMLHttpRequest();

                // Lacak progres upload
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        saveBtn.innerHTML = `<i class="fas fa-upload"></i> Mengunggah ${percent}%...`;
                        saveBtn.style.background = `linear-gradient(90deg, #10b981 ${percent}%, #1e293b ${percent}%)`;
                    }
                });

                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            saveBtn.innerHTML = '<i class="fas fa-check"></i> Berhasil Disimpan!';
                            saveBtn.style.background = '#10b981';
                            setTimeout(() => {
                                // Refresh halaman untuk melihat hasil
                                window.location.reload();
                            }, 1500);
                        } else {
                            console.error('Upload Error:', xhr.status, xhr.responseText);
                            let msg = 'Gagal menyimpan (Error ' + xhr.status + ')';
                            if (xhr.status === 413) msg = 'File TERLALU BESAR! (Batas server terlampaui)';
                            
                            alert(msg);
                            saveBtn.innerHTML = '<i class="fas fa-times"></i> Gagal';
                            saveBtn.style.background = '#ef4444';
                            saveBtn.style.pointerEvents = 'auto';
                            saveBtn.style.opacity = '1';
                            
                            setTimeout(() => {
                                saveBtn.innerHTML = originalHtml;
                                saveBtn.style.background = '';
                            }, 3000);
                        }
                    }
                };

                xhr.open('POST', form.action, true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.send(formData);

            } catch (e) {
                console.error('Exception:', e);
                alert('Terjadi kesalahan teknis: ' + e.message);
                saveBtn.innerHTML = originalHtml;
                saveBtn.style.pointerEvents = 'auto';
                saveBtn.style.opacity = '1';
            }
        }
    </script>

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
                if (!iframe) throw new Error('Elemen Iframe tidak ditemukan!');
                
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                if (!iframeDoc) throw new Error('Dokumen Iframe tidak dapat diakses!');

                const payload = {};
                const syncElements = iframeDoc.querySelectorAll('[id^="sync-"]');

                syncElements.forEach(function(el) {
                    if (el.contentEditable === 'true') {
                        const field = el.id.replace('sync-', '');
                        let value;
                        // Gunakan innerText agar data lebih ringan (hanya teks, bukan HTML/Gambar)
                        value = el.innerText.trim();
                        
                        if (field) {
                            payload[field] = value;
                        }
                    }
                });

                // Gabungkan dengan data form existing (file uploads, dll dari modal)
                const mainForm = document.getElementById('settingsForm');
                if (!mainForm) throw new Error('Form pengaturan (settingsForm) tidak ditemukan!');
                
                const formData = new FormData(mainForm);

                // Override dengan data segar dari editor
                for (const [key, value] of Object.entries(payload)) {
                    formData.set(key, value);
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

                // Tingkatkan timeout ke 60 detik (sangat penting untuk data besar)
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 60000);

                // Kirim ke server via AJAX
                fetch('{{ route("admin.settings.update") }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    signal: controller.signal
                })
                .then(function(res) {
                    clearTimeout(timeoutId);
                    if (!res.ok) throw new Error('Server error: ' + res.status);
                    
                    const contentType = res.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return res.json();
                    }
                    return { success: true };
                })
                .then(function(result) {
                    if (result.success !== false) {
                        saveBtn.innerHTML = '<i class="fas fa-check"></i> Tersimpan!';
                        saveBtn.style.background = '#10b981';
                        
                        // Beri tahu dashboard untuk lanjut polling
                        window.isSavingSettings = false;

                        setTimeout(function() {
                            iframe.src = iframe.src;
                            saveBtn.innerHTML = '<i class="fas fa-save"></i> Simpan';
                            saveBtn.style.background = '';
                            saveBtn.style.pointerEvents = '';
                            saveBtn.style.opacity = '';
                        }, 1500);
                    } else {
                        throw new Error(result.message || 'Gagal menyimpan');
                    }
                })
                .catch(function(err) {
                    clearTimeout(timeoutId);
                    window.isSavingSettings = false;
                    console.error('[Save] Error:', err);
                    
                    let errMsg = err.name === 'AbortError' ? 'Koneksi Timeout (Firebase lambat)' : err.message;
                    
                    saveBtn.innerHTML = '<i class="fas fa-times"></i> Gagal!';
                    saveBtn.style.background = '#ef4444';
                    alert('Gagal menyimpan: ' + errMsg);
                    
                    setTimeout(function() {
                        saveBtn.innerHTML = '<i class="fas fa-save"></i> Simpan';
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