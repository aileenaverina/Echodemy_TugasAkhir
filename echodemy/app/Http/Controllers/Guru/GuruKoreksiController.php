<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Latihan;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuruKoreksiController extends Controller
{
    public function show(Latihan $latihan, Submission $submission): View
    {
        $this->authorize($latihan);
        abort_unless($submission->latihan_id === $latihan->id, 404);

        $answers = $submission->answers()->with('detailLatihan.pilihans')->get();

        return view('guru.koreksi.show', compact('latihan', 'submission', 'answers'));
    }

    public function update(Request $request, Latihan $latihan, Submission $submission): RedirectResponse
    {
        $this->authorize($latihan);
        abort_unless($submission->latihan_id === $latihan->id, 404);

        $validated = $request->validate([
            'nilai' => ['required', 'array'],
            'nilai.*' => ['required', 'numeric', 'min:0'],
        ]);

        foreach ($validated['nilai'] as $answerId => $nilai) {
            Answer::where('id', $answerId)
                ->where('submission_id', $submission->id)
                ->update(['nilai' => $nilai]);
        }

        $submission->update(['status' => 'dinilai']);

        return redirect()->route('guru.latihan.show', $latihan)->with('success', 'Nilai berhasil disimpan.');
    }

    protected function authorize(Latihan $latihan): void
    {
        $mpk = $latihan->bahanAjar->mataPelajaranKelas;
        abort_unless($mpk->gurus->pluck('id')->contains(auth()->id()), 403);
    }
}
