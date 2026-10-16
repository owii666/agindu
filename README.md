# AGINDU - Agribisnis Indonesia Mendunia

Sistem Ketertelusuran Rantai Pasok dan Paspor Digital Komoditas Ekspor Indonesia

AGINDU adalah aplikasi web ketertelusuran (traceability) komoditas ekspor agribisnis Indonesia berbasis PHP Native dengan arsitektur Model-View-Controller (MVC) flat-root. Sistem ini mencatat perjalanan produk dari lahan panen, proses pengemasan, kepatuhan dokumen fitosanitari dan sertifikasi internasional, pemantauan telemetri rantai dingin (cold chain), hingga penerbitan paspor digital publik berupa QR Code yang dapat dipindai oleh importir maupun konsumen mancanegara.

---

## 1. Fitur Utama

- Autentikasi Pengguna: Sistem login aman berbasis sesi dengan pembatasan percobaan login (rate limiting) dan perlindungan CSRF token.
- Dashboard Eksekutif: Ringkasan statistik operasional, volume ekspor terdaftar, sebaran komoditas unggulan, dan status pemantauan termal terkini.
- Manajemen Batch Ekspor: Manajemen data terpadu (CRUD) untuk pencatatan komoditas ekspor, eksportir, daerah asal, tanggal panen, volume bersih, dan negara tujuan.
- Paspor Digital & Label QR Code: Pembuatan QR Code instan berbasis client-side yang memuat tautan publik unik (`/trace/{token}`). Dilengkapi fitur cetak label siap tempel untuk kemasan karton/goni, pengunduhan berkas PNG, dan salin tautan verifikasi.
- Linimasa Perjalanan Logistik: Pencatatan kronologis tahapan rantai pasok (panen, sortasi kualitas, pengemasan, cold storage, pelabuhan, pengapalan, hingga kedatangan di negara tujuan).
- Pemantauan Suhu IoT & Rantai Dingin: Visualisasi data telemetri suhu dan kelembapan secara interaktif berbasis grafik garis, deteksi anomali suhu otomatis di luar rentang aman komoditas, polling latar belakang cerdas yang otomatis dijeda saat tab peramban tidak aktif, serta simulator telemetri untuk pengujian.
- Panduan Kepatuhan Ekspor: Daftar periksa persyaratan regulasi terperinci untuk negara tujuan utama (Jepang, Belanda/Uni Eropa, Amerika Serikat, Tiongkok, Arab Saudi), mencakup sertifikasi fitosanitari, MRL pestisida, kepatuhan EUDR, FDA Prior Notice, registrasi GACC, hingga akreditasi Halal SFDA.
- Halaman Publik Dua Bahasa: Halaman paspor ketertelusuran yang dapat diakses publik tanpa login dalam Bahasa Indonesia dan Bahasa Inggris (bilingual switcher), menyajikan riwayat keaslian produk kepada mitra dagang global.

---

## 2. Teknologi yang Digunakan

- Bahasa Pemrograman: PHP 8.0+ (PHP Native, OOP MVC tanpa framework pihak ketiga).
- Basis Data: MySQL 5.7+ / MySQL 8.0+ / MariaDB 10.x melalui antarmuka PDO.
- Frontend: HTML5 Semantik, CSS3 murni dengan variabel desain responsif, JavaScript (Vanilla JS).
- Visualisasi Data: Chart.js (tersimpan lokal di `assets/vendor/chart.js`).
- Generator QR: QRCode.js (tersimpan lokal di `assets/vendor/qrcode.min.js`).
- Dependensi: Nol (Zero Dependencies). Tidak memerlukan Composer maupun Node.js/npm. Proyek langsung siap dijalankan setelah di-clone.

---

## 3. Prasyarat Sistem

- Laragon (disarankan versi Full) yang memuat:
  - Apache 2.4+ (mod_rewrite aktif secara default di Laragon).
  - MySQL 5.7+ atau MySQL 8.0+ / MariaDB 10.x.
  - PHP 8.0 atau versi yang lebih baru (ekstensi pdo_mysql, mbstring, dan session aktif).
