# AGENT.md

## Project Overview

Bangun aplikasi landing page sederhana untuk menampilkan kumpulan rekening yang digunakan pemilik website.

Tujuan utama aplikasi:

- Pengunjung dapat melihat daftar rekening.
- Pengunjung dapat menyalin nomor rekening dengan cepat.
- Landing page bersifat publik.
- Tidak ada tombol login pada halaman publik.
- Halaman login admin hanya dapat diakses melalui path `/admin`.
- Admin dapat mengelola daftar rekening melalui dashboard.
- Aplikasi harus ringan, sederhana, responsif, dan mudah dipelihara.

Aplikasi ini bukan payment gateway dan tidak perlu memiliki sistem transaksi.

---

# Technology Stack

Gunakan teknologi berikut:

- PHP Native
- SQLite
- HTML5
- CSS3
- JavaScript Vanilla
- Font Awesome untuk icon

Jangan menggunakan:

- Laravel
- CodeIgniter
- Symfony
- React
- Vue
- Node.js
- Bootstrap jika tidak benar-benar diperlukan
- Emoji sebagai icon interface

Gunakan desain custom CSS yang modern.

---

# Main Application Structure

Struktur aplikasi yang direkomendasikan:

```text
/
├── index.php
│
├── admin/
│   ├── index.php
│   ├── dashboard.php
│   ├── rekening.php
│   ├── rekening-add.php
│   ├── rekening-edit.php
│   ├── rekening-delete.php
│   └── logout.php
│
├── config/
│   └── database.php
│
├── includes/
│   ├── auth.php
│   ├── functions.php
│   ├── header.php
│   └── footer.php
│
├── assets/
│   ├── css/
│   │   ├── style.css
│   │   └── admin.css
│   │
│   ├── js/
│   │   └── app.js
│   │
│   └── images/
│
└── database/
    └── app.sqlite
```

Struktur boleh disesuaikan jika ada pendekatan yang lebih rapi, tetapi tetap gunakan PHP Native.

---

# Public Landing Page

URL:

```text
/
```

Halaman utama langsung menampilkan landing page rekening.

Jangan tampilkan:

- Login
- Tombol Admin
- Link Admin
- Tulisan Dashboard
- Menu menuju `/admin`

Pengunjung tidak perlu login.

---

# Landing Page Content

Bagian atas halaman dapat menampilkan:

```text
Rekening Pembayaran
```

Subjudul contoh:

```text
Silakan pilih rekening tujuan dan salin nomor rekening yang ingin digunakan.
```

Desain harus:

- clean
- modern
- simple
- responsive
- mobile-first
- mudah digunakan
- tidak terlalu banyak dekorasi

---

# Account Card

Setiap rekening ditampilkan sebagai card.

Contoh:

```text
BCA

1234567890
NANANG MRK

[ Salin Nomor Rekening ]
```

Informasi yang dapat ditampilkan:

- Nama bank
- Logo/icon bank jika tersedia
- Nomor rekening
- Nama pemilik rekening
- Label
- Tombol copy

Contoh label:

```text
Utama
Alternatif
Bisnis
Pribadi
E-Wallet
```

Label bersifat opsional.

---

# Copy Account Number

Setiap card harus mempunyai tombol:

```text
Salin Nomor Rekening
```

Ketika ditekan:

- nomor rekening masuk ke clipboard
- tidak perlu reload halaman
- tampil notifikasi kecil

Contoh:

```text
Nomor rekening berhasil disalin
```

Gunakan JavaScript Clipboard API.

Fallback dapat digunakan jika browser tidak mendukung Clipboard API.

---

# Active Account

Landing page hanya menampilkan rekening dengan:

```text
is_active = 1
```

Rekening yang dinonaktifkan oleh admin tidak boleh muncul pada halaman publik.

---

# Account Sorting

Daftar rekening harus dapat diurutkan oleh admin.

Gunakan field:

```text
sort_order
```

Query halaman publik:

```sql
ORDER BY sort_order ASC
```

---

# Admin Authentication

Halaman admin hanya tersedia melalui:

```text
/admin
```

Ketika membuka `/admin`, tampilkan halaman login.

