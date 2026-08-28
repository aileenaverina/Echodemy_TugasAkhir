<?php

namespace App\Http\Controllers\Sekolah;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\DetailUser;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SekolahSiswaController extends Controller
{
    public function index(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;
        $search = $request->input('search');

        $query = User::where('sekolah_id', $sekolahId)
            ->where('role', UserRole::Siswa)
            ->with(['detailUser', 'kelas']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('detailUser', fn ($q2) => $q2->where('nis', 'like', "%{$search}%"));
            });
        }

        $siswaList = $query->latest()->paginate(10)->withQueryString();
        $kelasList = Kelas::where('sekolah_id', $sekolahId)->where('status', 'aktif')->orderBy('nama')->get();

        return view('sekolah.siswa.index', compact('siswaList', 'search', 'kelasList'));
    }

    public function show(User $siswa)
    {
        $this->authorizeSiswa($siswa);

        return response()->json([
            'nama_lengkap' => $siswa->nama_lengkap,
            'email' => $siswa->email,
            'nis' => $siswa->detailUser?->nis,
            'kelas_id' => $siswa->kelas->first()?->id,
            'status' => $siswa->detailUser?->status ?? 'aktif',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'nis' => ['required', 'string', 'max:45'],
            'kelas_id' => ['required', 'exists:kelass,id'],
            'tahun_masuk' => ['required', 'digits:4'],
            'nama_ortu' => ['nullable', 'string', 'max:45'],
            'nomor_telepon_ortu_wali' => ['nullable', 'string', 'max:45'],
        ]);

        $sekolah = auth()->user()->sekolah;
        $tahunPendek = substr($validated['tahun_masuk'], -2);

        DB::transaction(function () use ($validated, $sekolah, $tahunPendek) {
            $nomorUrut = User::where('sekolah_id', $sekolah->id)
                ->where('role', UserRole::Siswa)
                ->where('kode_user', 'like', 'S' . $sekolah->singkatan . $tahunPendek . '%')
                ->count() + 1;

            $suffix = $sekolah->singkatan . $tahunPendek . str_pad($nomorUrut, 4, '0', STR_PAD_LEFT);

            $detailSiswa = DetailUser::create([
                'nis' => $validated['nis'],
                'nama_ortu' => $validated['nama_ortu'] ?? null,
                'nomor_telepon_ortu_wali' => $validated['nomor_telepon_ortu_wali'] ?? null,
                'status' => 'aktif',
            ]);

            $siswa = User::create([
                'role' => UserRole::Siswa,
                'nama_lengkap' => $validated['nama_lengkap'],
                'kode_user' => 'S' . $suffix,
                'email' => $validated['email'],
                'password' => Hash::make(Str::password(10)),
                'sekolah_id' => $sekolah->id,
                'detail_user_id' => $detailSiswa->id,
            ]);

            $siswa->kelas()->attach($validated['kelas_id']);

            User::create([
                'role' => UserRole::OrangTua,
                'nama_lengkap' => $validated['nama_ortu'] ?? null,
                'kode_user' => 'P' . $suffix,
                'email' => null,
                'password' => Hash::make(Str::password(10)),
                'sekolah_id' => $sekolah->id,
            ]);
        });

        return back()->with('success', 'Akun siswa dan orang tua berhasil dibuat.');
    }

    public function update(Request $request, User $siswa): RedirectResponse
    {
        $this->authorizeSiswa($siswa);

        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($siswa->id)],
            'nis' => ['required', 'string', 'max:45'],
            'kelas_id' => ['required', 'exists:kelass,id'],
            'status' => ['required', 'in:aktif,nonaktif,lulus,keluar'],
        ]);

        $siswa->update([
            'nama_lengkap' => $validated['nama_lengkap'],
            'email' => $validated['email'],
        ]);

        $siswa->detailUser?->update([
            'nis' => $validated['nis'],
            'status' => $validated['status'],
        ]);

        $siswa->kelas()->sync([$validated['kelas_id']]);

        return back()->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(User $siswa): RedirectResponse
    {
        $this->authorizeSiswa($siswa);

        $this->findOrtu($siswa)?->delete();
        $siswa->delete();

        return back()->with('success', "Akun siswa {$siswa->nama_lengkap} berhasil dihapus.");
    }

    public function showOrtu(User $siswa): JsonResponse
    {
        $this->authorizeSiswa($siswa);

        $ortu = $this->findOrtu($siswa);

        return response()->json([
            'nama_ortu' => $siswa->detailUser?->nama_ortu ?? '-',
            'kode_user' => $ortu?->kode_user ?? '-',
            'nomor_telepon' => $siswa->detailUser?->nomor_telepon_ortu_wali,
        ]);
    }

    public function resetOrtuPassword(User $siswa): JsonResponse
    {
        $this->authorizeSiswa($siswa);

        $ortu = $this->findOrtu($siswa);
        abort_unless($ortu, 404, 'Akun orang tua tidak ditemukan.');

        $plainPassword = Str::password(10);
        $ortu->update(['password' => Hash::make($plainPassword)]);

        $nomor = $siswa->detailUser?->nomor_telepon_ortu_wali;
        $waLink = null;

        if ($nomor) {
            $nomorWa = preg_replace('/[^0-9]/', '', $nomor);
            if (str_starts_with($nomorWa, '0')) {
                $nomorWa = '62' . substr($nomorWa, 1);
            }

            $pesan = "Halo, berikut akun login Echodemy untuk memantau {$siswa->nama_lengkap}:\n\n"
                . "Kode User: {$ortu->kode_user}\n"
                . "Password: {$plainPassword}\n\n"
                . "Silakan login di " . route('login');

            $waLink = 'https://wa.me/' . $nomorWa . '?text=' . urlencode($pesan);
        }

        return response()->json([
            'kode_user' => $ortu->kode_user,
            'password' => $plainPassword,
            'wa_link' => $waLink,
        ]);
    }

    protected function findOrtu(User $siswa): ?User
    {
        $suffix = substr($siswa->kode_user, 1);

        return User::where('sekolah_id', $siswa->sekolah_id)
            ->where('role', UserRole::OrangTua)
            ->where('kode_user', 'P' . $suffix)
            ->first();
    }

    protected function authorizeSiswa(User $siswa): void
    {
        abort_unless($siswa->sekolah_id === auth()->user()->sekolah_id && $siswa->role === UserRole::Siswa, 403);
    }
}
