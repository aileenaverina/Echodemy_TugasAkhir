<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\DetailUser;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
       $user = $request->user();
        $validated = $request->validated();

        match ($user->role) {
            UserRole::Admin => $this->updateAdmin($request, $user, $validated),
            UserRole::Siswa => $this->updateSiswa($request, $user, $validated),
            UserRole::Guru => $this->updateGuru($request, $user, $validated),
            default => null, // Sekolah & Orang Tua: tidak ada yang diupdate
        };

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    protected function ensureDetailUser(User $user): DetailUser
    {
        if ($user->detailUser) {
            return $user->detailUser;
        }

        $detailUser = DetailUser::create();
        $user->update(['detail_user_id' => $detailUser->id]);

        return $detailUser;
    }

    protected function handleFotoUpload(Request $request, DetailUser $detailUser): ?string
    {
        if (! $request->hasFile('foto_profil')) {
            return $detailUser->foto_profil;
        }

        if ($detailUser->foto_profil) {
            Storage::disk('public')->delete($detailUser->foto_profil);
        }

        return $request->file('foto_profil')->store('foto-profil', 'public');
    }

    protected function updateAdmin(Request $request, User $user, array $validated): void
    {
        $user->update([
            'nama_lengkap' => $validated['nama_lengkap'],
            'kode_user' => $validated['kode_user'],
            'email' => $validated['email'],
        ]);

        $detailUser = $this->ensureDetailUser($user);
        $detailUser->update([
            'nomor_telepon' => $validated['nomor_telepon'] ?? null,
            'foto_profil' => $this->handleFotoUpload($request, $detailUser),
        ]);
    }

    protected function updateSiswa(Request $request, User $user, array $validated): void
    {
        $user->update([
            'nama_lengkap' => $validated['nama_lengkap'],
        ]);

        $detailUser = $this->ensureDetailUser($user);
        $detailUser->update([
            'nis' => $validated['nis'],
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'detail_alamat' => $validated['detail_alamat'] ?? null,
            'nomor_telepon' => $validated['nomor_telepon'] ?? null,
            'nama_ortu' => $validated['nama_ortu'] ?? null,
            'nomor_telepon_ortu_wali' => $validated['nomor_telepon_ortu_wali'] ?? null,
            'foto_profil' => $this->handleFotoUpload($request, $detailUser),
        ]);
    }

    protected function updateGuru(Request $request, User $user, array $validated): void
    {
        $user->update([
            'nama_lengkap' => $validated['nama_lengkap'],
        ]);

        $detailUser = $this->ensureDetailUser($user);
        $detailUser->update([
            'nip' => $validated['nip'] ?? null,
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'detail_alamat' => $validated['detail_alamat'] ?? null,
            'nomor_telepon' => $validated['nomor_telepon'] ?? null,
            'foto_profil' => $this->handleFotoUpload($request, $detailUser),
        ]);
    }

    
}
