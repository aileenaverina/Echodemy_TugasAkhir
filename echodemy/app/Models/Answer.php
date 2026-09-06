<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Answer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'submission_id', 'detail_latihan_id', 'jawaban', 'nilai',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function detailLatihan()
    {
        return $this->belongsTo(DetailLatihan::class, 'detail_latihan_id');
    }
}