- Peramban web modern (Google Chrome, Mozilla Firefox, Microsoft Edge).
- Git (opsional bila mengunduh repositori dalam bentuk arsip ZIP).

---

## 4. Panduan Instalasi Langkah demi Langkah

### Langkah 1: Unduh atau Salin Repositori
Buka terminal dan masuk ke folder direktori `www` Laragon, lalu jalankan perintah clone:

```bash
cd C:\laragon\www
git clone URL_REPOSITORY agindu
```

Sebagai alternatif tanpa Git, unduh berkas arsip ZIP dari repositori GitHub, lalu ekstrak ke direktori:
`C:\laragon\www\agindu`

### Langkah 2: Jalankan Laragon
1. Buka aplikasi Laragon.
2. Klik tombol "Start All" untuk mengaktifkan layanan Apache dan MySQL.

### Langkah 3: Impor Basis Data
Sistem menyediakan dua cara impor:

#### Cara A: Melalui phpMyAdmin
1. Buka peramban dan akses alamat `http://localhost/phpmyadmin`.
2. Klik tab "Import" pada menu navigasi atas.
3. Klik tombol "Choose File" dan pilih berkas `database/agindu.sql` dari dalam folder proyek.
4. Klik tombol "Go" di bagian bawah. Skrip SQL akan otomatis membuat database `agindu`, menyusun seluruh tabel, dan mengisi data awal (seed).

#### Cara B: Melalui Terminal Laragon
1. Klik kanan pada jendela Laragon, pilih "Laragon" lalu klik "Terminal" (atau tekan tombol Terminal di antarmuka Laragon).
2. Jalankan perintah impor berikut:
   ```bash
   mysql -u root < database/agindu.sql
   ```
   *Catatan: Jika instalasi MySQL Anda menggunakan kata sandi, tambahkan parameter `-p`:*
   ```bash
   mysql -u root -p < database/agindu.sql
   ```

### Langkah 4: Buka Aplikasi di Peramban
Buka peramban dan akses salah satu alamat berikut:
- Akses Standar: `http://localhost/agindu`
- Akses Virtual Host Laragon: `http://agindu.test`

*Catatan: Jika folder proyek diberi nama lain (misalnya `agindu-main`), sesuaikan alamat akses menjadi `http://localhost/agindu-main` atau `http://agindu-main.test`.*

### Langkah 5: Masuk ke Sistem
1. Pada halaman login, masukkan kredensial akun:
   - Username: `admin` (atau email: `admin@agindu.test`)
   - Kata Sandi: `admin123`
2. Klik tombol "Masuk ke Dashboard".
3. Ganti kata sandi default demi keamanan setelah berhasil masuk.

---

## 5. Konfigurasi Lingkungan Lokal (Opsional)

Secara default, aplikasi langsung menggunakan konfigurasi standar Laragon (`localhost`, port `3306`, user `root`, tanpa password).

Jika laptop atau lingkungan lokal Anda memiliki kredensial MySQL berbeda atau port khusus:
1. Salin berkas `config/config.local.example.php` menjadi `config/config.local.php`.
2. Buka berkas `config/config.local.php` lalu sesuaikan pengaturannya:
   ```php
   <?php
   return [
       'DB_HOST'           => 'localhost',
       'DB_PORT'           => '3306',
       'DB_NAME'           => 'agindu',
       'DB_USER'           => 'root',
       'DB_PASS'           => 'katasandibaru',
       'BASE_URL_OVERRIDE' => '',
       'APP_DEBUG'         => true,
   ];
   ```
3. Berkas `config/config.local.php` ini otomatis diabaikan oleh Git melalui `.gitignore` sehingga tidak akan mengubah konfigurasi rekan tim lainnya.

---

## 6. Menguji Pemindaian QR Code dari Ponsel

