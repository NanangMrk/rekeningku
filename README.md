# RekeningKu - Direktori Rekening & Pembayaran Digital Modern

Aplikasi web direktori dan katalog rekening pembayaran digital (Bank, E-Wallet, dan QRIS) yang modern, elegan, responsif, dan mudah dibagikan kepada klien, pelanggan, pembeli, atau donatur untuk mempermudah transaksi transfer pembayaran.

---

## 🛠️ Teknologi & Versi Program

Aplikasi ini dibangun menggunakan arsitektur **PHP Native murni** yang sangat ringan, tanpa ketergantungan framework berat, dan menggunakan basis data berbasis file **SQLite** (zero-configuration):

| Komponen | Teknologi | Versi yang Didukung / Direkomendasikan | Keterangan |
| :--- | :--- | :--- | :--- |
| **Bahasa Pemrograman** | **PHP** | **PHP 8.1+** (Kompatibel PHP 8.0, 8.1, 8.2, 8.3) | Menggunakan typed properties, strict types, match expressions |
| **Ekstensi PHP** | **PDO SQLite** & **SQLite3** | Bundled dengan PHP | Ekstensi `pdo_sqlite`, `sqlite3`, `fileinfo` |
| **Basis Data** | **SQLite 3** | SQLite 3.x | Otomatis dibuat di `database/app.sqlite` tanpa konfigurasi MySQL |
| **Frontend Styling** | **Vanilla CSS3** | Modern CSS (Custom Design System) | Variabel CSS, Glassmorphism, Micro-animations, Responsive Grid |
| **Frontend Scripting**| **Vanilla JavaScript** | ES6+ Standard | Ringan tanpa dependencies (tanpa jQuery, React, Vue) |
| **Ikon Interface** | **Font Awesome** | **v6.5.1 CDN** | Ikon perbankan, e-wallet, status, dan navigasi |
| **Tipografi Font** | **Google Fonts** | Inter, Manrope, JetBrains Mono | Tipografi elegan dan modern |
| **Web Server** | **Apache / Nginx / LiteSpeed** | Apache 2.4+ / Nginx 1.20+ | Mendukung mod_rewrite & file security |

---

## 💡 Apa Itu RekeningKu?

**RekeningKu** adalah solusi praktis untuk menampilkan seluruh rekening bank, dompet digital (*E-Wallet* seperti GoPay, OVO, DANA, ShopeePay), serta barcode **QRIS** dalam satu tautan (*landing page*) yang profesional.

### Keunggulan Utama:
- **Tampilan Publik Menawan**: Meniru kartu ATM fisik digital (*digital card style*) dengan berbagai pilihan palet warna tema (*Sapphire*, *Emerald*, *Obsidian*, *Violet*, *Amber*).
- **Salin 1-Klik (*One-Click Copy*)**: Pengunjung dapat menyalin nomor rekening secara instan dengan indikator animasi visual.
- **Modal Pop-up QRIS Fullscreen**: Tampilan gambar barcode QRIS yang jernih dengan fitur *zoom*, tombol *download* barcode, dan rincian NMID.
- **Filter & Pencarian Cepat**: Filter berdasarkan kategori (*Semua*, *Rekening Bank*, *E-Wallet*, *QRIS*) dan pencarian instan nama bank atau pemilik.
- **Panel Admin Lengkap & Aman**:
  - Dashboard statistik ringkasan rekening (total, aktif, nonaktif).
  - Tambah, edit, dan hapus rekening dengan *Live ATM Card Preview Studio*.
  - Atur urutan tampil kartu (*drag & drop* / urutan angka).
  - Sakelar aktif/nonaktif (*toggle status*) rekening tanpa perlu menghapus data.
  - Menu **Setting** untuk mengganti password administrator secara aman.
  - Proteksi CSRF Token, enkripsi password `password_hash()`, dan sanitasi HTML.

---

## 🔑 Kredensial Default Login Admin

Secara bawaan, aplikasi telah menyiapkan akun administrator awal:

* **URL Login Admin**: `http://domain-anda.com/admin/` atau `http://localhost/rekeningku/admin/`
* **Username**: `admin`
* **Password Default**: `admin123`

