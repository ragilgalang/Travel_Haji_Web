<!-- TAB: STATISTIK -->
<div id="tab-stats" class="tab-pane card">
    <h2 class="card-title mb-4">Statistik Kepercayaan (Baris 4 Kolom)</h2>
    <div class="stats-grid-4">
        <div class="form-group advantage-point-card">
            <label class="form-label text-sm font-semibold mb-2 block">Angka 1</label>
            <input type="text" name="stat1_num" value="{{ $settings['stat1_num'] ?? '12.000+' }}"
                class="form-control mb-2" data-sync-target="#sync-stat1_num">
            <input type="text" name="stat1_label"
                value="{{ $settings['stat1_label'] ?? 'Jemaah Diberangkatkan' }}" class="form-control"
                data-sync-target="#sync-stat1_label">
        </div>
        <div class="form-group advantage-point-card">
            <label class="form-label text-sm font-semibold mb-2 block">Angka 2</label>
            <input type="text" name="stat2_num" value="{{ $settings['stat2_num'] ?? '20+' }}"
                class="form-control mb-2" data-sync-target="#sync-stat2_num">
            <input type="text" name="stat2_label"
                value="{{ $settings['stat2_label'] ?? 'Tahun Pengalaman' }}" class="form-control"
                data-sync-target="#sync-stat2_label">
        </div>
        <div class="form-group advantage-point-card">
            <label class="form-label text-sm font-semibold mb-2 block">Angka 3</label>
            <input type="text" name="stat3_num" value="{{ $settings['stat3_num'] ?? '99%' }}"
                class="form-control mb-2" data-sync-target="#sync-stat3_num">
            <input type="text" name="stat3_label"
                value="{{ $settings['stat3_label'] ?? 'Kepuasan Jemaah' }}" class="form-control"
                data-sync-target="#sync-stat3_label">
        </div>
        <div class="form-group advantage-point-card">
            <label class="form-label text-sm font-semibold mb-2 block">Angka 4</label>
            <input type="text" name="stat4_num" value="{{ $settings['stat4_num'] ?? '15+' }}"
                class="form-control mb-2" data-sync-target="#sync-stat4_num">
            <input type="text" name="stat4_label"
                value="{{ $settings['stat4_label'] ?? 'Kota Keberangkatan' }}" class="form-control"
                data-sync-target="#sync-stat4_label">
        </div>
    </div>
</div>
