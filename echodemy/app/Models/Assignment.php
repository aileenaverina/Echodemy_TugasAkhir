<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assignment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'judul', 'file', 'nilai', 'bahan_ajar_id', 'batas_waktu', 'is_lock', 'persen_nilai_akhir',
    ];

    protected $casts = [
        'is_lock' => 'boolean',
        'batas_waktu' => 'datetime',
    ];

    public function bahanAjar()
    {
        return $this->belongsTo(BahanAjar::class);
    }

    public function hasilAssignments()
    {
        return $this->hasMany(HasilAssignment::class);
    }
}