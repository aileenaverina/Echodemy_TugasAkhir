<?php

namespace App\Services;

use App\Models\Kelas;
use App\Models\Log;
use App\Models\Notif;
use App\Models\Raport;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class RaportService
{
    /**
     * Create a new class instance.
     */

    public function __construct(protected NilaiService $nilai) {}

    public function cek(Kelas $kelas, User $siswa, int $semester): array
    {
        $mpkList = $kelas->penugasanMataPelajaran()->with('mataPelajaran')->get();

        if ($mpkList->isEmpty()) {
            return ['lengkap' => false, 'alasan' => ['Kelas ini belum punya mata pelajaran.'], 'mapel' => collect()];
        }

        $alasan = [];

        $mapel = $mpkList->map(function ($mpk) use ($siswa, $semester, &$alasan) {
            $rekap = $this->nilai->rekapMapel($mpk, $siswa->id, $semester);

            foreach ($rekap['alasan'] as $a) {
                $alasan[] = $mpk->mataPelajaran->nama . ': ' . $a;
            }

            return ['nama' => $mpk->mataPelajaran->nama, 'rekap' => $rekap];
        });

        return ['lengkap' => empty($alasan), 'alasan' => $alasan, 'mapel' => $mapel];
    }

    public function terbitkan(Kelas $kelas, User $siswa, int $semester, User $penerbit): Raport
    {
        $cek = $this->cek($kelas, $siswa, $semester);

        if (! $cek['lengkap']) {
            throw new RuntimeException('Nilai belum lengkap: ' . implode('; ', $cek['alasan']));
        }

        $sekolah = $kelas->sekolah;

        $snapshot = [
            'sekolah' => [
                'nama' => $sekolah->nama,
                'npsn' => $sekolah->npsn,
                'alamat' => $sekolah->detail_alamat,
                'logo' => $sekolah->logo,
            ],
            'siswa' => [
                'nama' => $siswa->nama_lengkap,
                'nis' => $siswa->detailUser?->nis,
                'kode' => $siswa->kode_user,
            ],
            'kelas' => $kelas->nama,
            'tahun_ajaran' => $kelas->tahun_ajaran,
            'semester' => $semester,
            'wali_kelas' => $kelas->waliKelas->first()?->nama_lengkap,
            'mapel' => $cek['mapel']->map(fn($m) => [
                'nama' => $m['nama'],
                'harian' => $m['rekap']['kategori']['harian']['nilai'],
                'tengah_semester' => $m['rekap']['kategori']['tengah_semester']['nilai'],
                'akhir_semester' => $m['rekap']['kategori']['akhir_semester']['nilai'],
                'nilai_akhir' => $m['rekap']['hasil_akhir'],
            ])->values()->all(),
        ];

        $snapshot['rata_rata'] = round(collect($snapshot['mapel'])->avg('nilai_akhir'), 1);
        $snapshot['diterbitkan_pada'] = now()->toIso8601String();
        $snapshot['diterbitkan_oleh'] = $penerbit->nama_lengkap;

        return DB::transaction(function () use ($kelas, $siswa, $semester, $penerbit, $snapshot) {
            $versi = (int) Raport::withTrashed()
                ->where('user_id', $siswa->id)
                ->where('kelas_id', $kelas->id)
                ->where('semester', $semester)
                ->max('versi') + 1;

            $path = "raports/{$kelas->id}/semester-{$semester}/{$siswa->kode_user}-v{$versi}.pdf";

            $raport = Raport::create([
                'user_id' => $siswa->id,
                'kelas_id' => $kelas->id,
                'semester' => $semester,
                'versi' => $versi,
                'file' => $path,
                'snapshot' => $snapshot,
                'diterbitkan_oleh' => $penerbit->id,
            ]);

            Log::create([
                'deskripsi' => "menerbitkan raport {$siswa->nama_lengkap} semester {$semester} ({$kelas->nama}, versi {$versi})",
                'user_id' => $penerbit->id,
                'sekolah_id' => $kelas->sekolah_id,
            ]);

            Notif::create([
                'judul' => "RAPORT SEMESTER {$semester} TERBIT",
                'deskripsi' => "Raport {$kelas->nama} sudah bisa diunduh" . ($versi > 1 ? ' (diperbarui)' : ''),
                'is_read' => false,
                'user_id' => $siswa->id,
            ]);

            // file ditulis paling akhir: kalau gagal, seluruh transaksi di atas dibatalkan
            $pdf = Pdf::loadView('raport.pdf', [
                'd' => $snapshot,
                'logo' => $this->logoData($snapshot['sekolah']['logo']),
            ])->setPaper('a4')->output();

            Storage::disk('local')->put($path, $pdf);

            return $raport;
        });
    }

    protected function logoData(?string $logo): ?string
    {
        $disk = Storage::disk('public');

        if (! $logo || ! $disk->exists($logo)) {
            return null;
        }

        return 'data:' . $disk->mimeType($logo) . ';base64,' . base64_encode($disk->get($logo));
    }
}
