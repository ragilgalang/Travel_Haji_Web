@extends('admin.layout')

@section('page_title', 'Kelola Fasilitas')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/facilities/kelola_fasilitas.css') }}">
@endpush

@section('content')
<div class="card">
    <div class="card-header fac-header">
        <h2 class="card-title">Daftar Fasilitas</h2>
        <button type="button" onclick="openFasilitasForm('create')" class="btn-primary btn-add-fac">+ Tambah Fasilitas Baru</button>
    </div>

    @if(session('success'))
        <div class="fac-success-alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="bulk-actions" id="bulkActionsBarFacilities">
        <span id="selectedCountFacilities">0 fasilitas dipilih</span>
        <button type="button" class="btn-bulk btn-bulk-delete" onclick="executeBulkAction('facilities', 'delete')">🗑️ Hapus Fasilitas</button>
    </div>
    
    <div class="fac-table-wrapper">
        <table class="fac-table" id="facilitiesTable">
            <thead>
                <tr>
                    <th class="checkbox-col"><input type="checkbox" class="select-all-cb" data-target="fac-checkbox"></th>
                    <th class="label-col">Fasilitas</th>
                    <th class="label-col">Deskripsi</th>
                    <th class="label-col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($facilities ?? [] as $id => $facility)
                <tr>
                    <td><input type="checkbox" class="row-checkbox fac-checkbox" value="{{ $id }}"></td>
                    <td>
                        <div class="fac-info-box">
                            <span class="fac-icon-text">{{ $facility['icon'] ?? '🕌' }}</span>
                            <strong class="fac-title-text">{{ $facility['title'] ?? 'Fasilitas' }}</strong>
                        </div>
                    </td>
                    <td class="fac-desc-text">
                        {{ \Illuminate\Support\Str::limit($facility['description'] ?? '', 80) }}
                    </td>
                    <td class="fac-action-btns">
                        <button type="button" onclick="openFasilitasForm('edit', '{{ $id }}', {{ json_encode($facility) }})" class="btn-edit-fac-inline">Edit</button>
                        <form action="{{ route('admin.facilities.destroy', $id) }}" method="POST" onsubmit="return confirm('Hapus fasilitas ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-delete-fac-inline">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="empty-facilities">Belum ada fasilitas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- OVERLAY: FORM EDITOR FASILITAS -->
