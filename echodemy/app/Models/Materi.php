<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Materi extends Model
{
    use SoftDeletes;

    protected $table = 'materis';

    protected $fillable = [
        'nama', 'tipe', 'konten', 'file', 'is_lock', 'bahan_ajar_id',
    ];

    protected $casts = [
        'is_lock' => 'boolean',
    ];

    public function bahanAjar()
    {
        return $this->belongsTo(BahanAjar::class);
    }

    public function siswaProgres()
    {
        return $this->belongsToMany(User::class, 'progres_materis')
            ->withPivot('is_done');
    }
}