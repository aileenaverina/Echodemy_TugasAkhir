<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\BahanAjar;
use App\Models\Latihan;
use App\Models\Materi;
use App\Services\SalinKontenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruItemController extends Controller
{
    public function toggleLock(string $tipe, int $id): JsonResponse
    {
        $item = $this->cariItem($tipe, $id);
        $item->update(['is_lock' => ! $item->is_lock]);

        return response()->json(['is_lock' => (bool) $item->is_lock]);
    }

    public function destroy(string $tipe, int $id): RedirectResponse
    {
        $item = $this->cariItem($tipe, $id);
        $nama = $tipe === 'materi' ? $item->nama : $item->judul;

        $item->delete(); // soft delete

        return back()->with('success', "{$nama} berhasil dihapus.");
    }

    public function share(Request $request, string $tipe, int $id, SalinKontenService $salin): RedirectResponse
    {
        $item = $this->cariItem($tipe, $id);

        $validated = $request->validate([
            'target_bahan_ajar_id' => ['required', 'exists:bahan_ajars,id'],
        ]);

        $target = BahanAjar::with('mataPelajaranKelas.gurus')->findOrFail($validated['target_bahan_ajar_id']);
        abort_unless($target->mataPelajaranKelas->gurus->pluck('id')->contains(auth()->id()), 403);

        DB::transaction(fn () => $salin->salinItem($item, $target));

        return back()->with('success', 'Berhasil disalin ke section tujuan. Salinannya terkunci dan bobotnya 0.');
    }

    protected function cariItem(string $tipe, int $id): Materi|Assignment|Latihan
    {
        $model = match ($tipe) {
            'materi' => Materi::class,
            'assignment' => Assignment::class,
            'latihan' => Latihan::class,
            default => abort(404),
        };

        $item = $model::with('bahanAjar.mataPelajaranKelas.gurus')->findOrFail($id);

        abort_unless($item->bahanAjar->mataPelajaranKelas->gurus->pluck('id')->contains(auth()->id()), 403);

        return $item;
    }
}
