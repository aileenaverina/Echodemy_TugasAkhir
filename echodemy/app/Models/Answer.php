<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Answer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'submission_id', 'detail_tugas_id', 'jawaban', 'nilai',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function detailTugas()
    {
        return $this->belongsTo(DetailTugas::class, 'detail_tugas_id');
    }
}