<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Submission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'latihan_id', 'user_id', 'file', 'nilai', 'status',
    ];

    public function latihan()
    {
        return $this->belongsTo(Latihan::class, 'latihan_id');
    }

    public function siswa()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}