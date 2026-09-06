<?php

namespace App\Http\Controllers\Sekolah;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\MataPelajaranKelas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SekolahKelasController extends Controller
{
     public function index(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;
        $search = $request->input('search');
        $filter = $request->input('filter', 'aktif');

        $query = Kelas::where('sekolah_id', $sekolahId)
            ->with(['waliKelas', 'penugasanMataPelajaran.mataPelajaran'])
            ->withCount('users as jumlah_siswa')
            ->where('status', $filter);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhereHas('waliKelas', fn ($q2) => $q2->where('nama_lengkap', 'like', "%{$search}%"));
            });
        }

        $kelas = $query->latest()->paginate(10)->withQueryString();

        $guruList = User::where('sekolah_id', $sekolahId)->where('role', 'guru')->orderBy('nama_lengkap')->get();
        $mataPelajaranList = MataPelajaran::orderBy('nama')->get();

        return view('sekolah.kelas.index', compact('kelas', 'search', 'filter', 'guruList', 'mataPelajaranList'));
    }

    public function show(Kelas $kelas)
    {
        $this->authorizeKelas($kelas);

        return response()->json([
            'id' => $kelas->id,
            'nama' => $kelas->nama,
            'tahun_ajaran' => $kelas->tahun_ajaran,
            'status' => $kelas->status,
            'wali_kelas_id' => $kelas->waliKelas()->first()?->id,
            'mata_pelajaran_ids' => $kelas->penugasanMataPelajaran->pluck('mata_pelajaran_id'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRequest($request);

        $kelas = Kelas::create([
            'nama' => $validated['nama'],
            'sekolah_id' => auth()->user()->sekolah_id,
            'tahun_ajaran' => $validated['tahun_ajaran'],
            'status' => 'aktif',
        ]);

        $kelas->users()->attach($validated['wali_kelas_id']);
        $this->syncMataPelajaran($kelas, $validated['mata_pelajaran_ids'] ?? []);

        return back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function update(Request $request, Kelas $kelas): RedirectResponse
    {
        $this->authorizeKelas($kelas);

        $validated = $this->validateRequest($request);

        $kelas->update([
            'nama' => $validated['nama'],
            'tahun_ajaran' => $validated['tahun_ajaran'],
        ]);

        DB::table('anggota_kelas')
            ->where('kelas_id', $kelas->id)
            ->whereIn('user_id', User::where('role', 'guru')->pluck('id'))
            ->delete();

        $kelas->users()->attach($validated['wali_kelas_id']);

        $this->syncMataPelajaran($kelas, $validated['mata_pelajaran_ids'] ?? []);

        return back()->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas): RedirectResponse
    {
        $this->authorizeKelas($kelas);

        $kelas->delete();

        return back()->with('success', "Kelas {$kelas->nama} berhasil dihapus.");
    }

    protected function validateRequest(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'tahun_ajaran' => ['required', 'string', 'max:9'],
            'wali_kelas_id' => ['required', 'exists:users,id'],
            'mata_pelajaran_ids' => ['nullable', 'array'],
            'mata_pelajaran_ids.*' => ['exists:mata_pelajarans,id'],
        ]);
    }

    protected function syncMataPelajaran(Kelas $kelas, array $mataPelajaranIds): void
    {
        MataPelajaranKelas::where('kelas_id', $kelas->id)->delete();

        foreach ($mataPelajaranIds as $mataPelajaranId) {
            MataPelajaranKelas::create([
                'kelas_id' => $kelas->id,
                'mata_pelajaran_id' => $mataPelajaranId,
            ]);
        }
    }

    protected function authorizeKelas(Kelas $kelas): void
    {
        abort_unless($kelas->sekolah_id === auth()->user()->sekolah_id, 403);
    }
}
