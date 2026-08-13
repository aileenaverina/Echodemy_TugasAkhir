<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Sekolah = 'sekolah';
    case Guru = 'guru';
    case Siswa = 'siswa';
    case OrangTua = 'orang_tua';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Sekolah => 'Sekolah',
            self::Guru => 'Guru',
            self::Siswa => 'Siswa',
            self::OrangTua => 'Orang Tua',
        };
    }
}