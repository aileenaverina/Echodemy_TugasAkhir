<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pilihan extends Model
{
    protected $fillable = [
        'nomor_abjad', 'pilihan_jawaban', 'is_correct', 'detail_latihan_id',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function detailLatihan()
    {
        return $this->belongsTo(DetailLatihan::class, 'detail_latihan_id');
    }
    
}