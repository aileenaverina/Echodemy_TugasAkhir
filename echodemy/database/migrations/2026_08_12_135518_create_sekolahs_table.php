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
       Schema::create('sekolahs', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 500);
            $table->string('npsn', 8)->unique();
            $table->string('detail_alamat', 500);
            $table->string('nomor_telepon', 45);
            $table->string('email')->unique();
            $table->string('logo');
            $table->string('singkatan', 5);
            $table->enum('jenjang', ['SD', 'SMP', 'SMA', 'MA', 'MK', 'SMK']);
            $table->string('wilayah_kode')->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('wilayah_kode')->references('kode')->on('wilayahs')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sekolahs');
    }
};
