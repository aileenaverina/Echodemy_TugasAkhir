<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BahanAjar extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'judul',
        'user_id',
        'kelas_id',
        'mata_pelajaran_id',
        'notes',
        'is_lock',
    ];

    protected $casts = [
        'is_lock' => 'boolean',
    ];

    public function guru()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mataPelajaranKelas()
    {
        return $this->belongsTo(MataPelajaranKelas::class);
    }

    public function materis()
    {
        return $this->hasMany(Materi::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function latihans()
    {
        return $this->hasMany(Latihan::class);
    }
}
