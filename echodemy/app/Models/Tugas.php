<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tugas extends Model
{
    use SoftDeletes;

    protected $table = 'tugass';

    protected $fillable = [
        'judul', 'file', 'is_random', 'nilai', 'bahan_ajar_id',
        'batas_waktu', 'maksimal_percobaan', 'is_lock', 'persen_nilai_akhir', 'waktu_kerja',
    ];

    protected $casts = [
        'is_random' => 'boolean',
        'is_lock' => 'boolean',
        'batas_waktu' => 'datetime',
    ];

    public function bahanAjar()
    {
        return $this->belongsTo(BahanAjar::class);
    }

    public function detailTugass()
    {
        return $this->hasMany(DetailTugas::class, 'tugas_id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'tugas_id');
    }
}