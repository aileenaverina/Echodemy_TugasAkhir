<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\BahanAjar;
use App\Models\MataPelajaranKelas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruShareBahanAjarController extends Controller
{
    // Ambil daftar mata-pelajaran-kelas lain tempat guru ini jadi pengajar (untuk pilihan tujuan share)
    public function targets(Request $request)
    {
        $excludeId = $request->input('exclude');

        $targets = auth()->user()->mataPelajaranKelasDiajar()
            ->with(['kelas', 'mataPelajaran'])
            ->get()
            ->reject(fn ($mpk) => $mpk->id == $excludeId)
            ->map(fn ($mpk) => [
                'id' => $mpk->id,
                'label' => $mpk->kelas->nama . ' - ' . $mpk->mataPelajaran->nama,
            ])
            ->values();

        return response()->json($targets);
    }

    public function shareSection(Request $request, BahanAjar $bahanAjar): RedirectResponse
    {
        $sourceMpk = $bahanAjar->mataPelajaranKelas;
        abort_unless($sourceMpk->gurus->pluck('id')->contains(auth()->id()), 403);

        $validated = $request->validate([
            'target_id' => ['required', 'exists:mata_pelajaran_kelas,id'],
        ]);

        $targetMpk = MataPelajaranKelas::findOrFail($validated['target_id']);
        abort_unless($targetMpk->gurus->pluck('id')->contains(auth()->id()), 403);

        DB::transaction(function () use ($bahanAjar, $targetMpk) {
            $newBahanAjar = $bahanAjar->replicate();
            $newBahanAjar->mata_pelajaran_kelas_id = $targetMpk->id;
            $newBahanAjar->user_id = auth()->id();
            $newBahanAjar->save();

            foreach ($bahanAjar->materis as $materi) {
                $newMateri = $materi->replicate();
                $newMateri->bahan_ajar_id = $newBahanAjar->id;
                $newMateri->save();
            }

            foreach ($bahanAjar->assignments as $assignment) {
                $newAssignment = $assignment->replicate();
                $newAssignment->bahan_ajar_id = $newBahanAjar->id;
                $newAssignment->save();
                // hasil_assignments TIDAK dicopy — submission siswa tetap terpisah
            }

            foreach ($bahanAjar->latihans as $latihan) {
                $newLatihan = $latihan->replicate();
                $newLatihan->bahan_ajar_id = $newBahanAjar->id;
                $newLatihan->save();

                foreach ($latihan->detailLatihans as $detail) {
                    $newDetail = $detail->replicate();
                    $newDetail->latihan_id = $newLatihan->id;
                    $newDetail->save();

                    foreach ($detail->pilihans as $pilihan) {
                        $newPilihan = $pilihan->replicate();
                        $newPilihan->detail_latihan_id = $newDetail->id;
                        $newPilihan->save();
                    }
                }
                // submissions TIDAK dicopy — submission siswa tetap terpisah
            }
        });

        return back()->with('success', 'Section berhasil di-share.');
    }
}
