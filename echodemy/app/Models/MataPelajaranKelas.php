<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class MataPelajaranKelas extends Model
{
    protected $table = 'mata_pelajaran_kelas';

    protected $fillable = ['kelas_id', 'mata_pelajaran_id'];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }
    public function bahanAjars()
    {
        return $this->hasMany(BahanAjar::class);
    }

    public function gurus()
    {
        return $this->belongsToMany(
            User::class,
            'guru_mata_pelajaran',
            'mata_pelajaran_kelas_id',
            'user_id'
        );
    }
}
