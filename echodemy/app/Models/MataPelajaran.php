<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MataPelajaran extends Model
{
    use SoftDeletes;

    protected $fillable = ['nama', 'file', 'warna'];


    public function penugasanKelas()
    {
        return $this->hasMany(MataPelajaranKelas::class, 'mata_pelajaran_id');
    }

}
