@extends('admin.layout')

@section('page_title', 'Pengaturan Web')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-settings.css') }}">
@endpush

@section('content')
<div class="settings-container">
    <!-- BANNER ATAS -->
    <div class="welcome-banner">
        <div class="welcome-text">
            <div class="greeting-tag">Manajemen Web</div>
            <h2>Pengaturan <em>Website Portal</em></h2>
            <p>Kelola identitas, kontak, layout halaman beranda, hingga integrasi Google Maps secara tersentralisasi.</p>
        </div>
    </div>

    <!-- FORM UTAMA -->
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="settings-grid">
            <!-- MENU TAB KIRI -->
            <div class="tab-menu-card">
                <button type="button" class="tab-btn active" onclick="switchTab(event, 'tab-umum')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>
                    </svg>
                    ⚙️ Umum & Kontak
                </button>
                <button type="button" class="tab-btn" onclick="switchTab(event, 'tab-hero')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <path d="M21 15l-5-5L5 21"/>
                    </svg>
                    📸 Banner Hero
                </button>
                <button type="button" class="tab-btn" onclick="switchTab(event, 'tab-about')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4.5 8-11.8A8 8 0 0012 2a8 8 0 00-8 8.2c0 7.3 8 11.8 8 11.8z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    📖 Tentang Perusahaan
                </button>
                <button type="button" class="tab-btn" onclick="switchTab(event, 'tab-sections')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <line x1="9" y1="3" x2="9" y2="21" />
                    </svg>
                    📣 Judul Bagian
                </button>
                <button type="button" class="tab-btn" onclick="switchTab(event, 'tab-itinerary')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    🗺️ Alur Perjalanan
                </button>
                <button type="button" class="tab-btn" onclick="switchTab(event, 'tab-footer')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 15h18M3 9h18M3 21h18M9 3v18"/>
                    </svg>
                    🖼️ Bagian Footer
                </button>
                <button type="button" class="tab-btn" onclick="switchTab(event, 'tab-deploy')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21.2 15c.6-1 .8-2.2.8-3.5C22 7.4 17.6 3 12 3 7.8 3 4.3 5.5 3.2 9.2 1.4 10.4 0 12.5 0 15c0 3.3 2.7 6 6 6h13c2.8 0 5-2.2 5-5 0-1-.3-2-.8-2.9z"/>
                    </svg>
                    🚀 Pengaturan Deploy
                </button>
            </div>

            <!-- CARD KONTEN TAB KANAN -->
            <div class="tab-content-card">
                
                <!-- TAB 1: UMUM & KONTAK -->
                <div id="tab-umum" class="tab-pane active">
                    <h3 class="section-title-premium">⚙️ Pengaturan Dasar & Kontak</h3>
                    <p class="section-desc-premium">Sesuaikan identitas website, meta tag SEO, dan tautan kontak resmi.</p>

                    <div class="grid-2-col">
                        <div class="form-group-premium">
                            <label class="admin-label">Logo Utama Website (PNG)</label>
                            @if(!empty($settings['site_logo']))
                                <div class="image-preview-wrapper">
                                    <img src="{{ $settings['site_logo'] }}" class="preview-thumbnail" alt="Logo">

                                </div>
                            @endif
                            <input type="file" name="site_logo" class="form-control-premium" accept="image/*">
                            <span class="help-block-premium">Format disarankan PNG transparan, ukuran ideal max. 500kb.</span>
                        </div>

                        <div class="form-group-premium">
                            <label class="admin-label">Logo OG (Open Graph) / Sosial Media</label>
                            @if(!empty($settings['og_image']))
                                <div class="image-preview-wrapper">
                                    <img src="{{ $settings['og_image'] }}" class="preview-thumbnail" alt="OG Image">

                                </div>
                            @endif
                            <input type="file" name="og_image" class="form-control-premium" accept="image/*">
                            <span class="help-block-premium">Logo yang akan tampil ketika link website dibagikan ke medsos/WA.</span>
                        </div>
                    </div>

                    <div class="form-group-premium">
                        <label class="admin-label">Nama Website / Perusahaan</label>
                        <input type="text" name="site_name" class="form-control-premium" value="{{ $settings['site_name'] ?? 'PT. Umi Muthmainah Berkah' }}" required>
                    </div>

                    <div class="form-group-premium">
                        <label class="admin-label">Deskripsi Website (Meta Deskripsi SEO)</label>
                        <textarea name="site_description" class="form-control-premium" rows="3">{{ $settings['site_description'] ?? 'Penyelenggara Perjalanan Ibadah Haji & Umrah Resmi Kemenag RI.' }}</textarea>
                    </div>

                    <div class="form-group-premium">
                        <label class="admin-label">Keywords Website (Meta Keywords SEO)</label>
                        <input type="text" name="site_keywords" class="form-control-premium" value="{{ $settings['site_keywords'] ?? 'haji, umrah, travel haji premium' }}">
                        <span class="help-block-premium">Pisahkan setiap kata kunci dengan tanda koma (,).</span>
                    </div>

                    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 24px 0;">

                    <div class="grid-2-col">
                        <!-- ========================================== -->
                        <!-- [TANDA: PENGATURAN WHATSAPP - DASHBOARD ADMIN] -->
                        <!-- ========================================== -->
                        <div class="form-group-premium">
                            <label class="admin-label">Nomor WhatsApp CS (Gunakan format 62)</label>
                            <input type="text" name="contact_wa" class="form-control-premium" value="{{ $settings['contact_wa'] ?? '628123456789' }}">
                            <span class="help-block-premium">Contoh pengisian: <b>6281299998888</b> (tanpa spasi / tanda +)</span>
                        </div>
                        <div class="form-group-premium">
                            <label class="admin-label">Nomor Telepon CS (Kantor / Resmi)</label>
                            <input type="text" name="contact_phone" class="form-control-premium" value="{{ $settings['contact_phone'] ?? '021-1234567' }}">
                        </div>
                    </div>

                    <div class="form-group-premium">
                        <label class="admin-label">Email Layanan Jemaah</label>
                        <input type="email" name="contact_email" class="form-control-premium" value="{{ $settings['contact_email'] ?? 'info@travelhaji.com' }}">
                    </div>

                    <div class="form-group-premium">
                        <label class="admin-label">Pesan WhatsApp Otomatis (Default)</label>
                        <textarea name="wa_msg_default" class="form-control-premium" rows="2">{{ $settings['wa_msg_default'] ?? "Assalamu'alaikum Admin, saya ingin bertanya mengenai layanan di PT. Umi Muthmainah." }}</textarea>
                    </div>

                    <div class="form-group-premium">
                        <label class="admin-label">Pesan WhatsApp Tanya Paket (Selebihnya akan menyebut nama paket otomatis)</label>
                        <textarea name="wa_msg_package" class="form-control-premium" rows="2">{{ $settings['wa_msg_package'] ?? "Assalamu'alaikum Admin, saya tertarik dengan paket [NAMA_PAKET]. Mohon info detail pendaftarannya. Syukron." }}</textarea>
                    </div>

                    <div class="form-group-premium">
                        <label class="admin-label">Pesan WhatsApp Tanya Haji Khusus (CTA Bawah)</label>
                        <textarea name="wa_msg_haji" class="form-control-premium" rows="2">{{ $settings['wa_msg_haji'] ?? "Bismillah, saya ingin berkonsultasi mengenai rencana pendaftaran Haji khusus di PT. Umi Muthmainah. Terima kasih." }}</textarea>
                    </div>
                </div>

                <!-- TAB 2: BANNER HERO -->
                <div id="tab-hero" class="tab-pane">
                    <h3 class="section-title-premium">📸 Banner Hero & Media Utama</h3>
                    <p class="section-desc-premium">Kelola teks intro utama, slideshow gambar latar belakang hero, dan video profil.</p>

                    <div class="form-group-premium">
                        <label class="admin-label">Badge Atas (Baris 1)</label>
                        <input type="text" name="hero_badge" class="form-control-premium" value="{{ $settings['hero_badge'] ?? 'TERDAFTAR RESMI KEMENAG RI · IZIN PPIU NO. U - 207/2021' }}">
                    </div>

                    <div class="form-group-premium">
                        <label class="admin-label">Badge Atas (Baris 2)</label>
                        <input type="text" name="hero_badge_2" class="form-control-premium" value="{{ $settings['hero_badge_2'] ?? 'PIHK 81200009510360001' }}">
                    </div>

                    <div class="form-group-premium">
                        <label class="admin-label">Judul Utama H1 (Hero)</label>
                        <textarea name="hero_title" class="form-control-premium" rows="2">{{ $settings['hero_title'] ?? 'Wujudkan Perjalanan Suci ke Baitullah' }}</textarea>
                        <span class="help-block-premium">Gunakan kode html <b>&lt;br&gt;</b> untuk baris baru.</span>
                    </div>

                    <div class="form-group-premium">
                        <label class="admin-label">Deskripsi Intro Hero</label>
                        <textarea name="hero_description" class="form-control-premium" rows="3">{{ $settings['hero_description'] ?? 'Lebih dari 12.000 jemaah telah kami antarkan ke Tanah Suci dengan aman, nyaman, dan penuh keberkahan.' }}</textarea>
                    </div>

                    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 24px 0;">

                    <h4 style="font-size: 0.95rem; font-weight: 700; color: #1e293b; margin-bottom: 12px;">🖼️ Slideshow Foto Background Hero (Maksimal 4 Foto)</h4>
                    <div class="grid-2-col">
                        @for($i = 1; $i <= 4; $i++)
                            <div class="form-group-premium advantage-inner-card">
                                <label class="admin-label">Background Latar Belakang {{ $i }}</label>
                                @if(!empty($settings['hero_bg_' . $i]))
                                    @php
                                        $bgRaw = $settings['hero_bg_' . $i];
                                        $bgClean = preg_replace('/^https?:\/\/[^\/]+/', '', $bgRaw);
                                        $bgPreviewUrl = str_starts_with($bgRaw, 'http') && !str_contains($bgRaw, '/uploads/') ? $bgRaw : asset(ltrim($bgClean, '/'));
                                    @endphp
                                    <div class="image-preview-wrapper" style="margin-bottom:8px; padding:8px;">
                                        <img src="{{ $bgPreviewUrl }}" class="preview-thumbnail" style="width: 90px; height: 60px; object-fit: cover; border-radius:6px; border:1px solid #e2e8f0;" alt="Hero BG {{ $i }}">
                                    </div>
                                @endif
                                <input type="file" name="hero_bg_{{ $i }}" class="form-control-premium" accept="image/*">
                            </div>
                        @endfor
                    </div>

                    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 24px 0;">

                    <div class="form-group-premium">
                        <label class="admin-label">🎬 Video Profil (Pop-up "Tonton Video")</label>
                        @if(!empty($settings['hero_video_url']))
                            @php
                                $hvRaw = $settings['hero_video_url'];
                                $hvClean = preg_replace('/^https?:\/\/[^\/]+/', '', $hvRaw);
                                $hvPreviewUrl = asset(ltrim($hvClean, '/'));
                            @endphp
                            <div class="image-preview-wrapper">
                                <video src="{{ $hvPreviewUrl }}" controls class="video-preview-player" style="max-width:100%; border-radius:8px;"></video>
                            </div>
                        @endif
                        <input type="file" name="hero_video_url" class="form-control-premium" accept="video/mp4,video/webm">
                        <span class="help-block-premium">Format MP4/WebM. Disarankan kompres video di bawah 20MB demi kecepatan pemuatan.</span>
                    </div>
                </div>

                <!-- TAB 3: TENTANG KAMI & STATS -->
                <div id="tab-about" class="tab-pane">
                    <h3 class="section-title-premium">📖 Tentang Kami & Statistik Kepercayaan</h3>
                    <p class="section-desc-premium">Sesuaikan narasi profil singkat dan empat pilar statistik pencapaian di landing page.</p>

                    <div class="form-group-premium">
                        <label class="admin-label">Sub-Judul Atas (Eyebrow)</label>
                        <input type="text" name="about_badge" class="form-control-premium" value="{{ $settings['about_badge'] ?? 'Tentang Kami' }}">
                    </div>

                    <div class="form-group-premium">
                        <label class="admin-label">Judul Utama H2 (Tentang Kami)</label>
                        <input type="text" name="about_title" class="form-control-premium" value="{{ $settings['about_title'] ?? 'Melayani Sepenuh Hati Sejak 2014' }}">
                    </div>

                    <div class="form-group-premium">
                        <label class="admin-label">Deskripsi Panjang Tentang Kami</label>
                        <textarea name="about_description" class="form-control-premium" rows="5">{{ $settings['about_description'] ?? 'PT. Umi Muthmainah Berkah hadir untuk memberikan pengalaman ibadah terbaik bagi Anda.' }}</textarea>
                    </div>

                    <div class="grid-2-col">
                        <div class="form-group-premium">
                            <label class="admin-label">Foto Sisi Kiri Tentang Kami</label>
                            @if(!empty($settings['about_image']))
                                @php
                                    $aiRaw = $settings['about_image'];
                                    $aiClean = preg_replace('/^https?:\/\/[^\/]+/', '', $aiRaw);
                                    $aiPreviewUrl = str_starts_with($aiRaw, 'http') && !str_contains($aiRaw, '/uploads/') ? $aiRaw : asset(ltrim($aiClean, '/'));
                                @endphp
                                <div class="image-preview-wrapper">
                                    <img src="{{ $aiPreviewUrl }}" class="preview-thumbnail" style="width: 120px; height: 70px; object-fit: cover; border-radius:6px; border:1px solid #e2e8f0;" alt="About Image">
                                </div>
                            @endif
                            <input type="file" name="about_image" class="form-control-premium" accept="image/*">
                        </div>

                        <div class="form-group-premium">
                            <label class="admin-label">Video Samping Tentang (Opsional Pengganti Foto)</label>
                            @if(!empty($settings['about_video']))
                                @php
                                    $avRaw = $settings['about_video'];
                                    $avClean = preg_replace('/^https?:\/\/[^\/]+/', '', $avRaw);
                                    $avPreviewUrl = asset(ltrim($avClean, '/'));
                                @endphp
                                <div class="image-preview-wrapper">
                                    <video src="{{ $avPreviewUrl }}" controls class="video-preview-player" style="max-width:100%; border-radius:8px;"></video>
                                </div>
                            @endif
                            <input type="file" name="about_video" class="form-control-premium" accept="video/mp4,video/webm">
                        </div>
                    </div>

                    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 24px 0;">

                    <h4 style="font-size: 0.95rem; font-weight: 700; color: #1e293b; margin-bottom: 16px;">💎 3 Poin Keunggulan Utama</h4>
                    @for($i = 1; $i <= 3; $i++)
                        <div class="advantage-inner-card">
                            <h5 style="font-size: 0.85rem; font-weight: 700; color: #0f291e; margin-bottom: 10px;">Poin Keunggulan #{{ $i }}</h5>
                            <div class="grid-2-col" style="margin-bottom: 10px;">
                                <div class="form-group-premium" style="margin-bottom: 0;">
                                    <label class="admin-label">Ikon (Gunakan Emoji)</label>
                                    <input type="text" name="about_item{{ $i }}_icon" class="form-control-premium" value="{{ $settings['about_item' . $i . '_icon'] ?? ($i == 1 ? '🕋' : ($i == 2 ? '💎' : '🌟')) }}">
                                </div>
                                <div class="form-group-premium" style="margin-bottom: 0;">
                                    <label class="admin-label">Judul Singkat</label>
                                    <input type="text" name="about_item{{ $i }}_title" class="form-control-premium" value="{{ $settings['about_item' . $i . '_title'] ?? '' }}">
                                </div>
                            </div>
                            <div class="form-group-premium" style="margin-bottom: 0; margin-top: 10px;">
                                <label class="admin-label">Deskripsi Poin</label>
                                <input type="text" name="about_item{{ $i }}_text" class="form-control-premium" value="{{ $settings['about_item' . $i . '_text'] ?? '' }}">
                            </div>
                        </div>
                    @endfor

                </div>

                <!-- TAB 4: JUDUL BAGIAN -->
                <div id="tab-sections" class="tab-pane">
                    <h3 class="section-title-premium">📣 Judul Bagian (Section Titles)</h3>
                    <p class="section-desc-premium">Kelola teks intro/judul untuk masing-masing bagian yang tampil di halaman utama.</p>

                    <div class="advantage-inner-card">
                        <h4 style="font-size: 0.85rem; font-weight: 700; color: #0f291e; margin-bottom: 12px;">🛍️ Bagian Paket Perjalanan</h4>
                        <div class="grid-2-col">
                            <div class="form-group-premium" style="margin-bottom:0;">
                                <label class="admin-label">Eyebrow Teks</label>
                                <input type="text" name="sec_pkg_eye" class="form-control-premium" value="{{ $settings['sec_pkg_eye'] ?? 'Pilihan Paket' }}">
                            </div>
                            <div class="form-group-premium" style="margin-bottom:0;">
                                <label class="admin-label">Judul Utama</label>
                                <input type="text" name="sec_pkg_title" class="form-control-premium" value="{{ $settings['sec_pkg_title'] ?? 'Paket <em>Haji & Umrah</em> Terbaik' }}">
                            </div>
                        </div>
                    </div>

                    <div class="advantage-inner-card">
                        <h4 style="font-size: 0.85rem; font-weight: 700; color: #0f291e; margin-bottom: 12px;">📅 Bagian Alur Itinerary / Jadwal</h4>
                        <div class="grid-2-col">
                            <div class="form-group-premium" style="margin-bottom:0;">
                                <label class="admin-label">Eyebrow Teks</label>
                                <input type="text" name="sec_itin_eye" class="form-control-premium" value="{{ $settings['sec_itin_eye'] ?? 'Jadwal Perjalanan' }}">
                            </div>
                            <div class="form-group-premium" style="margin-bottom:0;">
                                <label class="admin-label">Judul Utama</label>
                                <input type="text" name="sec_itin_title" class="form-control-premium" value="{{ $settings['sec_itin_title'] ?? 'Alur Proses Keberangkatan' }}">
                            </div>
                        </div>
                    </div>

                    <div class="advantage-inner-card">
                        <h4 style="font-size: 0.85rem; font-weight: 700; color: #0f291e; margin-bottom: 12px;">📸 Bagian Galeri Media</h4>
                        <div class="grid-2-col">
                            <div class="form-group-premium" style="margin-bottom:0;">
                                <label class="admin-label">Eyebrow Teks</label>
                                <input type="text" name="sec_gal_eye" class="form-control-premium" value="{{ $settings['sec_gal_eye'] ?? 'Galeri Perjalanan' }}">
                            </div>
                            <div class="form-group-premium" style="margin-bottom:0;">
                                <label class="admin-label">Judul Utama</label>
                                <input type="text" name="sec_gal_title" class="form-control-premium" value="{{ $settings['sec_gal_title'] ?? 'Dokumentasi <em>Ibadah Jemaah</em>' }}">
                            </div>
                        </div>
                    </div>

                    <div class="grid-2-col">
                        <div class="advantage-inner-card" style="margin-bottom:0;">
                            <label class="admin-label">🏠 Judul Bagian Fasilitas</label>
                            <input type="text" name="sec_fac_title" class="form-control-premium" value="{{ $settings['sec_fac_title'] ?? 'Fasilitas Eksklusif Jemaah' }}">
                        </div>
                        <div class="advantage-inner-card" style="margin-bottom:0;">
                            <label class="admin-label">✍️ Judul Bagian Testimoni</label>
                            <input type="text" name="sec_testi_title" class="form-control-premium" value="{{ $settings['sec_testi_title'] ?? 'Cerita & Kesan Jemaah Kami' }}">
                        </div>
                    </div>

                    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 24px 0;">

                    <div class="advantage-inner-card">
                        <h4 style="font-size: 0.85rem; font-weight: 700; color: #0f291e; margin-bottom: 12px;">📣 Kartu CTA Bawah (Hubungi Kami)</h4>
                        <div class="grid-2-col">
                            <div class="form-group-premium">
                                <label class="admin-label">Teks Eyebrow</label>
                                <input type="text" name="sec_cta_eye" class="form-control-premium" value="{{ $settings['sec_cta_eye'] ?? 'Mulai Perjalanan Suci' }}">
                            </div>
                            <div class="form-group-premium">
                                <label class="admin-label">Judul Utama CTA</label>
                                <input type="text" name="sec_cta_title" class="form-control-premium" value="{{ $settings['sec_cta_title'] ?? 'Siap Berangkat ke <em>Tanah Suci?</em>' }}">
                            </div>
                        </div>
                        <div class="form-group-premium" style="margin-bottom:0;">
                            <label class="admin-label">Teks Tombol Chat Admin</label>
                            <input type="text" name="sec_cta_btn_text" class="form-control-premium" value="{{ $settings['sec_cta_btn_text'] ?? 'Hubungi Saya' }}">
                        </div>
                    </div>
                </div>

                <!-- TAB 5: ALUR PERJALANAN -->
                <div id="tab-itinerary" class="tab-pane">
                    <h3 class="section-title-premium">🗺️ Alur Perjalanan Haji/Umrah</h3>
                    <p class="section-desc-premium">Atur rincian hari, gambar samping, dan poin penting pendaftaran.</p>

                    <div class="grid-2-col">
                        <div class="form-group-premium">
                            <label class="admin-label">Foto / Gambar Samping Alur</label>
                            @if(!empty($settings['itin_aside_img']))
                                @php
                                    $iaiRaw = $settings['itin_aside_img'];
                                    $iaiClean = preg_replace('/^https?:\/\/[^\/]+/', '', $iaiRaw);
                                    $iaiPreviewUrl = str_starts_with($iaiRaw, 'http') && !str_contains($iaiRaw, '/uploads/') ? $iaiRaw : asset(ltrim($iaiClean, '/'));
                                @endphp
                                <div class="image-preview-wrapper">
                                    <img src="{{ $iaiPreviewUrl }}" class="preview-thumbnail" style="width: 120px; height: 160px; object-fit: cover; border-radius:6px; border:1px solid #e2e8f0;" alt="Itinerary Aside Image">
                                </div>
                            @endif
                            <input type="file" name="itin_aside_img" class="form-control-premium" accept="image/*">
                        </div>

                        <div class="form-group-premium">
                            <label class="admin-label">Video Samping (Opsional Pengganti Foto)</label>
                            @if(!empty($settings['itin_aside_video']))
                                @php
                                    $iavRaw = $settings['itin_aside_video'];
                                    $iavClean = preg_replace('/^https?:\/\/[^\/]+/', '', $iavRaw);
                                    $iavPreviewUrl = asset(ltrim($iavClean, '/'));
                                @endphp
                                <div class="image-preview-wrapper">
                                    <video src="{{ $iavPreviewUrl }}" controls class="video-preview-player" style="max-width:100%; border-radius:8px;"></video>
                                </div>
                            @endif
                            <input type="file" name="itin_aside_video" class="form-control-premium" accept="video/mp4,video/webm">
                        </div>
                    </div>

                    <div class="form-group-premium">
                        <label class="admin-label">Judul Foto Samping (Contoh: Baitullah, Makkah)</label>
                        <input type="text" name="itin_aside_title" class="form-control-premium" value="{{ $settings['itin_aside_title'] ?? 'Baitullah, Makkah Al-Mukarramah' }}">
                    </div>

                    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 24px 0;">
                    
                    <h4 style="font-size: 0.95rem; font-weight: 700; color: #1e293b; margin-bottom: 16px;">⏳ Timeline Hari (Maksimal 5)</h4>
                    @for($i = 1; $i <= 5; $i++)
                        <div class="advantage-inner-card">
                            <h5 style="font-size: 0.85rem; font-weight: 700; color: #0f291e; margin-bottom: 10px;">Hari Ke-{{ $i }}</h5>
                            <div class="grid-2-col" style="margin-bottom: 10px;">
                                <div class="form-group-premium" style="margin-bottom: 0;">
                                    <label class="admin-label">Rentang Hari (Misal: Hari 1-3)</label>
                                    <input type="text" name="itin{{ $i }}_day" class="form-control-premium" value="{{ $settings['itin' . $i . '_day'] ?? ($i == 1 ? 'Hari 1-3' : ($i == 2 ? 'Hari 4-8' : ($i == 3 ? 'Hari 9-12' : ($i == 4 ? 'Hari 13-16' : 'Hari 17-21')))) }}">
                                </div>
                                <div class="form-group-premium" style="margin-bottom: 0;">
                                    <label class="admin-label">Judul Aktivitas</label>
                                    <input type="text" name="itin{{ $i }}_title" class="form-control-premium" value="{{ $settings['itin' . $i . '_title'] ?? ($i == 1 ? 'Keberangkatan & Tiba di Madinah' : ($i == 2 ? 'Sholat Arbain di Madinah' : ($i == 3 ? 'Makkah & Umrah Wajib' : ($i == 4 ? 'Puncak Haji — Arafah, Muzdalifah, Mina' : 'Tawaf Wada & Kepulangan')))) }}">
                                </div>
                            </div>
                            <div class="form-group-premium" style="margin-bottom: 0; margin-top: 10px;">
                                <label class="admin-label">Deskripsi Aktivitas</label>
                                <textarea name="itin{{ $i }}_desc" class="form-control-premium" rows="2">{{ $settings['itin' . $i . '_desc'] ?? ($i == 1 ? 'Kumpul di embarkasi, penerbangan ke Madinah, sambutan, check-in hotel.' : ($i == 2 ? '40 waktu sholat berturut-turut di Masjid Nabawi. Ziarah Jabal Uhud, Masjid Quba.' : ($i == 3 ? 'Berihram dari Miqat, perjalanan ke Makkah. Tawaf Qudum, Sa\'i, Tahallul.' : ($i == 4 ? 'Wukuf di Arafah, mabit di Muzdalifah, lempar Jumroh.' : 'Tawaf Wada\' sebagai perpisahan dengan Baitullah.')))) }}</textarea>
                            </div>
                        </div>
                    @endfor

                    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 24px 0;">

                    <h4 style="font-size: 0.95rem; font-weight: 700; color: #1e293b; margin-bottom: 16px;">📌 3 Poin Info Penting (Dokumen, Kesehatan, Pendaftaran)</h4>
                    @for($i = 1; $i <= 3; $i++)
                        <div class="advantage-inner-card">
                            <h5 style="font-size: 0.85rem; font-weight: 700; color: #0f291e; margin-bottom: 10px;">Info #{{ $i }}</h5>
                            <div class="grid-2-col" style="margin-bottom: 10px;">
                                <div class="form-group-premium" style="margin-bottom: 0;">
                                    <label class="admin-label">Ikon (Gunakan Emoji)</label>
                                    <input type="text" name="itin_aside_i{{ $i }}_icon" class="form-control-premium" value="{{ $settings['itin_aside_i' . $i . '_icon'] ?? ($i == 1 ? '📋' : ($i == 2 ? '💉' : '📅')) }}">
                                </div>
                                <div class="form-group-premium" style="margin-bottom: 0;">
                                    <label class="admin-label">Judul Info</label>
                                    <input type="text" name="itin_aside_i{{ $i }}_title" class="form-control-premium" value="{{ $settings['itin_aside_i' . $i . '_title'] ?? ($i == 1 ? 'Dokumen Wajib' : ($i == 2 ? 'Pemeriksaan Kesehatan' : 'Pendaftaran Awal')) }}">
                                </div>
                            </div>
                            <div class="form-group-premium" style="margin-bottom: 0; margin-top: 10px;">
                                <label class="admin-label">Deskripsi Info</label>
                                <input type="text" name="itin_aside_i{{ $i }}_desc" class="form-control-premium" value="{{ $settings['itin_aside_i' . $i . '_desc'] ?? ($i == 1 ? 'Paspor berlaku min. 18 bulan, KTP, KK.' : ($i == 2 ? 'Dilakukan minimal 1 bulan sebelum keberangkatan.' : 'Daftar minimal 6 bulan sebelumnya.')) }}">
                            </div>
                        </div>
                    @endfor

                </div>

                <!-- TAB 6: BAGIAN FOOTER -->
                <div id="tab-footer" class="tab-pane">
                    <h3 class="section-title-premium">🖼️ Pengaturan Footer & Legalitas</h3>
                    <p class="section-desc-premium">Kelola logo footer dan badge logo asosiasi legalitas resmi Kemenag/ISO.</p>

                    <div class="form-group-premium">
                        <label class="admin-label">Logo Footer Khusus (PNG Transparan)</label>
                        @if(!empty($settings['footer_logo']))
                            <div class="image-preview-wrapper" style="background:#0a2116; border: 1px solid #047857;">
                                <img src="{{ $settings['footer_logo'] }}" class="preview-thumbnail" alt="Footer Logo">

                            </div>
                        @endif
                        <input type="file" name="footer_logo" class="form-control-premium" accept="image/*">
                        <span class="help-block-premium">Logo khusus footer dengan latar gelap. Biarkan kosong untuk menggunakan logo utama.</span>
                    </div>

                    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 24px 0;">

                    <h4 style="font-size: 0.95rem; font-weight: 700; color: #1e293b; margin-bottom: 12px;">🏅 Teks Badge Legalitas Bawah</h4>
                    <div class="grid-2-col">
                        <div class="form-group-premium">
                            <label class="admin-label">Badge 1</label>
                            <input type="text" name="footer_badge_1" class="form-control-premium" value="{{ $settings['footer_badge_1'] ?? 'KEMENAG RI' }}">
                        </div>
                        <div class="form-group-premium">
                            <label class="admin-label">Badge 2</label>
                            <input type="text" name="footer_badge_2" class="form-control-premium" value="{{ $settings['footer_badge_2'] ?? 'IATA' }}">
                        </div>
                        <div class="form-group-premium">
                            <label class="admin-label">Badge 3</label>
                            <input type="text" name="footer_badge_3" class="form-control-premium" value="{{ $settings['footer_badge_3'] ?? 'ISO 9001' }}">
                        </div>
                        <div class="form-group-premium">
                            <label class="admin-label">Badge 4 (Opsional)</label>
                            <input type="text" name="footer_badge_4" class="form-control-premium" value="{{ $settings['footer_badge_4'] ?? '' }}">
                        </div>
                    </div>

                    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 24px 0;">

                    <h4 style="font-size: 0.95rem; font-weight: 700; color: #1e293b; margin-bottom: 12px;">🔗 Tautan Media Sosial</h4>
                    <p class="section-desc-premium" style="margin-top: -8px; margin-bottom: 16px;">Tautan ini akan ditampilkan pada bagian footer website. Biarkan kosong jika tidak ingin menampilkannya.</p>
                    <div class="grid-2-col">
                        <div class="form-group-premium">
                            <label class="admin-label"><i class="fab fa-instagram" style="color: #E1306C; margin-right: 5px;"></i> Instagram Link</label>
                            <input type="text" name="social_ig" class="form-control-premium" value="{{ $settings['social_ig'] ?? '' }}" placeholder="https://instagram.com/...">
                        </div>
                        <div class="form-group-premium">
                            <label class="admin-label"><i class="fab fa-facebook" style="color: #1877F2; margin-right: 5px;"></i> Facebook Link</label>
                            <input type="text" name="social_fb" class="form-control-premium" value="{{ $settings['social_fb'] ?? '' }}" placeholder="https://facebook.com/...">
                        </div>
                        <div class="form-group-premium">
                            <label class="admin-label"><i class="fab fa-youtube" style="color: #FF0000; margin-right: 5px;"></i> YouTube Link</label>
                            <input type="text" name="social_yt" class="form-control-premium" value="{{ $settings['social_yt'] ?? '' }}" placeholder="https://youtube.com/...">
                        </div>
                        <div class="form-group-premium">
                            <label class="admin-label"><i class="fab fa-tiktok" style="color: #000000; margin-right: 5px;"></i> TikTok Link</label>
                            <input type="text" name="social_tiktok" class="form-control-premium" value="{{ $settings['social_tiktok'] ?? '' }}" placeholder="https://tiktok.com/@...">
                        </div>
                    </div>
                </div>

                <!-- TAB 7: PENGATURAN DEPLOY & HOSTING -->
                <div id="tab-deploy" class="tab-pane">
                    <h3 class="section-title-premium">🚀 Pengaturan Deploy & Sinkronisasi Hosting</h3>
                    <p class="section-desc-premium">Konfigurasikan target URL pemicu deploy otomatis dan kunci keamanan untuk sinkronisasi ke server hosting produksi.</p>

                    <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 15px; border-radius: 12px; font-size: 0.85rem; margin-bottom: 24px; line-height: 1.5; display: flex; gap: 10px; align-items: flex-start;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0; margin-top:2px;">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="16" x2="12" y2="12"/>
                            <line x1="12" y1="8" x2="12.01" y2="8"/>
                        </svg>
                        <div>
                            <strong>Informasi Sistem:</strong> Pengaturan di bawah ini disimpan secara aman dalam database Firebase. Jika kolom di bawah ini dibiarkan kosong, sistem secara otomatis akan menggunakan nilai cadangan (*fallback*) dari berkas konfigurasi <code>.env</code> lokal Anda.
                        </div>
                    </div>

                    <div class="form-group-premium">
                        <label class="admin-label">URL Target Deployment (Hosting / Webhook)</label>
                        <input type="url" name="deploy_target_url" class="form-control-premium" value="{{ $settings['deploy_target_url'] ?? env('DEPLOY_TARGET_URL') }}" placeholder="Contoh: https://nama-domain.com/deploy.php">
                        <span class="help-block-premium">URL skrip deploy.php atau webhook penerima pemicu di server hosting produksi Anda.</span>
                    </div>

                    <div class="form-group-premium">
                        <label class="admin-label">Token Rahasia Deployment (Deploy Secret)</label>
                        <div style="position: relative;">
                            <input type="password" name="deploy_secret" id="deploySecretInput" class="form-control-premium" value="{{ $settings['deploy_secret'] ?? env('DEPLOY_SECRET') }}" placeholder="Masukkan token keamanan rahasia" style="padding-right: 45px;">
                            <button type="button" onclick="toggleDeploySecret()" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                        <span class="help-block-premium">Token keamanan rahasia yang dikirimkan dalam header <code>X-Deploy-Token</code> untuk memverifikasi keabsahan request deploy.</span>
                    </div>
                </div>



                <!-- STICKY SUBMIT FOOTER -->
                <div class="submit-bar">
                    <!-- ========================================== -->
                    <!-- [TANDA: PENGATURAN WEB - TOMBOL SIMPAN] -->
                    <!-- ========================================== -->
                    <button type="submit" class="btn-submit-premium">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg>
                        Simpan Seluruh Pengaturan
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Tab switching logic
    function switchTab(evt, tabId) {
        // Hide all tab panes
        const tabPanes = document.querySelectorAll('.tab-pane');
        tabPanes.forEach(pane => pane.classList.remove('active'));

        // Remove active class from all buttons
        const tabBtns = document.querySelectorAll('.tab-btn');
        tabBtns.forEach(btn => btn.classList.remove('active'));

        // Show active pane & mark button active
        document.getElementById(tabId).classList.add('active');
        evt.currentTarget.classList.add('active');
        
        // Auto scroll to content top on mobile
        if(window.innerWidth < 991) {
            document.querySelector('.tab-content-card').scrollIntoView({ behavior: 'smooth' });
        }
    }

    // Auto-switch tab based on URL hash on load
    window.addEventListener('DOMContentLoaded', () => {
        const hash = window.location.hash;
        if (hash) {
            const tabId = hash.replace('#', '');
            const targetPane = document.getElementById(tabId);
            if (targetPane) {
                const tabBtns = document.querySelectorAll('.tab-btn');
                tabBtns.forEach(btn => {
                    const onclickAttr = btn.getAttribute('onclick');
                    if (onclickAttr && onclickAttr.includes(tabId)) {
                        btn.click();
                    }
                });
            }
        }
    });

    function toggleDeploySecret() {
        const input = document.getElementById('deploySecretInput');
        const eyeIcon = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
        } else {
            input.type = 'password';
            eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
        }
    }
</script>
@endpush
@endsection
