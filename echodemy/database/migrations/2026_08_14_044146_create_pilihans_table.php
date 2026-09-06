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
        Schema::create('pilihans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_abjad', 45)->nullable();
            $table->string('pilihan_jawaban', 500);
            $table->boolean('is_correct');
            $table->foreignId('detail_latihan_id')->constrained('detail_latihans')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pilihans');
    }
};
