<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Latihan;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuruLatihanController extends Controller
{
    public function show(Latihan $latihan): View
    {
        $this->authorizeLatihan($latihan);

        $kelas = $latihan->bahanAjar->mataPelajaranKelas->kelas;
        $totalSiswa = $kelas->siswa()->count();

        $submissions = Submission::where('latihan_id', $latihan->id)
            ->with('siswa')
            ->get()
            ->groupBy('user_id')
            ->map(function ($group) {
                return [
                    'siswa' => $group->first()->siswa,
                    'percobaan' => $group->count(),
                    'terakhir' => $group->sortByDesc('created_at')->first(),
                ];
            })
            ->values();

        $nilaiMaksimal = $latihan->detailLatihans->sum('nilai');

        return view('guru.latihan.show', compact('latihan', 'submissions', 'totalSiswa', 'nilaiMaksimal'));
    }

    public function soal(Latihan $latihan): View
    {
        $this->authorizeLatihan($latihan);

        $latihan->load('detailLatihans.pilihans');

        return view('guru.latihan.soal', compact('latihan'));
    }

    public function updateNilai(Request $request, Latihan $latihan, Submission $submission): RedirectResponse
    {
        $this->authorizeLatihan($latihan);
        abort_unless($submission->latihan_id === $latihan->id, 404);

        $validated = $request->validate([
            'nilai' => ['required', 'numeric', 'min:0'],
        ]);

        // Simpan sebagai penyesuaian nilai manual di jawaban pertama (ringkas) — 
        // idealnya nilai per-soal, tapi untuk override manual guru cukup di satu tempat
        $submission->answers()->update(['nilai' => 0]);
        $firstAnswer = $submission->answers()->first();
        if ($firstAnswer) {
            $firstAnswer->update(['nilai' => $validated['nilai']]);
        }

        $submission->update(['status' => 'dinilai']);

        return back()->with('success', 'Nilai berhasil diperbarui.');
    }

    protected function authorizeLatihan(Latihan $latihan): void
    {
        $mpk = $latihan->bahanAjar->mataPelajaranKelas;
        abort_unless($mpk->gurus->pluck('id')->contains(auth()->id()), 403);
    }
}
