<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\MataPelajaranKelas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GuruKelasMataPelajaranController extends Controller
{
     public function index(Kelas $kelas)
    {
        $this->authorizeWali($kelas);

        $items = $kelas->penugasanMataPelajaran()->with(['mataPelajaran', 'gurus'])->get();
        $mataPelajaranList = MataPelajaran::orderBy('nama')->get();
        $guruList = User::where('sekolah_id', $kelas->sekolah_id)->where('role', 'guru')->orderBy('nama_lengkap')->get();

        return view('guru.kelas.mata-pelajaran.index', compact('kelas', 'items', 'mataPelajaranList', 'guruList'));
    }

    public function store(Request $request, Kelas $kelas): RedirectResponse
    {
        $this->authorizeWali($kelas);

        $validated = $request->validate([
            'mata_pelajaran_id' => [
                'required', 'exists:mata_pelajarans,id',
                Rule::unique('mata_pelajaran_kelas')->where('kelas_id', $kelas->id),
            ],
            'guru_ids' => ['required', 'array', 'min:1'],
            'guru_ids.*' => ['exists:users,id'],
        ]);

        $mpk = MataPelajaranKelas::create([
            'kelas_id' => $kelas->id,
            'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
        ]);

        $mpk->gurus()->attach($validated['guru_ids']);

        return back()->with('success', 'Mata pelajaran berhasil ditambahkan ke kelas.');
    }

    public function update(Request $request, Kelas $kelas, MataPelajaranKelas $mataPelajaranKelas): RedirectResponse
    {
        $this->authorizeWali($kelas);
        abort_unless($mataPelajaranKelas->kelas_id === $kelas->id, 404);

        $validated = $request->validate([
            'guru_ids' => ['required', 'array', 'min:1'],
            'guru_ids.*' => ['exists:users,id'],
        ]);

        $mataPelajaranKelas->gurus()->sync($validated['guru_ids']);

        return back()->with('success', 'Pengajar berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas, MataPelajaranKelas $mataPelajaranKelas): RedirectResponse
    {
        $this->authorizeWali($kelas);
        abort_unless($mataPelajaranKelas->kelas_id === $kelas->id, 404);

        $mataPelajaranKelas->gurus()->detach();
        $mataPelajaranKelas->delete();

        return back()->with('success', 'Mata pelajaran berhasil dihapus dari kelas.');
    }

    protected function authorizeWali(Kelas $kelas): void
    {
        abort_unless($kelas->waliKelas->pluck('id')->contains(auth()->id()), 403);
    }
}
