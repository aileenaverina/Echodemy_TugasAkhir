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
        Schema::create('detail_latihans', function (Blueprint $table) {
            $table->id();
            $table->integer('nomor')->nullable();
            $table->longText('pertanyaan');
            $table->enum('tipe_soal', ['pilihan_ganda', 'isian', 'esai']);
            $table->text('jawaban')->nullable();
            $table->boolean('is_random')->nullable();
            $table->double('nilai')->comment('nilai full utk 1 soal`');
            $table->foreignId('latihan_id')->constrained('latihans')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_latihans');
    }
};
