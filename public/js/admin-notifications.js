/**
 * ╔══════════════════════════════════════════════════════════════════════╗
 * ║  FILE: public/js/admin-notifications.js                             ║
 * ║  FUNGSI: Notifikasi Real-time Pendaftaran Baru di Panel Admin        ║
 * ║                                                                      ║
 * ║  KONSEP PENTING untuk Pemula:                                        ║
 * ║  File ini SECARA OTOMATIS memeriksa database setiap 30 detik.       ║
 * ║  Jika ada pendaftaran baru, browser akan berbunyi & memunculkan      ║
 * ║  notifikasi popup (seperti notif WhatsApp di desktop).               ║
 * ║                                                                      ║
 * ║  MENGAPA file ini terpisah dari Blade?                               ║
 * ║  Karena kode JS yang campur dengan HTML/Blade susah di-debug,        ║
 * ║  tidak bisa di-lint, dan tampak berantakan. File terpisah            ║
 * ║  membuat kode lebih bersih dan mudah dipahami.                       ║
 * ║                                                                      ║
 * ║  CARA DIBACA: File ini diload di admin/layout.blade.php              ║
 * ║  dengan: <script src="{{ asset('js/admin-notifications.js') }}" defer>║
 * ║                                                                      ║
 * ║  DATA YANG DIBUTUHKAN (dikirim dari Blade lewat window.AdminConfig): ║
 * ║  - checkNewUrl      : URL endpoint untuk cek pendaftaran baru        ║
 * ║  - siteLogo         : URL logo (ikon notifikasi browser)             ║
 * ║  - registrationsUrl : URL halaman pendaftaran (untuk klik notif)     ║
 * ║  - notificationSoundUrl : URL file MP3 bunyi notifikasi              ║
 * ╚══════════════════════════════════════════════════════════════════════╝
 */

/**
 * IIFE — Immediately Invoked Function Expression
 * Artinya: fungsi yang langsung dijalankan begitu file dimuat.
 *
 * MENGAPA pakai IIFE (function(){})() ?
 * → Agar semua variabel di dalam sini (lastKnownRegistrationId, dll)
 *   TIDAK bocor ke global scope (window).
 * → Bayangkan namespace/ruang privat: aman dari konflik dengan kode JS lain.
 *
 * Pola umum:
 *   (function() {
 *       // kode aman di sini
 *   })();
 */