Untuk memindai label QR code menggunakan kamera ponsel fisik secara lokal:

1. Pastikan ponsel dan komputer/laptop terhubung ke jaringan Wi-Fi yang sama.
2. Periksa alamat IP komputer pada jaringan lokal:
   - Buka Command Prompt / PowerShell di komputer.
   - Ketik `ipconfig` lalu cari baris `IPv4 Address` (contoh: `192.168.1.10`).
3. Buka aplikasi di komputer menggunakan alamat IP tersebut:
   `http://192.168.1.10/agindu`
4. Masuk ke sistem, lalu buka halaman detail batch atau halaman cetak QR.
5. QR code yang dihasilkan otomatis menyematkan alamat IP tersebut ke dalam tautan paspor publik (`http://192.168.1.10/agindu/trace/{token}`).
6. Arahkan kamera ponsel ke QR code di layar. Halaman publik paspor ketertelusuran akan langsung terbuka di peramban ponsel tanpa memerlukan autentikasi.
7. Jika ponsel gagal membuka alamat, pastikan layanan Apache diizinkan melewati Windows Firewall untuk profil Private Network.
8. Catatan Penting: QR code yang dibuat ketika aplikasi diakses melalui alamat `localhost` hanya dapat dibuka pada peramban komputer lokal yang bersangkutan.

---

## 7. Akun Demo dan Data Awal

Basis data awal yang diimpor dari `database/agindu.sql` telah dilengkapi data percontohan lengkap:

- Akun Akses Pengguna:
  - Role: Administrator Produsen Ekspor
  - Username: `admin`
  - Email: `admin@agindu.test`
  - Kata Sandi: `admin123`
- Komoditas Unggulan (5 Komoditas):
  - Kopi Arabika Gayo Specialty (Rentang aman: 18 - 24 °C)
  - Kakao Fermentasi Kolaka Grade A (Rentang aman: 16 - 22 °C)
  - Pala Organik Banda Whole (Rentang aman: 20 - 26 °C)
  - Manggis Super Fresh Tabanan (Rentang aman: 8 - 12 °C)
  - Udang Vaname Beku Tarakan IQF (Rentang aman: -22 s/d -18 °C)
- Negara Tujuan Ekspor (5 Negara & Kawasan):
  - Jepang (Asia Timur)
  - Belanda (Uni Eropa)
  - Amerika Serikat (Amerika Utara)
  - Tiongkok (Asia Timur)
  - Arab Saudi (Timur Tengah)
- Batch Ekspor: 8 batch komoditas dengan kode tetap, token publik terenkripsi, dan beragam status rantai pasok (Proses, Dikemas, Dalam Perjalanan, Terkirim, Selesai).
- Linimasa Logistik: 28 catatan peristiwa perjalanan rantai pasok dari kebun panen hingga pelabuhan bongkar.
- Telemetri Suhu: 66 titik pencatatan sensor realistis dengan pola variasi alami dan skenario anomali suhu.
- Dokumen Ekspor: 23 item daftar periksa kelengkapan fitosanitari, sertifikat asal (COO/SKA), packing list, dan perizinan internasional.

---

## 8. Struktur Direktori Proyek

