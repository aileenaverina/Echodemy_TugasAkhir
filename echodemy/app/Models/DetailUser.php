<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailUser extends Model
{
    protected $fillable = [
        'nip',
        'nis',
        'jenis_kelamin',
        'tanggal_lahir',
        'detail_alamat',
        'nomor_telepon',
        'nama_ortu',
        'nomor_telepon_ortu_wali',
        'foto_profil',
        'status',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
