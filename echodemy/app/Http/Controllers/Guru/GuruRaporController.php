<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuruRaporController extends Controller
{
      public function index(Request $request): View
    {
        $filter = $request->input('filter', 'aktif');

        $kelasList = auth()->user()->kelas()
            ->where('status', $filter)
            ->withCount('siswa as jumlah_siswa')
            ->get();

        return view('guru.rapor.index', compact('kelasList', 'filter'));
    }

    public function kelas(Request $request, Kelas $kelas): View
    {
        $this->authorizeWali($kelas);

        $search = $request->input('search');

        $query = $kelas->siswa()->with('detailUser');

        if ($search) {
            $query->where('nama_lengkap', 'like', "%{$search}%");
        }

        $siswaList = $query->orderBy('nama_lengkap')->get();

        return view('guru.rapor.kelas', compact('kelas', 'siswaList', 'search'));
    }

    public function siswa(Request $request, Kelas $kelas, User $siswa): View
    {
        $this->authorizeWali($kelas);
        abort_unless($kelas->siswa->pluck('id')->contains($siswa->id), 404);

        $mataPelajaranKelasList = $kelas->penugasanMataPelajaran()->with('mataPelajaran')->get();

        $activeMpkId = $request->input('mapel', $mataPelajaranKelasList->first()?->id);
        $activeMpk = $mataPelajaranKelasList->firstWhere('id', (int) $activeMpkId);

        $items = collect();
        $hasilAkhir = null;

        if ($activeMpk) {
            $bahanAjars = $activeMpk->bahanAjars()->with(['latihans.submissions.answers', 'assignments.hasilAssignments'])->get();

            foreach ($bahanAjars as $bahanAjar) {
                foreach ($bahanAjar->latihans as $latihan) {
                    $submission = $latihan->submissions->where('user_id', $siswa->id)->sortByDesc('created_at')->first();
                    $nilai = $submission ? $submission->answers->sum('nilai') : null;

                    $items->push([
                        'nama' => $latihan->judul,
                        'section' => $bahanAjar->judul,
                        'nilai' => $nilai,
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

        return view('guru.rapor.siswa', compact(
            'kelas', 'siswa', 'mataPelajaranKelasList', 'activeMpk', 'items', 'hasilAkhir'
        ));
    }

    protected function authorizeWali(Kelas $kelas): void
    {
        abort_unless($kelas->waliKelas->pluck('id')->contains(auth()->id()), 403);
    }
}
