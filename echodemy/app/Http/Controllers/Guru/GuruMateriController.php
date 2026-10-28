<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Materi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GuruMateriController extends Controller
{
    public function edit(Materi $materi): View
    {
        $this->authorizeMateri($materi);

        return view('guru.materi.edit', compact('materi'));
    }

    public function update(Request $request, Materi $materi): RedirectResponse
    {
        $this->authorizeMateri($materi);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'tipe' => ['required', 'in:teks,url,file'],
            'konten' => ['required_if:tipe,teks', 'nullable', 'string'],
            'url' => ['required_if:tipe,url', 'nullable', 'url', 'max:500'],
            'file' => [
                Rule::requiredIf(fn () => $request->input('tipe') === 'file' && ! $materi->file),
                'nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx', 'max:20480',
            ],
        ]);

        $path = $materi->file;

        if ($validated['tipe'] !== 'file') {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
            $path = null;
        } elseif ($request->hasFile('file')) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
            $path = $request->file('file')->store('materi-files', 'public');
        }

        $materi->update([
            'nama' => $validated['nama'],
            'tipe' => $validated['tipe'],
            'konten' => $validated['tipe'] === 'teks' ? $validated['konten'] : null,
            'url' => $validated['tipe'] === 'url' ? $validated['url'] : null,
            'file' => $path,
        ]);

        return redirect()
            ->route('guru.mata-pelajaran-kelas.show', $materi->bahanAjar->mataPelajaranKelas)
            ->with('success', 'Materi berhasil diperbarui.');
    }

    protected function authorizeMateri(Materi $materi): void
    {
        abort_unless($materi->bahanAjar->mataPelajaranKelas->gurus->pluck('id')->contains(auth()->id()), 403);
    }
}
