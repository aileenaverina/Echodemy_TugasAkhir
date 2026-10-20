<?php

namespace App\Http\Controllers\Ortu;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\MataPelajaranKelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrtuNilaiController extends Controller
{
    public function index(): View
    {
        $anak = $this->getAnak();
        $kelas = $anak?->kelas()->where('status', 'aktif')->first();

        $mataPelajaranKelasList = $kelas
            ? $kelas->penugasanMataPelajaran()->with('mataPelajaran')->get()
            : collect();

        return view('ortu.nilai.index', compact('anak', 'mataPelajaranKelasList'));
    }

    public function show(MataPelajaranKelas $mataPelajaranKelas): View
    {
        $anak = $this->getAnak();
        abort_unless($anak, 404);

        $isAnggota = $mataPelajaranKelas->kelas->siswa->pluck('id')->contains($anak->id);
        abort_unless($isAnggota, 403);

        $mataPelajaranKelas->load(['kelas', 'mataPelajaran', 'gurus']);
        $jumlahSiswa = $mataPelajaranKelas->kelas->siswa()->count();

        $bahanAjars = $mataPelajaranKelas->bahanAjars()
            ->with(['latihans.submissions.answers', 'assignments.hasilAssignments'])
            ->get();

        $items = collect();

        foreach ($bahanAjars as $bahanAjar) {
            foreach ($bahanAjar->latihans as $latihan) {
                $submission = $latihan->submissions->where('user_id', $anak->id)->sortByDesc('created_at')->first();

                $items->push([
                    'nama' => $latihan->judul,
                    'section' => $bahanAjar->judul,
                    'nilai' => $submission ? $submission->answers->sum('nilai') : null,
                    'bobot' => $latihan->persen_nilai_akhir,
                ]);
            }

            foreach ($bahanAjar->assignments as $assignment) {
                $hasil = $assignment->hasilAssignments->where('user_id', $anak->id)->first();

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

        return view('ortu.nilai.show', compact('mataPelajaranKelas', 'jumlahSiswa', 'items', 'hasilAkhir', 'anak'));
    }

    protected function getAnak(): ?User
    {
        $ortu = auth()->user();
        $suffix = substr($ortu->kode_user, 1); // buang huruf 'P' di depan

        return User::where('sekolah_id', $ortu->sekolah_id)
            ->where('role', UserRole::Siswa)
            ->where('kode_user', 'S' . $suffix)
            ->first();
    }
}
