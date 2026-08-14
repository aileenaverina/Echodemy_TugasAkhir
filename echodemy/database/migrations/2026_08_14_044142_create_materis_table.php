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
        Schema::create('materis', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('tipe', ['teks', 'video', 'url', 'file']);
            $table->longText('konten')->nullable();
            $table->string('file')->nullable();
            $table->boolean('is_lock')->default(false);
            $table->foreignId('bahan_ajar_id')->constrained('bahan_ajars');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materis');
    }
};
