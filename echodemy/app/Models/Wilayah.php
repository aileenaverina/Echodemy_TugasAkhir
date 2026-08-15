<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    protected $primaryKey = 'kode';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['kode', 'nama'];

    public function sekolahs()
    {
        return $this->hasMany(Sekolah::class, 'wilayah_kode', 'kode');
    }

    public function scopeProvinsi(Builder $query)
    {
        return $query->where('kode', 'not like', '%.%');
    }

    public function scopeChildrenOf(Builder $query, string $parentKode)
    {
        $dotCount = substr_count($parentKode, '.');

        // 0 titik (provinsi) -> anaknya kab/kota, 2 digit
        // 1 titik (kab/kota) -> anaknya kecamatan, 2 digit
        // 2 titik (kecamatan) -> anaknya kelurahan, 4 digit
        $segmentLength = match ($dotCount) {
            0 => 2,
            1 => 2,
            2 => 4,
            default => 2,
        };

        return $query->where('kode', 'like', $parentKode . '.' . str_repeat('_', $segmentLength));
    }
}
