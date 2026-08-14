<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailTugas extends Model
{
    use SoftDeletes;

    protected $table = 'detail_tugass';

    protected $fillable = [
        'nomor', 'pertanyaan', 'tipe_soal', 'jawaban', 'is_random', 'nilai', 'tugas_id',
    ];

    protected $casts = [
        'is_random' => 'boolean',
    ];

    public function tugas()
    {
        return $this->belongsTo(Tugas::class, 'tugas_id');
    }

    public function pilihans()
    {
        return $this->hasMany(Pilihan::class, 'detail_tugas_id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'detail_tugas_id');
    }
}