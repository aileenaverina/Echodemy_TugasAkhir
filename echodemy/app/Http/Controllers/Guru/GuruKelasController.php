<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class GuruKelasController extends Controller
{
    public function index(Request $request)
    {
        $guru = auth()->user();
        $filter = $request->input('filter', 'aktif');

        // Kelas di mana guru ini jadi Wali Kelas
        $kelasWali = $guru->kelas()
            ->where('status', $filter)
            ->withCount('users as jumlah_siswa')
            ->get()
            ->map(function ($kelas) {
                return (object) [
                    'kelas' => $kelas,
                    'label' => 'Wali Kelas',
                    'mata_pelajaran' => null,
                ];
            });

        // Kelas di mana guru ini jadi Pengajar mata pelajaran
        $kelasPengajar = $guru->mataPelajaranKelasDiajar()
            ->with(['kelas' => function ($q) use ($filter) {
                $q->where('status', $filter)->withCount('users as jumlah_siswa');
            }, 'mataPelajaran'])
            ->get()
            ->filter(fn($mpk) => $mpk->kelas !== null)
            ->map(function ($mpk) {
                return (object) [
                    'kelas' => $mpk->kelas,
                    'label' => 'Pengajar',
                    'mata_pelajaran' => $mpk->mataPelajaran,
                ];
            });

        $items = $kelasWali->concat($kelasPengajar);

        return view('guru.kelas.index', compact('items', 'filter'));
    }

    public function show(Kelas $kelas)
    {
        $guru = auth()->user();
        $isWali = $kelas->waliKelas->pluck('id')->contains($guru->id);
        $isPengajar = $kelas->penugasanMataPelajaran()->whereHas('gurus', fn($q) => $q->where('users.id', $guru->id))->exists();

        abort_unless($isWali || $isPengajar, 403);

        $waliKelas = $kelas->waliKelas->first();
        $mataPelajaranKelas = $kelas->penugasanMataPelajaran()->with(['mataPelajaran', 'gurus'])->get();
        $jumlahSiswa = $kelas->siswa()->count();

        return view('guru.kelas.show', compact('kelas', 'waliKelas', 'mataPelajaranKelas', 'isWali', 'jumlahSiswa'));
    }
}
