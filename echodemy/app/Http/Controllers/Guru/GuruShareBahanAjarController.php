<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\BahanAjar;
use App\Models\MataPelajaranKelas;
use App\Services\SalinKontenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruShareBahanAjarController extends Controller
{
     // Kelas+mapel lain tempat guru ini jadi pengajar, lengkap dengan section-nya
    public function targets(Request $request)
    {
        $targets = auth()->user()->mataPelajaranKelasDiajar()
            ->with(['kelas', 'mataPelajaran', 'bahanAjars'])
            ->get()
            ->filter(fn ($mpk) => $mpk->kelas && $mpk->mataPelajaran)
            ->reject(fn ($mpk) => $mpk->id == $request->input('exclude'))
            ->map(fn ($mpk) => [
                'id' => $mpk->id,
                'label' => $mpk->kelas->nama . ' - ' . $mpk->mataPelajaran->nama,
                'sections' => $mpk->bahanAjars->map(fn ($b) => [
                    'id' => $b->id,
                    'label' => 'Semester ' . $b->semester . ' · ' . $b->judul,
                ])->values(),
            ])
            ->values();

        return response()->json($targets);
    }

    public function shareSection(Request $request, BahanAjar $bahanAjar, SalinKontenService $salin): RedirectResponse
    {
        abort_unless($bahanAjar->mataPelajaranKelas->gurus->pluck('id')->contains(auth()->id()), 403);

        $validated = $request->validate([
            'target_id' => ['required', 'exists:mata_pelajaran_kelas,id'],
        ]);

        $target = MataPelajaranKelas::with('gurus')->findOrFail($validated['target_id']);
        abort_unless($target->gurus->pluck('id')->contains(auth()->id()), 403);

        DB::transaction(fn () => $salin->salinSection($bahanAjar, $target));

        return back()->with('success', 'Section disalin. Salinannya terkunci dan bobotnya 0: atur di halaman Bobot, lalu buka kuncinya.');
    }
}
