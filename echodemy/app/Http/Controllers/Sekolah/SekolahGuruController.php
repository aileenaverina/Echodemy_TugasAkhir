<?php

namespace App\Http\Controllers\Sekolah;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Mail\GuruAccountCreatedMail;
use App\Models\DetailUser;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SekolahGuruController extends Controller
{
    public function index(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;
        $search = $request->input('search');

        $query = User::where('sekolah_id', $sekolahId)
            ->where('role', UserRole::Guru)
            ->with(['detailUser', 'kelasSebagaiWali', 'mataPelajaranKelasDiajar.mataPelajaran']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $guruList = $query->latest()->paginate(10)->withQueryString();

        return view('sekolah.guru.index', compact('guruList', 'search'));
    }

    public function show(User $guru)
    {
        $this->authorizeGuru($guru);

        return response()->json([
            'nama_lengkap' => $guru->nama_lengkap,
            'email' => $guru->email,
            'nomor_telepon' => $guru->detailUser?->nomor_telepon,
            'status' => $guru->detailUser?->status ?? 'aktif',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'nomor_telepon' => ['nullable', 'string', 'max:45'],
            'tahun_masuk' => ['required', 'digits:4'],
        ]);

        $sekolah = auth()->user()->sekolah;
        $tahunPendek = substr($validated['tahun_masuk'], -2);
        $plainPassword = Str::password(10);

        $detailUser = DetailUser::create([
            'nomor_telepon' => $validated['nomor_telepon'] ?? null,
            'status' => 'aktif',
        ]);

        $nomorUrut = User::where('sekolah_id', $sekolah->id)
            ->where('role', UserRole::Guru)
            ->where('kode_user', 'like', 'T' . $sekolah->singkatan . $tahunPendek . '%')
            ->count() + 1;

        $kodeUser = 'T' . $sekolah->singkatan . $tahunPendek . str_pad($nomorUrut, 4, '0', STR_PAD_LEFT);

        $guru = User::create([
            'role' => UserRole::Guru,
            'nama_lengkap' => $validated['nama_lengkap'],
            'kode_user' => $kodeUser,
            'email' => $validated['email'],
            'password' => Hash::make($plainPassword),
            'sekolah_id' => $sekolah->id,
            'detail_user_id' => $detailUser->id,
        ]);

        Mail::to($guru->email)->send(new GuruAccountCreatedMail($guru, $plainPassword));

        return back()->with('success', 'Akun guru berhasil dibuat dan email terkirim.');
    }

    public function update(Request $request, User $guru): RedirectResponse
    {
        $this->authorizeGuru($guru);

        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($guru->id)],
            'nomor_telepon' => ['nullable', 'string', 'max:45'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        $guru->update([
            'nama_lengkap' => $validated['nama_lengkap'],
            'email' => $validated['email'],
        ]);

        if ($guru->detailUser) {
            $guru->detailUser->update([
                'nomor_telepon' => $validated['nomor_telepon'] ?? null,
                'status' => $validated['status'],
            ]);
        }

        return back()->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(User $guru): RedirectResponse
    {
        $this->authorizeGuru($guru);

        $guru->delete();

        return back()->with('success', "Akun guru {$guru->nama_lengkap} berhasil dihapus.");
    }

    protected function authorizeGuru(User $guru): void
    {
        abort_unless($guru->sekolah_id === auth()->user()->sekolah_id && $guru->role === UserRole::Guru, 403);
    }
}
