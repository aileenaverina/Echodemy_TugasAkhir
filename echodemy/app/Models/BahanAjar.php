<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BahanAjar extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'judul', 'user_id', 'kelas_id', 'mata_pelajaran_id', 'notes', 'is_lock',
    ];

    protected $casts = [
        'is_lock' => 'boolean',
    ];

    public function guru()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function materis()
    {
        return $this->hasMany(Materi::class);
    }

    public function tugass()
    {
        return $this->hasMany(Tugas::class);
    }
}