# KosManager - Inti Arsitektur MVC, Routing, Layout & Dashboard

Repositori ini berfokus pada fondasi utama sistem **KosManager**, mencakup penyiapan struktur direktori, arsitektur inti PHP 8 MVC (*Model-View-Controller*), sistem routing dinamis, koneksi database PDO yang aman, layout antarmuka pengelola (sidebar & header), serta implementasi halaman Dashboard statistik.

---

## 🎯 Cakupan Pengerjaan (Jobdesk)

1. **Repositori & Struktur Proyek:**
   - Inisialisasi struktur direktori bersih (*Clean MVC*).
   - Konfigurasi rewrite URL Apache (`.htaccess`) dan root redirect (`index.php`).
   - Konfigurasi skema database awal (`database/schema.sql` dan `database/seeder.sql`).

2. **Inti Aplikasi MVC & Routing:**
   - **Autoloader PSR-4:** `public/index.php` untuk memuat kelas secara otomatis dari namespace `KosManager\`.
   - **Router Engine (`src/Core/Router.php`):** Menangani *routing* berbasis HTTP Method (GET, POST) dengan *dispatching callback*.
   - **Helper Functions (`src/Core/helpers.php`):** Menyediakan fungsi utilitas global seperti `base_url()`, proteksi `csrf_token()`, `verify_csrf()`, serta sistem notifikasi `set_flash()` dan `get_flash()`.

3. **Koneksi Database:**
   - **PDO Connection (`src/config/koneksi.php`):** Menggunakan pola *Singleton* dengan penanganan *native prepared statements* (`ATTR_EMULATE_PREPARES => false`), error handling exception, dan mekanisme *fallback* otomatis antara host `127.0.0.1` dan `localhost`.

4. **Layout Antarmuka Pengelola:**
   - **Sidebar Navigasi (`views/admin/layout/sidebar.php`):** Navigasi responsif dengan deteksi halaman aktif, profil singkat pengelola, dan logo vektor lokal.
   - **Admin Header (`views/admin/layout/admin-header.php`):** Aksi cepat penambahan data dan tombol logout.
   - **Aset & Styling (`public/assets/`):** Konfigurasi CSS Tailwind, JS helper, dan ikon logo SVG (`logo.svg`).

5. **Halaman Dashboard & Autentikasi:**
   - **Dashboard Controller & View (`src/Controllers/DashboardController.php`, `views/admin/dashboard/index.php`):** Menghitung dan menyajikan ringkasan statistik kamar (Total Kos, Total Kamar, Kamar Kosong, Kamar Terisi) serta daftar penghuni terbaru.
   - **Session & Auth Guard (`src/Controllers/AuthController.php`, `views/admin/login.php`):** Penguncian sesi pengguna agar halaman pengelola tidak dapat diakses tanpa login.

---

## 📁 Struktur Direktori Repositori

```text
Web-UTS/
├── database/
│   ├── schema.sql
│   ├── seeder.sql
│   └── seeders.sql
├── public/
│   ├── assets/
│   │   ├── css/ (admin.css, style.css)
│   │   ├── js/ (admin.js, admin-theme.js)
│   │   └── images/ (logo.svg)
│   └── index.php (Front Controller & Routing)
├── src/
│   ├── config/
│   │   └── koneksi.php
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   └── DashboardController.php
│   ├── Core/
│   │   ├── helpers.php
│   │   └── Router.php
│   ├── Models/
│   │   ├── Kamar.php
│   │   └── Penghuni.php
│   └── Repositories/
│       ├── KamarRepository.php
│       └── PenghuniRepository.php
├── views/
│   └── admin/
│       ├── dashboard/
│       │   └── index.php
│       ├── layout/
│       │   ├── admin-header.php
│       │   └── sidebar.php
│       └── login.php
├── .gitignore
├── .htaccess
├── index.php
└── README.md
```

---

## 🚀 Panduan Menjalankan

1. **Persiapan Database:**
   - Buka phpMyAdmin (`http://localhost/phpmyadmin/`).
   - Buat database dengan nama: `kostmanager`.
   - Impor file `database/schema.sql` dan `database/seeder.sql`.

2. **Menjalankan Web Server:**
   - Aktifkan **Apache** dan **MySQL** di XAMPP Control Panel.
   - Buka browser dan akses alamat:
     ```text
     http://localhost/Web-UTS/
     ```
     *(atau via PHP Built-in server: `C:\xampp\php\php.exe -S localhost:8000 -t public`)*

3. **Akun Login Demo:**
   - **Username:** `admin`
   - **Password:** `admin123`
