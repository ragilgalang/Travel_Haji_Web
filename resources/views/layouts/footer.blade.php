<!-- FOOTER -->
@if(request()->has('preview'))
<style>
    .main-footer a, 
    .main-footer button,
    .main-footer .social-btn {
        pointer-events: none !important;
        cursor: default !important;
    }
</style>
@endif
<footer class="main-footer no-edit" id="sync-footer">
    <div class="footer-container">
        <div class="footer-content">
            <!-- Brand Column -->
            <div class="footer-col brand-col">
                <div class="footer-logo" id="sync-footer-logo">
                    
                    @if(!empty($settings['footer_logo']))
                        <img src="{{ $settings['footer_logo'] }}" alt="Logo" id="sync-footer-logo-img" width="160" height="40">
                    @endif
                    
                    <span class="brand-text" id="sync-footer-site-name">{{ !empty(trim($settings['site_name'] ?? '')) ? $settings['site_name'] : 'PT. UMI MUTHMAINAH BERKAH' }}</span>
                </div>
                <div id="sync-footer-desc" class="footer-desc-box">
                    @if(!empty($settings['office_address']))
                    <div class="footer-contact-item">
                        <span class="footer-contact-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="#22c55e" xmlns="http://www.w3.org/2000/svg" class="footer-icon-mt">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                        </span>
                        @if(!empty($settings['office_map_url']))
                            <a href="{{ $settings['office_map_url'] }}" target="_blank" rel="noopener" class="footer-map-link">
                                <span id="sync-footer-addr">{{ !empty(trim($settings['office_address'] ?? '')) ? $settings['office_address'] : 'Alamat Kantor Belum Diatur' }}</span>
                            </a>
                        @else
                            <span class="brand-desc footer-brand-desc-sm" id="sync-footer-addr">{{ !empty(trim($settings['office_address'] ?? '')) ? $settings['office_address'] : 'Alamat Kantor Belum Diatur' }}</span>
                        @endif
                    </div>
                    @endif
                    @if(!empty($settings['contact_phone']))
                    <div class="footer-contact-flex">
                        <span class="footer-contact-icon px-px">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="#22c55e" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                            </svg>
                        </span>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $settings['contact_phone']) }}" class="footer-contact-link" id="sync-office-phone">{{ $settings['contact_phone'] }}</a>
                    </div>
                    @endif
                    @if(!empty($settings['contact_email']))
                    <div class="footer-contact-flex">
                        <span class="footer-contact-icon px-px">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="#22c55e" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                        </span>
                        <a href="mailto:{{ $settings['contact_email'] }}" class="footer-contact-link" id="sync-office-email">{{ $settings['contact_email'] }}</a>
                    </div>
                    @endif
                    @if(empty($settings['office_address']) && empty($settings['contact_phone']) && empty($settings['contact_email']))
                    <p class="brand-desc m-0">Silakan atur alamat kantor di menu Pengaturan Web → Footer.</p>
                    @endif
                </div>
                <div class="footer-social" id="sync-footer-social">
                    @php
                        $wa_raw = $settings['contact_wa'] ?? '081234567890';
                        $wa_clean = preg_replace('/[^0-9]/', '', $wa_raw);
                        if (str_starts_with($wa_clean, '0')) {
                            $wa_number = '62' . substr($wa_clean, 1);
                        } elseif (str_starts_with($wa_clean, '8')) {
                            $wa_number = '62' . $wa_clean;
                        } else {
                            $wa_number = $wa_clean;
                        }
                    @endphp
                    @if(!empty($settings['contact_wa']))
                        <!-- ========================================== -->
                        <!-- [TANDA: LAYANAN WHATSAPP - TOMBOL SOSMED FOOTER] -->
                        <!-- ========================================== -->
                        <a href="https://wa.me/{{ $wa_number }}?text={{ urlencode($settings['wa_msg_default'] ?? "Assalamu'alaikum Admin, saya ingin bertanya mengenai layanan di PT. Umi Muthmainah.") }}" target="_blank" class="social-btn wa" title="WhatsApp">
                            <svg viewBox="0 0 448 512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.7 17.8 69.4 27.2 106.2 27.2h.1c122.3 0 222-99.6 222-222 0-59.3-23-115.1-65.1-157.2zM223.9 445.8c-33.1 0-65.5-8.9-93.7-25.7l-6.7-4-69.8 18.3 18.7-68.1-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 54 81.2 54 130.5 0 101.7-82.8 184.5-184.5 184.5zm101.1-138.3c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-5.5-2.8-23.2-8.5-44.2-27.2-16.4-14.6-27.4-32.7-30.6-38.2-3.2-5.6-.3-8.6 2.4-11.3 2.5-2.4 5.5-6.5 8.3-9.7 2.8-3.3 3.7-5.6 5.6-9.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 13.3 5.7 23.6 9.2 31.7 11.7 13.3 4.2 25.4 3.6 35 2.2 10.7-1.5 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
                        </a>
                    @endif
                    @if(!empty($settings['site_instagram']))
                        <a href="{{ $settings['site_instagram'] }}" target="_blank" class="social-btn ig" title="Instagram">
                            <svg viewBox="0 0 448 512"><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg>
                        </a>
                    @endif
                    @if(!empty($settings['site_facebook']))
                        <a href="{{ $settings['site_facebook'] }}" target="_blank" class="social-btn fb" title="Facebook">
                            <svg viewBox="0 0 320 512"><path d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"/></svg>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Links Columns -->
            <div class="footer-links">
                <div class="link-col">
                    <h4 id="sync-footer-col1-title">{{ $settings['footer_col1_title'] ?? 'Layanan' }}</h4>
                    <ul>
                        @foreach(explode("\n", trim($settings['footer_col1_links'] ?? "Haji Reguler\nHaji Plus\nUmrah Reguler\nUmrah Ramadhan\nUmrah Keluarga")) as $link)
                            @if(trim($link)) <li><a href="#">{{ trim($link) }}</a></li> @endif
                        @endforeach
                    </ul>
                </div>
                <div class="link-col">
                    <h4 id="sync-footer-col2-title">{{ $settings['footer_col2_title'] ?? 'Informasi' }}</h4>
                    <ul>
                        @foreach(explode("\n", trim($settings['footer_col2_links'] ?? "Tentang Kami\nSyarat & Ketentuan\nKebijakan Privasi\nFAQ\nBlog Ibadah")) as $link)
                            @if(trim($link)) <li><a href="#">{{ trim($link) }}</a></li> @endif
                        @endforeach
                    </ul>
                </div>
                <div class="link-col">
                    <h4 id="sync-footer-col3-title">{{ $settings['footer_col3_title'] ?? 'Kantor Cabang' }}</h4>
                    <ul>
                        @foreach(explode("\n", trim($settings['footer_col3_links'] ?? "Jakarta Pusat\nSurabaya\nBandung\nYogyakarta\nMakassar")) as $link)
                            @if(trim($link)) <li><a href="#">{{ trim($link) }}</a></li> @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="footer-certificates" id="sync-footer-legalitas">
            <div class="cert-item footer-cert-full">
                <div class="footer-cert-flex">
                    <span class="cert-label">{{ $settings['footer_legalitas_label'] ?? 'LEGALITAS RESMI:' }}</span>
                    <div class="cert-badges">
                        @if(!empty($settings['footer_badge_1']))
                            <span class="badge">{{ $settings['footer_badge_1'] }}</span>
                        @else
                            <span class="badge">KEMENAG RI</span>
                        @endif
                        @if(!empty($settings['footer_badge_2']))
                            <span class="badge">{{ $settings['footer_badge_2'] }}</span>
                        @else
                            <span class="badge">IATA</span>
                        @endif
                        @if(!empty($settings['footer_badge_3']))
                            <span class="badge">{{ $settings['footer_badge_3'] }}</span>
                        @else
                            <span class="badge">ISO 9001</span>
                        @endif
                        @if(!empty($settings['footer_badge_4']))
                            <span class="badge">{{ $settings['footer_badge_4'] }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="copyright">
                <p id="sync-footer-copyright">
                    © {{ date('Y') }} {{ !empty($settings['site_name']) ? $settings['site_name'] : 'PT. UMI MUTHMAINAH BERKAH' }}. Seluruh Hak Cipta Dilindungi. | Support by : PT UMB
                </p>
            </div>
            <div class="arabic-quote">
                <span>لَبَّيْك اللَّهُمَّ لَبَّيْك</span>
            </div>
        </div>
    </div>
</footer>

<!-- MODAL ULASAN -->
<div id="reviewModal" class="review-modal">
    <div class="review-modal-content">
        <button class="review-close" onclick="closeReviewModal()">&times;</button>
        <h3>Bagikan Pengalaman Anda</h3>
        <p class="review-subtitle">Ulasan Anda membantu kami melayani lebih baik.</p>
        
        <form id="reviewForm" onsubmit="submitReviewForm(event)" enctype="multipart/form-data">
            @csrf
            <div class="review-form-group" style="position: relative;">
                <input type="text" id="reviewTokenInput" name="token" required placeholder="Token / No. Referensi Pendaftaran (contoh: REG-ABCD1234)" style="padding-right: 5.5rem;">
                <div style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); display: flex; gap: 4px; z-index: 10;">
                    <button type="button" onclick="startReviewScanner()" title="Scan QR Code / Barcode"
                            style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; width: 34px; height: 34px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1rem; padding: 0; transition: 0.2s;"
                            onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                        📷
                    </button>
                    <button type="button" onclick="triggerReviewQrUpload()" title="Upload Screenshot Barcode/QR Code"
                            style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; width: 34px; height: 34px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1rem; padding: 0; transition: 0.2s;"
                            onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                        📁
                    </button>
                </div>
            </div>
            <input type="file" id="reviewQrFileInput" accept="image/*" style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); border: 0; opacity: 0; pointer-events: none;" onchange="handleReviewQrFileUpload(event)">

            <!-- Review Scanner Box -->
            <div id="reviewQrScannerWrapper" style="display: none; margin-bottom: 1.5rem; border-radius: 12px; overflow: hidden; border: 2px solid var(--green, #22c55e); background: #000; position: relative;">
                <div id="review-qr-reader" style="width: 100%;"></div>
                <button type="button" onclick="stopReviewScanner()" style="position: absolute; top: 10px; right: 10px; background: rgba(220, 38, 38, 0.9); color: white; border: none; padding: 6px 12px; border-radius: 6px; font-weight: 700; cursor: pointer; z-index: 20; font-size: 0.8rem;">
                    ✕ Batal Scan
                </button>
            </div>
            <div class="review-form-group">
                <input type="text" name="name" required placeholder="Nama Lengkap">
            </div>
            <div class="review-form-group">
                <input type="text" name="location" required placeholder="Asal Kota / Cabang Pendaftaran">
            </div>
            
            <div class="review-rating-box">
                <p>Bagaimana pelayanan kami?</p>
                <div class="star-rating">
                    <input type="radio" id="star5" name="rating" value="5" required />
                    <label for="star5" title="5 Bintang">★</label>
                    <input type="radio" id="star4" name="rating" value="4" />
                    <label for="star4" title="4 Bintang">★</label>
                    <input type="radio" id="star3" name="rating" value="3" />
                    <label for="star3" title="3 Bintang">★</label>
                    <input type="radio" id="star2" name="rating" value="2" />
                    <label for="star2" title="2 Bintang">★</label>
                    <input type="radio" id="star1" name="rating" value="1" />
                    <label for="star1" title="1 Bintang">★</label>
                </div>
            </div>
            
            <div class="review-form-group">
                <textarea name="text" required rows="4" placeholder="Ceritakan kesan ibadah Anda bersama kami..." maxlength="300"></textarea>
            </div>

            <div class="review-form-group">
                <label style="display:block; font-size:0.85rem; color:#64748b; margin-bottom:8px;">
                    📸 Lampirkan Foto Kenangan (Opsional, maks. 3MB)
                </label>
                <label for="reviewImageInput" class="review-upload-label" id="reviewUploadLabel" onclick="document.getElementById('reviewImageInput').click(); event.preventDefault();">
                    <span id="reviewUploadText">Klik untuk pilih foto...</span>
                    <img id="reviewImagePreview" src="" alt="" style="display:none; max-height:140px; border-radius:8px; margin-top:8px; object-fit:cover;">
                </label>
                <input type="file" name="image" id="reviewImageInput" accept="image/jpeg,image/png,image/webp" style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); border: 0; opacity: 0; pointer-events: none;" onchange="previewReviewImage(this)">
            </div>
            
            <button type="submit" class="review-submit-btn" id="reviewSubmitBtn">
                <span class="btn-text">Kirim Ulasan</span>
                <div class="btn-spinner d-none"></div>
            </button>
        </form>
    </div>
