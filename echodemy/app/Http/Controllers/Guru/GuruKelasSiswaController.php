<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GuruKelasSiswaController extends Controller
{
    public function index(Kelas $kelas)
    {
        $this->authorizeWali($kelas);

        $siswaKelas = $kelas->siswa()->with('detailUser')->get();

        $siswaTersedia = User::where('sekolah_id', $kelas->sekolah_id)
            ->where('role', 'siswa')
            ->whereDoesntHave('kelas', fn ($q) => $q->where('kelass.status', 'aktif'))
            ->orderBy('nama_lengkap')
            ->get();

        return view('guru.kelas.siswa.index', compact('kelas', 'siswaKelas', 'siswaTersedia'));
    }

    public function store(Request $request, Kelas $kelas): RedirectResponse
    {
        $this->authorizeWali($kelas);

        $validated = $request->validate([
            'siswa_id' => ['required', 'exists:users,id'],
        ]);

        $kelas->users()->attach($validated['siswa_id']);

        return back()->with('success', 'Siswa berhasil ditambahkan ke kelas.');
    }

    public function destroy(Kelas $kelas, User $siswa): RedirectResponse
    {
        $this->authorizeWali($kelas);

        $kelas->users()->detach($siswa->id);

        return back()->with('success', "{$siswa->nama_lengkap} berhasil dikeluarkan dari kelas.");
    }

    protected function authorizeWali(Kelas $kelas): void
    {
        abort_unless($kelas->waliKelas->pluck('id')->contains(auth()->id()), 403);
    }
}
