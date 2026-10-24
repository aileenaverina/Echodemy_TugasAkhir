<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Raport;
use App\Services\NilaiService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiswaNilaiController extends Controller
{
    public function index(Request $request, NilaiService $nilai): View
    {
        $siswa = auth()->user();
        $kelas = $siswa->kelas()->where('status', 'aktif')->first();
        $semester = (int) $request->input('semester', 1);

        $mataPelajaranKelasList = $kelas ? $kelas->penugasanMataPelajaran()->with('mataPelajaran')->get() : collect();
        $activeMpk = $mataPelajaranKelasList->firstWhere('id', (int) $request->input('mapel', $mataPelajaranKelasList->first()?->id));
        $rekap = $activeMpk ? $nilai->rekapMapel($activeMpk, $siswa->id, $semester) : null;
        $raports = Raport::terbaruUntuk($siswa->id);

        return view('siswa.nilai.index', compact('kelas', 'mataPelajaranKelasList', 'activeMpk', 'rekap', 'semester', 'raports'));
    }
}
