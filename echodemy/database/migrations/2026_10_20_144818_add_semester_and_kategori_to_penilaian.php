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
         Schema::table('bahan_ajars', function (Blueprint $table) {
            $table->unsignedTinyInteger('semester')->default(1)->after('judul');
        });

        Schema::table('latihans', function (Blueprint $table) {
            $table->enum('kategori', ['tengah_semester', 'akhir_semester'])->nullable()->after('judul');
            $table->enum('penilaian_percobaan', ['tertinggi', 'terakhir'])->default('tertinggi')->after('maksimal_percobaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bahan_ajars', fn (Blueprint $t) => $t->dropColumn('semester'));
        Schema::table('latihans', fn (Blueprint $t) => $t->dropColumn(['kategori', 'penilaian_percobaan']));
    }
};
