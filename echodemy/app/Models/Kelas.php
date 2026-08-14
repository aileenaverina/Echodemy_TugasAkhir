<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kelas extends Model
{
    use SoftDeletes;

    protected $table = 'kelass';

    protected $fillable = [
        'nama', 'sekolah_id', 'tahun_ajaran', 'status',
    ];

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function siswa()
    {
        return $this->belongsToMany(User::class, 'anggota_kelas');
    }

    public function mataPelajarans()
    {
        return $this->belongsToMany(MataPelajaran::class, 'mata_pelajaran_kelas');
    }

    public function guruMataPelajarans()
    {
        return $this->belongsToMany(User::class, 'guru_mata_pelajaran')
            ->withPivot('mata_pelajaran_id');
    }

    public function bahanAjars()
    {
        return $this->hasMany(BahanAjar::class);
    }
}