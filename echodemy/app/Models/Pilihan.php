<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pilihan extends Model
{
    protected $fillable = [
        'nomor_abjad', 'pilihan_jawaban', 'is_correct', 'detail_tugas_id',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function detailTugas()
    {
        return $this->belongsTo(DetailTugas::class, 'detail_tugas_id');
    }
}