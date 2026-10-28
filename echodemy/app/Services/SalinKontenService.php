<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\BahanAjar;
use App\Models\Latihan;
use App\Models\MataPelajaranKelas;
use App\Models\Materi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SalinKontenService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function salinSection(BahanAjar $sumber, MataPelajaranKelas $target): BahanAjar
    {
        $baru = $sumber->replicate();
        $baru->mata_pelajaran_kelas_id = $target->id;
        $baru->user_id = auth()->id();
        $baru->is_lock = true;
        $baru->save();

        foreach ($sumber->materis as $m) {
            $this->salinMateri($m, $baru);
        }
        foreach ($sumber->assignments as $a) {
            $this->salinAssignment($a, $baru);
        }
        foreach ($sumber->latihans as $l) {
            $this->salinLatihan($l, $baru);
        }

        return $baru;
    }

    public function salinItem(Materi|Assignment|Latihan $item, BahanAjar $target): Materi|Assignment|Latihan
    {
        return match (true) {
            $item instanceof Materi => $this->salinMateri($item, $target),
            $item instanceof Assignment => $this->salinAssignment($item, $target),
            default => $this->salinLatihan($item, $target),
        };
    }

    public function salinMateri(Materi $m, BahanAjar $target): Materi
    {
        $baru = $m->replicate();
        $baru->bahan_ajar_id = $target->id;
        $baru->file = $this->salinFile($m->file, 'materi-files');
        $baru->is_lock = true;
        $baru->save();

        return $baru;
    }

    public function salinAssignment(Assignment $a, BahanAjar $target): Assignment
    {
        $baru = $a->replicate();
        $baru->bahan_ajar_id = $target->id;
        $baru->file = $this->salinFile($a->file, 'assignment-files');
        $baru->persen_nilai_akhir = 0;
        $baru->batas_waktu = null;
        $baru->is_lock = true;
        $baru->save();
        // hasil_assignments tidak ikut disalin

        return $baru;
    }

    public function salinLatihan(Latihan $l, BahanAjar $target): Latihan
    {
        $baru = $l->replicate();
        $baru->bahan_ajar_id = $target->id;
        $baru->persen_nilai_akhir = 0;
        $baru->batas_waktu = null;
        $baru->is_lock = true;
        $baru->save();

        foreach ($l->detailLatihans()->with('pilihans')->get() as $detail) {
            $d = $detail->replicate();
            $d->latihan_id = $baru->id;
            $d->save();

            foreach ($detail->pilihans as $p) {
                $np = $p->replicate();
                $np->detail_latihan_id = $d->id;
                $np->save();
            }
        }
        // submissions siswa tidak ikut disalin

        return $baru;
    }

    // File disalin secara fisik, jadi mengganti file di satu kelas tidak memengaruhi kelas lain
    protected function salinFile(?string $path, string $dir): ?string
    {
        $disk = Storage::disk('public');

        if (! $path || ! $disk->exists($path)) {
            return null;
        }

        $baru = $dir . '/' . Str::random(40) . '.' . pathinfo($path, PATHINFO_EXTENSION);
        $disk->copy($path, $baru);

        return $baru;
    }
}
