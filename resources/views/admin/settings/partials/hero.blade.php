<!-- TAB: HERO -->
<div id="tab-hero" class="tab-pane card">
    <h2 class="card-title mb-4">📸 Foto Hero</h2>

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

    <hr class="my-4">

    {{-- UPLOAD VIDEO HERO --}}
    <div class="form-group upload-section-video">
        <label class="upload-label-video">🎬 Video Hero (Opsional — tampil menggantikan slideshow foto)</label>

        @if(!empty($settings['hero_video']))
            <div class="video-preview-box">
                <video src="{{ $settings['hero_video'] }}" controls class="video-preview-player" style="width:100%; max-height:180px; border-radius:8px; margin-bottom:8px;"></video>
                <label class="delete-photo-label" style="color:#94a3b8;">
                    <input type="checkbox" name="delete_hero_video" value="1"> 🗑️ Hapus Video
                </label>
            </div>
        @endif

        <input type="file" name="hero_video" class="form-control" accept="video/mp4,video/webm" style="margin-top:10px;">
        <span class="help-block" style="margin-top:8px; color:#64748b;">
            Jika diisi, video akan tampil sebagai latar belakang hero menggantikan slideshow foto. Format: MP4, WebM. Maks. 50MB.
        </span>
    </div>

    {{-- Hidden Inputs untuk sinkronisasi Visual Editor --}}
    <input type="hidden" name="hero_title" value="{{ $settings['hero_title'] ?? '' }}">
    <input type="hidden" name="hero_description" value="{{ $settings['hero_description'] ?? '' }}">
    <input type="hidden" name="hero_badge" value="{{ $settings['hero_badge'] ?? '' }}">
    <input type="hidden" name="hero_badge_2" value="{{ $settings['hero_badge_2'] ?? '' }}">
    <input type="hidden" name="hero_cta" value="{{ $settings['hero_cta'] ?? '' }}">
</div>

