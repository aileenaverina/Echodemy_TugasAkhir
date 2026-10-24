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
        $isPengajar = $mataPelajaranKelas->gurus->pluck('id')->contains(auth()->id());
        $isWaliKelas = $mataPelajaranKelas->kelas->waliKelas->pluck('id')->contains(auth()->id());

        abort_unless($isPengajar || $isWaliKelas, 403);

        $mataPelajaranKelas->load(['kelas', 'mataPelajaran', 'gurus']);

        $bahanAjars = $mataPelajaranKelas->bahanAjars()
            ->with(['materis', 'assignments', 'latihans'])
            ->orderBy('created_at')
            ->get();

        $bobotPerSemester = collect([1, 2])->mapWithKeys(fn($s) => [
            $s => (int) $bahanAjars->where('semester', $s)->sum(
                fn($ba) => $ba->latihans->sum('persen_nilai_akhir') + $ba->assignments->sum('persen_nilai_akhir')
            ),
        ]);

        $jumlahSiswa = $mataPelajaranKelas->kelas->siswa()->count();

        $guruTersedia = User::where('sekolah_id', $mataPelajaranKelas->kelas->sekolah_id)
            ->where('role', 'guru')
            ->whereNotIn('id', $mataPelajaranKelas->gurus->pluck('id'))
            ->orderBy('nama_lengkap')
            ->get();

        return view('guru.mata-pelajaran-kelas.show', compact(
            'mataPelajaranKelas',
            'bahanAjars',
            'jumlahSiswa',
            'guruTersedia',
            'isPengajar',
            'bobotPerSemester'
        ));
    }

    public function storeSection(Request $request, MataPelajaranKelas $mataPelajaranKelas): RedirectResponse
    {
        $this->authorizePengajar($mataPelajaranKelas);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'semester' => ['required', 'in:1,2'],
        ]);

        $mataPelajaranKelas->bahanAjars()->create([
            'judul' => $validated['judul'],
            'notes' => $validated['notes'] ?? '',
            'semester' => $validated['semester'],
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
            'semester' => ['required', 'in:1,2'],
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
        $this->authorizePengajarAtauWali($mataPelajaranKelas);

        $validated = $request->validate([
            'guru_id' => ['required', 'exists:users,id'],
        ]);

        $mataPelajaranKelas->gurus()->syncWithoutDetaching([$validated['guru_id']]);

        return back()->with('success', 'Guru berhasil ditambahkan sebagai pengajar.');
    }

    public function removeGuru(MataPelajaranKelas $mataPelajaranKelas, User $guru): RedirectResponse
    {
        $this->authorizePengajarAtauWali($mataPelajaranKelas);

        if ($mataPelajaranKelas->gurus->count() <= 1) {
            return back()->with('error', 'Tidak bisa menghapus pengajar terakhir.');
        }

        $mataPelajaranKelas->gurus()->detach($guru->id);

        return back()->with('success', 'Guru berhasil dihapus dari daftar pengajar.');
    }

    protected function authorizePengajar(MataPelajaranKelas $mataPelajaranKelas): void
    {
        abort_unless($mataPelajaranKelas->gurus->pluck('id')->contains(auth()->id()), 403);
    }

    protected function authorizePengajarAtauWali(MataPelajaranKelas $mataPelajaranKelas): void
    {
        $isPengajar = $mataPelajaranKelas->gurus->pluck('id')->contains(auth()->id());
        $isWaliKelas = $mataPelajaranKelas->kelas->waliKelas->pluck('id')->contains(auth()->id());

        abort_unless($isPengajar || $isWaliKelas, 403);
    }

    public function toggleLockSection(\App\Models\BahanAjar $bahanAjar): RedirectResponse
    {
        abort_unless($bahanAjar->mataPelajaranKelas->gurus->pluck('id')->contains(auth()->id()), 403);

        $bahanAjar->update(['is_lock' => ! $bahanAjar->is_lock]);

        return back()->with('success', 'Status kunci section diperbarui.');
    }

    public function rekapNilai(MataPelajaranKelas $mataPelajaranKelas)
    {
        $this->authorizePengajarAtauWali($mataPelajaranKelas);

        $mataPelajaranKelas->load(['kelas', 'mataPelajaran', 'gurus']);
        $jumlahSiswa = $mataPelajaranKelas->kelas->siswa()->count();

        $bahanAjars = $mataPelajaranKelas->bahanAjars()->with(['latihans.submissions.answers', 'assignments.hasilAssignments'])->get();

        $items = collect();

        foreach ($bahanAjars as $bahanAjar) {
            foreach ($bahanAjar->latihans as $latihan) {
                $nilaiPerSiswa = $latihan->submissions
                    ->groupBy('user_id')
                    ->map(fn($subs) => $subs->sortByDesc('created_at')->first()->answers->sum('nilai'));

                $rata = $nilaiPerSiswa->isNotEmpty() ? round($nilaiPerSiswa->avg(), 1) : null;

                $items->push([
                    'nama' => $latihan->judul,
                    'section' => $bahanAjar->judul,
                    'nilai' => $rata,
                    'bobot' => $latihan->persen_nilai_akhir,
                ]);
            }

            foreach ($bahanAjar->assignments as $assignment) {
                $nilaiTerisi = $assignment->hasilAssignments->pluck('nilai')->filter(fn($n) => $n !== null);
                $rata = $nilaiTerisi->isNotEmpty() ? round($nilaiTerisi->avg(), 1) : null;

                $items->push([
                    'nama' => $assignment->judul,
                    'section' => $bahanAjar->judul,
                    'nilai' => $rata,
                    'bobot' => $assignment->persen_nilai_akhir,
                ]);
            }
        }

        $itemDenganNilai = $items->filter(fn($i) => $i['nilai'] !== null && $i['bobot']);
        $totalBobot = $itemDenganNilai->sum('bobot');
        $hasilAkhir = $totalBobot > 0
            ? round($itemDenganNilai->sum(fn($i) => $i['nilai'] * $i['bobot']) / $totalBobot, 1)
            : null;

        return view('guru.mata-pelajaran-kelas.rekap', compact('mataPelajaranKelas', 'jumlahSiswa', 'items', 'hasilAkhir'));
    }
}