</div>

<style>
    .d-none { display: none !important; }
</style>

<!-- html5-qrcode Library for Barcode/QR Scanning & Image Upload -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    // Review Modal Logic & Barcode/QR Scanner
    let reviewHtml5QrCode = null;

    function safeNotify(message, type = 'success') {
        if (typeof showNotification === 'function') {
            showNotification(message, type);
        } else {
            alert(message);
        }
    }

    function startReviewScanner() {
        const wrapper = document.getElementById('reviewQrScannerWrapper');
        wrapper.style.display = 'block';

        if (reviewHtml5QrCode) {
            reviewHtml5QrCode.clear();
        }

        // Gunakan pendeteksian format spesifik (QR Code & Barcode Code 39/128)
        let formats = [];
        if (typeof Html5QrcodeSupportedFormats !== 'undefined') {
            formats = [
                Html5QrcodeSupportedFormats.QR_CODE,
                Html5QrcodeSupportedFormats.CODE_39,
                Html5QrcodeSupportedFormats.CODE_128,
                Html5QrcodeSupportedFormats.EAN_13,
                Html5QrcodeSupportedFormats.EAN_8,
                Html5QrcodeSupportedFormats.UPC_A
            ];
        }

        reviewHtml5QrCode = new Html5Qrcode("review-qr-reader", formats.length > 0 ? { formatsToSupport: formats } : undefined);
        
        // Konfigurasi area scan persegi panjang (sangat optimal untuk Barcode mendatar & QR Code)
        const config = { 
            fps: 20, 
            qrbox: (width, height) => {
                const boxWidth = Math.min(width * 0.85, 320);
                const boxHeight = Math.min(height * 0.45, 160);
                return { width: boxWidth, height: boxHeight };
            }
        };

        reviewHtml5QrCode.start(
            { facingMode: "environment" },
            config,
            (decodedText, decodedResult) => {
                console.log("Review Barcode/QR Code terdeteksi:", decodedText);

                // Ekstrak kode REG-XXXX
                let matchedCode = decodedText.trim();
                const regex = /(REG-[A-Z0-9]+)/i;
                const match = decodedText.match(regex);
                if (match) {
                    matchedCode = match[1].toUpperCase();
                }

                document.getElementById('reviewTokenInput').value = matchedCode;

                stopReviewScanner();
                safeNotify('QR Code / Barcode berhasil dipindai!', 'success');
            },
            (errorMessage) => {
                // Abaikan pembacaan frame gagal
            }
        ).catch(err => {
            console.error("Gagal memulai kamera ulasan: ", err);
            safeNotify("Gagal mengakses kamera. Silakan periksa izin kamera perangkat Anda.", "error");
            wrapper.style.display = 'none';
        });
    }

    function stopReviewScanner() {
        const wrapper = document.getElementById('reviewQrScannerWrapper');
        wrapper.style.display = 'none';

        if (reviewHtml5QrCode) {
            reviewHtml5QrCode.stop().then(() => {
                console.log("Scanner ulasan dihentikan.");
                reviewHtml5QrCode = null;
            }).catch(err => {
                console.error("Gagal menghentikan scanner ulasan: ", err);
            });
        }
    }

    function triggerReviewQrUpload() {
        stopReviewScanner();
        document.getElementById('reviewQrFileInput').click();
    }

    function handleReviewQrFileUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        safeNotify('Sedang membaca gambar...', 'info');

        const fileScanner = new Html5Qrcode("review-qr-reader");

        fileScanner.scanFile(file, true)
            .then(decodedText => {
                console.log("Review QR Code berhasil dibaca dari file:", decodedText);

                let matchedCode = decodedText.trim();
                const regex = /(REG-[A-Z0-9]+)/i;
                const match = decodedText.match(regex);
                if (match) {
                    matchedCode = match[1].toUpperCase();
                }

                document.getElementById('reviewTokenInput').value = matchedCode;
                safeNotify('QR Code / Barcode dari gambar berhasil dibaca!', 'success');

                event.target.value = '';
            })
            .catch(err => {
                console.error("Gagal memindai file gambar ulasan:", err);
                safeNotify("Barcode/QR Code tidak terdeteksi pada gambar. Pastikan gambar jelas dan pas.", "error");
                event.target.value = '';
            });
    }

    function openReviewModal() {
        // RESET TOMBOL SEBELUM DIBUKA
        const submitBtn = document.getElementById('reviewSubmitBtn');
        const btnText = submitBtn.querySelector('.btn-text');
        const spinner = submitBtn.querySelector('.btn-spinner');
        
        submitBtn.disabled = false;
        btnText.classList.remove('d-none');
        spinner.classList.add('d-none');

        document.getElementById('reviewModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeReviewModal() {
        document.getElementById('reviewModal').classList.remove('active');
        document.body.style.overflow = '';
        stopReviewScanner();
    }

    function previewReviewImage(input) {
        const preview = document.getElementById('reviewImagePreview');
        const label   = document.getElementById('reviewUploadText');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (file.size > 3 * 1024 * 1024) {
                alert('Ukuran foto terlalu besar! Maksimal 3MB.');
                input.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.style.display = 'block';
                label.textContent = '✅ ' + file.name;
            };
            reader.readAsDataURL(file);
        }
    }

    document.getElementById('reviewModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeReviewModal();
        }
    });

    async function submitReviewForm(event) {
        event.preventDefault();
        
        const form = event.target;

        // CEK VALIDASI BROWSER DULU
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const submitBtn = document.getElementById('reviewSubmitBtn');
        const btnText = submitBtn.querySelector('.btn-text');
        const spinner = submitBtn.querySelector('.btn-spinner');
        
        // Show loading state
        submitBtn.disabled = true;
        btnText.classList.add('d-none');
        spinner.classList.remove('d-none');
        
        const formData = new FormData(form);
        
        try {
            const response = await fetch('{{ route('review.submit') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            const contentType = response.headers.get("content-type");
            let result;
            if (contentType && contentType.indexOf("application/json") !== -1) {
                result = await response.json();
            } else {
                throw new Error('Terjadi kesalahan pada server (Bukan JSON). Silakan coba lagi.');
            }
            
            if (response.ok) {
                // Success
                form.innerHTML = `
                    <div class="review-success-box" style="text-align:center; padding: 20px;">
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 15px;">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        <h4 style="color:#10b981; font-weight:700; margin-bottom:10px;">Terima Kasih!</h4>
                        <p style="color:#64748b; font-size:0.9rem;">${result.message}</p>
                    </div>
                `;
                
                setTimeout(() => {
                    closeReviewModal();
                    setTimeout(() => window.location.reload(), 500);
                }, 3000);
            } else {
                throw new Error(result.message || 'Token pendaftaran tidak valid atau terjadi kesalahan.');
            }
        } catch (error) {
            // MATIKAN LOADING JIKA ERROR
            submitBtn.disabled = false;
            btnText.classList.remove('d-none');
            spinner.classList.add('d-none');

            // Tampilkan error di dalam modal dengan gaya premium
            const originalContent = form.innerHTML;
            form.innerHTML = `
                <div class="review-error-box" style="text-align:center; padding: 20px;">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 15px;">
                        <circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <h4 style="color:#ef4444; font-weight:700; margin-bottom:10px;">Akses Ditolak</h4>
                    <p style="color:#64748b; font-size:0.9rem; margin-bottom:20px;">${error.message}</p>
                    <button type="button" onclick="location.reload()" class="btn" style="background:#ef4444; color:white; padding:8px 20px; border-radius:50px; font-size:0.85rem; cursor:pointer; border:none;">Coba Lagi</button>
                </div>
            `;
            
            // Opsional: Jika tidak ingin reload, bisa kembalikan form setelah beberapa detik
            // setTimeout(() => { form.innerHTML = originalContent; }, 4000);
        }
    }
</script>
