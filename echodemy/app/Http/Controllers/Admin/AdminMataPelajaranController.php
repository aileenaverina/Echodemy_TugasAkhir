<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminMataPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = MataPelajaran::withCount('penugasanKelas');

        if ($search) {
            $query->where('nama', 'like', "%{$search}%");
        }

        $mataPelajarans = $query->orderBy('nama')->paginate(10)->withQueryString();

        return view('admin.mata-pelajaran.index', compact('mataPelajarans', 'search'));
    }

    public function show(MataPelajaran $mataPelajaran)
    {
        return response()->json([
            'id' => $mataPelajaran->id,
            'nama' => $mataPelajaran->nama,
            'warna' => $mataPelajaran->warna,
            'file' => $mataPelajaran->file ? Storage::url($mataPelajaran->file) : null,
            'dipakai_di' => $mataPelajaran->penugasanKelas()->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:mata_pelajarans,nama'],
            'warna' => ['required', 'string', 'max:45'],
            'file' => ['required', 'file', 'mimes:pdf,png,jpg,jpeg', 'max:2048'],
        ]);

        $filePath = $request->file('file')->store('mata-pelajaran-files', 'public');

        MataPelajaran::create([
            'nama' => $request->nama,
            'warna' => $request->warna,
            'file' => $filePath,
        ]);

        return back()->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, MataPelajaran $mataPelajaran): RedirectResponse
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:mata_pelajarans,nama,' . $mataPelajaran->id],
            'warna' => ['required', 'string', 'max:45'],
            'file' => ['nullable', 'file', 'mimes:pdf,png,jpg,jpeg', 'max:2048'],
        ]);

        $data = $request->only(['nama', 'warna']);

        if ($request->hasFile('file')) {
            if ($mataPelajaran->file) {
                Storage::disk('public')->delete($mataPelajaran->file);
            }
            $data['file'] = $request->file('file')->store('mata-pelajaran-files', 'public');
        }

        $mataPelajaran->update($data);

        return back()->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(MataPelajaran $mataPelajaran): RedirectResponse
    {
        $mataPelajaran->delete();

        return back()->with('success', "Mata pelajaran {$mataPelajaran->nama} berhasil dihapus.");
    }
}
