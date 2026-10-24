<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Raport extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'kelas_id', 'semester', 'versi', 'file', 'snapshot', 'diterbitkan_oleh'];

    protected $casts = ['snapshot' => 'array'];

    public function siswa()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class)->withTrashed();
    }

    public function penerbit()
    {
        return $this->belongsTo(User::class, 'diterbitkan_oleh');
    }
}