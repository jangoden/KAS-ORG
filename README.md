<div align="center">
<a href="https://laravel.com" target="_blank">
<img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</a>
<h1>Sistem Kas Organisasi (KAS-ORG)</h1>
<p>
<strong>Solusi modern untuk manajemen keuangan organisasi yang transparan dan efisien.</strong>
</p>
<p>
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>
</div>

KAS-ORG adalah aplikasi berbasis web yang dirancang untuk mempermudah pengelolaan keuangan dan pencatatan kas dalam sebuah organisasi. Dibangun di atas framework Laravel, aplikasi ini menawarkan solusi yang kuat, aman, dan efisien untuk menggantikan pencatatan manual.

Dilengkapi dengan Admin Panel canggih yang dibuat menggunakan Filament, sistem ini memungkinkan administrator untuk mengelola seluruh aspek data dengan antarmuka yang intuitif. Keamanan dan manajemen hak akses pengguna ditangani secara profesional oleh Spatie Laravel-Permission (Shield).

📖 Daftar Isi
Fitur Utama

Teknologi yang Digunakan

Panduan Instalasi

Cara Menjalankan Aplikasi

Struktur Proyek

Kontribusi

Lisensi

Tentang Laravel

📋 Fitur Utama
Fitur Aplikasi
Dashboard Interaktif: Menampilkan ringkasan kondisi kas terkini, termasuk total pemasukan, total pengeluaran, dan saldo akhir dalam bentuk visual yang mudah dipahami.

Manajemen Transaksi: Pencatatan dana masuk (pemasukan) dan dana keluar (pengeluaran) dengan detail lengkap.

Manajemen Kategori: Pengelompokan transaksi berdasarkan kategori (misal: Iuran Anggota, Donasi, Biaya Operasional) untuk analisis yang lebih detail.

Rekapitulasi & Laporan: Menghasilkan laporan keuangan periodik (bulanan, tahunan) yang dapat dicetak atau diunduh dalam format PDF/Excel.

✨ Fitur Admin Panel (Filament)
Manajemen Pengguna (Users): Menambah, mengubah, dan menghapus data pengguna yang dapat mengakses sistem.

Manajemen Hak Akses (Roles & Permissions):

Roles: Membuat peran/jabatan (misal: Administrator, Bendahara, Anggota).

Permissions: Menentukan izin spesifik untuk setiap tindakan (misal: create-pemasukan, edit-laporan, delete-user).

Menetapkan peran dan izin kepada setiap pengguna dengan mudah melalui antarmuka visual.

CRUD Universal: Antarmuka yang seragam dan cepat untuk semua data master (Kategori, Pengguna, dll.).

Notifikasi Real-time: Pemberitahuan instan untuk setiap aksi penting yang terjadi di sistem.

🛠️ Teknologi yang Digunakan
Proyek ini dibangun menggunakan tumpukan teknologi modern dan terpercaya:

Kategori

Teknologi

Framework

Laravel 11

Admin Panel

Filament 3

Manajemen Akses

Spatie Laravel-Permission (Filament Shield)

Frontend

Blade Engine, Tailwind CSS

Database

MySQL / MariaDB

Server

Apache / Nginx

🚀 Panduan Instalasi Lengkap
Ikuti langkah-langkah di bawah ini untuk menginstal dan menjalankan proyek ini di lingkungan lokal Anda.

1. Prasyarat
Pastikan perangkat lunak berikut sudah terinstal di sistem Anda:

PHP 8.2 atau lebih baru

Composer

Node.js & NPM

Web Server (XAMPP, Laragon, atau sejenisnya)

Git

2. Kloning Repositori
Buka terminal, masuk ke direktori web server Anda (htdocs, www, dll.), lalu kloning repositori ini.

git clone https://github.com/jangoden/KAS-ORG.git
cd KAS-ORG

3. Konfigurasi Lingkungan
Salin file .env.example menjadi .env untuk menyimpan konfigurasi lokal Anda.

cp .env.example .env

