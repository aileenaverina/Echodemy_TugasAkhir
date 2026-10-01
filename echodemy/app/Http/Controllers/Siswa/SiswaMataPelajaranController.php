<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaranKelas;
use App\Models\Materi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SiswaMataPelajaranController extends Controller
{
    public function show(MataPelajaranKelas $mataPelajaranKelas): View
    {
        $siswa = auth()->user();
        $this->authorizeSiswa($mataPelajaranKelas);

        $mataPelajaranKelas->load(['kelas', 'mataPelajaran', 'gurus']);

        $jumlahSiswa = $mataPelajaranKelas->kelas->siswa()->count();

        $bahanAjars = $mataPelajaranKelas->bahanAjars()
            ->where('is_lock', false)
            ->with(['materis' => fn ($q) => $q->where('is_lock', false)])
            ->with(['assignments' => fn ($q) => $q->where('is_lock', false)])
            ->with(['latihans' => fn ($q) => $q->where('is_lock', false)])
            ->orderBy('created_at')
            ->get();

        // Ambil progres materi siswa ini sekaligus, biar gak query berulang
        $materiIds = $bahanAjars->flatMap->materis->pluck('id');
        $progresSelesai = DB::table('progres_materis')
            ->where('user_id', $siswa->id)
            ->whereIn('materi_id', $materiIds)
            ->where('is_done', true)
            ->pluck('materi_id')
            ->toArray();

        return view('siswa.mata-pelajaran-kelas.show', compact(
            'mataPelajaranKelas', 'bahanAjars', 'jumlahSiswa', 'progresSelesai'
        ));
    }

    public function toggleProgress(Materi $materi): JsonResponse
    {
        $siswa = auth()->user();

        $existing = DB::table('progres_materis')
            ->where('user_id', $siswa->id)
            ->where('materi_id', $materi->id)
            ->first();

        if ($existing) {
            $isDone = ! $existing->is_done;
            DB::table('progres_materis')
                ->where('user_id', $siswa->id)
                ->where('materi_id', $materi->id)
                ->update(['is_done' => $isDone]);
        } else {
            $isDone = true;
            DB::table('progres_materis')->insert([
                'user_id' => $siswa->id,
                'materi_id' => $materi->id,
                'is_done' => true,
            ]);
        }

        return response()->json(['is_done' => $isDone]);
    }

    protected function authorizeSiswa(MataPelajaranKelas $mataPelajaranKelas): void
    {
        $siswa = auth()->user();
        $isAnggota = $mataPelajaranKelas->kelas->siswa->pluck('id')->contains($siswa->id);
        abort_unless($isAnggota, 403);
    }
}
