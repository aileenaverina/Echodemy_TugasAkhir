<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // harian jadi nilai eksplisit di latihans
        DB::statement("ALTER TABLE latihans MODIFY kategori ENUM('harian','tengah_semester','akhir_semester') NULL");
        DB::table('latihans')->whereNull('kategori')->update(['kategori' => 'harian']);
        DB::statement("ALTER TABLE latihans MODIFY kategori ENUM('harian','tengah_semester','akhir_semester') NOT NULL DEFAULT 'harian'");

        Schema::table('assignments', function (Blueprint $table) {
            $table->enum('kategori', ['harian', 'tengah_semester', 'akhir_semester'])->default('harian')->after('judul');
        });

        Schema::table('submissions', function (Blueprint $table) {
            $table->double('nilai_akhir')->nullable();
        });

        Schema::table('pilihans', function (Blueprint $table) {
            $table->TinyInteger('persentase_correct')->default(0)->after('pilihan_jawaban');
        });
        DB::table('pilihans')->where('is_correct', true)->update(['persentase_correct' => 100]);
        Schema::table('pilihans', fn (Blueprint $t) => $t->dropColumn('is_correct'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pilihans', function (Blueprint $table) {
            $table->boolean('is_correct')->nullable()->after('pilihan_jawaban');
        });
        DB::table('pilihans')->where('persentase_correct', '>', 0)->update(['is_correct' => true]);
        Schema::table('pilihans', fn (Blueprint $t) => $t->dropColumn('persentase_correct'));

        Schema::table('submissions', fn (Blueprint $t) => $t->dropColumn('nilai_akhir'));
        Schema::table('assignments', fn (Blueprint $t) => $t->dropColumn('kategori'));

        DB::statement("ALTER TABLE latihans MODIFY kategori ENUM('harian','tengah_semester','akhir_semester') NULL");
        DB::table('latihans')->where('kategori', 'harian')->update(['kategori' => null]);
        DB::statement("ALTER TABLE latihans MODIFY kategori ENUM('tengah_semester','akhir_semester') NULL");
    }
};
