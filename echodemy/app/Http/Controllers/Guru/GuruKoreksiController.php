<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Latihan;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class GuruKoreksiController extends Controller
{
    public function show(Latihan $latihan, Submission $submission): View
    {
        $this->authorize($latihan);
        abort_unless($submission->latihan_id === $latihan->id, 404);
        app(\App\Services\SubmissionService::class)->finalisasiKedaluwarsa($latihan->id);
        $submission->refresh();
        abort_if($submission->status === 'draft', 403, 'Siswa masih mengerjakan.');

        $answers = $submission->answers()->with('detailLatihan.pilihans')->get();

        return view('guru.koreksi.show', compact('latihan', 'submission', 'answers'));
    }

    public function update(Request $request, Latihan $latihan, Submission $submission): RedirectResponse
    {
        $this->authorize($latihan);
        abort_unless($submission->latihan_id === $latihan->id, 404);
        app(\App\Services\SubmissionService::class)->finalisasiKedaluwarsa($latihan->id);
        $submission->refresh();
        abort_if($submission->status === 'draft', 403, 'Siswa masih mengerjakan.');

        $validated = $request->validate([
            'nilai' => ['required', 'array'],
            'nilai.*' => ['required', 'numeric', 'min:0'],
            'nilai_akhir' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $jawabans = $submission->answers()->with('detailLatihan')->get()->keyBy('id');

        foreach ($validated['nilai'] as $answerId => $nilai) {
            $jawaban = $jawabans->get((int) $answerId);
            if ($jawaban && (float) $nilai > (float) $jawaban->detailLatihan->nilai) {
                throw ValidationException::withMessages(["nilai.$answerId" => 'Nilai melebihi poin soal.']);
            }
        }

        foreach ($validated['nilai'] as $answerId => $nilai) {
            $jawabans->get((int) $answerId)?->update(['nilai' => $nilai]);
        }
        $submission->update([
            'status' => 'dinilai',
            'nilai_akhir' => $validated['nilai_akhir'] ?? null,
        ]);

        return redirect()->route('guru.latihan.show', $latihan)->with('success', 'Nilai berhasil disimpan.');
    }

    protected function authorize(Latihan $latihan): void
    {
        $mpk = $latihan->bahanAjar->mataPelajaranKelas;
        abort_unless($mpk->gurus->pluck('id')->contains(auth()->id()), 403);
    }
}
