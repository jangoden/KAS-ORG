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
        Schema::table('dues_transactions', function (Blueprint $table) {
            // Baris ini menambahkan kolom 'recipient_name' tipe string (teks),
            // bisa kosong (nullable), dan diletakkan setelah kolom 'description'.
            $table->string('recipient_name')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dues_transactions', function (Blueprint $table) {
            // Baris ini akan dijalankan jika kita perlu membatalkan migrasi.
            $table->dropColumn('recipient_name');
        });
    }
};