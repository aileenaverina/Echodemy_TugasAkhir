<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    protected $primaryKey = 'kode';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['kode', 'nama'];

    public function sekolahs()
    {
        return $this->hasMany(Sekolah::class, 'wilayah_kode', 'kode');
    }
}
