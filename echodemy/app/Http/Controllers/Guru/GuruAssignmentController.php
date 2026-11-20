<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\HasilAssignment;
use App\Models\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class GuruAssignmentController extends Controller
{
    public function show(Assignment $assignment): View
    {
        $this->authorizeAssignment($assignment);

        $kelas = $assignment->bahanAjar->mataPelajaranKelas->kelas;
        $totalSiswa = $kelas->siswa()->count();

        $hasilList = HasilAssignment::where('assignment_id', $assignment->id)
            ->with('siswa')
            ->get();

        return view('guru.assignment.show', compact('assignment', 'hasilList', 'totalSiswa'));
    }

    public function updateNilai(Request $request, Assignment $assignment, HasilAssignment $hasilAssignment): RedirectResponse
    {
        $this->authorizeAssignment($assignment);
        abort_unless($hasilAssignment->assignment_id === $assignment->id, 404);

        $validated = $request->validate([
            'nilai' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $hasilAssignment->update([
            'nilai' => $validated['nilai'],
            'status' => 'dinilai',
        ]);

        Log::create([
            'deskripsi' => "Memberi nilai tugas \"{$assignment->judul}\" untuk {$hasilAssignment->siswa->nama_lengkap}",
            'user_id'   => auth()->id(),
            'sekolah_id' => $assignment->bahanAjar->mataPelajaranKelas->kelas->sekolah_id,
        ]);

        return back()->with('success', 'Nilai berhasil disimpan.');
    }

    public function updatePengaturan(Request $request, Assignment $assignment): RedirectResponse
    {
        $this->authorizeAssignment($assignment);

        $validated = $request->validate([
            'judul'             => ['required', 'string', 'max:255'],
            'kategori'          => ['required', 'in:harian,tengah_semester,akhir_semester'],
            'tipe_pengumpulan'  => ['required', 'in:file,text,keduanya'],
            'batas_waktu' => ['required', 'date'],
            'persen_nilai_akhir' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $assignment->update([
            'judul'             => $validated['judul'],
            'kategori'          => $validated['kategori'],
            'tipe_pengumpulan'  => $validated['tipe_pengumpulan'],
            'batas_waktu'       => $validated['batas_waktu'],
            'persen_nilai_akhir' => $validated['persen_nilai_akhir'] ?? 0,
        ]);

        return back()->with('success', 'Pengaturan tugas berhasil diperbarui.');
    }

    protected function authorizeAssignment(Assignment $assignment): void
    {
        $mpk = $assignment->bahanAjar->mataPelajaranKelas;
        abort_unless($mpk->gurus->pluck('id')->contains(auth()->id()), 403);
    }
}
