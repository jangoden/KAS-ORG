<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Menambahkan kolom tanggal_aktif setelah kolom 'nia'
            // Nilainya bisa null untuk data lama sebelum diisi
            $table->date('tanggal_aktif')->after('nia')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('tanggal_aktif');
        });
    }
};