<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HasilAssignment extends Model
{
    use SoftDeletes;

    protected $fillable = ['file', 'text', 'assignment_id', 'user_id', 'status'];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function siswa()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}