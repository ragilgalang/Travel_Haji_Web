@extends('admin.layout')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/registrations/show.css') }}">
@endpush

@section('content')
<div class="card-header registrations-header">
    <div>
        <a href="{{ route('admin.registrations.index') }}" class="btn-back-modern">
            <span>←</span> Kembali ke Daftar
        </a>
        <h1 class="card-title">
            <span class="card-title-icon">📋</span> Detail Pendaftaran
        </h1>
    </div>
    
    @php
        $wa = $data['no_hp'] ?? ($data['telepon'] ?? '');
        $wa = preg_replace('/[^0-9]/', '', $wa);
    @endphp
    @if($wa)
    <a href="https://wa.me/62{{ ltrim($wa,'0') }}" target="_blank" class="btn-whatsapp-modern">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12.031 6.172c-2.135 0-3.834 1.705-3.834 3.844 0 2.146 1.7 3.847 3.834 3.847 2.138 0 3.841-1.7 3.841-3.847 0-2.139-1.703-3.844-3.841-3.844zM12.031 12c-1.123 0-2.003-.88-2.003-2.003 0-1.123.88-2.003 2.003-2.003 1.126 0 2.003.88 2.003 2.003C14.034 11.12 13.157 12 12.031 12zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"/></svg>
        Hubungi WhatsApp
    </a>
    @endif
</div>

@if(session('success'))
<div class="alert-success-modern">
    ✅ {{ session('success') }}
</div>
@endif

<div class="detail-grid">
    
    <!-- DATA UTAMA -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        <div class="detail-card">
            <div class="detail-card-header">
                <h2>👤 Informasi Data Jemaah</h2>
            </div>
            <div class="info-grid">
                @if(isset($data['dynamic_fields']))
                    @foreach($data['dynamic_fields'] as $field)
                    <div class="info-item">
                        <label class="info-label">{{ $field['label'] }}</label>
                        <div class="info-value">
                            @if(($field['type'] ?? '') == 'file' && !empty($field['value']) && $field['value'] !== '-')
                                <a href="{{ $field['value'] }}" target="_blank" class="btn-view-doc">
                                    📁 Lihat Dokumen
                                </a>
                            @else
                                {{ $field['value'] }}
                            @endif
                        </div>
                    </div>
                    @endforeach
                @else
                    @foreach($data as $key => $val)
                        @if(!in_array($key, ['dynamic_fields', 'status', 'created_at', 'id', 'is_archived']))
                        <div class="info-item">
                            <label class="info-label">{{ str_replace('_', ' ', $key) }}</label>
                            <div class="info-value">{{ is_array($val) ? json_encode($val) : $val }}</div>
                        </div>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <!-- SIDEBAR / ACTION -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        <!-- UPDATE STATUS -->
        <div class="status-card">
            <h3>⚙️ Perbarui Status</h3>
            <form action="{{ route('admin.registrations.status', $data['id']) }}" method="POST">
                @csrf @method('PATCH')
                <div style="margin-bottom: 1.5rem;">
                    <select name="status" class="status-select">
                        @foreach(['Menunggu Verifikasi','Sedang Diproses','Sudah Dikonfirmasi','Selesai','Dibatalkan'] as $st)
                        <option value="{{ $st }}" {{ ($data['status'] ?? 'Menunggu Verifikasi') === $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-save-status">
                    💾 Simpan Perubahan
                </button>
            </form>
        </div>

        <!-- INFO PENDAFTARAN -->
        <div class="log-card">
            <h3>🕋 Log Pendaftaran</h3>
            
            <div class="log-item">
                <label class="log-label">Waktu Daftar</label>
                <div class="log-value">
                    {{ isset($data['created_at']) ? \Carbon\Carbon::parse($data['created_at'])->format('d M Y, H:i') : '-' }}
                </div>
            </div>

            <div class="log-item">
                <label class="log-label">Status Saat Ini</label>
                <div class="status-badge-modern">
                    {{ $data['status'] ?? 'Menunggu Verifikasi' }}
                </div>
            </div>

            <div class="log-item" style="margin-bottom: 0;">
                <label class="log-label">ID Unik Firebase</label>
                <div class="id-badge">
                    {{ $data['id'] }}
                </div>
            </div>
        </div>

        <!-- ASISTEN CHAT BOT -->
        <div class="bot-card">
            <h3>🤖 Asisten Chat Bot</h3>
            <p>Pilih template pesan di bawah untuk menghubungi jemaah via WhatsApp secara otomatis:</p>
            
            <div class="template-list">
                @php
                    $nama = $data['nama_lengkap'] ?? ($data['dynamic_fields'][0]['value'] ?? 'Jemaah');
                    $paket = $data['paket'] ?? ($data['dynamic_fields'][4]['value'] ?? 'Paket Haji/Umrah');
                    
                    $msgSalam = rawurlencode("Assalamu'alaikum Bapak/Ibu *$nama*,\n\nSaya Admin dari *PT. UMI MUTHMAINAH BERKAH*. Kami telah menerima pendaftaran Anda untuk *$paket*. Apakah ada yang bisa kami bantu?");
                    
                    $msgMintaData = rawurlencode("Halo Bapak/Ibu *$nama*,\n\nUntuk melengkapi pendaftaran *$paket*, mohon bantuannya untuk mengirimkan foto berkas berikut:\n1. KTP\n2. Kartu Keluarga\n3. Pas Foto (Background Putih)\n\nTerima kasih.");
                    
                    $msgTagihan = rawurlencode("Assalamu'alaikum Bapak/Ibu *$nama*,\n\nTerima kasih telah memilih kami untuk perjalanan ibadah *$paket*. Mohon konfirmasi jika sudah melakukan pembayaran Down Payment (DP) agar pendaftaran segera kami proses. Terima kasih.");
                @endphp

                <a href="https://wa.me/62{{ ltrim($wa,'0') }}?text={{ $msgSalam }}" target="_blank" class="btn-template">
                    👋 Sapaan & Salam Pembuka
                </a>

                <a href="https://wa.me/62{{ ltrim($wa,'0') }}?text={{ $msgMintaData }}" target="_blank" class="btn-template">
                    📄 Minta Dokumen (KTP/KK)
                </a>

                <a href="https://wa.me/62{{ ltrim($wa,'0') }}?text={{ $msgTagihan }}" target="_blank" class="btn-template">
                    💰 Tagihan & Konfirmasi DP
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
