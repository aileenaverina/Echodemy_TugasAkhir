<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kelas extends Model
{
    use SoftDeletes;

    protected $table = 'kelass';

    protected $fillable = [
        'nama',
        'sekolah_id',
        'tahun_ajaran',
        'status',
    ];

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function siswa()
    {
        return $this->belongsToMany(User::class, 'anggota_kelas', 'kelas_id', 'user_id')
            ->where('role', 'siswa');
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'anggota_kelas',
            'kelas_id',
            'user_id'
        );
    }

    public function waliKelas()
    {
        return $this->belongsToMany(User::class, 'anggota_kelas', 'kelas_id', 'user_id')
            ->where('role', 'guru');
    }

    public function penugasanMataPelajaran()
    {
        return $this->hasMany(MataPelajaranKelas::class, 'kelas_id');
    }

    public function bahanAjars()
    {
        return BahanAjar::whereHas('mataPelajaranKelas', function ($q) {
            $q->where('kelas_id', $this->id);
        });
    }
}