> ⚠️ **PENTING / KEAMANAN:**
> Setelah berhasil login pertama kali, **sangat disarankan** untuk segera mengganti password default melalui menu **Setting** di sidebar navigasi admin demi keamanan sistem Anda.

---

## 📖 Cara Penggunaan Aplikasi

### 1. Untuk Pengunjung Publik
1. Buka URL utama website (misal: `https://domain-anda.com` atau `http://localhost/rekeningku`).
2. Pengunjung dapat memilih tab filter (**Semua**, **Bank**, **E-Wallet**, **QRIS**) atau mengetik nama bank di kolom pencarian.
3. Klik tombol **Salin Nomor** pada kartu untuk menyalin nomor rekening ke clipboard.
4. Klik **Lihat QRIS** untuk memperbesar barcode QRIS dan mengunduh gambar jika diperlukan.

### 2. Untuk Administrator
1. Buka path `/admin` di browser.
2. Masukkan username (`admin`) dan password Anda.
3. **Dashboard**: Memantau jumlah rekening aktif dan status visibilitas.
4. **Kelola Rekening**:
   - Klik **Tambah Rekening Baru** untuk menambahkan rekening bank, e-wallet, atau upload barcode QRIS.
   - Gunakan *Live Card Studio* di sebelah kiri untuk melihat simulasi kartu ATM secara langsung saat mengetik.
   - Klik tombol toggle hijau/abu-abu untuk mengaktifkan atau menyembunyikan rekening dari publik.
   - Klik **Edit** untuk mengubah nomor rekening, nama pemilik, catatan transfer (misal: *BIFast Rp 2.500*), atau mengganti tema kartu.
5. **Menu Setting**:
   - Klik menu **Setting** di navbar samping.
   - Masukkan password saat ini, tentukan password baru (minimal 6 karakter, terdapat indikator kekuatan password *Lemah/Sedang/Kuat*), lalu simpan.
6. **Keluar / Logout**:
   - Klik tombol **Logout** untuk mengakhiri sesi admin dengan aman.

---

## 🚀 Panduan Instalasi Lengkap

Pilih salah satu metode lingkungan server di bawah ini:

---

### A. Panduan Instalasi di XAMPP (Windows / macOS / Linux)

1. **Unduh / Salin Source Code**:
   - Pindahkan folder proyek ini ke dalam direktori web root XAMPP:
     - **Windows**: `C:\xampp\htdocs\rekeningku`
     - **macOS**: `/Applications/XAMPP/xamppfiles/htdocs/rekeningku`
     - **Linux**: `/opt/lampp/htdocs/rekeningku`

2. **Pastikan Ekstensi PDO SQLite Aktif di PHP**:
   - Buka **XAMPP Control Panel**.
   - Pada baris **Apache**, klik tombol **Config** -> pilih **PHP (php.ini)**.
   - Cari baris berikut (gunakan Ctrl + F):
     ```ini
     ;extension=pdo_sqlite
     ;extension=sqlite3
     ```
   - Hilangkan tanda titik koma (`;`) di depan baris tersebut sehingga menjadi:
     ```ini
     extension=pdo_sqlite
     extension=sqlite3
     ```
   - Simpan file `php.ini`.

3. **Jalankan Web Server**:
   - Di XAMPP Control Panel, klik **Start** pada modul **Apache**. *(Modul MySQL tidak wajib dijalankan karena aplikasi ini menggunakan SQLite)*.

4. **Buka di Browser**:
   - Halaman Publik: `http://localhost/rekeningku/`
   - Panel Admin: `http://localhost/rekeningku/admin/`

---

### B. Panduan Instalasi di Laragon (Windows)

1. **Salin Folder Proyek**:
   - Pindahkan folder proyek ke folder root Laragon:
     `C:\laragon\www\rekeningku`

2. **Periksa Ekstensi SQLite**:
   - Klik kanan pada area kosong di jendela Laragon -> arahkan ke **PHP** -> **Quick Settings** atau **Extensions**.
   - Pastikan opsi **pdo_sqlite** dan **sqlite3** memiliki tanda centang (aktif).

3. **Start All Services**:
   - Buka aplikasi **Laragon** dan klik tombol **Start All**.