Tidak ada akses login melalui halaman publik.

Default account development:

```text
Username: admin
Password: admin123
```

Password tersebut hanya merupakan password awal.

Jangan menyimpan password plaintext pada database.

Gunakan:

```php
password_hash()
```

untuk menyimpan password.

Gunakan:

```php
password_verify()
```

untuk validasi login.

---

# Admin Session

Gunakan PHP Session.

Contoh:

```php
session_start();
```

Setelah login berhasil, simpan informasi login admin pada session.

Contoh:

```php
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_id'] = $admin['id'];
```

Semua halaman dashboard harus memeriksa session.

Jika user belum login tetapi mencoba membuka:

```text
/admin/dashboard.php
/admin/rekening.php
/admin/rekening-add.php
/admin/rekening-edit.php
```

maka redirect ke:

```text
/admin
```

---

# Admin Dashboard

Dashboard admin harus mempunyai desain modern dan sederhana.

Gunakan layout:

```text
Sidebar
Topbar
Main Content
```

Sidebar minimal memiliki menu:

```text
Dashboard
Rekening
Logout
```

Gunakan Font Awesome untuk icon.

Contoh:

```text
Dashboard   -> fa-gauge-high
Rekening    -> fa-building-columns
Logout      -> fa-right-from-bracket
```

Jangan gunakan emoji.

---

# Dashboard Overview

Dashboard dapat menampilkan statistik sederhana:

```text
Total Rekening
Rekening Aktif
Rekening Nonaktif
```

Jangan membuat dashboard terlalu kompleks.

---

# Account Management

Admin harus dapat:

- melihat daftar rekening
- menambahkan rekening
- mengedit rekening
- menghapus rekening
- mengaktifkan rekening
- menonaktifkan rekening
- menentukan urutan rekening
- memberikan label

---

# Add Account

Form tambah rekening minimal memiliki:

```text
Nama Bank
Nomor Rekening
Nama Pemilik
Label
Urutan
Status
```

Contoh:

```text
Nama Bank:
BCA

Nomor Rekening:
1234567890

Nama Pemilik:
NANANG MRK

Label:
Utama

Urutan:
1

Status:
Aktif
```

---

# Edit Account

Semua informasi rekening harus dapat diedit.

Admin dapat mengubah:

- nama bank
- nomor rekening
- nama pemilik
- label
- urutan
- status

---

# Delete Account

Sebelum menghapus rekening tampilkan konfirmasi.

Contoh:

```text
Apakah Anda yakin ingin menghapus rekening ini?
```

Gunakan modal atau dialog sederhana.

---

# Database

Gunakan SQLite.

Nama database:

```text
app.sqlite
```

---

# Database Table: admins

Gunakan struktur:

```sql
CREATE TABLE IF NOT EXISTS admins (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

---

# Database Table: accounts

Gunakan struktur:

```sql
CREATE TABLE IF NOT EXISTS accounts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    bank_name TEXT NOT NULL,
    account_number TEXT NOT NULL,
    account_name TEXT NOT NULL,
    label TEXT,
    icon TEXT,
    sort_order INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

---

# Database Initialization

Jika database belum ada, aplikasi harus dapat membuat database secara otomatis.

Aplikasi juga harus mengecek apakah admin default sudah tersedia.

Jika belum tersedia, buat:

```text
username: admin
password: admin123
```

Password tetap harus dimasukkan ke database dalam bentuk hash.

Contoh:

```php
$passwordHash = password_hash('admin123', PASSWORD_DEFAULT);
```

Jangan menyimpan:

```text
admin123
```

langsung di database.

---

# SQLite Connection

Gunakan PDO.

Contoh:

```php
$pdo = new PDO('sqlite:' . $databasePath);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
```

Gunakan prepared statement untuk query yang menerima input user.

Contoh:

```php
$stmt = $pdo->prepare(
    'SELECT * FROM admins WHERE username = :username LIMIT 1'
);

$stmt->execute([
    ':username' => $username
]);
```

---

# Security Requirements

Walaupun aplikasi sederhana, keamanan dasar tetap wajib.

Implementasikan:

