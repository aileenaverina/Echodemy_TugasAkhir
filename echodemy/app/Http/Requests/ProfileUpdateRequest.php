<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         $user = $this->user();

        return match ($user->role) {
            UserRole::Admin => [
                'nama_lengkap' => ['required', 'string', 'max:255'],
                'kode_user' => ['required', 'string', 'max:45', Rule::unique(User::class, 'kode_user')->ignore($user->id)],
                'email' => ['required', 'email', 'max:255', Rule::unique(User::class, 'email')->ignore($user->id)],
                'nomor_telepon' => ['nullable', 'string', 'max:45'],
                'foto_profil' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            ],

            UserRole::Siswa => [
                'nama_lengkap' => ['required', 'string', 'max:255'],
                'nis' => ['required', 'string', 'max:45'],
                'jenis_kelamin' => ['nullable', 'in:l,p'],
                'tanggal_lahir' => ['nullable', 'date'],
                'detail_alamat' => ['nullable', 'string', 'max:500'],
                'nomor_telepon' => ['nullable', 'string', 'max:45'],
                'nama_ortu' => ['nullable', 'string', 'max:45'],
                'nomor_telepon_ortu_wali' => ['nullable', 'string', 'max:45'],
                'foto_profil' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            ],

            UserRole::Guru => [
                'nama_lengkap' => ['required', 'string', 'max:255'],
                'nip' => ['nullable', 'string', 'max:45'],
                'jenis_kelamin' => ['nullable', 'in:l,p'],
                'tanggal_lahir' => ['nullable', 'date'],
                'detail_alamat' => ['nullable', 'string', 'max:500'],
                'nomor_telepon' => ['nullable', 'string', 'max:45'],
                'foto_profil' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            ],

            // Sekolah: semua field read only, gak ada yang divalidasi
            // OrangTua: gak ada halaman profil sama sekali
            default => [],
        };
    }
}
