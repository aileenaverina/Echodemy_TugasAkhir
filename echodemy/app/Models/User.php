<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
   
  
   
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $fillable = [
        'role',
        'nama_lengkap',
        'kode_user',
        'email',
        'password',
        'last_login_at',
        'detail_user_id',
        'sekolah_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function detailUser()
    {
        return $this->belongsTo(DetailUser::class);
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'anggota_kelas');
    }

    public function mataPelajaranDiajar()
    {
        return $this->belongsToMany(MataPelajaran::class, 'guru_mata_pelajaran')
            ->withPivot('kelas_id');
    }

    public function bahanAjars()
    {
        return $this->hasMany(BahanAjar::class);
    }

    public function progresMateris()
    {
        return $this->belongsToMany(Materi::class, 'progres_materis')
            ->withPivot('is_done');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function notifs()
    {
        return $this->hasMany(Notif::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }
}
