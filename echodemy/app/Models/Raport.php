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

    public static function terbaruUntuk(int $userId)
    {
        return static::with('kelas')
            ->where('user_id', $userId)
            ->orderByDesc('versi')
            ->get()
            ->unique(fn($r) => $r->kelas_id . '-' . $r->semester)
            ->sortByDesc('created_at')
            ->values();
    }
}
