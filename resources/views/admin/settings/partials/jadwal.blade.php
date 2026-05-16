<!-- TAB: JADWAL (ITINERARY) -->
<div id="tab-jadwal" class="tab-pane card">
    <h2 class="card-title mb-4">Alur Perjalanan (Itinerary)</h2>
    <p class="mb-4 text-sm text-muted">Atur 5 langkah utama perjalanan haji/umrah Anda.</p>

    <div class="grid-gap-1">
        @for($i = 1; $i <= 5; $i++)
            <div class="form-group itin-step-card mb-3">
                <label class="form-label text-xs font-bold uppercase mb-2 block text-primary">Langkah
                    {{ $i }}</label>
                <div class="itin-grid-2 mb-2">
                    <input type="text" name="itin{{ $i }}_day"
                        value="{{ $settings['itin' . $i . '_day'] ?? '' }}" class="form-control"
                        placeholder="Hari" data-sync-target="#sync-itin{{ $i }}_day">
                    <input type="text" name="itin{{ $i }}_title"
                        value="{{ $settings['itin' . $i . '_title'] ?? '' }}" class="form-control"
                        placeholder="Kegiatan" data-sync-target="#sync-itin{{ $i }}_title">
                </div>
                <textarea name="itin{{ $i }}_desc" class="form-control text-sm" rows="2"
                    data-sync-target="#sync-itin{{ $i }}_desc">{{ $settings['itin' . $i . '_desc'] ?? '' }}</textarea>
            </div>
        @endfor
    </div>

    <hr class="dashed-hr">

    <h2 class="card-title mb-4">Kartu Informasi Samping</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="form-group mb-4">
            <label class="form-label text-sm font-semibold mb-2 block">Gambar Kartu (Ka'bah)</label>
            @if(!empty($settings['itin_aside_img']))
                <div class="itin-aside-preview-row">
                    <img src="{{ $settings['itin_aside_img'] }}" class="itin-aside-preview-img">
                    <label class="delete-photo-label"><input
                            type="checkbox" name="delete_itin_aside_img" value="1"> Hapus (Ganti
                        Default)</label>
                </div>
            @endif
            <input type="file" name="itin_aside_img" class="form-control" accept="image/*">
        </div>
        <div class="form-group mb-4">
            <label class="form-label text-sm font-semibold mb-2 block">Judul Kartu</label>
            <input type="text" name="itin_aside_title"
                value="{{ $settings['itin_aside_title'] ?? 'Baitullah, Makkah Al-Mukarramah' }}"
                class="form-control" data-sync-target="#sync-jadwal-card">
        </div>
    </div>

    <div class="form-grid-1 mt-4">
        @for($i = 1; $i <= 3; $i++)
            <div class="form-group itin-info-item-card mb-3">
                <label class="form-label text-xs font-bold mb-2 block text-primary">Info Item {{ $i }}</label>
                <div class="itin-info-grid mb-2">
                    <input type="text" name="itin_aside_i{{ $i }}_icon"
                        value="{{ $settings['itin_aside_i' . $i . '_icon'] ?? ($i == 1 ? '📋' : ($i == 2 ? '💉' : '📅')) }}"
                        class="form-control text-center" placeholder="Emoji"
                        data-sync-target="#sync-jadwal-card">
                    <input type="text" name="itin_aside_i{{ $i }}_title"
                        value="{{ $settings['itin_aside_i' . $i . '_title'] ?? ($i == 1 ? 'Dokumen Wajib' : ($i == 2 ? 'Pemeriksaan Kesehatan' : 'Pendaftaran Awal')) }}"
                        class="form-control" placeholder="Judul" data-sync-target="#sync-jadwal-card">
                </div>
                <input type="text" name="itin_aside_i{{ $i }}_desc"
                    value="{{ $settings['itin_aside_i' . $i . '_desc'] ?? ($i == 1 ? 'Paspor berlaku min. 18 bulan, KTP, KK.' : ($i == 2 ? 'Dilakukan minimal 1 bulan sebelum keberangkatan.' : 'Daftar minimal 6 bulan sebelumnya.')) }}"
                    class="form-control text-xs" placeholder="Deskripsi singkat"
                    data-sync-target="#sync-jadwal-card">
            </div>
        @endfor
    </div>
</div>
