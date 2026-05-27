<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Pembaruan Sistem</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/changelog.css') }}">
    <style>
        .add-mode-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #dcfce7;
            color: #166534;
            border-radius: 8px;
            padding: 4px 12px;
            font-size: 0.78rem;
            font-weight: 700;
            margin-bottom: 12px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .add-date-preview {
            font-size: 0.82rem;
            color: #94a3b8;
            margin-bottom: 8px;
            font-style: italic;
        }
        .btn-green {
            background: #16a34a;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-green:hover { background: #15803d; }
        .mode-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div class="header-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
            </svg>
        </div>
        <div>
            <h1>Log Pembaruan Sistem</h1>
            <p>Catatan riwayat pembaruan (What's New) pada website PT. UMB</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert-success">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- ============================================================ --}}
    {{-- MODE TAMPILAN (default) --}}
    {{-- ============================================================ --}}
    <div id="viewMode">
        @php
            $normalized = str_replace("\r\n", "\n", $content ?? '');
            $pattern = '/^(\d{1,2}\s+[A-Za-z\x7f-\xff]+\s+\d{4})\n-+/m';
            
            $entries = [];
            if (preg_match_all($pattern, $normalized, $matches, PREG_OFFSET_CAPTURE)) {
                $count = count($matches[0]);
                for ($i = 0; $i < $count; $i++) {
                    $date = $matches[1][$i][0];
                    $startOffset = $matches[0][$i][1] + strlen($matches[0][$i][0]);
                    $endOffset = ($i < $count - 1) ? $matches[0][$i+1][1] : strlen($normalized);
                    $blockContent = trim(substr($normalized, $startOffset, $endOffset - $startOffset));
                    
                    $entries[] = [
                        'date' => $date,
                        'content' => $blockContent
                    ];
                }
            }
            
            if (empty($entries) && trim($content ?? '') !== '') {
                $entries[] = [
                    'date' => 'Pembaruan',
                    'content' => trim($content)
                ];
            }
        @endphp

        @if(empty($entries))
            <div style="background: #f8fafc; padding: 40px; border-radius: 12px; border: 1px solid #e2e8f0; text-align: center; color: #94a3b8; margin-bottom: 30px;">
                <p style="margin: 0; font-size: 0.95rem;">Belum ada catatan pembaruan. Silakan klik tombol di bawah untuk menambahkan log baru.</p>
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 20px; margin-bottom: 30px;">
                @foreach($entries as $index => $entry)
                    <div style="background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: transform 0.2s;">
                        <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px dashed #cbd5e1; padding-bottom: 12px; margin-bottom: 16px;">
                            <span style="background: #eafaf1; color: #10b981; font-weight: 700; font-size: 0.85rem; padding: 6px 12px; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display: inline-block;">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                {{ $entry['date'] }}
                            </span>
                            @if($index === 0)
                                <span style="background: #eff6ff; color: #2563eb; font-weight: 800; font-size: 0.75rem; padding: 4px 10px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.05em;">Terbaru</span>
                            @endif
                        </div>
                        <div style="font-size: 0.95rem; color: #334155; line-height: 1.7; white-space: pre-wrap; text-align: left;">@php
                            $escapedContent = htmlspecialchars($entry['content']);
                            $withLinks = preg_replace(
                                '/(https?:\/\/[^\s<>"]+)/',
                                '<a href="$1" target="_blank" rel="noopener noreferrer" class="changelog-link">$1</a>',
                                $escapedContent
                            );
                            echo $withLinks;
                        @endphp</div>
                    </div>
                @endforeach
            </div>
        @endif


        <div class="mode-actions">
            {{-- Tombol Tambah Pembaruan Baru --}}
            <button class="btn-green" onclick="toggleAddMode()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Tambah Pembaruan Baru
            </button>

            {{-- Tombol Edit Riwayat --}}
            <button class="btn btn-outline" onclick="toggleEditMode()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Edit Riwayat
            </button>

            <a href="/admin/dashboard" class="btn btn-outline">Kembali ke Admin</a>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODE TAMBAH BARU --}}
    {{-- ============================================================ --}}
    <div id="addMode" style="display: none;">
        <div class="add-mode-badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Lembar Pembaruan Baru
        </div>
        <p class="add-date-preview">Tanggal akan otomatis diisi: <strong>{{ now()->locale('id')->translatedFormat('d F Y') }}</strong></p>
        <form action="{{ url()->current() }}" method="POST">
            @csrf
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <textarea name="new_entry" placeholder="Tuliskan daftar perubahan hari ini...&#10;- Perbaikan bug X&#10;- Penambahan fitur Y&#10;- Pembaruan tampilan Z"></textarea>
            </div>
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <button type="submit" class="btn-green">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    Tambah Pembaruan
                </button>
                <button type="button" class="btn btn-outline" onclick="toggleAddMode()">Batal</button>
            </div>
        </form>
    </div>

    {{-- ============================================================ --}}
    {{-- MODE EDIT RIWAYAT --}}
    {{-- ============================================================ --}}
    <div id="editMode" style="display: none;">
        <p style="color:#94a3b8; font-size:0.85rem; margin-bottom:8px;">⚠️ Mode ini mengedit <strong>seluruh riwayat log</strong>. Gunakan dengan hati-hati.</p>
        <form action="{{ url()->current() }}" method="POST">
            @csrf
            <input type="hidden" name="action" value="edit">
            <div class="form-group">
                <textarea name="content" placeholder="Tuliskan log pembaruan di sini... (Anda bisa menggunakan teks biasa)">{{ $content ?? '' }}</textarea>
            </div>
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <button type="submit" class="btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    Simpan Perubahan
                </button>
                <button type="button" class="btn btn-outline" onclick="toggleEditMode()">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleAddMode() {
        const viewMode = document.getElementById('viewMode');
        const addMode  = document.getElementById('addMode');
        const editMode = document.getElementById('editMode');
        editMode.style.display = 'none';
        if (addMode.style.display === 'none') {
            viewMode.style.display = 'none';
            addMode.style.display  = 'block';
        } else {
            viewMode.style.display = 'block';
            addMode.style.display  = 'none';
        }
    }

    function toggleEditMode() {
        const viewMode = document.getElementById('viewMode');
        const addMode  = document.getElementById('addMode');
        const editMode = document.getElementById('editMode');
        addMode.style.display = 'none';
        if (editMode.style.display === 'none') {
            viewMode.style.display = 'none';
            editMode.style.display = 'block';
        } else {
            viewMode.style.display = 'block';
            editMode.style.display = 'none';
        }
    }
</script>

</body>
</html>

