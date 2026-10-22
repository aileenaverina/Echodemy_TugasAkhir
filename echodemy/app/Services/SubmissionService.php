<?php

namespace App\Services;

use App\Models\Submission;

class SubmissionService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    // Kumpulkan percobaan dengan jawaban yang sudah tersimpan, apa adanya
    public function finalisasi(Submission $submission): void
    {
        $latihan = $submission->latihan;

        $batasKerja = $submission->created_at->copy()->addMinutes($latihan->waktu_kerja);
        $waktuKumpul = now()->min($batasKerja);

        $telat = $latihan->batas_waktu && $waktuKumpul->greaterThan($latihan->batas_waktu);
        $adaEsai = $latihan->detailLatihans()->where('tipe_soal', 'esai')->exists();

        $submission->update([
            'status' => $telat ? 'telat' : ($adaEsai ? 'dikumpulkan' : 'dinilai'),
        ]);
    }

    // Draft yang waktu kerjanya sudah habis tapi belum dikumpulkan (misal koneksi putus)
    public function finalisasiKedaluwarsa(): void
    {
        Submission::with('latihan')
            ->where('status', 'draft')
            ->get()
            ->each(function (Submission $s) {
                if (! $s->latihan) {
                    return;
                }

                if (now()->greaterThan($s->created_at->copy()->addMinutes($s->latihan->waktu_kerja))) {
                    $this->finalisasi($s);
                }
            });
    }
}