```
agindu/
├── app/
│   ├── Controllers/         # Controller aplikasi (Auth, Batch, Dashboard, ExportGuide, Suhu, Trace)
│   ├── Core/                # Komponen inti sistem (Router, Controller base, Database, Auth, Session, Csrf, Validator, Helpers)
│   ├── Lang/                # Berkas kamus multibahasa (id.php, en.php)
│   ├── Models/              # Model data interaksi basis data
│   ├── Views/               # Tampilan antarmuka (Auth, Batch, Dashboard, Export Guide, Suhu, Trace, Errors, Layouts)
│   └── .htaccess            # Proteksi akses langsung ke folder aplikasi
├── assets/
│   ├── css/                 # Lembar gaya CSS (app.css, trace.css)
│   ├── js/                  # Skrip logika frontend (app.js, qr-helper.js, temperature.js)
│   └── vendor/              # Pustaka lokal tanpa CDN (chart.js, qrcode.min.js)
├── config/
│   ├── config.php           # Konfigurasi utama aplikasi dan basis data default Laragon
│   ├── config.local.example.php # Contoh berkas override konfigurasi lokal
│   ├── database.php         # Handler koneksi PDO MySQL tersinkronisasi WIB (+07:00)
│   └── .htaccess            # Proteksi akses langsung ke berkas konfigurasi
├── database/
│   ├── agindu.sql           # Skrip SQL skema basis data dan data seed awal
│   └── .htaccess            # Proteksi akses langsung ke berkas basis data
├── storage/
│   ├── logs/                # Berkas catatan error dan log sistem (app.log)
│   ├── uploads/             # Direktori penyimpanan berkas unggahan
│   └── .htaccess            # Proteksi akses langsung ke folder storage
├── .gitattributes           # Konfigurasi normalisasi line ending dan berkas biner
├── .gitignore               # Konfigurasi berkas yang diabaikan dari Git
├── .htaccess                # Konfigurasi penulisan ulang URL (Front Controller Apache)
├── index.php                # Titik masuk utama aplikasi (Front Controller)
├── LICENSE                  # Lisensi perangkat lunak terbuka MIT
└── README.md                # Dokumentasi panduan operasional proyek
```

---

## 9. Daftar Rute Utama Aplikasi

| Rute URL | Metode HTTP | Keterangan |
| :--- | :--- | :--- |
| `/` atau `/dashboard` | GET | Ringkasan metrik operasional dan status rantai pasok |
| `/login` | GET, POST | Tampilan form autentikasi dan pemrosesan masuk |
| `/logout` | POST | Terminasi sesi login dan keluar akun aman |
| `/batches` | GET | Tabel daftar seluruh batch ekspor |
| `/batches/create` | GET, POST | Formulir pendaftaran batch komoditas baru |
| `/batches/{id}` | GET | Tampilan rincian lengkap komoditas, linimasa, dokumen, dan QR |
| `/batches/{id}/edit` | GET, POST | Pembaruan status dan data operasional batch |
| `/batches/{id}/delete` | POST | Penghapusan catatan batch |
| `/batches/{id}/timeline` | POST | Penambahan peristiwa linimasa perjalanan logistik |
| `/batches/{id}/qr` | GET | Halaman sertifikat label cetak QR paspor digital |
| `/temperature` | GET | Dasbor grafik telemetri suhu IoT dan log anomali |
| `/api/temperature/{batch}` | GET | Endpoint JSON pembacaan sensor suhu per batch |
| `/api/simulate/{batch}` | POST | Endpoint simulasi penambahan data suhu acak |
| `/export-guide` | GET | Panduan regulasi fitosanitari dan standar dokumen negara tujuan |
| `/export-guide/toggle/{id}` | POST | Pengalihan status kelengkapan checklist dokumen ekspor |
| `/trace/{token}` | GET | Halaman paspor ketertelusuran publik (akses bebas tanpa login) |

---

## 10. Cara Mereset Data Awal

Jika Anda ingin mengembalikan kondisi data ke awal setelah melakukan pengujian CRUD atau simulasi suhu:
1. Impor kembali berkas `database/agindu.sql` menggunakan phpMyAdmin atau Terminal Laragon.
2. Seluruh tabel akan dihapus ulang (`DROP TABLE IF EXISTS`) dan diinisialisasi kembali dengan data awal lengkap.

---

## 11. Panduan Pemecahan Masalah (Troubleshooting)

