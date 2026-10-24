<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\UserRole;
use App\Models\Raport;
use Illuminate\Support\Facades\Storage;

class RaportDownloadController extends Controller
{
     public function unduh(Raport $raport)
    {
        abort_unless($this->boleh($raport), 403);
        abort_unless(Storage::disk('local')->exists($raport->file), 404);

        $nama = 'Raport-' . $raport->snapshot['siswa']['kode'] . '-Semester' . $raport->semester . '-v' . $raport->versi . '.pdf';

        return Storage::disk('local')->download($raport->file, $nama);
    }

    protected function boleh(Raport $raport): bool
    {
        $user = auth()->user();
        $kelas = $raport->kelas;

        // siswa dan ortu hanya boleh membuka versi terbaru
        $terbaru = (int) Raport::where('user_id', $raport->user_id)
            ->where('kelas_id', $raport->kelas_id)
            ->where('semester', $raport->semester)
            ->max('versi');

        return match ($user->role) {
            UserRole::Siswa => $user->id === $raport->user_id && $raport->versi === $terbaru,
            UserRole::OrangTua => $user->anak()?->id === $raport->user_id && $raport->versi === $terbaru,
            UserRole::Guru => $kelas->waliKelas->pluck('id')->contains($user->id),
            UserRole::Sekolah => $user->sekolah_id === $kelas->sekolah_id,
            default => false,
        };
    }
}
