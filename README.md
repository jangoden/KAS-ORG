Sistem Kas Organisasi (KAS-ORG)
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

KAS-ORG adalah aplikasi berbasis web modern yang dirancang untuk mempermudah pengelolaan keuangan dan pencatatan kas dalam sebuah organisasi. Dibangun di atas framework Laravel, aplikasi ini menawarkan solusi yang kuat, aman, dan efisien untuk menggantikan pencatatan manual.

Dilengkapi dengan Admin Panel canggih yang dibuat menggunakan Filament, sistem ini memungkinkan administrator untuk mengelola seluruh aspek data dengan antarmuka yang intuitif. Keamanan dan manajemen hak akses pengguna ditangani secara profesional oleh Spatie Laravel-Permission (Shield).

📋 Fitur Utama
Dashboard Interaktif: Menampilkan ringkasan kondisi kas terkini, termasuk total pemasukan, total pengeluaran, dan saldo akhir dalam bentuk visual yang mudah dipahami.

Manajemen Transaksi:

Pemasukan: Pencatatan semua dana yang masuk ke kas organisasi.

Pengeluaran: Pencatatan semua dana yang keluar dari kas.

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

Framework: Laravel 11

Admin Panel: Filament 3

Manajemen Hak Akses: Spatie Laravel-Permission (Filament Shield)

Frontend: Blade Engine, Tailwind CSS

Database: MySQL / MariaDB

Server: Apache / Nginx

🚀 Panduan Instalasi Lengkap
Ikuti langkah-langkah di bawah ini untuk menginstal dan menjalankan proyek ini di lingkungan lokal Anda.

1. Prasyarat
Pastikan Anda sudah menginstal perangkat lunak berikut:

PHP 8.2 atau lebih baru

Composer

Node.js & NPM

Web Server (XAMPP, Laragon, atau sejenisnya)

Git

2. Kloning Repositori
Buka terminal, masuk ke direktori htdocs (atau direktori web server Anda), lalu kloning repositori ini.

git clone https://github.com/jangoden/KAS-ORG.git
cd KAS-ORG

3. Konfigurasi Lingkungan (.env)
Salin file .env.example menjadi .env. File ini berisi semua konfigurasi untuk aplikasi Anda.

cp .env.example .env

Buka file .env dan sesuaikan konfigurasi database:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_kas_organisasi
DB_USERNAME=root
DB_PASSWORD=

Penting: Jangan lupa membuat database db_kas_organisasi di phpMyAdmin terlebih dahulu.

4. Instalasi Dependensi
Instal semua dependensi PHP (termasuk Laravel & Filament) dan dependensi JavaScript.

# Instal dependensi PHP
composer install

# Instal dependensi Node.js
npm install

# Generate application key
php artisan key:generate

5. Migrasi dan Seeding Database
Jalankan migrasi untuk membuat semua tabel di database, lalu jalankan seeder untuk mengisi data awal (termasuk user admin dan roles/permissions).

# Menjalankan migrasi untuk membuat struktur tabel
php artisan migrate

# (Opsional) Menjalankan seeder untuk data awal
php artisan db:seed

6. Buat User Admin (Jika Seeder Tidak Tersedia)
Jika db:seed tidak membuat user admin, Anda bisa membuatnya secara manual dengan perintah Filament berikut. Ikuti instruksi yang muncul di terminal.

php artisan make:filament-user

▶️ Cara Menjalankan Aplikasi
Compile Aset Frontend: Jalankan perintah ini untuk meng-compile file CSS dan JS.

npm run dev

(Biarkan terminal ini tetap berjalan selama pengembangan)

Jalankan Server Lokal: Buka terminal baru di direktori proyek dan jalankan server pengembangan Laravel.

php artisan serve

Akses Aplikasi:

Halaman Utama: Buka browser dan akses http://127.0.0.1:8000

Halaman Admin: Untuk masuk ke panel admin, akses http://127.0.0.1:8000/admin

Akun Default Admin:

Email: admin@example.com

Password: password
(Sesuaikan jika Anda mengubahnya di seeder atau saat membuat user manual)

🤝 Kontribusi Proyek
Kami sangat terbuka untuk kontribusi dari komunitas untuk pengembangan KAS-ORG. Jika Anda ingin berkontribusi, silakan ikuti langkah-langkah berikut:

Fork repositori ini.

Buat branch baru untuk fitur Anda (git checkout -b fitur/NamaFiturBaru).

Commit perubahan Anda (git commit -m 'Menambahkan fitur A').

Push ke branch Anda (git push origin fitur/NamaFiturBaru).

Buat Pull Request baru.

Tentang Laravel
Laravel adalah kerangka kerja aplikasi web dengan sintaks yang ekspresif dan elegan. Kami percaya pengembangan harus menjadi pengalaman yang menyenangkan dan kreatif agar benar-benar memuaskan. Laravel menghilangkan kesulitan dalam pengembangan dengan mempermudah tugas-tugas umum yang digunakan di banyak proyek web, seperti:

Mesin routing yang sederhana dan cepat.

Container injeksi dependensi yang kuat.

Berbagai back-end untuk penyimpanan sesi dan cache.

ORM database yang ekspresif dan intuitif.

Migrasi skema yang tidak bergantung pada database.

Pemrosesan pekerjaan latar belakang yang tangguh.

Penyiaran acara secara real-time.

Laravel mudah diakses, kuat, dan menyediakan alat yang diperlukan untuk aplikasi besar dan tangguh.

Belajar Laravel
Laravel memiliki dokumentasi dan perpustakaan tutorial video yang paling luas dan menyeluruh dari semua kerangka kerja aplikasi web modern, membuatnya mudah untuk memulai.

Anda juga dapat mencoba Laravel Bootcamp, di mana Anda akan dipandu membangun aplikasi Laravel modern dari awal.

Jika Anda tidak suka membaca, Laracasts dapat membantu. Laracasts berisi ribuan tutorial video tentang berbagai topik termasuk Laravel, PHP modern, pengujian unit, dan JavaScript.

Sponsor Laravel
Kami ingin mengucapkan terima kasih kepada sponsor berikut yang mendanai pengembangan Laravel. Jika Anda tertarik untuk menjadi sponsor, silakan kunjungi program Mitra Laravel.

Mitra Premium
Vehikl

Tighten Co.

Kirschbaum Development Group

64 Robots

Curotec

DevSquad

Redberry

Active Logic

Kontribusi untuk Laravel
Terima kasih telah mempertimbangkan untuk berkontribusi pada kerangka kerja Laravel! Panduan kontribusi dapat ditemukan di dokumentasi Laravel.

Kode Etik
Untuk memastikan bahwa komunitas Laravel ramah untuk semua, harap tinjau dan patuhi Kode Etik.

Kerentanan Keamanan
Jika Anda menemukan kerentanan keamanan dalam Laravel, silakan kirim e-mail ke Taylor Otwell melalui taylor@laravel.com. Semua kerentanan keamanan akan segera ditangani.

Lisensi
Kerangka kerja Laravel adalah perangkat lunak sumber terbuka yang dilisensikan di bawah lisensi MIT.
