<!-- TAB: TENTANG KAMI -->
<div id="tab-about" class="tab-pane card">
    <h2 class="card-title mb-4">📖 Konten Tentang Kami</h2>

    <div class="form-group mb-4">
        <label class="admin-label">Eyebrow (Teks Kecil Atas)</label>
        <input type="text" name="about_badge" value="{{ $settings['about_badge'] ?? 'Tentang Kami' }}" 
            class="form-control" data-sync-target="#sync-about-badge">
    </div>
    <div class="form-group mb-4">
        <label class="admin-label">Judul Tentang (H2)</label>
        <textarea name="about_title" class="form-control" rows="2" data-sync-target="#sync-about_title">{{ $settings['about_title'] ?? 'Melayani Sepenuh Hati' }}</textarea>
    </div>
    <div class="form-group mb-4">
        <label class="admin-label">Deskripsi Tentang</label>
        <textarea name="about_description" class="form-control" rows="4" data-sync-target="#sync-about_description">{{ $settings['about_description'] ?? 'Deskripsi singkat perusahaan...' }}</textarea>
    </div>

    <hr class="my-4">
    <h3 class="text-md font-semibold mb-3">Poin Keunggulan (3 Poin)</h3>
    <div class="grid-gap-1">
        @for($i = 1; $i <= 3; $i++)
            <div class="advantage-point-card p-3 border rounded">
                <label class="font-bold block mb-2">Poin {{ $i }}</label>
                <input type="text" name="about_item{{ $i }}_title" value="{{ $settings['about_item' . $i . '_title'] ?? '' }}" 
                    class="form-control mb-2" placeholder="Judul Poin" data-sync-target="#sync-about_item{{ $i }}_title">
                <textarea name="about_item{{ $i }}_text" class="form-control" rows="2" 
                    placeholder="Deskripsi Poin" data-sync-target="#sync-about_item{{ $i }}_text">{{ $settings['about_item' . $i . '_text'] ?? ($settings['about_item' . $i . '_desc'] ?? '') }}</textarea>
            </div>
        @endfor
    </div>

    <hr class="my-4">

    {{-- UPLOAD FOTO --}}
    <div class="form-group mb-4 upload-section-green">
        <label class="upload-label-primary">📸 Foto Profil Perusahaan</label>

        @if(!empty($settings['about_image']))
            <div style="position:relative; margin-bottom:12px;">
                <img src="{{ $settings['about_image'] }}" class="preview-img-sm" style="height:120px; width:100%; object-fit:cover;">
                <label class="delete-photo-label">
                    <input type="checkbox" name="delete_about_image" value="1"> 🗑️ Hapus Foto
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
            <div class="video-preview-box">
                <video src="{{ $settings['about_video'] }}" controls class="video-preview-player"></video>
                <label class="delete-photo-label" style="margin-top:8px; color:#94a3b8;">
                    <input type="checkbox" name="delete_about_video" value="1"> 🗑️ Hapus Video
                </label>
            </div>
        @endif

        <input type="file" name="about_video" class="form-control" accept="video/mp4,video/webm" style="margin-top:10px;">
        <span class="help-block" style="margin-top:8px; color:#64748b;">
            Jika diisi, video akan tampil menggantikan foto di bagian Tentang. Format: MP4, WebM.
        </span>
    </div>
</div>
