<!-- TAB: JADWAL (ITINERARY) -->
<div id="tab-jadwal" class="tab-pane card">
    <h2 class="card-title mb-4">Media Jadwal Perjalanan</h2>
    <p class="mb-4 text-sm text-muted">Upload foto atau video yang akan tampil di samping alur perjalanan.</p>

    <div class="form-group mb-4">
        <label class="form-label text-sm font-semibold mb-2 block">Gambar Kartu</label>
        @if(!empty($settings['itin_aside_img']))
            <div class="itin-aside-preview-row">
                <img src="{{ $settings['itin_aside_img'] }}" class="itin-aside-preview-img">
                <label class="delete-photo-label"><input
                        type="checkbox" name="delete_itin_aside_img" value="1"> Hapus Foto</label>
            </div>
        @endif
        <input type="file" name="itin_aside_img" class="form-control" accept="image/*">
    </div>

    <div class="form-group mb-4">
        <label class="form-label text-sm font-semibold mb-2 block">Video Kartu (Opsional)</label>
        @if(!empty($settings['itin_aside_video']))
            <div class="video-preview-box mb-2">
                <video src="{{ $settings['itin_aside_video'] }}" controls class="video-preview-player" style="max-height:100px;"></video>
                <label class="delete-photo-label" style="display:block; margin-top:5px;">
                    <input type="checkbox" name="delete_itin_aside_video" value="1"> Hapus Video
                </label>
            </div>
        @endif
        <input type="file" name="itin_aside_video" class="form-control" accept="video/mp4,video/webm">
        <span class="help-block" style="font-size:10px; color:#64748b;">Jika diisi, video akan tampil menggantikan gambar kartu.</span>
    </div>

    <div class="form-group mb-4">
        <label class="form-label text-sm font-semibold mb-2 block">Judul Kartu</label>
        <input type="text" name="itin_aside_title"
            value="{{ $settings['itin_aside_title'] ?? 'Baitullah, Makkah Al-Mukarramah' }}"
            class="form-control" data-sync-target="#sync-jadwal-card">
    </div>
</div>
