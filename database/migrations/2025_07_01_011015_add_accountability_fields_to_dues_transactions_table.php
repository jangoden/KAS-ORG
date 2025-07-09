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
            // Menambahkan kolom untuk Penanggung Jawab (User yang login)
            // Ditempatkan setelah kolom 'member_id' agar rapi
            $table->foreignId('user_id')
                  ->nullable()
                  ->after('member_id')
                  ->constrained('users')
                  ->onDelete('set null');

            // Menambahkan kolom untuk Penerima Dana (diisi manual untuk kas keluar)
            // Ditempatkan setelah kolom 'user_id'
            $table->string('penerima')->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dues_transactions', function (Blueprint $table) {
            // Hapus foreign key constraint sebelum drop kolom
            $table->dropForeign(['user_id']);
            // Hapus kolom jika migrasi di-rollback
            $table->dropColumn(['user_id', 'penerima']);
        });
    }
};