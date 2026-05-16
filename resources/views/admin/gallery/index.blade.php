@extends('admin.layout')

@section('page_title', 'Kelola Galeri Dokumentasi')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    :root {
        --dark-card: #0a1f16;
        --accent-green: #34d399;
        --text-gray: #94a3b8;
    }

    .gallery-admin-container {
        padding: 20px;
        background: #f1f5f9;
        min-height: 100vh;
        border-radius: 20px;
    }

    /* STATS ROW */
    .gallery-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card-premium {
        background: white;
        padding: 25px;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        gap: 20px;
        border: 1px solid rgba(0,0,0,0.02);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .icon-foto { background: rgba(52, 211, 153, 0.1); color: #10b981; }
    .icon-video { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }

    .stat-info h3 { font-size: 1.8rem; font-weight: 800; margin: 0; color: #1e293b; }
    .stat-info p { margin: 0; color: #64748b; font-size: 0.9rem; font-weight: 600; }

    /* ACTION BAR */
    .gallery-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .filter-tabs {
        display: flex;
        background: #e2e8f0;
        padding: 5px;
        border-radius: 12px;
        gap: 5px;
    }

    .tab-btn {
        padding: 8px 20px;
        border-radius: 8px;
        border: none;
        background: transparent;
        color: #64748b;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
    }

    .tab-btn.active {
        background: white;
        color: #1a5c3a;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    .btn-upload-premium {
        background: linear-gradient(135deg, #1a5c3a, #2d7a4d);
        color: white;
        padding: 12px 25px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 10px 20px rgba(26, 92, 58, 0.2);
        transition: all 0.3s;
        border: none;
        cursor: pointer;
    }

    .btn-upload-premium:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(26, 92, 58, 0.3);
    }

    /* MODAL STYLE */
    .modal-overlay-premium {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(5px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 2000;
        padding: 20px;
    }

    .modal-content-premium {
        background: white;
        width: 100%;
        max-width: 550px;
        border-radius: 24px;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        max-height: 90vh; /* BATASI TINGGI MAKSIMAL */
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .modal-body-premium {
        padding: 30px;
        overflow-y: auto; /* AKTIFKAN SCROLL JIKA KONTEN PANJANG */
        flex: 1;
    }

    .modal-footer-premium {
        padding: 20px 30px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .upload-preview-container {
        margin-top: 25px;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        background: #f1f5f9;
        display: none;
        max-height: 350px; /* BATASI TINGGI PREVIEW */
    }

    .upload-preview-container img, 
    .upload-preview-container video {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
    }

    @keyframes modalSlideUp {
        from { transform: translateY(50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .upload-zone {
        border: 2px dashed #e2e8f0;
        border-radius: 16px;
        padding: 40px 20px;
        text-align: center;
        transition: all 0.3s;
        cursor: pointer;
        position: relative;
    }
    .upload-zone:hover { border-color: #10b981; background: #f0fdf4; }
    .upload-zone i { font-size: 2.5rem; color: #10b981; margin-bottom: 15px; }
    .upload-zone p { color: #64748b; font-size: 0.9rem; font-weight: 600; margin: 0; }

    /* GRID */
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
    }

    .media-card-admin {
        background: white;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
        transition: all 0.3s;
    }

    .media-card-admin:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08); }

    .media-preview {
        width: 100%;
        height: 180px;
        position: relative;
        background: #f8fafc;
    }

    .media-preview img, .media-preview video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .badge-type {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 4px 10px;
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(5px);
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 800;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .card-footer {
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .source-info { font-size: 0.75rem; color: #64748b; font-weight: 600; }
    
    .card-btns { display: flex; gap: 8px; }
    .btn-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
        color: #64748b;
        background: white;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-icon:hover { background: #f1f5f9; color: #1e293b; }
    .btn-delete:hover { background: #fef2f2; color: #ef4444; border-color: #fee2e2; }

    /* BULK ACTIONS BAR */
    .bulk-action-bar {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%) translateY(-100px);
        background: white;
        padding: 15px 30px;
        border-radius: 100px;
        box-shadow: 0 15px 50px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        gap: 30px;
        z-index: 3000;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 1px solid #e2e8f0;
    }

    .bulk-action-bar.active { transform: translateX(-50%) translateY(0); }

    .selected-count { font-weight: 800; color: #1a5c3a; font-size: 1rem; }
    
    .bulk-btns { display: flex; gap: 10px; }
    .btn-bulk-delete {
        background: #ef4444;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 50px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s;
    }
    .btn-bulk-delete:hover { background: #dc2626; transform: scale(1.05); }

    /* CHECKBOX STYLE */
    .media-card-admin { position: relative; }
    .card-checkbox {
        position: absolute;
        top: 15px;
        left: 15px;
        z-index: 10;
        width: 24px;
        height: 24px;
        cursor: pointer;
        accent-color: #10b981;
        opacity: 0;
        transition: opacity 0.3s;
    }
    .media-card-admin:hover .card-checkbox, .card-checkbox:checked { opacity: 1; }

    .select-all-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        color: #64748b;
        cursor: pointer;
        user-select: none;
        margin-bottom: 20px;
    }
    .select-all-wrapper input { width: 18px; height: 18px; accent-color: #10b981; }

</style>
@endpush

@section('content')
<div class="gallery-admin-container">
    
    <!-- BULK ACTION BAR -->
    <div id="bulkBar" class="bulk-action-bar">
        <div class="selected-count"><i class="fas fa-check-circle"></i> <span id="countText">0</span> data dipilih</div>
        <div class="bulk-btns">
            <button class="btn-bulk-show" onclick="submitBulkVisibility('show')" style="background: #10b981; color: white; border: none; padding: 10px 20px; border-radius: 50px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-eye"></i> Tampilkan
            </button>
            <button class="btn-bulk-hide" onclick="submitBulkVisibility('hide')" style="background: #64748b; color: white; border: none; padding: 10px 20px; border-radius: 50px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-eye-slash"></i> Sembunyikan
            </button>
            <button class="btn-bulk-delete" onclick="submitBulkDelete()">
                <i class="fas fa-trash"></i> Hapus
            </button>
            <button class="btn-cancel" onclick="unselectAll()" style="border-radius: 50px; padding: 10px 20px; border: 1px solid #e2e8f0; background: white; color: #64748b; font-weight: 700; cursor: pointer;">Batal</button>
        </div>
    </div>

    <!-- STATS -->
    <div class="gallery-stats">
        <div class="stat-card-premium">
            <div class="stat-icon icon-foto"><i class="fas fa-image"></i></div>
            <div class="stat-info">
                <h3>{{ $photoCount }}</h3>
                <p>Total Foto</p>
            </div>
        </div>
        <div class="stat-card-premium">
            <div class="stat-icon icon-video"><i class="fas fa-video"></i></div>
            <div class="stat-info">
                <h3>{{ $videoCount }}</h3>
                <p>Total Video</p>
            </div>
        </div>
    </div>

    <!-- ACTIONS -->
    <div class="gallery-actions">
        <div class="filter-tabs">
            <button class="tab-btn active" onclick="filterGallery('semua', this)">Semua</button>
            <button class="tab-btn" onclick="filterGallery('foto', this)">Foto</button>
            <button class="tab-btn" onclick="filterGallery('video', this)">Video</button>
        </div>

        <button type="button" class="btn-upload-premium" onclick="openUploadModal()">
            <i class="fas fa-plus-circle"></i> Tambah Media Baru
        </button>
    </div>

    <!-- SELECT ALL -->
    <label class="select-all-wrapper">
        <input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)">
        Pilih Semua Media
    </label>

    <!-- GRID -->
    <div class="gallery-grid">
        {{-- Sistem sekarang dinamis (Tanpa Batas), tidak butuh slot index lagi --}}

        @foreach($allMedia as $item)
            <div class="media-card-admin item-{{ $item['type'] }} {{ !($item['is_published'] ?? true) ? 'is-hidden' : '' }}" id="card-{{ $item['id'] }}">
                <input type="checkbox" class="card-checkbox" 
                       data-id="{{ $item['id'] }}" 
                       data-local="{{ isset($item['is_local']) ? 'true' : 'false' }}"
                       onclick="updateBulkBar()">
                
                <div class="media-preview">
                    <div class="badge-type">
                        <i class="fas {{ $item['type'] == 'foto' ? 'fa-image' : 'fa-play-circle' }}"></i>
                        {{ strtoupper($item['type']) }}
                    </div>
                    
                    @if($item['type'] == 'foto')
                        <img src="{{ $item['url'] }}?v={{ time() }}" alt="Media">
                    @else
                        <video muted>
                            <source src="{{ $item['url'] }}" type="video/mp4">
                        </video>
                    @endif
                </div>
                <div class="card-footer">
                    <div class="source-info">
                        <i class="fas {{ isset($item['is_local']) ? 'fa-folder' : 'fa-database' }}"></i>
                        {{ isset($item['is_local']) ? 'Local Folder' : 'Admin Setting' }}
                    </div>
                    <div class="card-btns">
                        @if(!isset($item['is_local']))
                            <button class="btn-icon btn-visibility" 
                                    title="{{ ($item['is_published'] ?? true) ? 'Sembunyikan dari Landing Page' : 'Tampilkan di Landing Page' }}" 
                                    onclick="toggleVisibility('{{ $item['id'] }}', this)">
                                <i class="fas {{ ($item['is_published'] ?? true) ? 'fa-eye' : 'fa-eye-slash' }}" 
                                   style="color: {{ ($item['is_published'] ?? true) ? '#10b981' : '#64748b' }}"></i>
                            </button>
                        @endif
                        <button class="btn-icon" title="Lihat" onclick="window.open('{{ $item['url'] }}', '_blank')">
                            <i class="fas fa-external-link-alt"></i>
                        </button>
                        @if(isset($item['source']) && $item['source'] == 'legacy')
                            <button class="btn-icon btn-delete" title="Hapus Data Lama" onclick="confirmDelete('{{ $item['id'] }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        @elseif(!isset($item['is_local']))
                            <button class="btn-icon btn-delete" title="Hapus" onclick="confirmDeleteDynamic('{{ $item['id'] }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        @else
                            <button class="btn-icon btn-delete" title="Hapus File Lokal" onclick="confirmDeleteLocal('{{ $item['filename'] }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- MODAL UPLOAD -->
<div id="uploadModal" class="modal-overlay-premium">
    <div class="modal-content-premium">
        <div class="modal-body-premium">
            <h3 style="font-weight: 800; font-size: 1.5rem; color: #1e293b; margin-bottom: 8px;">Tambah Media Baru</h3>
            <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 25px;">Unggah foto atau video perjalanan terbaru Anda.</p>

            <form id="uploadForm" action="{{ route('admin.gallery.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="redirect_to" value="gallery">
                
                <div class="upload-zone" id="dropArea" onclick="document.getElementById('fileInput').click()">
                    <input type="file" name="media_file" id="fileInput" hidden accept="image/*,video/*" onchange="previewFile(this)">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Klik untuk memilih Foto atau Video</p>
                    <p style="font-size: 0.7rem; color: #94a3b8; margin-top: 5px;">Sistem Galeri Tanpa Batas Aktif</p>
                </div>

                <div class="upload-preview-container" id="previewContainer">
                    <img id="imagePreview" src="" style="display:none;">
                    <div id="videoPreviewName" style="display:none; padding: 20px; text-align: center; color: #10b981; font-weight: 700;"></div>
                </div>
            </form>
        </div>
        
        <div class="modal-footer-premium">
            <button type="button" class="btn-cancel" onclick="closeUploadModal()" style="padding: 12px 24px; border-radius: 12px; border: 1px solid #e2e8f0; background: white; color: #64748b; font-weight: 600; cursor: pointer;">Batal</button>
            <button type="button" class="btn-save-premium" id="btnSubmit" onclick="submitUpload()" disabled style="padding: 12px 24px; border-radius: 12px; background: #10b981; color: white; border: none; font-weight: 700; cursor: pointer; opacity: 0.5;">Simpan & Unggah</button>
        </div>
    </div>
</div>

<!-- FORM TERSEMBUNYI UNTUK HAPUS (SYNC KE SETTINGS) -->
<form id="deleteForm" action="{{ route('admin.settings.update') }}" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="" id="deleteField" value="1">
</form>

<!-- FORM TERSEMBUNYI UNTUK HAPUS LOKAL -->
<form id="deleteLocalForm" action="{{ route('admin.gallery.delete-local') }}" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="filename" id="deleteLocalFilename">
</form>

<!-- FORM TERSEMBUNYI UNTUK BULK DELETE -->
<form id="bulkDeleteForm" action="{{ route('admin.gallery.bulk-delete') }}" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="items" id="bulkDeleteItems">
</form>

<!-- FORM TERSEMBUNYI UNTUK BULK VISIBILITY -->
<form id="bulkVisibilityForm" action="{{ route('admin.gallery.bulkVisibility') }}" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="ids[]" id="bulkVisIds">
    <input type="hidden" name="status" id="bulkVisStatus">
</form>

<style>
    .media-card-admin.is-hidden {
        opacity: 0.7;
        filter: grayscale(0.5);
    }
    .media-card-admin.is-hidden .media-preview::after {
        content: 'DISEMBUNYIKAN';
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0,0,0,0.4);
        color: white;
        font-weight: 800;
        font-size: 0.8rem;
        letter-spacing: 1px;
    }
</style>

<script>
    function toggleSelectAll(source) {
        const checkboxes = document.querySelectorAll('.card-checkbox');
        checkboxes.forEach(cb => {
            if(cb.parentElement.style.display !== 'none') {
                cb.checked = source.checked;
            }
        });
        updateBulkBar();
    }

    function unselectAll() {
        const selectAll = document.getElementById('selectAll');
        if(selectAll) selectAll.checked = false;
        
        const checkboxes = document.querySelectorAll('.card-checkbox');
        checkboxes.forEach(cb => cb.checked = false);
        updateBulkBar();
    }

    function updateBulkBar() {
        const checkboxes = document.querySelectorAll('.card-checkbox:checked');
        const bar = document.getElementById('bulkBar');
        const countText = document.getElementById('countText');

        if(checkboxes.length > 0) {
            bar.classList.add('active');
            countText.textContent = checkboxes.length;
        } else {
            bar.classList.remove('active');
        }
    }

    function submitBulkDelete() {
        const checkboxes = document.querySelectorAll('.card-checkbox:checked');
        if(checkboxes.length === 0) return;

        if (confirm(`Apakah Anda yakin ingin menghapus ${checkboxes.length} media terpilih secara permanen?`)) {
            const items = [];
            checkboxes.forEach(cb => {
                items.push({
                    id: cb.getAttribute('data-id'),
                    is_local: cb.getAttribute('data-local') === 'true'
                });
            });

            document.getElementById('bulkDeleteItems').value = JSON.stringify(items);
            document.getElementById('bulkDeleteForm').submit();
        }
    }

    function openUploadModal() {
        document.getElementById('uploadModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeUploadModal() {
        document.getElementById('uploadModal').style.display = 'none';
        document.body.style.overflow = '';
        
        // Reset Form
        document.getElementById('uploadForm').reset();
        document.getElementById('previewContainer').style.display = 'none';
        document.getElementById('imagePreview').style.display = 'none';
        document.getElementById('videoPreviewName').style.display = 'none';
        
        const btn = document.getElementById('btnSubmit');
        btn.disabled = true;
        btn.style.opacity = '0.5';
    }

    function previewFile(input) {
        const file = input.files[0];
        const container = document.getElementById('previewContainer');
        const img = document.getElementById('imagePreview');
        const vidName = document.getElementById('videoPreviewName');
        const btn = document.getElementById('btnSubmit');

        if (file) {
            container.style.display = 'block';
            btn.disabled = false;
            btn.style.opacity = '1';

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    img.src = e.target.result;
                    img.style.display = 'block';
                    vidName.style.display = 'none';
                };
                reader.readAsDataURL(file);
            } else if (file.type.startsWith('video/')) {
                img.style.display = 'none';
                vidName.textContent = '📹 Video Terpilih: ' + file.name;
                vidName.style.display = 'block';
            }
        }
    }

    function submitUpload() {
        document.getElementById('uploadForm').submit();
    }

    function toggleVisibility(id, btn) {
        const icon = btn.querySelector('i');
        const card = document.getElementById('card-' + id);
        
        fetch(`/admin/gallery/${id}/toggle-visibility`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.new_status) {
                    icon.className = 'fas fa-eye';
                    icon.style.color = '#10b981';
                    card.classList.remove('is-hidden');
                    btn.title = 'Sembunyikan dari Landing Page';
                } else {
                    icon.className = 'fas fa-eye-slash';
                    icon.style.color = '#64748b';
                    card.classList.add('is-hidden');
                    btn.title = 'Tampilkan di Landing Page';
                }
            }
        });
    }

    function submitBulkVisibility(status) {
        const checkboxes = document.querySelectorAll('.card-checkbox:checked');
        if (checkboxes.length === 0) return;

        const form = document.getElementById('bulkVisibilityForm');
        form.innerHTML = '@csrf'; // Reset but keep CSRF
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = status;
        form.appendChild(statusInput);

        checkboxes.forEach(cb => {
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'ids[]';
            idInput.value = cb.getAttribute('data-id');
            form.appendChild(idInput);
        });

        form.submit();
    }

    function filterGallery(type, btn) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const cards = document.querySelectorAll('.media-card-admin');
        cards.forEach(card => {
            if (type === 'semua' || card.classList.contains('item-' + type)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
                card.querySelector('.card-checkbox').checked = false; 
            }
        });
        updateBulkBar();
    }

    function confirmDeleteDynamic(id) {
        if (confirm('Apakah Anda yakin ingin menghapus media ini secara permanen?')) {
            const form = document.getElementById('bulkDeleteForm');
            const input = document.getElementById('bulkDeleteItems');
            input.value = JSON.stringify([{ id: id, is_local: false }]);
            form.submit();
        }
    }

    function confirmDelete(fieldName) {
        if (confirm('Apakah Anda yakin ingin menghapus data lama ini?')) {
            const form = document.getElementById('deleteForm');
            const input = document.getElementById('deleteField');
            input.name = 'delete_' + fieldName;
            form.submit();
        }
    }

    function confirmDeleteLocal(filename) {
        if (confirm('Apakah Anda yakin ingin menghapus file ini dari folder lokal secara permanen?')) {
            const form = document.getElementById('deleteLocalForm');
            const input = document.getElementById('deleteLocalFilename');
            input.value = filename;
            form.submit();
        }
    }

    // Video Hover Preview
    document.querySelectorAll('.media-card-admin.item-video').forEach(card => {
        const video = card.querySelector('video');
        if(video) {
            card.addEventListener('mouseenter', () => video.play());
            card.addEventListener('mouseleave', () => {
                video.pause();
                video.currentTime = 0;
            });
        }
    });
</script>
@endsection
