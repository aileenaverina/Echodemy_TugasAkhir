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
        Schema::create('bahan_ajars', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('mata_pelajaran_kelas_id')->constrained('mata_pelajaran_kelas')->cascadeOnDelete();
            $table->longText('notes')->nullable();
            $table->boolean('is_lock')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahan_ajars');
    }
};
