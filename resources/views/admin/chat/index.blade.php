@extends('admin.layout')

@section('page_title', 'Asisten Chat Bot')

@section('content')
<div class="card" style="margin-bottom: 2rem; border: 2px solid #3b82f6; background: #f0f9ff; padding: 1.5rem; border-radius: 1.25rem;">
    <div style="display: flex; align-items: center; gap: 1rem;">
        <div style="font-size: 2.5rem;">🤖</div>
        <div>
            <h2 style="margin: 0; color: #1e40af; font-size: 1.25rem; font-weight: 800;">Pusat Komunikasi Jemaah</h2>
            <p style="margin: 0.25rem 0 0; color: #1e40af; font-size: 0.875rem;">Gunakan halaman ini untuk mem-follow up jemaah secara cepat menggunakan template WhatsApp.</p>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom: 2rem;">
    <form method="GET" style="display: flex; gap: 1rem;">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama jemaah atau nomor HP..." style="flex: 1; height: 48px; padding: 0 1rem; border-radius: 10px; border: 1px solid #e2e8f0;">
        <button type="submit" class="btn-primary" style="height: 48px; padding: 0 24px; border-radius: 10px;">🔍 Cari</button>
        @if(request('q'))
            <a href="{{ route('admin.chat.index') }}" style="display: flex; align-items: center; text-decoration: none; color: #64748b;">✕ Reset</a>
        @endif
    </form>
</div>

<div class="card p-0" style="border-radius: 1.25rem; overflow: hidden; border: 1px solid #e2e8f0;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Jemaah & Paket</th>
                <th style="padding: 1rem 1.5rem; text-align: center; font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Asisten Chat (Klik Pesan)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($registrations as $reg)
                @php
                    $wa = $reg['no_hp'] ?? ($reg['telepon'] ?? '');
                    $wa = preg_replace('/[^0-9]/', '', $wa);
                    $nama = $reg['nama_lengkap'] ?? 'Jemaah';
                    $paket = $reg['paket'] ?? 'paket ibadah';
                    
                    $msgSalam = rawurlencode("Assalamu'alaikum Bapak/Ibu *$nama*,\n\nSaya Admin dari *PT. UMI MUTHMAINAH BERKAH*. Terima kasih telah mendaftar untuk *$paket*. Apakah ada yang bisa kami bantu? 🙏");
                    
                    $msgMintaData = rawurlencode("Halo Bapak/Ibu *$nama*,\n\nUntuk melengkapi pendaftaran *$paket*, mohon bantuannya untuk mengirimkan foto berkas berikut:\n1. KTP\n2. Kartu Keluarga\n3. Pas Foto (Background Putih)\n\nTerima kasih.");
                    
                    $msgTagihan = rawurlencode("Assalamu'alaikum Bapak/Ibu *$nama*,\n\nTerima kasih telah memilih kami untuk perjalanan ibadah *$paket*. Mohon konfirmasi jika sudah melakukan pembayaran Down Payment (DP) agar pendaftaran segera kami proses. Terima kasih.");
                @endphp
                <tr style="border-bottom: 1px solid #f1f5f9; transition: 0.2s;">
                    <td style="padding: 1.25rem 1.5rem;">
                        <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem;">{{ $nama }}</div>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">📦 {{ $paket }}</div>
                        <div style="font-size: 0.75rem; color: #3b82f6; margin-top: 2px; font-weight: 600;">📱 {{ $wa ?: 'No HP Tidak Ada' }}</div>
                    </td>
                    <td style="padding: 1.25rem 1.5rem; text-align: center;">
                        @if($wa)
                        <div style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
                            <a href="https://wa.me/62{{ ltrim($wa,'0') }}?text={{ $msgSalam }}" target="_blank" style="background: #ecfdf5; color: #059669; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 0.75rem; font-weight: 700; border: 1px solid #d1fae5;">
                                👋 Sapaan
                            </a>
                            <a href="https://wa.me/62{{ ltrim($wa,'0') }}?text={{ $msgMintaData }}" target="_blank" style="background: #f0f9ff; color: #0369a1; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 0.75rem; font-weight: 700; border: 1px solid #e0f2fe;">
                                📄 Minta Berkas
                            </a>
                            <a href="https://wa.me/62{{ ltrim($wa,'0') }}?text={{ $msgTagihan }}" target="_blank" style="background: #fffbeb; color: #b45309; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 0.75rem; font-weight: 700; border: 1px solid #fef3c7;">
                                💰 Tagih DP
                            </a>
                        </div>
                        @else
                        <span style="color: #ef4444; font-size: 0.75rem; font-style: italic;">Tidak dapat menghubungi via WA</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="padding: 4rem; text-align: center; color: #64748b;">
                        <div>📭</div>
                        <div style="margin-top: 1rem;">Tidak ada jemaah aktif yang ditemukan.</div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
