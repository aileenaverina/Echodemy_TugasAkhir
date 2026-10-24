<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Latihan;
use App\Models\MataPelajaranKelas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class GuruBobotController extends Controller
{
    public function index(Request $request, MataPelajaranKelas $mataPelajaranKelas): View
    {
        $bisaEdit = $this->isPengajar($mataPelajaranKelas);
        abort_unless($bisaEdit || $this->isWali($mataPelajaranKelas), 403);

        $semester = (int) $request->input('semester', 1);
        $mataPelajaranKelas->load(['kelas', 'mataPelajaran']);

        $bahanAjars = $mataPelajaranKelas->bahanAjars()
            ->where('semester', $semester)
            ->with(['latihans', 'assignments'])
            ->orderBy('created_at')
            ->get();

        return view('guru.bobot.index', compact('mataPelajaranKelas', 'bahanAjars', 'semester', 'bisaEdit'));
    }

    public function update(Request $request, MataPelajaranKelas $mataPelajaranKelas): RedirectResponse
    {
        abort_unless($this->isPengajar($mataPelajaranKelas), 403);

        $validated = $request->validate([
            'semester' => ['required', 'in:1,2'],
            'latihan' => ['nullable', 'array'],
            'latihan.*.bobot' => ['required', 'integer', 'min:0', 'max:100'],
            'latihan.*.kategori' => ['required', 'in:harian,tengah_semester,akhir_semester'],
            'latihan.*.penilaian_percobaan' => ['required', 'in:tertinggi,terakhir'],
            'latihan.*.batas_waktu' => ['nullable', 'date'],
            'assignment' => ['nullable', 'array'],
            'assignment.*.bobot' => ['required', 'integer', 'min:0', 'max:100'],
            'assignment.*.kategori' => ['required', 'in:harian,tengah_semester,akhir_semester'],
            'assignment.*.batas_waktu' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($validated, $mataPelajaranKelas) {
            $this->terapkan(Latihan::class, $validated['latihan'] ?? [], $mataPelajaranKelas, 'latihan');
            $this->terapkan(Assignment::class, $validated['assignment'] ?? [], $mataPelajaranKelas, 'assignment');
        });

        return redirect()
            ->route('guru.bobot.index', [$mataPelajaranKelas, 'semester' => $validated['semester']])
            ->with('success', 'Bobot penilaian disimpan.');
    }

    protected function terapkan(string $model, array $rows, MataPelajaranKelas $mpk, string $prefix): void
    {
        if (empty($rows)) {
            return;
        }

        // hanya item milik mapel+kelas ini yang boleh diubah
        $items = $model::whereIn('id', array_keys($rows))
            ->whereHas('bahanAjar', fn($q) => $q->where('mata_pelajaran_kelas_id', $mpk->id))
            ->get()
            ->keyBy('id');

        foreach ($rows as $id => $row) {
            abort_unless($items->has((int) $id), 403);

            if ((int) $row['bobot'] > 0 && empty($row['batas_waktu'])) {
                throw ValidationException::withMessages([
                    "{$prefix}.{$id}.batas_waktu" => 'Batas waktu wajib diisi untuk item yang masuk penilaian.',
                ]);
            }
        }

        foreach ($rows as $id => $row) {
            $data = [
                'persen_nilai_akhir' => (int) $row['bobot'],
                'kategori' => $row['kategori'],
                'batas_waktu' => $row['batas_waktu'] ?: null,
            ];

            if (isset($row['penilaian_percobaan'])) {
                $data['penilaian_percobaan'] = $row['penilaian_percobaan'];
            }

            $items[(int) $id]->update($data);
        }
    }

    protected function isPengajar(MataPelajaranKelas $mpk): bool
    {
        return $mpk->gurus->pluck('id')->contains(auth()->id());
    }

    protected function isWali(MataPelajaranKelas $mpk): bool
    {
        return $mpk->kelas->waliKelas->pluck('id')->contains(auth()->id());
    }
}
