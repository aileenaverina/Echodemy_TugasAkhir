<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\BahanAjar;
use App\Models\DetailLatihan;
use App\Models\Materi;
use App\Models\Pilihan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class GuruSectionController extends Controller
{
    public function create(BahanAjar $bahanAjar): View
    {
        $this->authorizeSection($bahanAjar);

        return view('guru.section.create', compact('bahanAjar'));
    }

    protected function notifySiswa(BahanAjar $bahanAjar, string $nama, ?\Carbon\Carbon $batasWaktu = null): void
    {
        $kelas = $bahanAjar->mataPelajaranKelas->kelas;
        $mataPelajaran = $bahanAjar->mataPelajaranKelas->mataPelajaran;

        $deskripsi = "Mata Pelajaran {$mataPelajaran->nama}";

        if ($batasWaktu) {
            $hari = (int) now()->diffInDays($batasWaktu, false);
            $deskripsi .= $hari > 0 ? ", Deadline {$hari} hari lagi" : ", Deadline hari ini";
        }

        $siswaIds = $kelas->siswa()->pluck('users.id');

        $rows = $siswaIds->map(fn($id) => [
            'judul' => strtoupper($nama) . ' DI UPLOAD',
            'deskripsi' => $deskripsi,
            'is_read' => false,
            'user_id' => $id,
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        if ($rows) {
            \App\Models\Notif::insert($rows);
        }
    }

    public function storeMateri(Request $request, BahanAjar $bahanAjar): RedirectResponse
    {
        $this->authorizeSection($bahanAjar);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'tipe' => ['required', 'in:teks,url,file'],
            'konten' => ['required_if:tipe,teks', 'nullable', 'string'],
            'url' => ['required_if:tipe,url', 'nullable', 'url', 'max:500'],
            'file' => ['required_if:tipe,file', 'nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx', 'max:20480'],
        ]);

        $filePath = $request->hasFile('file') ? $request->file('file')->store('materi-files', 'public') : null;

        Materi::create([
            'nama' => $validated['nama'],
            'tipe' => $validated['tipe'],
            'konten' => $validated['tipe'] === 'teks' ? $validated['konten'] : null,
            'url' => $validated['tipe'] === 'url' ? $validated['url'] : null,
            'file' => $filePath,
            'is_lock' => false,
            'bahan_ajar_id' => $bahanAjar->id,
        ]);

        $this->notifySiswa($bahanAjar, $validated['nama']);

        return redirect()->route('guru.mata-pelajaran-kelas.show', $bahanAjar->mataPelajaranKelas)
            ->with('success', 'Materi berhasil ditambahkan.');
    }

    public function storeAssignment(Request $request, BahanAjar $bahanAjar): RedirectResponse
    {
        $this->authorizeSection($bahanAjar);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'in:harian,tengah_semester,akhir_semester'],
            'tipe_pengumpulan' => ['required', 'in:file,text,keduanya'],
            'file' => ['nullable', 'file', 'max:20480'],
            'batas_waktu' => ['nullable', 'date', Rule::requiredIf(fn() => (int) $request->input('persen_nilai_akhir') > 0)],
            'persen_nilai_akhir' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $filePath = $request->hasFile('file') ? $request->file('file')->store('assignment-files', 'public') : null;

        $bahanAjar->assignments()->create([
            'judul' => $validated['judul'],
            'kategori' => $validated['kategori'],
            'tipe_pengumpulan' => $validated['tipe_pengumpulan'],
            'file' => $filePath,
            'batas_waktu' => $validated['batas_waktu'] ?? null,
            'persen_nilai_akhir' => $validated['persen_nilai_akhir'] ?? null,
            'is_lock' => false,
        ]);

        $this->notifySiswa($bahanAjar, $validated['judul'], $validated['batas_waktu'] ? \Carbon\Carbon::parse($validated['batas_waktu']) : null);

        return redirect()->route('guru.mata-pelajaran-kelas.show', $bahanAjar->mataPelajaranKelas)
            ->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function storeLatihan(Request $request, BahanAjar $bahanAjar): RedirectResponse
    {
        $this->authorizeSection($bahanAjar);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'in:harian,tengah_semester,akhir_semester'],
            'penilaian_percobaan' => ['required', 'in:tertinggi,terakhir'],
            'batas_waktu' => ['nullable', 'date', Rule::requiredIf(fn() => (int) $request->input('persen_nilai_akhir') > 0)],
            'maksimal_percobaan' => ['required', 'integer', 'min:1'],
            'waktu_kerja' => ['required', 'integer', 'min:1'],
            'persen_nilai_akhir' => ['nullable', 'integer', 'min:0', 'max:100'],
            'is_random' => ['nullable', 'boolean'],

            'soal' => ['required', 'array', 'min:1'],
            'soal.*.pertanyaan' => ['required', 'string'],
            'soal.*.tipe_soal' => ['required', 'in:pilihan_ganda,isian,esai'],
            'soal.*.jawaban' => ['required_if:soal.*.tipe_soal,isian', 'nullable', 'array'],
            'soal.*.jawaban.*' => ['nullable', 'string'],
            'soal.*.nilai' => ['required', 'numeric', 'min:0'],
            'soal.*.is_random' => ['nullable', 'boolean'],
            'soal.*.pilihan' => ['required_if:soal.*.tipe_soal,pilihan_ganda', 'array', 'min:2'],
            'soal.*.pilihan.*.teks' => ['required', 'string'],
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
                $benar = collect($soal['pilihan'])->pluck('persentase')
                    ->map(fn($p) => (int) $p)->filter(fn($p) => $p > 0)->sum();

                if ($benar !== 100) {
                    throw ValidationException::withMessages([
                        "soal.$i.pilihan" => 'Soal ' . ($i + 1) . ': total persentase jawaban benar harus 100% (sekarang ' . $benar . '%).',
                    ]);
                }
            }
        }

        DB::transaction(function () use ($validated, $bahanAjar, $request) {
            $latihan = $bahanAjar->latihans()->create([
                'judul' => $validated['judul'],
                'kategori' => $validated['kategori'],
                'penilaian_percobaan' => $validated['penilaian_percobaan'],
                'batas_waktu' => $validated['batas_waktu'] ?? null,
                'maksimal_percobaan' => $validated['maksimal_percobaan'],
                'waktu_kerja' => $validated['waktu_kerja'],
                'persen_nilai_akhir' => $validated['persen_nilai_akhir'] ?? null,
                'is_random' => $request->boolean('is_random'),
                'is_lock' => false,
            ]);

            foreach ($validated['soal'] as $index => $soal) {
                $detail = DetailLatihan::create([
                    'nomor' => $index + 1,
                    'pertanyaan' => $soal['pertanyaan'],
                    'tipe_soal' => $soal['tipe_soal'],
                    'jawaban' => $soal['tipe_soal'] === 'isian' ? ($soal['jawaban'] ?? null) : null,
                    'is_random' => $request->boolean("soal.$index.is_random"),
                    'nilai' => $soal['nilai'],
                    'latihan_id' => $latihan->id,
                ]);

                if ($soal['tipe_soal'] === 'pilihan_ganda' && ! empty($soal['pilihan'])) {
                    $abjad = ['A', 'B', 'C', 'D', 'E', 'F'];
                    foreach ($soal['pilihan'] as $i => $pilihan) {
                        Pilihan::create([
                            'nomor_abjad' => $abjad[$i] ?? (string) $i,
                            'pilihan_jawaban' => $pilihan['teks'],
                            'persentase_correct' => (int) $pilihan['persentase'],
                            'detail_latihan_id' => $detail->id,
                        ]);
                    }
                }
            }
        });

        $this->notifySiswa($bahanAjar, $validated['judul'], $validated['batas_waktu'] ? \Carbon\Carbon::parse($validated['batas_waktu']) : null);

        return redirect()->route('guru.mata-pelajaran-kelas.show', $bahanAjar->mataPelajaranKelas)
            ->with('success', 'Latihan berhasil ditambahkan.');
    }

    protected function authorizeSection(BahanAjar $bahanAjar): void
    {
        abort_unless($bahanAjar->mataPelajaranKelas->gurus->pluck('id')->contains(auth()->id()), 403);
    }
}
