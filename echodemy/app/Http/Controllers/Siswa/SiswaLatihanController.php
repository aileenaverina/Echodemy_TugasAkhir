<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\DetailLatihan;
use App\Models\Latihan;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiswaLatihanController extends Controller
{
    public function show(Latihan $latihan): View
    {
        $siswa = auth()->user();
        $this->authorize($latihan);

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

    public function kerjakan(Request $request, Latihan $latihan, Submission $submission): View|RedirectResponse
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

        $deadline = $submission->created_at->addMinutes($latihan->waktu_kerja);

        if (now()->greaterThan($deadline)) {
            return $this->finalisasi($latihan, $submission, telat: true);
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
            'pilihan_id' => ['nullable', 'exists:pilihans,id'],
        ]);

        $detail = DetailLatihan::with('pilihans')->find($validated['detail_latihan_id']);

        $jawabanText = $validated['jawaban'] ?? null;
        $nilai = 0;


        if ($detail->tipe_soal === 'pilihan_ganda' && ! empty($validated['pilihan_id'])) {
            $pilihan = $detail->pilihans->firstWhere('id', $validated['pilihan_id']);
            $jawabanText = $pilihan?->pilihan_jawaban;
            $nilai = $pilihan?->is_correct ? $detail->nilai : 0;
        } elseif ($detail->tipe_soal === 'isian' && is_array($detail->jawaban) && count($detail->jawaban)) {
            $cocok = collect($detail->jawaban)->contains(function ($benar) use ($jawabanText) {
                return strtolower(trim($benar)) === strtolower(trim((string) $jawabanText));
            });
            $nilai = $cocok ? $detail->nilai : 0;
        }
        // Esai murni: nilai dibiarkan 0, menunggu koreksi manual guru

        Answer::updateOrCreate(
            ['submission_id' => $submission->id, 'detail_latihan_id' => $detail->id],
            ['jawaban' => $jawabanText, 'nilai' => $nilai]
        );

        return response()->json(['success' => true]);
    }

    public function selesai(Latihan $latihan, Submission $submission): RedirectResponse
    {
        $this->authorizeSubmission($latihan, $submission);
        abort_if($submission->status !== 'draft', 403);

        $this->finalisasi($latihan, $submission, telat: false);

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
