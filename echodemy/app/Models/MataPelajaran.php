<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MataPelajaran extends Model
{
    use SoftDeletes;

    protected $fillable = ['nama', 'file', 'warna'];

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'mata_pelajaran_kelas');
    }

    public function bahanAjars()
    {
        return $this->hasMany(BahanAjar::class);
    }
}