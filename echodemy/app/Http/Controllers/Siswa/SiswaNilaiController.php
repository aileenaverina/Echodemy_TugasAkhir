<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiswaNilaiController extends Controller
{
    public function index(Request $request): View
    {
        $siswa = auth()->user();
        $kelas = $siswa->kelas()->where('status', 'aktif')->first();

        $mataPelajaranKelasList = collect();
        $items = collect();
        $hasilAkhir = null;
        $activeMpk = null;

        if ($kelas) {
            $mataPelajaranKelasList = $kelas->penugasanMataPelajaran()->with('mataPelajaran')->get();

            $activeMpkId = $request->input('mapel', $mataPelajaranKelasList->first()?->id);
            $activeMpk = $mataPelajaranKelasList->firstWhere('id', (int) $activeMpkId);

            if ($activeMpk) {
                $bahanAjars = $activeMpk->bahanAjars()
                    ->with(['latihans.submissions.answers', 'assignments.hasilAssignments'])
                    ->get();

                foreach ($bahanAjars as $bahanAjar) {
                    foreach ($bahanAjar->latihans as $latihan) {
                        $submission = $latihan->submissions
                            ->where('user_id', $siswa->id)
                            ->sortByDesc('created_at')
                            ->first();

                        $items->push([
                            'nama' => $latihan->judul,
                            'section' => $bahanAjar->judul,
                            'nilai' => $submission ? $submission->answers->sum('nilai') : null,
                            'bobot' => $latihan->persen_nilai_akhir,
                        ]);
                    }

                    foreach ($bahanAjar->assignments as $assignment) {
                        $hasil = $assignment->hasilAssignments->where('user_id', $siswa->id)->first();

                        $items->push([
                            'nama' => $assignment->judul,
                            'section' => $bahanAjar->judul,
                            'nilai' => $hasil?->nilai,
                            'bobot' => $assignment->persen_nilai_akhir,
                        ]);
                    }
                }

                $itemDenganNilai = $items->filter(fn ($i) => $i['nilai'] !== null && $i['bobot']);
                $totalBobot = $itemDenganNilai->sum('bobot');
                $hasilAkhir = $totalBobot > 0
                    ? round($itemDenganNilai->sum(fn ($i) => $i['nilai'] * $i['bobot']) / $totalBobot, 1)
                    : null;
            }
        }

        return view('siswa.nilai.index', compact(
            'kelas', 'mataPelajaranKelasList', 'activeMpk', 'items', 'hasilAkhir'
        ));
    }
}
