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
        Schema::create('kelass', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('status', ['aktif', 'selesai'])->default('aktif');
            $table->string('tahun_ajaran', 9);
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['sekolah_id', 'nama', 'tahun_ajaran']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelass');
    }
};
