@extends('admin.layout')

@section('page_title', 'Buku Panduan Administrator')

@section('content')
<style>
    .guide-container {
        max-width: 1100px;
        margin: 0 auto;
        padding-bottom: 50px;
    }
    .guide-header {
        background: linear-gradient(135deg, #1a5c3a 0%, #0d3d25 100%);
        padding: 50px 40px;
        border-radius: 24px;
        color: white;
        margin-bottom: 40px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        text-align: center;
    }
    .guide-header h2 {
        font-weight: 800;
        font-size: 2.2rem;
        letter-spacing: -1px;
        margin-bottom: 15px;
    }
    .guide-header p {
        opacity: 0.85;
        font-size: 1.1rem;
        max-width: 700px;
        margin: 0 auto;
    }
    .section-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1a5c3a;
        margin: 40px 0 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .section-title::after {
        content: "";
        flex: 1;
        height: 1px;
        background: #eee;
    }
    .guide-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 25px;
    }
    .guide-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #f0f0f0;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .guide-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.06);
        border-color: #d4a843;
    }
    .guide-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 22px;
        font-size: 1.5rem;
    }
    
    /* Icon Colors */
    .icon-dash { background: #e6f4ea; color: #1a5c3a; }
    .icon-pkg { background: #fff7ed; color: #ea580c; }
    .icon-fac { background: #f0f9ff; color: #0369a1; }
    .icon-reg { background: #f5f3ff; color: #7c3aed; }
    .icon-testi { background: #fefce8; color: #ca8a04; }
    .icon-view { background: #fef2f2; color: #dc2626; }
    .icon-acc { background: #f0fdf4; color: #16a34a; }
    .icon-set { background: #f8fafc; color: #475569; }

    .guide-card h3 {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 12px;
        color: #111;
    }
    .guide-card p {
        color: #666;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 15px;
    }
    .guide-card ul {
        list-style: none;
        padding: 0;
        margin: 0;
        margin-top: auto;
    }
    .guide-card ul li {
        padding-left: 22px;
        position: relative;
        margin-bottom: 10px;
        font-size: 0.9rem;
        color: #444;
        font-weight: 500;
    }
    .guide-card ul li::before {
        content: "•";
        position: absolute;
        left: 0;
        color: #d4a843;
        font-size: 1.5rem;
        line-height: 1;
        top: -2px;
    }
    .tip-box {
        background: #fffbeb;
        border-left: 4px solid #f59e0b;
        padding: 20px;
        border-radius: 12px;
        margin-top: 40px;
        display: flex;
        gap: 15px;
    }
    .tip-box svg { color: #f59e0b; flex-shrink: 0; }
    .tip-box p { margin: 0; color: #92400e; font-size: 0.95rem; line-height: 1.5; }

    .btn-footer {
        display: flex;
        justify-content: center;
        margin-top: 50px;
    }
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 14px 30px;
        background: #111;
        color: white;
        border-radius: 14px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-back:hover {
        background: #333;
        transform: scale(1.02);
    }
</style>

<div class="guide-container">
    <div class="guide-header">
        <h2>Pusat Panduan Administrator</h2>
        <p>Pelajari fungsi setiap menu di panel admin untuk mengelola website PT. UMI MUTHMAINAH BERKAH dengan maksimal.</p>
    </div>

    <!-- UTAMA SECTION -->
    <div class="section-title">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
        Menu Utama (Operasional)
    </div>

    <div class="guide-grid">
        <!-- 1. Dashboard -->
        <div class="guide-card">
            <div class="guide-icon icon-dash">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
            </div>
            <h3>Dashboard</h3>
            <p>Pusat kendali dan ringkasan seluruh data website Anda secara visual.</p>
            <ul>
                <li>Melihat total paket, testimoni, dan pendaftar.</li>
                <li>Memantau jumlah pengunjung harian.</li>
                <li>Tombol cepat untuk Hapus Cache dan Lihat Website.</li>
            </ul>
        </div>

        <!-- 2. Kelola Paket -->
        <div class="guide-card">
            <div class="guide-icon icon-pkg">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"></path><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"></path></svg>
            </div>
            <h3>Kelola Paket</h3>
            <p>Menu untuk mengatur produk perjalanan Haji dan Umrah yang Anda tawarkan.</p>
            <ul>
                <li>Menambah, mengubah, atau menghapus paket.</li>
                <li>Mengatur harga, durasi, dan fasilitas hotel.</li>
                <li>Menampilkan/menyembunyikan paket dari jemaah.</li>
            </ul>
        </div>

        <!-- 3. Kelola Fasilitas -->
        <div class="guide-card">
            <div class="guide-icon icon-fac">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
            </div>
            <h3>Kelola Fasilitas</h3>
            <p>Mengatur daftar layanan yang didapatkan jemaah (misal: Bus AC, Muthawif).</p>
            <ul>
                <li>Menambah ikon fasilitas yang menarik.</li>
                <li>Mengatur deskripsi layanan di halaman utama.</li>
                <li>Mengubah urutan fasilitas yang tampil.</li>
            </ul>
        </div>

        <!-- 4. Pendaftaran -->
        <div class="guide-card">
            <div class="guide-icon icon-reg">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 00-3-3.87"></path><path d="M16 3.13a4 4 0 010 7.75"></path></svg>
            </div>
            <h3>Pendaftaran</h3>
            <p>Tempat memproses data calon jemaah yang masuk melalui formulir website.</p>
            <ul>
                <li>Melihat detail data diri dan paket yang dipilih jemaah.</li>
                <li><strong>Kelola Status:</strong> Tandai jemaah sebagai "Dikonfirmasi" atau "Selesai".</li>
                <li><strong>Indikator Warna:</strong> Baris berwarna <span style="background:#f0f7ff; color:#1e40af; padding:2px 6px; border-radius:4px; border-left:3px solid #3b82f6; font-weight:bold;">Biru Muda</span> menandakan pendaftar yang statusnya masih <strong>Menunggu Verifikasi</strong>.</li>
                <li><strong>Data Jemaah:</strong> Seluruh data formulir tersimpan aman dan teratur berdasarkan waktu terbaru.</li>
            </ul>
        </div>

        <!-- 5. Testimoni -->
        <div class="guide-card">
            <div class="guide-icon icon-testi">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"></path></svg>
            </div>
            <h3>Testimoni</h3>
            <p>Manajemen ulasan jemaah untuk meningkatkan kepercayaan calon pelanggan.</p>
            <ul>
                <li>Menyetujui ulasan baru sebelum tampil di website.</li>
                <li>Mengedit teks ulasan jika ada kesalahan ketik.</li>
                <li>Memberikan rating bintang pada ulasan jemaah.</li>
            </ul>
        </div>

        <!-- 6. Detail Pengunjung -->
        <div class="guide-card">
            <div class="guide-icon icon-view">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            </div>
            <h3>Detail Pengunjung</h3>
            <p>Data teknis mengenai siapa saja yang sedang melihat website Anda.</p>
            <ul>
                <li>Melihat alamat IP dan perangkat pengunjung (HP/PC).</li>
                <li>Mengetahui waktu kunjungan terpopuler.</li>
                <li>Melihat total klik pada setiap halaman.</li>
            </ul>
        </div>
    </div>

    <!-- PENGATURAN SECTION -->
    <div class="section-title">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"></path></svg>
        Pengaturan Sistem
    </div>

    <div class="guide-grid">
        <!-- 7. Kelola Akun -->
        <div class="guide-card">
            <div class="guide-icon icon-acc">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </div>
            <h3>Kelola Akun</h3>
            <p>Pengaturan profil pribadi Anda sebagai administrator sistem.</p>
            <ul>
                <li>Mengubah Nama Lengkap dan Email Admin.</li>
                <li>Mengganti Kata Sandi (Password) secara berkala.</li>
                <li>Mengatur foto profil admin.</li>
            </ul>
        </div>

        <!-- 8. Pengaturan Web -->
        <div class="guide-card">
            <div class="guide-icon icon-set">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
            </div>
            <h3>Pengaturan Web</h3>
            <p>Command center untuk mengubah tampilan dan informasi seluruh website.</p>
            <ul>
                <li>Ganti Logo, Nama Travel, dan Kontak WhatsApp.</li>
                <li>Ubah Warna Tema (Branding) secara instan.</li>
                <li>Update Foto Slideshow dan Teks di halaman depan.</li>
            </ul>
        </div>
    </div>

    <!-- TIP BOX -->
    <div class="tip-box">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
        <p><strong>Satu Tips Penting:</strong> Setelah Anda melakukan perubahan besar di menu <strong>Pengaturan Web</strong>, jangan lupa untuk kembali ke <strong>Dashboard</strong> dan klik tombol <strong>"Hapus Cache Website"</strong> agar perubahan tersebut langsung muncul di layar jemaah.</p>
    </div>

    <div class="btn-footer">
        <a href="{{ route('admin.dashboard') }}" class="btn-back">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
            Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
