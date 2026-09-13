<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaranKelas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GuruMataPelajaranKelasController extends Controller
{
    public function show(MataPelajaranKelas $mataPelajaranKelas)
    {
        $this->authorizePengajar($mataPelajaranKelas);

        $mataPelajaranKelas->load(['kelas', 'mataPelajaran', 'gurus']);

        $bahanAjars = $mataPelajaranKelas->bahanAjars()
            ->with(['materis', 'assignments', 'latihans'])
            ->orderBy('created_at')
            ->get();

        $jumlahSiswa = $mataPelajaranKelas->kelas->siswa()->count();

        // Guru sekolah yang belum jadi pengajar di kombinasi ini, buat modal Tambah Guru
        $guruTersedia = User::where('sekolah_id', $mataPelajaranKelas->kelas->sekolah_id)
            ->where('role', 'guru')
            ->whereNotIn('id', $mataPelajaranKelas->gurus->pluck('id'))
            ->orderBy('nama_lengkap')
            ->get();

        return view('guru.mata-pelajaran-kelas.show', compact('mataPelajaranKelas', 'bahanAjars', 'jumlahSiswa', 'guruTersedia'));
    }

    public function storeSection(Request $request, MataPelajaranKelas $mataPelajaranKelas): RedirectResponse
    {
        $this->authorizePengajar($mataPelajaranKelas);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $mataPelajaranKelas->bahanAjars()->create([
            'judul' => $validated['judul'],
            'notes' => $validated['notes'] ?? '',
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Section berhasil ditambahkan.');
    }

    public function updateSection(Request $request, MataPelajaranKelas $mataPelajaranKelas, \App\Models\BahanAjar $bahanAjar): RedirectResponse
    {
        $this->authorizePengajar($mataPelajaranKelas);
        abort_unless($bahanAjar->mata_pelajaran_kelas_id === $mataPelajaranKelas->id, 404);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $bahanAjar->update($validated);

        return back()->with('success', 'Section berhasil diperbarui.');
    }

    public function destroySection(MataPelajaranKelas $mataPelajaranKelas, \App\Models\BahanAjar $bahanAjar): RedirectResponse
    {
        $this->authorizePengajar($mataPelajaranKelas);
        abort_unless($bahanAjar->mata_pelajaran_kelas_id === $mataPelajaranKelas->id, 404);

        $bahanAjar->delete();

        return back()->with('success', 'Section berhasil dihapus.');
    }

    public function addGuru(Request $request, MataPelajaranKelas $mataPelajaranKelas): RedirectResponse
    {
        $this->authorizePengajar($mataPelajaranKelas);

        $validated = $request->validate([
            'guru_id' => ['required', 'exists:users,id'],
        ]);

        $mataPelajaranKelas->gurus()->syncWithoutDetaching([$validated['guru_id']]);

        return back()->with('success', 'Guru berhasil ditambahkan sebagai pengajar.');
    }

    protected function authorizePengajar(MataPelajaranKelas $mataPelajaranKelas): void
    {
        abort_unless($mataPelajaranKelas->gurus->pluck('id')->contains(auth()->id()), 403);
    }

    public function toggleLockSection(\App\Models\BahanAjar $bahanAjar): RedirectResponse
    {
        abort_unless($bahanAjar->mataPelajaranKelas->gurus->pluck('id')->contains(auth()->id()), 403);

        $bahanAjar->update(['is_lock' => ! $bahanAjar->is_lock]);

        return back()->with('success', 'Status kunci section diperbarui.');
    }
}
