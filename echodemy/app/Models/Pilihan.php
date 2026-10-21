<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pilihan extends Model
{
    protected $fillable = [
        'nomor_abjad', 'pilihan_jawaban', 'persentase_correct', 'detail_latihan_id',
    ];

    public function detailLatihan()
    {
        return $this->belongsTo(DetailLatihan::class, 'detail_latihan_id');
    }
    
}