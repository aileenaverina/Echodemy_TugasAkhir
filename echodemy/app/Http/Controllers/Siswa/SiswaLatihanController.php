<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\DetailLatihan;
use App\Models\Latihan;
use App\Models\Submission;
use App\Services\SubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiswaLatihanController extends Controller
{
    public function show(Latihan $latihan,  SubmissionService $svc): View
    {
        $siswa = auth()->user();
        $this->authorize($latihan);
        $svc->finalisasiKedaluwarsa();

        $submissions = Submission::where('latihan_id', $latihan->id)
            ->where('user_id', $siswa->id)
            ->whereIn('status', ['dikumpulkan', 'telat', 'dinilai'])
            ->with('answers')
            ->get();

        $percobaanDipakai = $submissions->count();
        $sisaPercobaan = max(0, $latihan->maksimal_percobaan - $percobaanDipakai);

        $nilaiTertinggi = $submissions->isNotEmpty()
            ? $submissions->max(fn($s) => $s->answers->sum('nilai'))
            : null;

        $submissionAktif = Submission::where('latihan_id', $latihan->id)
            ->where('user_id', $siswa->id)
            ->where('status', 'draft')
            ->first();

        $sudahPernahKerja = $percobaanDipakai > 0;
        $submissionTerakhir = $submissions->sortByDesc('created_at')->first();

        return view('siswa.latihan.show', compact(
            'latihan',
            'sisaPercobaan',
            'nilaiTertinggi',
            'sudahPernahKerja',
            'submissionAktif',
            'submissionTerakhir'
        ));
    }

    public function mulai(Latihan $latihan): RedirectResponse
    {
        $siswa = auth()->user();
        $this->authorize($latihan);

        // Lanjutkan draft yang belum selesai kalau ada
        $existing = Submission::where('latihan_id', $latihan->id)
            ->where('user_id', $siswa->id)
            ->where('status', 'draft')
            ->first();

        if ($existing) {
            return redirect()->route('siswa.latihan.kerjakan', [$latihan, $existing]);
        }

        $percobaanDipakai = Submission::where('latihan_id', $latihan->id)
            ->where('user_id', $siswa->id)
            ->whereIn('status', ['dikumpulkan', 'telat', 'dinilai'])
            ->count();

        abort_if($percobaanDipakai >= $latihan->maksimal_percobaan, 403, 'Percobaan sudah habis.');

        $submission = Submission::create([
            'latihan_id' => $latihan->id,
            'user_id' => $siswa->id,
            'status' => 'draft',
        ]);

        return redirect()->route('siswa.latihan.kerjakan', [$latihan, $submission]);
    }

    public function kerjakan(Request $request, Latihan $latihan, Submission $submission, SubmissionService $svc): View|RedirectResponse
    {
        $this->authorizeSubmission($latihan, $submission);
        abort_if($submission->status !== 'draft', 403, 'Percobaan ini sudah selesai.');

        $soalList = $latihan->detailLatihans()->with('pilihans')->orderBy('nomor')->get();

        if ($latihan->is_random) {
            $soalList = $soalList->shuffle()->values();
        }

        $nomor = (int) $request->input('soal', 1);
        $nomor = max(1, min($nomor, $soalList->count()));
        $soal = $soalList->get($nomor - 1);

        $jawabanTersimpan = Answer::where('submission_id', $submission->id)
            ->pluck('jawaban', 'detail_latihan_id');

        $deadline = $submission->created_at->copy()->addMinutes($latihan->waktu_kerja);

        if (now()->greaterThan($deadline)) {
            $svc->finalisasi($submission);

            return redirect()->route('siswa.latihan.show', $latihan)
                ->with('error', 'Waktu habis, jawabanmu sudah tersimpan otomatis.');
        }

        return view('siswa.latihan.kerjakan', compact(
            'latihan',
            'submission',
            'soalList',
            'soal',
            'nomor',
            'jawabanTersimpan',
            'deadline'
        ));
    }

    public function jawab(Request $request, Latihan $latihan, Submission $submission): JsonResponse
    {
        $this->authorizeSubmission($latihan, $submission);
        abort_if($submission->status !== 'draft', 403);

        $validated = $request->validate([
            'detail_latihan_id' => ['required', 'exists:detail_latihans,id'],
            'jawaban' => ['nullable', 'string'],
            'pilihan_ids' => ['nullable', 'array'],
            'pilihan_ids.*' => ['integer'],
        ]);

        $detail = DetailLatihan::with('pilihans')->findOrFail($validated['detail_latihan_id']);
        abort_unless($detail->latihan_id === $latihan->id, 404);

        $jawabanText = $validated['jawaban'] ?? '';
        $nilai = 0;

        if ($detail->tipe_soal === 'pilihan_ganda') {
            $ids = collect($validated['pilihan_ids'] ?? [])
                ->map(fn($id) => (int) $id)
                ->unique()
                ->filter(fn($id) => $detail->pilihans->contains('id', $id))
                ->values();

            // persentase positif menambah, negatif mengurangi; hasil dibatasi 0-100%
            $persen = $detail->pilihans->whereIn('id', $ids)->sum('persentase_correct');
            $nilai = round($detail->nilai * max(0, min($persen, 100)) / 100, 2);
            $jawabanText = $ids->isEmpty() ? '' : $ids->toJson();
        } elseif ($detail->tipe_soal === 'isian' && is_array($detail->jawaban) && count($detail->jawaban)) {
            $cocok = collect($detail->jawaban)->contains(
                fn($benar) => strtolower(trim($benar)) === strtolower(trim($jawabanText))
            );
            $nilai = $cocok ? $detail->nilai : 0;
        }
        // esai: nilai 0 sampai dikoreksi guru

        Answer::updateOrCreate(
            ['submission_id' => $submission->id, 'detail_latihan_id' => $detail->id],
            ['jawaban' => $jawabanText, 'nilai' => $nilai]
        );

        return response()->json(['success' => true]);
    }

    public function selesai(Latihan $latihan, Submission $submission, SubmissionService $svc): RedirectResponse
    {
        $this->authorizeSubmission($latihan, $submission);

        if ($submission->status === 'draft') {
            $svc->finalisasi($submission);
        }

        return redirect()->route('siswa.latihan.show', $latihan)->with('success', 'Latihan berhasil dikumpulkan.');
    }

    protected function finalisasi(Latihan $latihan, Submission $submission, bool $telat)
    {
        $deadline = $submission->created_at->addMinutes($latihan->waktu_kerja);
        $adaEsai = $latihan->detailLatihans()->where('tipe_soal', 'esai')->exists();
        $status = $telat || now()->greaterThan($deadline) ? 'telat' : 'dikumpulkan';

        $submission->update(['status' => $adaEsai ? $status : 'dinilai']);

        return redirect()->route('siswa.latihan.show', $latihan)
            ->with($telat ? 'error' : 'success', $telat ? 'Waktu habis, jawaban otomatis dikumpulkan.' : 'Latihan berhasil dikumpulkan.');
    }

    public function hasil(Latihan $latihan, Submission $submission): View
    {
        $this->authorizeSubmission($latihan, $submission);
        abort_if($submission->status === 'draft', 404);

        $answers = $submission->answers()->with('detailLatihan.pilihans')->get();

        return view('siswa.latihan.hasil', compact('latihan', 'submission', 'answers'));
    }

    protected function authorize(Latihan $latihan): void
    {
        $kelas = $latihan->bahanAjar->mataPelajaranKelas->kelas;
        abort_unless($kelas->siswa->pluck('id')->contains(auth()->id()), 403);
        abort_if($latihan->is_lock, 403);
    }

    protected function authorizeSubmission(Latihan $latihan, Submission $submission): void
    {
        $this->authorize($latihan);
        abort_unless($submission->latihan_id === $latihan->id && $submission->user_id === auth()->id(), 403);
    }
}