4. **Akses Aplikasi**:
   - Jika fitur Pretty URL Laragon aktif:
     - Halaman Publik: `http://rekeningku.test/`
     - Panel Admin: `http://rekeningku.test/admin/`
   - Atau melalui localhost biasa:
     - Halaman Publik: `http://localhost/rekeningku/`
     - Panel Admin: `http://localhost/rekeningku/admin/`

---

### C. Panduan Instalasi di aaPanel (Linux VPS / Server Production)

aaPanel adalah salah satu control panel hosting server Linux yang sangat populer. Berikut langkah-langkah detail memasang RekeningKu di aaPanel:

#### 1. Buat Website Baru di aaPanel
1. Login ke dashboard **aaPanel** Anda.
2. Masuk ke menu **Website** di sidebar kiri -> klik **Add site**.
3. Masukkan domain Anda (misal: `rekening.domainanda.com`).
4. Pada pilihan **PHP Version**, pilih **PHP 8.1** atau **PHP 8.2** (atau PHP 8.0+).
5. Pada pilihan **Database**, pilih **Don't create** *(karena aplikasi menggunakan SQLite)*.
6. Klik **Submit**.

#### 2. Unggah Source Code Proyek
1. Buka menu **Files** di aaPanel.
2. Masuk ke direktori website Anda (biasanya di `/www/wwwroot/rekening.domainanda.com`).
3. Hapus file default bawaan aaPanel (`index.html`, `404.html`).
4. Unggah seluruh file proyek RekeningKu (atau upload file `.zip` lalu ekstrak di folder tersebut).

#### 3. Pastikan Ekstensi PHP Terpasang di aaPanel
1. Masuk ke menu **App Store** di sidebar aaPanel.
2. Cari versi PHP yang digunakan website Anda (contoh: **PHP 8.1**), lalu klik **Setting**.
3. Masuk ke tab **Install extensions**:
   - Pastikan ekstensi **fileinfo** terpasang.
   - Ekstensi `pdo_sqlite` dan `sqlite3` pada aaPanel biasanya sudah aktif secara default. Jika belum, masuk ke tab **Configuration file** dan pastikan baris `extension=pdo_sqlite` tidak dikomentari.
4. Klik tab **Service** -> klik **Restart** pada PHP.

#### 4. Atur Hak Akses Folder (Permissions)
Server Linux memerlukan izin tulis (*write permission*) untuk membuat file database SQLite dan menyimpan upload gambar QRIS:
1. Di menu **Files** aaPanel, buka folder `/www/wwwroot/rekening.domainanda.com`.
2. Klik kanan pada folder `database` -> pilih **Permission**:
   - Owner: `www`
   - Permission: `755` atau `775` (Centang Read, Write, Execute).
3. Lakukan hal yang sama pada folder `assets/images/qris` (buat folder ini jika belum ada):
   - Owner: `www`
   - Permission: `755` atau `775`.
4. *Alternatif via Terminal Terminal aaPanel*:
   ```bash
   chown -R www:www /www/wwwroot/rekening.domainanda.com
   chmod -R 755 /www/wwwroot/rekening.domainanda.com
   chmod -R 775 /www/wwwroot/rekening.domainanda.com/database
   chmod -R 775 /www/wwwroot/rekening.domainanda.com/assets/images
   ```

#### 5. Konfigurasi Keamanan Web Server (Nginx)
Jika Anda menggunakan **Nginx** di aaPanel, lindungi file database `.sqlite` agar tidak bisa diunduh oleh pengunjung luar:
1. Masuk ke menu **Website** -> klik nama domain Anda -> buka tab **Config** (atau **URL rewrite**).
2. Tambahkan baris proteksi berikut di dalam block `server { ... }`:
   ```nginx
   # Blokir akses langsung ke file sqlite & direktori database
   location ~* \.(sqlite|sqlite3|db)$ {
       deny all;
       return 404;
   }

   location ^~ /database/ {
       deny all;
       return 404;
   }
   ```
3. Klik **Save**.

#### 6. Pasang SSL (HTTPS) Gratis
1. Pada menu setting website di aaPanel, masuk ke tab **SSL**.
2. Pilih tab **Let's Encrypt**.
3. Centang domain Anda dan klik **Apply**.
4. Aktifkan sakelar **Force HTTPS** untuk keamanan maksimal.

#### 7. Selesai!
Buka website Anda:
- Landing Page: `https://rekening.domainanda.com/`
- Login Admin: `https://rekening.domainanda.com/admin/`

