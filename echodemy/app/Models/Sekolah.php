<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    const STATUS_PENDING = 0;
    const STATUS_VERIFIED = 1;
    const STATUS_REJECTED = 2;
    protected $fillable = [
        'nama',
        'npsn',
        'detail_alamat',
        'nomor_telepon',
        'email',
        'logo',
        'singkatan',
        'jenjang',
        'wilayah_kode',
        'is_active',
        'status',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_kode', 'kode');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }

    public function kotaKabupaten(): ?string
    {
        if (! $this->wilayah_kode) {
            return null;
        }

        $segments = explode('.', $this->wilayah_kode);

        if (count($segments) < 2) {
            return null;
        }

        $kabKode = $segments[0] . '.' . $segments[1];

        return Wilayah::find($kabKode)?->nama;
    }
}
