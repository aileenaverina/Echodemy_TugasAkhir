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
       Schema::create('detail_users', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 45)->nullable();
            $table->string('nis', 45)->nullable();
            $table->enum('jenis_kelamin', ['l', 'p'])->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('detail_alamat', 500)->nullable();
            $table->string('nomor_telepon', 45)->nullable();
            $table->string('nama_ortu', 45)->nullable();
            $table->string('nomor_telepon_ortu_wali', 45)->nullable();
            $table->string('foto_profil', 100)->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_users');
    }
};
