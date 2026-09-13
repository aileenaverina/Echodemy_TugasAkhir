<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\HasilAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        return back()->with('success', 'Nilai berhasil disimpan.');
    }

    protected function authorizeAssignment(Assignment $assignment): void
    {
        $mpk = $assignment->bahanAjar->mataPelajaranKelas;
        abort_unless($mpk->gurus->pluck('id')->contains(auth()->id()), 403);
    }
}
