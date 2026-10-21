<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Answer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'submission_id',
        'detail_latihan_id',
        'jawaban',
        'nilai',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function detailLatihan()
    {
        return $this->belongsTo(DetailLatihan::class, 'detail_latihan_id');
    }

    public function teksJawaban(): string
    {
        $detail = $this->detailLatihan;

        if ($detail?->tipe_soal === 'pilihan_ganda') {
            $ids = json_decode($this->jawaban, true) ?: [];

            return $detail->pilihans->whereIn('id', $ids)->pluck('pilihan_jawaban')->join(', ');
        }

        return (string) $this->jawaban;
    }
}
