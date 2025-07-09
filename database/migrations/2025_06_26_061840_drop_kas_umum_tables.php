<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hapus tabel transactions terlebih dahulu jika ada foreign key ke categories
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('categories');
        // Ganti nama 'categories' jika nama tabel Anda berbeda (misal: 'kategori')
    }

    /**
     * Reverse the migrations.
     * (Untuk jaga-jaga jika ingin mengembalikan)
     */
    public function down(): void
    {
        // Anda bisa meng-copy isi dari method up() dari file migrasi
        // pembuatan tabel categories dan transactions ke sini agar
        // proses penghapusan ini bisa dibatalkan (reversible).
        // Contoh:
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            // ... kolom-kolom lainnya
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            // ... kolom-kolom lainnya
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }
};