(function () {
    'use strict'; // Mode ketat: mencegah kesalahan JS yang umum (variabel tidak dideklarasi, dll)

    /**
     * Baca konfigurasi dari window.AdminConfig yang sudah diisi oleh Blade.
     * Jika AdminConfig tidak ada (misal: diakses langsung tanpa Blade), pakai {} kosong
     * agar kode tidak crash / error.
     */
    const config = window.AdminConfig || {};

    /**
     * localStorage — penyimpanan data permanen di browser pengguna.
     * Data tetap ada meskipun halaman di-refresh atau browser ditutup.
     * BERBEDA dengan sessionStorage (hilang saat tab ditutup).
     *
     * Di sini kita simpan ID terakhir pendaftaran yang sudah diketahui,
     * agar saat muat ulang halaman tidak memicu notifikasi berulang.
     */
    let lastKnownRegistrationId = localStorage.getItem('last_reg_id');

    /**
     * Buat objek Audio untuk bunyi notifikasi.
     * Audio API bawaan browser — tidak butuh library tambahan.
     * .play() akan diblokir browser jika pengguna BELUM berinteraksi dengan halaman.
     */
    const notificationSound = new Audio(config.notificationSoundUrl || '');

    /**
     * Minta izin notifikasi browser (popup di pojok layar seperti notif WhatsApp).
     * Browser hanya mengizinkan jika pengguna klik "Allow" pada dialog yang muncul.
     * Status izin ada 3: 'default' (belum ditanya), 'granted' (diizinkan), 'denied' (ditolak).
     */
    if (Notification.permission !== 'granted' && Notification.permission !== 'denied') {
        Notification.requestPermission();
    }

    /**
     * ─────────────────────────────────────────────────────────────
     * FUNGSI: checkNewRegistrations()
     * ─────────────────────────────────────────────────────────────
     * Mengirim permintaan ke server untuk mengecek apakah ada
     * pendaftaran baru sejak ID terakhir yang kita ketahui.
     *
     * Teknik ini disebut "polling" — cek berkala ke server.
     * Alternatif yang lebih canggih adalah "WebSocket" (koneksi dua arah),
     * tapi polling lebih mudah untuk pemula.
     */
    function checkNewRegistrations() {
        /**
         * Template literal (`) — cara modern menulis string dengan variabel di dalamnya.
         * ${variabel} akan diganti nilainya secara otomatis.
         * Hasil: "https://domain.com/admin/check-new?last_id=123"
         */
        const url = `${config.checkNewUrl}?last_id=${lastKnownRegistrationId || ''}`;

        /**
         * fetch() — cara modern JavaScript untuk mengirim HTTP request.
         * Menggantikan XMLHttpRequest (XHR) yang lebih lama dan rumit.
         * fetch() bersifat ASYNCHRONOUS (tidak memblokir — kode lain tetap berjalan).
         *
         * .then() dijalankan saat server merespons.
         * response.json() mengurai teks JSON dari server menjadi objek JavaScript.
         */
        fetch(url)
            .then(response => response.json())
            .then(data => {
                /**
                 * data.new_count  — jumlah pendaftaran baru
                 * data.latest_id  — ID pendaftaran terbaru
                 * data.latest_data — detail pendaftaran terbaru (nama, dll)
                 *
                 * CATATAN: Kita hanya notifikasi jika SUDAH ADA ID sebelumnya.
                 * Ini mencegah notifikasi "palsu" saat admin pertama kali buka halaman.
                 */
                if (data.new_count > 0 && lastKnownRegistrationId) {
                    playNotification(data.new_count, data.latest_data);
                }

                // Perbarui ID terakhir di memori dan localStorage
                if (data.latest_id) {
                    lastKnownRegistrationId = data.latest_id;
                    localStorage.setItem('last_reg_id', data.latest_id);
                }
            })
            /**
             * .catch() menangkap error (misalnya: koneksi internet putus, server down).
             * Tanpa ini, error akan diam-diam gagal tanpa pemberitahuan.
             */
            .catch(err => console.error('Notification check failed:', err));
    }

    /**
     * ─────────────────────────────────────────────────────────────
     * FUNGSI: playNotification(count, data)
     * ─────────────────────────────────────────────────────────────
     * Memainkan bunyi dan menampilkan notifikasi popup browser.
     *
     * @param {number} count - Jumlah pendaftaran baru
     * @param {object|null} data - Data detail pendaftaran terbaru dari server
     */
    function playNotification(count, data) {
        /**
         * .play() mengembalikan Promise.
         * Browser bisa MENOLAK .play() jika pengguna belum berinteraksi dengan halaman.
         * .catch(() => {}) mencegah error tak tertangani (unhandled rejection) muncul di konsol.
         */
        notificationSound.play().catch(() => {
            console.warn('Sound play was blocked by the browser.');
        });

        // Tampilkan notifikasi browser hanya jika izin sudah diberikan
        if (Notification.permission === 'granted') {
            /**
             * Operator ternary bertingkat untuk mengambil nama pendaftar.
             * Urutan prioritas: data.nama_lengkap → 'Customer' → 'Jemaah'
             */
            const name = data ? (data.nama_lengkap || 'Customer') : 'Jemaah';

            /**
             * new Notification() membuat popup notifikasi di pojok layar.
             * Sama seperti notifikasi dari aplikasi desktop.
             * - body: teks konten notifikasi
             * - icon: gambar/ikon kecil di pojok kiri notifikasi
             */
            const notification = new Notification('Pendaftaran Baru!', {
                body: `${name} baru saja mendaftar. Total ${count} pendaftaran baru.`,
                icon: config.siteLogo,
            });

            /**
             * Saat notifikasi diklik → fokus ke tab browser ini
             * dan arahkan ke halaman daftar pendaftaran.
             */
            notification.onclick = () => {
                window.focus();
                window.location.href = config.registrationsUrl;
            };
        }
    }

    /**
     * JALANKAN SATU KALI saat halaman pertama dimuat.
     * Tujuan: menangkap ID pendaftaran terbaru saat ini,
     * agar polling berikutnya tahu kapan ada "yang baru".
     * (Tidak memicu notifikasi karena lastKnownRegistrationId masih null)
     */
    checkNewRegistrations();

    /**
     * setInterval(fungsi, millisecond) — jalankan fungsi secara berulang setiap N ms.
     * 30000 ms = 30 detik.
     * Jadi setiap 30 detik, browser akan bertanya ke server: "ada pendaftaran baru?"
     */
    setInterval(checkNewRegistrations, 30000);

})(); // Tanda kurung di akhir ini yang membuat IIFE langsung dijalankan
