<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Materi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiswaMateriController extends Controller
{
    public function show(Materi $materi): View
    {
        $bahanAjar = $materi->bahanAjar;
        $mataPelajaranKelas = $bahanAjar->mataPelajaranKelas;

        $isAnggota = $mataPelajaranKelas->kelas->siswa->pluck('id')->contains(auth()->id());
        abort_unless($isAnggota && ! $materi->is_lock, 403);

        $ext = $materi->file ? strtolower(pathinfo($materi->file, PATHINFO_EXTENSION)) : null;
        $isPdf = $ext === 'pdf';
        $ttsAvailable = $materi->tipe === 'teks' || ($materi->tipe === 'file' && $isPdf);

        return view('siswa.materi.show', compact('materi', 'mataPelajaranKelas', 'ext', 'isPdf', 'ttsAvailable'));
    }
}