<div id="fasilitasFormOverlay" class="modal-overlay">
    <div class="modal-content card">
        <div class="card-header fac-modal-header">
            <h2 class="card-title fac-modal-title" id="fasilitasFormTitle">Tambah Fasilitas Baru</h2>
            <button type="button" onclick="closeFasilitasForm()" class="btn-close-fac-modal">&times;</button>
        </div>
        
        <form id="fasilitasCrudForm" action="{{ route('admin.facilities.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="fasilitasMethod" value="POST">

            <div class="fac-form-grid-2">
                <!-- Kolom Icon Select -->
                <div class="form-group mb-4">
                    <label class="form-label text-sm font-semibold mb-2 block">Ikon</label>
                    <select name="icon" id="fac_icon" class="form-control fac-icon-select">
                        <option value="🕌">🕌 Masjid / Ibadah</option>
                        <option value="🏨">🏨 Hotel / Akomodasi</option>
                        <option value="🚌">🚌 Transportasi / Bus</option>
                        <option value="✈️">✈️ Penerbangan / Pesawat</option>
                        <option value="🍽️">🍽️ Katering / Makan</option>
                        <option value="🛂">🛂 Dokumen / Visa</option>
                        <option value="📚">📚 Bimbingan / Manasik</option>
                        <option value="⛺">⛺ Tenda / Masyair</option>
                        <option value="🏥">🏥 Kesehatan / Medis</option>
                        <option value="🇮🇩">🇮🇩 Layanan Domestik</option>
                        <option value="👨‍🏫">👨‍🏫 Mutawwif / Pembimbing</option>
                        <option value="🧳">🧳 Bagasi / Koper</option>
                        <option value="💧">💧 Zam-zam / Air</option>
                        <option value="🎫">🎫 Tiket / Boarding</option>
                    </select>
                </div>
                
                <!-- Kolom Judul -->
                <div class="fac-title-grid" style="grid-column: span 1;">
                    <div class="form-group mb-4">
                        <label class="form-label text-sm font-semibold mb-2 block">Judul Fasilitas</label>
                        <input type="text" name="title" id="fac_title" class="form-control" required placeholder="Contoh: Hotel Bintang 5">
                    </div>
                </div>
            </div>

            <div class="form-group mb-4">
                <label class="form-label text-sm font-semibold mb-2 block">Deskripsi Detail</label>
                <textarea name="description" id="fac_description" class="form-control" rows="3" required placeholder="Jelaskan fasilitas ini secara singkat..."></textarea>
            </div>


            <div class="fac-modal-footer">
                <button type="submit" class="btn-primary btn-submit-fac">Simpan Fasilitas</button>
                <button type="button" onclick="closeFasilitasForm()" class="btn-cancel-fac">Batal</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select All Logic
        const selectAllCbs = document.querySelectorAll('.select-all-cb');
        selectAllCbs.forEach(cb => {
            cb.addEventListener('change', function() {
                const targetClass = this.getAttribute('data-target');
                const rowCheckboxes = document.querySelectorAll('.' + targetClass);
                rowCheckboxes.forEach(rowCb => rowCb.checked = this.checked);
                updateBulkActionsVisibility(targetClass);
            });
        });

        // Individual Checkbox Logic
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('row-checkbox')) {
                const classes = e.target.className.split(' ');
                const targetClass = classes.find(c => c.endsWith('-checkbox'));
                if (targetClass) updateBulkActionsVisibility(targetClass);
            }
        });

        function updateBulkActionsVisibility(targetClass) {
            const checkedCount = document.querySelectorAll('.' + targetClass + ':checked').length;
            const bar = document.getElementById('bulkActionsBarFacilities');
            const countLabel = document.getElementById('selectedCountFacilities');

            if (bar && countLabel) {
                if (checkedCount > 0) {
                    bar.classList.add('active');
                    countLabel.innerText = checkedCount + ' fasilitas dipilih';
                } else {
                    bar.classList.remove('active');
                    const selectAll = document.querySelector(`.select-all-cb[data-target="${targetClass}"]`);
                    if (selectAll) selectAll.checked = false;
                }
            }
        }

        window.executeBulkAction = function(module, actionType) {
            const targetClass = 'fac-checkbox';
            const confirmMsg = 'Hapus permanen fasilitas yang terpilih?';

            const checkedRows = document.querySelectorAll('.' + targetClass + ':checked');
            if(checkedRows.length === 0) return;
            if(!confirm(confirmMsg)) return;

            const ids = Array.from(checkedRows).map(cb => cb.value);
            checkedRows.forEach(cb => cb.closest('tr').style.opacity = '0.5');

            fetch(`/admin/facilities/bulk-action`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ action: actionType, ids: ids })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                    checkedRows.forEach(cb => cb.closest('tr').style.opacity = '1');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan koneksi.');
                checkedRows.forEach(cb => cb.closest('tr').style.opacity = '1');
            });
        };
    });

    function openFasilitasForm(mode, id = null, data = null) {
        const formOverlay = document.getElementById('fasilitasFormOverlay');
        const formTitle = document.getElementById('fasilitasFormTitle');
        const crudForm = document.getElementById('fasilitasCrudForm');
        const methodInput = document.getElementById('fasilitasMethod');

        const inputs = {
            icon: document.getElementById('fac_icon'),
            title: document.getElementById('fac_title'),
            description: document.getElementById('fac_description')
        };

        if (mode === 'create') {
            formTitle.innerText = 'Tambah Fasilitas Baru';
            crudForm.action = "{{ route('admin.facilities.store') }}";
            methodInput.value = 'POST';
            crudForm.reset();
        } else if (mode === 'edit' && data) {
            formTitle.innerText = 'Edit Fasilitas: ' + (data.title || '');
            crudForm.action = `/admin/facilities/${id}`;
            methodInput.value = 'PUT';
            
            inputs.icon.value = data.icon || '🕌';
            inputs.title.value = data.title || '';
            inputs.description.value = data.description || '';
        }

        formOverlay.classList.add('active');
    }

    function closeFasilitasForm() {
        document.getElementById('fasilitasFormOverlay').classList.remove('active');
    }
</script>
@endpush
