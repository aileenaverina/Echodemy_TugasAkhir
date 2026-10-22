<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\BahanAjar;
use App\Models\Latihan;
use App\Models\MataPelajaranKelas;
use Illuminate\Support\Collection;
use App\Services\SubmissionService;

class NilaiService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function nilaiLatihan(Latihan $latihan, int $userId): array
    {
        $submissions = $latihan->submissions
            ->where('user_id', $userId)
            ->whereIn('status', ['dikumpulkan', 'telat', 'dinilai']);

        if ($submissions->isEmpty()) {
            return $this->tanpaPengumpulan($latihan->batas_waktu);
        }

        $maks = $latihan->detailLatihans->sum('nilai');
        $skor = fn($s) => $s->nilai_akhir !== null
            ? (float) $s->nilai_akhir
            : ($maks > 0 ? round($s->answers->sum('nilai') / $maks * 100, 2) : 0);

        $dinilai = $submissions->where('status', 'dinilai');
        $dipilih = $this->pilih($dinilai, $latihan->penilaian_percobaan, $skor);

        // Ada percobaan yang belum final (esai belum dikoreksi) -> belum bisa dianggap final
        if ($submissions->count() > $dinilai->count()) {
            return ['nilai' => null, 'status' => 'menunggu_koreksi', 'sementara' => $dipilih];
        }

        return ['nilai' => $dipilih, 'status' => 'final', 'sementara' => $dipilih];
    }

    public function nilaiAssignment(Assignment $assignment, int $userId): array
    {
        $hasil = $assignment->hasilAssignments->where('user_id', $userId)->first();

        if (! $hasil || $hasil->status === 'belum dikerjakan') {
            return $this->tanpaPengumpulan($assignment->batas_waktu);
        }

        if ($hasil->status === 'dinilai' && $hasil->nilai !== null) {
            return ['nilai' => (float) $hasil->nilai, 'status' => 'final', 'sementara' => (float) $hasil->nilai];
        }

        return ['nilai' => null, 'status' => 'menunggu_koreksi', 'sementara' => null];
    }

    public function rekapMapel(MataPelajaranKelas $mpk, int $userId, int $semester): array
    {
        app(SubmissionService::class)->finalisasiKedaluwarsa();
        $bahanAjars = $mpk->bahanAjars()
            ->where('semester', $semester)
            ->with([
                'latihans.detailLatihans',
                'latihans.submissions' => fn($q) => $q->where('user_id', $userId)->with('answers'),
                'assignments.hasilAssignments' => fn($q) => $q->where('user_id', $userId),
            ])
            ->orderBy('created_at')
            ->get();

        $items = collect();

        foreach ($bahanAjars as $ba) {
            foreach ($ba->latihans as $l) {
                $items->push($this->item($ba, $l->judul, 'latihan', $l->kategori, $l->persen_nilai_akhir, $this->nilaiLatihan($l, $userId)));
            }
            foreach ($ba->assignments as $a) {
                //$items->push($this->item($ba, $a->judul, 'assignment', 'harian', $a->persen_nilai_akhir, $this->nilaiAssignment($a, $userId)));
                $items->push($this->item($ba, $a->judul, 'assignment', $a->kategori, $a->persen_nilai_akhir, $this->nilaiAssignment($a, $userId)));
            }
        }

        $dihitung = $items->where('bobot', '>', 0);
        $totalBobot = (int) $dihitung->sum('bobot');

        $alasan = [];
        if ($dihitung->isEmpty()) {
            $alasan[] = 'Belum ada penilaian yang diberi bobot.';
        } elseif ($totalBobot !== 100) {
            $alasan[] = "Total bobot {$totalBobot}/100.";
        }
        foreach ($dihitung->whereNull('nilai') as $i) {
            $alasan[] = $i['nama'] . ' ' . ($i['status'] === 'menunggu_koreksi' ? 'menunggu koreksi' : 'belum dikerjakan');
        }

        $lengkap = empty($alasan);
        $hasilAkhir = $lengkap ? round($dihitung->sum(fn($i) => $i['nilai'] * $i['bobot']) / 100, 1) : null;

        $punyaSementara = $dihitung->filter(fn($i) => $i['sementara'] !== null);
        $bobotSementara = (int) $punyaSementara->sum('bobot');
        $sementara = $bobotSementara > 0
            ? round($punyaSementara->sum(fn($i) => $i['sementara'] * $i['bobot']) / $bobotSementara, 1)
            : null;

        $kategori = [];
        foreach (['harian', 'tengah_semester', 'akhir_semester'] as $k) {
            $g = $dihitung->where('kategori', $k);
            $b = (int) $g->sum('bobot');
            $kategori[$k] = [
                'bobot' => $b,
                'nilai' => $b > 0 && $g->whereNull('nilai')->isEmpty()
                    ? round($g->sum(fn($i) => $i['nilai'] * $i['bobot']) / $b, 1)
                    : null,
            ];
        }

        return [
            'items' => $items,
            'total_bobot' => $totalBobot,
            'lengkap' => $lengkap,
            'alasan' => $alasan,
            'hasil_akhir' => $hasilAkhir,
            'sementara' => $sementara,
            'kategori' => $kategori,
        ];
    }

    protected function pilih(Collection $dinilai, ?string $mode, callable $skor): ?float
    {
        if ($dinilai->isEmpty()) {
            return null;
        }

        return $mode === 'terakhir'
            ? $skor($dinilai->sortByDesc('created_at')->first())
            : $dinilai->map($skor)->max();
    }

    // Tidak mengumpulkan: 0 kalau deadline lewat, belum ada nilai kalau deadline belum lewat
    protected function tanpaPengumpulan($batasWaktu): array
    {
        if ($batasWaktu && now()->greaterThan($batasWaktu)) {
            return ['nilai' => 0.0, 'status' => 'tidak_mengumpulkan', 'sementara' => 0.0];
        }

        return ['nilai' => null, 'status' => 'belum', 'sementara' => null];
    }

    protected function item(BahanAjar $ba, string $nama, string $jenis, string $kategori, $bobot, array $hasil): array
    {
        return [
            'nama' => $nama,
            'section' => $ba->judul,
            'jenis' => $jenis,
            'kategori' => $kategori,
            'bobot' => (int) $bobot,
            'nilai' => $hasil['nilai'],
            'sementara' => $hasil['sementara'],
            'status' => $hasil['status'],
        ];
    }
}