Selanjutnya, buka file .env dan sesuaikan konfigurasi database:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_kas_organisasi
DB_USERNAME=root
DB_PASSWORD=

Penting: Jangan lupa membuat database db_kas_organisasi di phpMyAdmin terlebih dahulu.

4. Instalasi Dependensi
Instal semua dependensi PHP dan JavaScript yang dibutuhkan oleh proyek.

# Instal dependensi PHP via Composer
composer install

# Instal dependensi Node.js via NPM
npm install

# Generate kunci aplikasi Laravel
php artisan key:generate

5. Migrasi dan Seeding Database
Jalankan migrasi untuk membuat struktur tabel, lalu jalankan seeder untuk mengisi data awal (termasuk user admin, roles, dan permissions).

# Membuat semua tabel di database
php artisan migrate

# Mengisi data awal (opsional)
php artisan db:seed

6. Membuat User Admin (Alternatif)
Jika seeder tidak tersedia atau Anda ingin membuat admin baru, gunakan perintah Filament berikut dan ikuti instruksinya.

php artisan make:filament-user

▶️ Cara Menjalankan Aplikasi
Compile Aset Frontend
Jalankan perintah ini untuk meng-compile file CSS dan JS. Biarkan terminal ini tetap berjalan selama pengembangan untuk hot-reloading.

npm run dev

Jalankan Server Lokal
Buka terminal baru di direktori proyek dan jalankan server pengembangan Laravel.

php artisan serve

Akses Aplikasi

Halaman Utama: Buka browser dan akses http://127.0.0.1:8000

Halaman Admin: Masuk ke panel admin melalui http://127.0.0.1:8000/admin

Akun Default Admin:

Email: admin@example.com

Password: password
(Sesuaikan jika Anda mengubahnya di seeder atau saat membuat user manual)

📁 Struktur Proyek
Berikut adalah gambaran singkat tentang direktori utama dalam proyek ini:

.
├── app/
│   ├── Http/Controllers/
│   ├── Models/
│   ├── Providers/
│   └── Filament/              # Semua resource Filament (Admin Panel)
├── config/                    # File konfigurasi aplikasi
├── database/
│   ├── migrations/            # Skema tabel database
│   └── seeders/               # Data awal untuk database
├── public/                    # Root dokumen publik
├── resources/
│   ├── css/
│   ├── js/
│   └── views/                 # File Blade template
├── routes/
│   └── web.php                # Definisi rute aplikasi
├── tests/                     # File pengujian (unit & feature)
└── .env                       # File konfigurasi lingkungan

🤝 Kontribusi Proyek
Kami sangat terbuka untuk kontribusi dari komunitas. Jika Anda ingin membantu mengembangkan KAS-ORG, silakan ikuti langkah-langkah berikut:

Fork repositori ini.

Buat branch baru untuk fitur Anda (git checkout -b fitur/NamaFiturBaru).

Commit perubahan Anda (git commit -m 'feat: Menambahkan fitur A').

Push ke branch Anda (git push origin fitur/NamaFiturBaru).

Buat Pull Request baru dan jelaskan perubahan yang Anda buat.

📜 Lisensi
Proyek ini dilisensikan di bawah Lisensi MIT.

Tentang Laravel
Laravel adalah kerangka kerja aplikasi web dengan sintaks yang ekspresif dan elegan. Kami percaya pengembangan harus menjadi pengalaman yang menyenangkan dan kreatif agar benar-benar memuaskan. Laravel menghilangkan kesulitan dalam pengembangan dengan mempermudah tugas-tugas umum yang digunakan di banyak proyek web.

Mesin routing yang sederhana dan cepat.

Container injeksi dependensi yang kuat.

Berbagai back-end untuk penyimpanan sesi dan cache.

ORM database yang ekspresif dan intuitif.

Migrasi skema yang tidak bergantung pada database.

Pemrosesan pekerjaan latar belakang yang tangguh.

Penyiaran acara secara real-time.

Untuk informasi lebih lanjut, kunjungi dokumentasi resmi Laravel.
