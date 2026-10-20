<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\HasilAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiswaAssignmentController extends Controller
{
    public function show(Assignment $assignment): View
    {
        $this->authorizeAssignment($assignment);

        $hasil = HasilAssignment::where('assignment_id', $assignment->id)
            ->where('user_id', auth()->id())
            ->first();

        return view('siswa.assignment.show', compact('assignment', 'hasil'));
    }

    public function kumpul(Request $request, Assignment $assignment): RedirectResponse
    {
        $this->authorizeAssignment($assignment);

        $hasilLama = HasilAssignment::where('assignment_id', $assignment->id)
            ->where('user_id', auth()->id())
            ->first();

        abort_if($hasilLama?->status === 'dinilai', 403, 'Tugas ini sudah dinilai dan tidak bisa diubah lagi.');

        $butuhFile = in_array($assignment->tipe_pengumpulan, ['file', 'keduanya']);
        $butuhText = in_array($assignment->tipe_pengumpulan, ['text', 'keduanya']);
        $wajibSalahSatu = $assignment->tipe_pengumpulan === 'keduanya';

        $rules = [
            'file' => [$wajibSalahSatu || ! $butuhFile ? 'nullable' : 'required', 'file', 'max:20480'],
            'text' => [$wajibSalahSatu || ! $butuhText ? 'nullable' : 'required', 'string'],
        ];

        if (! $butuhFile) {
            unset($rules['file']);
        }
        if (! $butuhText) {
            unset($rules['text']);
        }

        $validated = $request->validate($rules);

        if ($wajibSalahSatu && ! $request->hasFile('file') && empty($validated['text'] ?? null) && ! $hasilLama?->file && ! $hasilLama?->text) {
            return back()->withErrors(['file' => 'Isi minimal salah satu: file atau teks.'])->withInput();
        }

        $filePath = $hasilLama?->file;
        if ($request->hasFile('file')) {
            if ($filePath) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = $request->file('file')->store('hasil-assignment', 'public');
        }

        $status = $assignment->batas_waktu && now()->greaterThan($assignment->batas_waktu) ? 'telat' : 'dikumpulkan';

        HasilAssignment::updateOrCreate(
            ['assignment_id' => $assignment->id, 'user_id' => auth()->id()],
            [
                'file' => $filePath,
                'text' => $validated['text'] ?? $hasilLama?->text,
                'status' => $status,
            ]
        );

        return redirect()->route('siswa.assignment.show', $assignment)->with('success', 'Tugas berhasil dikumpulkan.');
    }

    protected function authorizeAssignment(Assignment $assignment): void
    {
        $kelas = $assignment->bahanAjar->mataPelajaranKelas->kelas;
        abort_unless($kelas->siswa->pluck('id')->contains(auth()->id()), 403);
        abort_if($assignment->is_lock, 403);
    }
}
