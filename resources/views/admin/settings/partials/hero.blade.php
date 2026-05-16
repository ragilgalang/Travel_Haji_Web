<!-- TAB: HERO -->
<div id="tab-hero" class="tab-pane card">
    <h2 class="card-title mb-4">📸 Konten & Media Hero</h2>

    <div class="form-group mb-4">
        <label class="admin-label">Badge Atas (1)</label>
        <input type="text" name="hero_badge" value="{{ $settings['hero_badge'] ?? 'TERDAFTAR RESMI KEMENAG RI' }}" 
            class="form-control" data-sync-target="#sync-hero_badge">
    </div>
    <div class="form-group mb-4">
        <label class="admin-label">Badge Atas (2)</label>
        <input type="text" name="hero_badge_2" value="{{ $settings['hero_badge_2'] ?? 'PIHK 81200009510360001' }}" 
            class="form-control" data-sync-target="#sync-hero_badge_2">
    </div>
    <div class="form-group mb-4">
        <label class="admin-label">Judul Utama Hero (H1)</label>
        <textarea name="hero_title" class="form-control" rows="2" data-sync-target="#sync-hero_title">{{ $settings['hero_title'] ?? 'Wujudkan Perjalanan Suci ke Baitullah' }}</textarea>
        <span class="help-block">Gunakan &lt;br&gt; untuk baris baru.</span>
    </div>
    <div class="form-group mb-4">
        <label class="admin-label">Deskripsi Hero</label>
        <textarea name="hero_description" class="form-control" rows="3" data-sync-target="#sync-hero_description">{{ $settings['hero_description'] ?? 'Lebih dari 12.000 jemaah telah kami antarkan ke Tanah Suci.' }}</textarea>
    </div>

    <hr class="my-4">

    {{-- UPLOAD FOTO BACKGROUND --}}
    <div class="form-group mb-4 upload-section-green">
        <label class="upload-label-primary">Foto Background Slideshow (Maks. 4 Foto)</label>
        <div class="photo-grid-4">
            @for($i = 1; $i <= 4; $i++)
                <div>
                    <label class="form-label">Foto {{ $i }}</label>
                    @if(!empty($settings['hero_bg_' . $i]))
                        <img src="{{ $settings['hero_bg_' . $i] }}" class="preview-img-sm">
                        <label class="delete-photo-label">
                            <input type="checkbox" name="delete_hero_bg_{{ $i }}" value="1"> 🗑️ Hapus
                        </label>
                    @endif
                    <input type="file" name="hero_bg_{{ $i }}" class="form-control input-p6" accept="image/*" style="margin-top:8px;">
                </div>
            @endfor
        </div>
        <span class="help-block" style="margin-top:12px;">Foto akan tampil sebagai slideshow di latar belakang hero.</span>
    </div>

    {{-- UPLOAD VIDEO PROFIL --}}
    <div class="form-group upload-section-video">
        <label class="upload-label-video">🎬 Video Profil (Tombol "Tonton Video")</label>

        @if(!empty($settings['hero_video_url']))
            <div class="video-preview-box">
                <video src="{{ $settings['hero_video_url'] }}" controls class="video-preview-player"></video>
                <label class="delete-photo-label" style="margin-top:8px;">
                    <input type="checkbox" name="delete_hero_video_url" value="1"> 🗑️ Hapus Video
                </label>
            </div>
        @endif

        <input type="file" name="hero_video_url" class="form-control" accept="video/*" style="margin-top:10px;">
        <span class="help-block" style="margin-top:8px;">
            Upload video MP4 untuk diputar saat pengunjung klik tombol "Tonton Video Profil". Disarankan ukuran &lt; 50MB.
        </span>
    </div>
</div>
