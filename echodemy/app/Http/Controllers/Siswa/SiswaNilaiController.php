<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Services\NilaiService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiswaNilaiController extends Controller
{
    public function index(Request $request, NilaiService $nilai): View
    {
        $siswa = auth()->user();
        $kelas = $siswa->kelas()->where('status', 'aktif')->first();

        $mataPelajaranKelasList = collect();
        $activeMpk = null;

        if ($kelas) {
            $mataPelajaranKelasList = $kelas->penugasanMataPelajaran()->with('mataPelajaran')->get();

            $activeMpkId = $request->input('mapel', $mataPelajaranKelasList->first()?->id);
            $activeMpk = $mataPelajaranKelasList->firstWhere('id', (int) $activeMpkId);
        }

        $semester = (int) $request->input('semester', 1);
        $rekap = $activeMpk ? $nilai->rekapMapel($activeMpk, $siswa->id, $semester) : null;

        return view('siswa.nilai.index', compact(
            'kelas',
            'mataPelajaranKelasList',
            'activeMpk',
            'rekap',
            'semester'
        ));
    }
}
