<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Latihan extends Model
{
    use SoftDeletes;

    protected $table = 'latihans';

    protected $fillable = [
        'judul', 'is_random', 'bahan_ajar_id',
        'batas_waktu', 'maksimal_percobaan', 'is_lock', 'persen_nilai_akhir', 'waktu_kerja',
    ];

    protected $casts = [
        'is_random' => 'boolean',
        'is_lock' => 'boolean',
        'batas_waktu' => 'datetime',
    ];

    public function bahanAjar()
    {
        return $this->belongsTo(BahanAjar::class);
    }

    public function detailLatihans()
    {
        return $this->hasMany(DetailLatihan::class, 'latihan_id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'latihan_id');
    }
}