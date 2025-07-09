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
    Schema::create('dues_transactions', function (Blueprint $table) {
        $table->id();
        $table->date('date');
        $table->enum('type', ['masuk', 'keluar']);
        $table->text('description')->nullable(); // Boleh kosong
        $table->unsignedInteger('amount');
        $table->foreignId('member_id')->nullable()->constrained()->onDelete('set null');
        $table->unsignedSmallInteger('period_year')->nullable();
        $table->unsignedTinyInteger('period_month')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dues_transactions');
    }
};
