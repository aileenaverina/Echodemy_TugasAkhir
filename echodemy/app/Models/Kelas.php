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

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'anggota_kelas',
            'kelas_id',
            'user_id'
        );
    }

    public function mataPelajaranKelass()
    {
        return $this->hasMany(MataPelajaranKelas::class, 'kelas_id');
    }
}