- password hashing
- prepared statements
- session authentication
- output escaping
- input validation
- CSRF protection untuk operasi admin
- session regeneration setelah login

Gunakan:

```php
htmlspecialchars()
```

saat menampilkan data dari database.

Contoh:

```php
<?= htmlspecialchars($account['bank_name']) ?>
```

---

# Session Security

Setelah login:

```php
session_regenerate_id(true);
```

Logout harus:

```php
session_unset();
session_destroy();
```

kemudian redirect ke:

```text
/admin
```

---

# CSRF Protection

Form berikut harus mempunyai CSRF token:

- tambah rekening
- edit rekening
- hapus rekening
- aktif/nonaktif rekening

Generate token menggunakan:

```php
bin2hex(random_bytes(32))
```

Simpan token pada session.

---

# Input Validation

Validasi input pada server.

Minimal:

```text
Nama bank:
tidak boleh kosong

Nomor rekening:
tidak boleh kosong

Nama pemilik:
tidak boleh kosong

Urutan:
harus integer

Status:
hanya 0 atau 1
```

Jangan hanya mengandalkan validasi JavaScript.

---

# UI Design

Gunakan desain dashboard yang modern.

Arah desain:

```text
Minimal
Clean
Professional
Modern SaaS Dashboard
```

Gunakan:

- rounded card
- subtle shadow
- border ringan
- spacing yang lega
- typography yang jelas

Jangan terlalu banyak warna.

---

# Typography

Gunakan font modern.

Contoh:

```text
Inter
```

Jika mengambil dari Google Fonts diperbolehkan.

Fallback:

```css
font-family:
    Inter,
    -apple-system,
    BlinkMacSystemFont,
    "Segoe UI",
    sans-serif;
```

---

# Icons

Gunakan Font Awesome.

Contoh:

```html
<i class="fa-solid fa-copy"></i>
```

Tidak menggunakan emoji seperti:

```text
🏦
💰
🔐
⚙️
```

---

# Responsive Design

Aplikasi harus responsive terutama untuk smartphone.

Target:

```text
Mobile
Tablet
Desktop
```

Landing page kemungkinan besar dibuka melalui:

- WhatsApp
- Telegram
- Instagram
- browser mobile

Karena itu pengalaman mobile harus menjadi prioritas utama.

---

# Mobile Account Card

Pada mobile:

- card menggunakan hampir seluruh lebar layar
- nomor rekening cukup besar
- tombol copy mudah ditekan
- spacing tidak terlalu sempit
- nama bank mudah terlihat

---

# Copy Button Feedback

Setelah tombol copy ditekan, tampilkan toast.

Contoh:

```text
Nomor rekening berhasil disalin.
```

Toast dapat menghilang otomatis setelah sekitar 2–3 detik.

---

# Error Handling

Jangan menampilkan:

```text
Fatal error
Warning
Stack trace
Database path
SQL query
```

kepada user publik.

Gunakan pesan umum.

Contoh:

```text
Terjadi kesalahan. Silakan coba kembali.
```

---

# Database File Security

Jika memungkinkan, letakkan file SQLite di luar web root.

Contoh:

```text
/home/user/data/app.sqlite
```

bukan:

```text
/public_html/database/app.sqlite
```

Jika environment tidak memungkinkan, lindungi folder database melalui konfigurasi web server.

Database tidak boleh dapat didownload langsung melalui browser.

---

# Public URL Behaviour

Contoh domain:

```text
https://rekening.example.com
```

Maka:

```text
https://rekening.example.com/
```

langsung membuka daftar rekening.

Sedangkan:

```text
https://rekening.example.com/admin
```

membuka halaman login.

Tidak perlu URL seperti:

```text
/rekening
/home
/login
```

untuk halaman publik.

---

# Admin URL Behaviour

Flow:

```text
/admin
   ↓
Login
   ↓
/admin/dashboard.php
```

Jika session masih aktif dan admin membuka:

```text
/admin
```

redirect langsung ke dashboard.

---

# Public Navigation

Landing page tidak membutuhkan navigation kompleks.

Cukup:

```text
Logo / Nama Website
Daftar Rekening
Footer
```

