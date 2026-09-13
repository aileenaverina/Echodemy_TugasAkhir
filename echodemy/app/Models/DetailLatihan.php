<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailLatihan extends Model
{
    use SoftDeletes;

    protected $table = 'detail_latihans';

    protected $fillable = [
        'nomor', 'pertanyaan', 'tipe_soal', 'jawaban', 'is_random', 'nilai', 'latihan_id',
    ];

    protected $casts = [
        'is_random' => 'boolean',
        'jawaban' => 'array',
    ];

    public function latihan()
    {
        return $this->belongsTo(Latihan::class, 'latihan_id');
    }

    public function pilihans()
    {
        return $this->hasMany(Pilihan::class, 'detail_latihan_id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'detail_latihan_id');
    }
}