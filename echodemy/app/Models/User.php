<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

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

    public function kelass()
    {
        return $this->belongsToMany(
            Kelas::class,
            'anggota_kelas',
            'user_id',
            'kelas_id'
        );
    }
    public function mataPelajaranKelass()
    {
        return $this->belongsToMany(
            MataPelajaranKelas::class,
            'guru_mata_pelajaran',
            'user_id',
            'mata_pelajaran_kelas_id'
        );
    }

    public function bahanAjars()
    {
        return $this->hasMany(BahanAjar::class);
    }

    public function progresMateris()
    {
        return $this->belongsToMany(Materi::class, 'progres_materi')
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

    public function photoUrl(): string
    {
        if ($this->role?->value === 'sekolah' && $this->sekolah?->logo) {
            return Storage::url($this->sekolah->logo);
        }

        if ($this->detailUser?->foto_profil) {
            return Storage::url($this->detailUser->foto_profil);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->nama_lengkap ?? 'U') . '&background=F5A524&color=1C1A17';
    }

    public function displayName(): string
    {
        return match ($this->role?->value) {
            'admin' => $this->kode_user,
            'sekolah' => $this->sekolah?->nama ?? $this->nama_lengkap,
            default => $this->nama_lengkap,
        };
    }
}
