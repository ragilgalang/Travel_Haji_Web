<!-- TAB: TENTANG KAMI -->
<div id="tab-about" class="tab-pane card">
    <h2 class="card-title mb-4">📖 Foto & Video Tentang Kami</h2>

    {{-- UPLOAD FOTO --}}
    <div class="form-group mb-4 upload-section-green">
        <label class="upload-label-primary">📸 Foto Profil Perusahaan</label>

        @if(!empty($settings['about_image']))
            @php
                $aboutImgUrl = str_starts_with($settings['about_image'], '/')
                    ? request()->getSchemeAndHttpHost() . $settings['about_image']
                    : $settings['about_image'];
            @endphp
            <div style="position:relative; margin-bottom:12px;">
                <img src="{{ $aboutImgUrl }}" class="preview-img-sm" style="height:120px; width:100%; object-fit:cover;">
                <label class="delete-photo-label">
                    <input type="checkbox" name="delete_about_image" value="1" id="chk_delete_about_image" style="display:none">
                    <span onclick="if(confirm('Hapus foto tentang kami?')){ document.getElementById('chk_delete_about_image').checked=true; performFinalSave(); }">🗑️ Hapus Foto</span>
                </label>
            </div>
        @endif

        <input type="file" name="about_image" class="form-control input-p6" accept="image/*">
        <span class="help-block" style="margin-top:8px;">Foto ini tampil di sisi kiri bagian "Tentang Kami".</span>
    </div>

    {{-- UPLOAD VIDEO --}}
    <div class="form-group upload-section-video">
        <label class="upload-label-video">🎬 Video Profil Perusahaan (Opsional)</label>

        @if(!empty($settings['about_video']))
            @php
                $aboutVideoUrl = str_starts_with($settings['about_video'], '/')
                    ? request()->getSchemeAndHttpHost() . $settings['about_video']
                    : $settings['about_video'];
            @endphp
            <div class="video-preview-box">
                <video src="{{ $aboutVideoUrl }}" controls class="video-preview-player"></video>
                <label class="delete-photo-label" style="margin-top:8px; color:#94a3b8;">
                    <input type="checkbox" name="delete_about_video" value="1" id="chk_delete_about_video" style="display:none">
                    <span onclick="if(confirm('Hapus video tentang kami?')){ document.getElementById('chk_delete_about_video').checked=true; performFinalSave(); }">🗑️ Hapus Video</span>
                </label>
            </div>
        @endif

        <input type="file" name="about_video" class="form-control" accept="video/mp4,video/webm" style="margin-top:10px;">
        <span class="help-block" style="margin-top:8px; color:#64748b;">
            Jika diisi, video akan tampil menggantikan foto di bagian Tentang. Format: MP4, WebM.
        </span>
    </div>

    {{-- Hidden Inputs untuk sinkronisasi Visual Editor --}}
    <input type="hidden" name="about_badge" value="{{ $settings['about_badge'] ?? '' }}">
    <input type="hidden" name="about_title" value="{{ $settings['about_title'] ?? '' }}">
    <input type="hidden" name="about_description" value="{{ $settings['about_description'] ?? '' }}">
    <input type="hidden" name="about_item1_icon" value="{{ $settings['about_item1_icon'] ?? '' }}">
    <input type="hidden" name="about_item1_title" value="{{ $settings['about_item1_title'] ?? '' }}">
    <input type="hidden" name="about_item1_text" value="{{ $settings['about_item1_text'] ?? '' }}">
    <input type="hidden" name="about_item2_icon" value="{{ $settings['about_item2_icon'] ?? '' }}">
    <input type="hidden" name="about_item2_title" value="{{ $settings['about_item2_title'] ?? '' }}">
    <input type="hidden" name="about_item2_text" value="{{ $settings['about_item2_text'] ?? '' }}">
    <input type="hidden" name="about_item3_icon" value="{{ $settings['about_item3_icon'] ?? '' }}">
    <input type="hidden" name="about_item3_title" value="{{ $settings['about_item3_title'] ?? '' }}">
    <input type="hidden" name="about_item3_desc" value="{{ $settings['about_item3_desc'] ?? '' }}">
</div>