| Masalah | Kemungkinan Penyebab | Solusi |
| :--- | :--- | :--- |
| Semua halaman selain beranda menghasilkan error 404 Not Found | Modul Apache `mod_rewrite` belum aktif atau berkas `.htaccess` di root tidak terbaca | Pastikan berkas `.htaccess` di root proyek ter-clone dengan benar. Di Laragon, klik Menu, Apache, Modules, pastikan `rewrite_module` dicentang, lalu klik Restart Apache. |
| Muncul pesan "Koneksi database gagal..." | Layanan MySQL belum menyala atau database `agindu` belum diimpor | Buka Laragon dan pastikan tombol menunjukkan bahwa MySQL sedang aktif (port 3306). Pastikan Anda telah mengimpor berkas `database/agindu.sql`. Jika Anda menggunakan password root MySQL khusus, buat berkas `config/config.local.php` sesuai panduan pada Bagian 5. |
| Halaman putih kosong (blank screen) atau error 500 | Terjadi kesalahan eksekusi PHP internal | Buka berkas `storage/logs/app.log` untuk membaca rincian pesan error dan baris kode terkait. Pastikan versi PHP di Laragon adalah versi 8.0 atau yang lebih baru. |
| Grafik telemetri atau QR Code tidak muncul di layar | Berkas pustaka lokal di `assets/vendor` tidak lengkap atau diblokir peramban | Pastikan berkas `assets/vendor/chart.js` dan `assets/vendor/qrcode.min.js` ada di komputer Anda. Buka Developer Tools peramban (tekan F12) pada tab Console untuk melihat pesan error skrip. |
| Domain `agindu.test` tidak dapat dibuka | Fitur Auto Virtual Hosts Laragon belum memetakan folder proyek | Pastikan fitur Auto Virtual Hosts aktif melalui menu Laragon (Menu, Apache, centang Auto Virtual Hosts), lalu pilih Reload. Alternatifnya, gunakan alamat jalur subfolder `http://localhost/agindu`. |
| Akun login terkunci dengan pesan percobaan berlebih | Mekanisme proteksi brute-force mengunci akun setelah 5 kali gagal berturut-turut | Tunggu selama 15 menit agar pembatasan kedaluwarsa secara otomatis, atau tutup dan buka kembali jendela browser dalam mode penyamaran (Incognito), atau impor ulang skrip `database/agindu.sql`. |

---

## 12. Catatan Keamanan

1. Repositori ini dirancang sebagai sistem purwarupa (prototype) untuk pengembangan dan pengujian pada lingkungan lokal Laragon.
2. Segera perbarui kata sandi default pengguna administratif (`admin`) setelah instalasi pertama kali.
3. Jangan pernah menambahkan atau meng-commit berkas `config/config.local.php` ke repositori publik apabila memuat kredensial rahasia komputer pribadi Anda.

---

## 13. Pernyataan Penyangkalan (Disclaimer)

Seluruh informasi regulasi karantina, fitosanitari, batasan batas maksimum residu (MRL), sertifikasi EUDR, dan persyaratan kepabeanan yang disajikan pada modul Panduan Ekspor (Export Guide) dirancang sebagai referensi arsitektur data dan ilustrasi alur bisnis. Untuk kegiatan ekspor agribisnis aktual, eksportir diwajibkan melakukan konfirmasi dan validasi langsung ke instansi berwenang terkait seperti Badan Karantina Indonesia (Barantin), Kementerian Perdagangan Republik Indonesia, Kementerian Kelautan dan Perikanan (KKP), serta otoritas karantina resmi negara tujuan.

---

## 14. Kontribusi & Lisensi

Alur kontribusi pengembangan:
1. Lakukan *Fork* repositori ini ke akun GitHub Anda.
2. Buat *branch* fitur baru (`git checkout -b fitur/nama-fitur-baru`).
3. Lakukan *commit* terhadap perubahan Anda (`git commit -m "Deskripsi singkat perubahan"`).
4. Dorong ke *branch* repositori Anda (`git push origin fitur/nama-fitur-baru`).
5. Ajukan permohonan *Pull Request* dengan deskripsi yang jelas.

Proyek ini didistribusikan di bawah lisensi terbuka [MIT License](LICENSE).
