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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('file')->nullable();
            $table->double('nilai');
            $table->foreignId('bahan_ajar_id')->constrained('bahan_ajars')->cascadeOnDelete();
            $table->timestamp('batas_waktu');
            $table->boolean('is_lock');
            $table->integer('persen_nilai_akhir');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
