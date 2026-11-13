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
use Illuminate\Validation\Rule;

class SekolahKelasController extends Controller
{
    public function index(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;
        $search = $request->input('search');
        $filter = $request->input('filter', 'aktif');

        $query = Kelas::where('sekolah_id', $sekolahId)
            ->with(['waliKelas', 'penugasanMataPelajaran.mataPelajaran'])
            ->withCount(['users as jumlah_siswa' => fn($q) => $q->where('role', 'siswa')])
            ->where('status', $filter);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhereHas('waliKelas', fn($q2) => $q2->where('nama_lengkap', 'like', "%{$search}%"));
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

        DB::transaction(function () use ($validated) {
            $kelas = Kelas::create([
                'nama' => $validated['nama'],
                'sekolah_id' => auth()->user()->sekolah_id,
                'tahun_ajaran' => $validated['tahun_ajaran'],
                'status' => 'aktif',
            ]);

            $kelas->users()->attach($validated['wali_kelas_id']);
            $this->syncMataPelajaran($kelas, $validated['mata_pelajaran_ids'] ?? []);
        });

        return back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function update(Request $request, Kelas $kelas): RedirectResponse
    {
        $this->authorizeKelas($kelas);

        $validated = $this->validateRequest($request);

        DB::transaction(function () use ($kelas, $validated) {
            $kelas->update([
                'nama' => $validated['nama'],
                'tahun_ajaran' => $validated['tahun_ajaran'],
            ]);

            DB::table('anggota_kelas')
                ->where('kelas_id', $kelas->id)
                ->whereIn('user_id', User::withTrashed()
                    ->where('sekolah_id', $kelas->sekolah_id)
                    ->where('role', 'guru')
                    ->pluck('id'))
                ->delete();

            $kelas->users()->attach($validated['wali_kelas_id']);

            $this->syncMataPelajaran($kelas, $validated['mata_pelajaran_ids'] ?? []);
        });

        return back()->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas): RedirectResponse
    {
        $this->authorizeKelas($kelas);

        if ($kelas->users()->where('role', 'siswa')->exists()) {
            return back()->with('error', 'Kelas masih memiliki siswa. Pindahkan atau luluskan dulu.');
        }
        $kelas->delete();
        return back()->with('success', "Kelas {$kelas->nama} berhasil dihapus.");
    }

    protected function validateRequest(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'tahun_ajaran' => ['required', 'string', 'max:9'],
            'wali_kelas_id' => ['required', Rule::exists('users', 'id')
                ->where('sekolah_id', auth()->user()->sekolah_id)
                ->where('role', 'guru')
                ->whereNull('deleted_at')],
            'mata_pelajaran_ids' => ['nullable', 'array'],
            'mata_pelajaran_ids.*' => ['exists:mata_pelajarans,id'],
        ]);
    }

    protected function syncMataPelajaran(Kelas $kelas, array $mataPelajaranIds): void
    {
        $ids = array_map('intval', $mataPelajaranIds);

        $ada = MataPelajaranKelas::where('kelas_id', $kelas->id)
            ->pluck('mata_pelajaran_id')->all();

        MataPelajaranKelas::where('kelas_id', $kelas->id)
            ->whereNotIn('mata_pelajaran_id', $ids)
            ->delete();

        foreach (array_diff($ids, $ada) as $id) {
            MataPelajaranKelas::create([
                'kelas_id' => $kelas->id,
                'mata_pelajaran_id' => $id,
            ]);
        }
    }

    public function selesai(Kelas $kelas): RedirectResponse
    {
        $this->authorizeKelas($kelas);

        if ($kelas->status === 'selesai') {
            return back()->with('error', 'Kelas ini sudah selesai.');
        }

        $masihAktif = $kelas->siswa()
            ->whereHas('detailUser', fn($q) => $q->where('status', 'aktif'))
            ->count();

        if ($masihAktif > 0) {
            return back()->with('error', "Masih ada {$masihAktif} siswa aktif di kelas ini. Pindahkan ke kelas baru atau ubah statusnya (lulus/keluar) dulu.");
        }

        $kelas->update(['status' => 'selesai']);

        return back()->with('success', "Kelas {$kelas->nama} ditandai selesai.");
    }

    protected function authorizeKelas(Kelas $kelas): void
    {
        abort_unless($kelas->sekolah_id === auth()->user()->sekolah_id, 403);
    }
}
