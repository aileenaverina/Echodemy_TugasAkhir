<?php

namespace App\Http\Controllers\Ortu;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\MataPelajaranKelas;
use App\Models\Raport;
use App\Models\User;
use App\Services\NilaiService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrtuNilaiController extends Controller
{
    public function index(): View
    {
        $anak = auth()->user()->anak();
        $kelas = $anak?->kelas()->where('status', 'aktif')->first();

        $mataPelajaranKelasList = $kelas ? $kelas->penugasanMataPelajaran()->with('mataPelajaran')->get() : collect();
        $raports = $anak ? Raport::terbaruUntuk($anak->id) : collect();

        return view('ortu.nilai.index', compact('anak', 'mataPelajaranKelasList', 'raports'));
    }

    public function show(Request $request, MataPelajaranKelas $mataPelajaranKelas, NilaiService $nilai): View
    {
        $anak = auth()->user()->anak();
        abort_unless($anak, 404);
        abort_unless($mataPelajaranKelas->kelas->siswa->pluck('id')->contains($anak->id), 403);

        $mataPelajaranKelas->load(['kelas', 'mataPelajaran', 'gurus']);
        $semester = (int) $request->input('semester', 1);
        $rekap = $nilai->rekapMapel($mataPelajaranKelas, $anak->id, $semester);
        $jumlahSiswa = $mataPelajaranKelas->kelas->siswa()->count();

        return view('ortu.nilai.show', compact('mataPelajaranKelas', 'jumlahSiswa', 'rekap', 'semester', 'anak'));
    }
}
