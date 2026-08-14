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
         Schema::create('tugass', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('file')->nullable();
            $table->boolean('is_random')->nullable();
            $table->double('nilai')->nullable();
            $table->foreignId('bahan_ajar_id')->constrained('bahan_ajars');
            $table->timestamp('batas_waktu');
            $table->integer('maksimal_percobaan')->nullable();
            $table->boolean('is_lock')->default(false);
            $table->integer('persen_nilai_akhir');
            $table->integer('waktu_kerja')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tugass');
    }
};
