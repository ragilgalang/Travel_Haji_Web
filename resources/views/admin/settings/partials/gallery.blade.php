<!-- TAB: GALERI -->
<div id="tab-gallery" class="tab-pane card">
    <h2 class="card-title" style="margin-bottom:6px;">🖼️ Galeri Perjalanan</h2>
    <p style="font-size:12px; color:#94a3b8; margin-bottom:20px;">
        Upload foto &amp; video momen jemaah untuk ditampilkan di galeri website.
    </p>

    {{-- BATAS UPLOAD INFO --}}
    <div class="gallery-limit-info">
        <div class="limit-badge">📸 Foto: maks. <strong>20 foto</strong> · maks. <strong>5 MB/foto</strong></div>
        <div class="limit-badge">🎬 Video: maks. <strong>10 video</strong> · maks. <strong>50 MB/video</strong></div>
    </div>

    {{-- FOTO GALERI --}}
    <div class="gallery-section-header">📸 Upload Foto Galeri (1–20)</div>
    <div class="gallery-grid-photos">
        @for($i = 1; $i <= 20; $i++)
            <div class="gallery-photo-card">
                <label class="form-label">Foto {{ $i }}</label>
                @if(!empty($settings['gallery_img_' . $i]))
                    <img src="{{ $settings['gallery_img_' . $i] }}" class="gallery-preview-img">
                    <label class="delete-photo-label">
                        <input type="checkbox" name="delete_gallery_img_{{ $i }}" value="1"> 🗑️ Hapus
                    </label>
                @endif
                <input type="file" name="gallery_img_{{ $i }}" class="form-control input-p6"
                    accept="image/jpeg,image/png,image/webp" style="margin-top:6px;">
            </div>
        @endfor
    </div>
    <span class="help-block" style="margin-bottom:24px; display:block;">Format: JPG, PNG, WEBP. Maks. 5 MB per foto.</span>

    {{-- VIDEO GALERI --}}
    <div class="gallery-section-header" style="background:#0f172a; color:#94a3b8; border-color:#1e293b;">🎬 Upload Video Galeri (1–10)</div>
    <div class="gallery-grid-videos">
        @for($i = 1; $i <= 10; $i++)
            <div class="upload-section-video" style="margin-bottom:12px;">
                <label class="upload-label-video">Video {{ $i }}</label>
                @if(!empty($settings['gallery_video_' . $i]))
                    <div class="video-preview-box">
                        <video src="{{ $settings['gallery_video_' . $i] }}" controls class="video-preview-player"></video>
                        <label class="delete-photo-label" style="margin-top:6px; color:#94a3b8;">
                            <input type="checkbox" name="delete_gallery_video_{{ $i }}" value="1"> 🗑️ Hapus
                        </label>
                    </div>
                @endif
                <input type="file" name="gallery_video_{{ $i }}" class="form-control"
                    accept="video/mp4,video/webm" style="margin-top:8px;">
            </div>
        @endfor
    </div>
    <span class="help-block" style="display:block;">Format: MP4, WebM. Maks. 50 MB per video.</span>
</div>