---

## 📁 Struktur Direktori Proyek

```text
rekeningku/
├── index.php                      # Halaman publik direktori rekening (Landing Page)
├── README.md                      # Dokumentasi lengkap & panduan instalasi
├── admin/                         # Area panel kendali administrator
│   ├── index.php                  # Halaman login admin dengan proteksi CSRF
│   ├── dashboard.php              # Dashboard ringkasan & statistik rekening
│   ├── rekening.php               # Halaman manajemen daftar rekening & kelola urutan
│   ├── rekening-add.php           # Formulir tambah rekening + Live Card Studio
│   ├── rekening-edit.php          # Formulir edit data rekening & upload QRIS
│   ├── rekening-delete.php        # Handler hapus rekening
│   ├── rekening-toggle.php        # Handler sakelar aktif/nonaktif rekening (AJAX)
│   ├── reorder.php                # Handler simpan urutan posisi rekening (AJAX)
│   ├── settings.php               # Menu Setting untuk ganti password administrator
│   └── logout.php                 # Handler logout & penghapusan sesi aman
├── config/
│   └── database.php               # Konfigurasi PDO SQLite, inisialisasi tabel & auto-seed
├── includes/
│   ├── auth.php                   # Autentikasi sesi admin & proteksi rute
│   ├── functions.php              # Helper (CSRF, sanitasi HTML, tema kartu, upload QRIS)
│   ├── header.php                 # Template header & navigasi navbar admin
│   └── footer.php                 # Template footer & script admin
├── assets/
│   ├── css/
│   │   ├── style.css              # Styling utama landing page publik
│   │   └── admin.css              # Styling dashboard & panel admin
│   ├── js/
│   │   ├── app.js                 # Logika interaktif publik (copy, search, modal QRIS)
│   │   └── admin.js               # Logika live preview kartu, toggle, modal konfirmasi
│   └── images/
│       └── qris/                  # Folder penyimpanan upload berkas barcode QRIS
└── database/
    ├── .htaccess                  # Proteksi Apache (Deny from all)
    └── app.sqlite                 # File basis data SQLite (dibuat otomatis oleh PHP)
```

---

## 🛡️ Fitur Keamanan

1. **CSRF Protection**: Setiap formulir POST dilengkapi token anti-CSRF unik per sesi browser.
2. **Password Hashing Modern**: Menggunakan algoritma default PHP (`PASSWORD_DEFAULT` - Bcrypt/Argon2).
3. **Database Guard**: Direktori `database/` diproteksi file `.htaccess` dan aturan Nginx agar file `.sqlite` tidak dapat diunduh publik.
4. **Validasi File Upload**: Gambar QRIS divalidasi tipe MIME, ekstensi (`jpg, jpeg, png, webp`), dan ukuran maksimal (5MB). Nama file di-generate secara acak menggunakan `bin2hex(random_bytes(4))` untuk mencegah *file collision* dan eksekusi skrip berbahaya.
5. **Sanitasi Output**: Seluruh teks dinamis di-escape menggunakan fungsi `e()` (`htmlspecialchars` dengan bendera `ENT_QUOTES`) guna mencegah serangan Cross-Site Scripting (XSS).

---

## ❓ FAQ & Troubleshooting

**Q: Muncul error `could not find driver` saat pertama kali membuka website?**  
A: Ekstensi `pdo_sqlite` belum aktif di server PHP Anda. Aktifkan `extension=pdo_sqlite` di file `php.ini` Anda lalu restart web server (Apache/PHP-FPM).

**Q: Muncul error `unable to open database file` atau `attempt to write a readonly database`?**  
A: Masalah izin folder (*file permissions*). Pastikan folder `database/` dan file `app.sqlite` memiliki izin tulis oleh user web server (misal: user `www` atau `www-data`, chmod 755/775).

**Q: Bagaimana cara mencadangkan (*backup*) data rekening?**  
A: Sangat mudah! Karena menggunakan SQLite, Anda cukup menyalin file `database/app.sqlite` dan folder `assets/images/qris/`. Tidak memerlukan dump SQL eksternal.

---

## 📄 Lisensi

Proyek ini bersifat *Open-Source* dan bebas digunakan untuk kebutuhan personal maupun komersial.
