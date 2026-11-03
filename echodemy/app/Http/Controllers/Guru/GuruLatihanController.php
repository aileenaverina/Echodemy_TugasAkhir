<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\DetailLatihan;
use App\Models\Latihan;
use App\Models\Pilihan;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class GuruLatihanController extends Controller
{
    public function show(Latihan $latihan): View
    {
        $this->authorizeLatihan($latihan);

        $kelas = $latihan->bahanAjar->mataPelajaranKelas->kelas;
        $totalSiswa = $kelas->siswa()->count();

        $submissions = Submission::where('latihan_id', $latihan->id)
            ->with('siswa')
            ->get()
            ->groupBy('user_id')
            ->map(function ($group) {
                return [
                    'siswa' => $group->first()->siswa,
                    'percobaan' => $group->count(),
                    'terakhir' => $group->sortByDesc('created_at')->first(),
                ];
            })
            ->values();

        $nilaiMaksimal = $latihan->detailLatihans->sum('nilai');

        $adaYangMulai = $latihan->submissions()
            ->whereIn('status', ['draft', 'dikumpulkan', 'telat', 'dinilai'])
            ->exists();

        return view('guru.latihan.show', compact('latihan', 'submissions', 'totalSiswa', 'nilaiMaksimal', 'adaYangMulai'));
    }

    public function soal(Latihan $latihan): View
    {
        $this->authorizeLatihan($latihan);

        $latihan->load('detailLatihans.pilihans');

        return view('guru.latihan.soal', compact('latihan'));
    }

    public function updateNilai(Request $request, Latihan $latihan, Submission $submission): RedirectResponse
    {
        $this->authorizeLatihan($latihan);
        abort_unless($submission->latihan_id === $latihan->id, 404);

        $validated = $request->validate([
            'nilai' => ['required', 'numeric', 'min:0'],
        ]);

        // Simpan sebagai penyesuaian nilai manual di jawaban pertama (ringkas) — 
        // idealnya nilai per-soal, tapi untuk override manual guru cukup di satu tempat
        $submission->answers()->update(['nilai' => 0]);
        $firstAnswer = $submission->answers()->first();
        if ($firstAnswer) {
            $firstAnswer->update(['nilai' => $validated['nilai']]);
        }

        $submission->update(['status' => 'dinilai']);

        return back()->with('success', 'Nilai berhasil diperbarui.');
    }

    public function updatePengaturan(Request $request, Latihan $latihan): RedirectResponse
    {
        $this->authorizeLatihan($latihan);

        $validated = $request->validate([
            'judul'                => ['required', 'string', 'max:255'],
            'kategori'             => ['required', 'in:harian,tengah_semester,akhir_semester'],
            'penilaian_percobaan'  => ['required', 'in:tertinggi,terakhir'],
            'batas_waktu'          => ['nullable', 'date'],
            'maksimal_percobaan'   => ['required', 'integer', 'min:1'],
            'waktu_kerja'          => ['required', 'integer', 'min:1'],
            'persen_nilai_akhir'   => ['nullable', 'integer', 'min:0', 'max:100'],
            'is_random'            => ['nullable', 'boolean'],
        ]);

        if ((int) ($validated['persen_nilai_akhir'] ?? 0) > 0 && empty($validated['batas_waktu'])) {
            throw ValidationException::withMessages([
                'batas_waktu' => 'Batas waktu wajib diisi untuk latihan yang masuk penilaian.',
            ]);
        }

        $latihan->update([
            'judul'               => $validated['judul'],
            'kategori'            => $validated['kategori'],
            'penilaian_percobaan' => $validated['penilaian_percobaan'],
            'batas_waktu'         => $validated['batas_waktu'] ?? null,
            'maksimal_percobaan'  => $validated['maksimal_percobaan'],
            'waktu_kerja'         => $validated['waktu_kerja'],
            'persen_nilai_akhir'  => $validated['persen_nilai_akhir'] ?? 0,
            'is_random'           => $request->boolean('is_random'),
        ]);

        return back()->with('success', 'Pengaturan latihan berhasil diperbarui.');
    }

    public function editSoal(Latihan $latihan): View
    {
        $this->authorizeLatihan($latihan);

        $adaYangMulai = $latihan->submissions()
            ->whereIn('status', ['draft', 'dikumpulkan', 'telat', 'dinilai'])
            ->exists();

        abort_if($adaYangMulai, 403, 'Soal tidak bisa diedit karena ada siswa yang sudah mengerjakan.');

        $latihan->load('detailLatihans.pilihans');

        return view('guru.latihan.edit-soal', compact('latihan'));
    }

    public function updateSoal(Request $request, Latihan $latihan): RedirectResponse
    {
        $this->authorizeLatihan($latihan);

        abort_if(
            $latihan->submissions()->whereIn('status', ['draft', 'dikumpulkan', 'telat', 'dinilai'])->exists(),
            403
        );

        $validated = $request->validate([
            'soal'                      => ['required', 'array', 'min:1'],
            'soal.*.pertanyaan'         => ['required', 'string'],
            'soal.*.tipe_soal'          => ['required', 'in:pilihan_ganda,isian,esai'],
            'soal.*.jawaban'            => ['nullable', 'array'],
            'soal.*.jawaban.*'          => ['nullable', 'string'],
            'soal.*.nilai'              => ['required', 'numeric', 'min:0'],
            'soal.*.is_random'          => ['nullable', 'boolean'],
            'soal.*.pilihan'            => ['nullable', 'array'],
            'soal.*.pilihan.*.teks'     => ['required', 'string'],
            'soal.*.pilihan.*.persentase' => ['required', 'integer', 'min:-100', 'max:100'],
        ]);

        $totalPoin = round(collect($validated['soal'])->sum(fn($s) => (float) $s['nilai']), 2);
        if (abs($totalPoin - 100) > 0.001) {
            throw ValidationException::withMessages([
                'soal' => "Total poin semua soal harus 100 (sekarang {$totalPoin}).",
            ]);
        }

        foreach ($validated['soal'] as $i => $soal) {
            if ($soal['tipe_soal'] === 'pilihan_ganda') {
                $benar = collect($soal['pilihan'] ?? [])->pluck('persentase')
                    ->map(fn($p) => (int) $p)->filter(fn($p) => $p > 0)->sum();
                if ($benar !== 100) {
                    throw ValidationException::withMessages([
                        "soal.$i.pilihan" => 'Soal ' . ($i + 1) . ': total persentase jawaban benar harus 100% (sekarang ' . $benar . '%).',
                    ]);
                }
            }
        }

        DB::transaction(function () use ($validated, $latihan, $request) {
            $latihan->detailLatihans->each(fn($d) => $d->pilihans()->delete());
            $latihan->detailLatihans()->delete();

            $abjad = ['A', 'B', 'C', 'D', 'E', 'F'];
            foreach ($validated['soal'] as $index => $soal) {
                $detail = DetailLatihan::create([
                    'nomor'       => $index + 1,
                    'pertanyaan'  => $soal['pertanyaan'],
                    'tipe_soal'   => $soal['tipe_soal'],
                    'jawaban'     => $soal['tipe_soal'] === 'isian' ? ($soal['jawaban'] ?? null) : null,
                    'is_random'   => $request->boolean("soal.$index.is_random"),
                    'nilai'       => $soal['nilai'],
                    'latihan_id'  => $latihan->id,
                ]);

                if ($soal['tipe_soal'] === 'pilihan_ganda' && !empty($soal['pilihan'])) {
                    foreach ($soal['pilihan'] as $i => $pilihan) {
                        Pilihan::create([
                            'nomor_abjad'       => $abjad[$i] ?? (string) $i,
                            'pilihan_jawaban'    => $pilihan['teks'],
                            'persentase_correct' => (int) $pilihan['persentase'],
                            'detail_latihan_id'  => $detail->id,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('guru.latihan.show', $latihan)->with('success', 'Soal berhasil diperbarui.');
    }

    protected function authorizeLatihan(Latihan $latihan): void
    {
        $mpk = $latihan->bahanAjar->mataPelajaranKelas;
        abort_unless($mpk->gurus->pluck('id')->contains(auth()->id()), 403);
    }
}