Tidak perlu navbar jika tidak ada kebutuhan.

---

# Footer

Gunakan footer sederhana.

Contoh:

```text
© 2026 NanangMRK
```

atau data footer dibuat configurable kemudian jika diperlukan.

---

# Coding Style

Gunakan kode:

- sederhana
- mudah dibaca
- modular
- tidak over-engineering

Pisahkan:

```text
database connection
authentication
helper functions
layout
business logic
```

Hindari satu file PHP berisi seluruh aplikasi.

---

# PHP Requirements

Target minimal:

```text
PHP 8.1+
```

Pastikan extension berikut tersedia:

```text
pdo
pdo_sqlite
sqlite3
```

---

# No Framework Philosophy

Project ini sengaja menggunakan PHP Native.

Jangan mengubah project menjadi framework kecuali diminta secara eksplisit.

Tujuan utamanya:

```text
simple
fast
easy deployment
easy maintenance
small codebase
```

---

# Development Priority

Urutan implementasi:

## Phase 1

Bangun:

```text
SQLite connection
Database initialization
Admin default
Login admin
Session authentication
```

## Phase 2

Bangun CRUD rekening:

```text
List
Add
Edit
Delete
Active / Inactive
Sorting
```

## Phase 3

Bangun landing page publik:

```text
Account card
Copy button
Responsive layout
Toast notification
```

## Phase 4

Finishing:

```text
Security
Validation
Responsive testing
Error handling
UI polishing
```

---

# Definition of Done

Project dianggap selesai apabila:

1. `/` langsung menampilkan landing page rekening.

2. Tidak ada tombol atau link login pada landing page.

3. `/admin` menampilkan halaman login.

4. Login default tersedia:

```text
admin
admin123
```

5. Password tersimpan dalam bentuk hash.

6. Admin dapat login dan logout.

7. Admin dapat menambah rekening.

8. Admin dapat mengedit rekening.

9. Admin dapat menghapus rekening.

10. Admin dapat mengaktifkan atau menonaktifkan rekening.

11. Admin dapat menentukan urutan rekening.

12. Landing page hanya menampilkan rekening aktif.

13. Pengunjung dapat menyalin nomor rekening.

14. Terdapat feedback setelah nomor rekening berhasil disalin.

15. UI responsive di smartphone.

16. Tidak menggunakan emoji.

17. Menggunakan Font Awesome untuk icon.

18. Menggunakan PHP Native.

19. Menggunakan SQLite.

20. Semua query input menggunakan prepared statement.

21. Semua output dari database yang ditampilkan telah di-escape.

22. Halaman admin terlindungi session.

23. Operasi CRUD admin memiliki CSRF protection.

---

# Important Rules for AI Agent

Saat mengembangkan project ini:

- Jangan mengganti PHP Native dengan framework.
- Jangan mengganti SQLite tanpa instruksi.
- Jangan membuat sistem registrasi user.
- Jangan membuat login untuk pengunjung.
- Jangan menampilkan link admin pada landing page.
- Jangan menggunakan emoji pada UI.
- Jangan menambahkan fitur payment gateway.
- Jangan membuat fitur transaksi pembayaran.
- Jangan menambahkan fitur yang tidak diminta tanpa alasan kuat.
- Gunakan Font Awesome jika membutuhkan icon.
- Prioritaskan pengalaman pengguna mobile.
- Jaga source code tetap sederhana.
- Jangan menyimpan password plaintext.
- Jangan menampilkan informasi sensitif ketika terjadi error.

Jika harus memilih antara solusi kompleks dan sederhana, pilih solusi sederhana selama keamanan dan fungsi utama tetap terpenuhi.

---

# Project Goal

Hasil akhirnya harus terasa seperti sebuah halaman resmi yang dapat dibagikan melalui WhatsApp ketika seseorang bertanya:

```text
Transfer ke rekening mana?
```

Pemilik website cukup mengirim satu URL.

Pengguna membuka URL tersebut, memilih rekening yang sesuai, menekan tombol copy, lalu menggunakan nomor rekening tersebut untuk transfer.

Tidak ada langkah tambahan yang tidak diperlukan.