<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User;
use App\Models\Raport;
use App\Services\NilaiService;
use App\Services\RaportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use RuntimeException;

class GuruRaporController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->input('filter', 'aktif');

        $kelasList = auth()->user()->kelas()
            ->where('status', $filter)
            ->withCount('siswa as jumlah_siswa')
            ->get();

        return view('guru.rapor.index', compact('kelasList', 'filter'));
    }

    public function kelas(Request $request, Kelas $kelas): View
    {
        $this->authorizeWali($kelas);

        $search = $request->input('search');
        $semester = (int) $request->input('semester', 1);

        $query = $kelas->siswa()->with('detailUser');
        if ($search) {
            $query->where('nama_lengkap', 'like', "%{$search}%");
        }
        $siswaList = $query->orderBy('nama_lengkap')->get();

        // raport versi terbaru per siswa untuk semester ini
        $raports = Raport::where('kelas_id', $kelas->id)
            ->where('semester', $semester)
            ->whereIn('user_id', $siswaList->pluck('id'))
            ->orderByDesc('versi')
            ->get()
            ->groupBy('user_id')
            ->map->first();

        return view('guru.rapor.kelas', compact('kelas', 'siswaList', 'search', 'semester', 'raports'));
    }

    public function siswa(Request $request, Kelas $kelas, User $siswa, NilaiService $nilai): View
    {
        $this->authorizeWali($kelas);
        abort_unless($kelas->siswa->pluck('id')->contains($siswa->id), 404);

        $semester = (int) $request->input('semester', 1);
        $mataPelajaranKelasList = $kelas->penugasanMataPelajaran()->with('mataPelajaran')->get();
        $activeMpk = $mataPelajaranKelasList->firstWhere('id', (int) $request->input('mapel', $mataPelajaranKelasList->first()?->id));
        $rekap = $activeMpk ? $nilai->rekapMapel($activeMpk, $siswa->id, $semester) : null;

        return view('guru.rapor.siswa', compact('kelas', 'siswa', 'mataPelajaranKelasList', 'activeMpk', 'rekap', 'semester'));
    }

    public function cek(Request $request, Kelas $kelas, User $siswa, RaportService $svc): JsonResponse
    {
        $this->authorizeWali($kelas);
        abort_unless($kelas->siswa->pluck('id')->contains($siswa->id), 404);

        $semester = (int) $request->input('semester', 1);
        $cek = $svc->cek($kelas, $siswa, $semester);

        $versiTerakhir = Raport::where('user_id', $siswa->id)
            ->where('kelas_id', $kelas->id)
            ->where('semester', $semester)
            ->max('versi');

        return response()->json([
            'lengkap' => $cek['lengkap'],
            'alasan' => $cek['alasan'],
            'mapel' => $cek['mapel']->map(fn($m) => [
                'nama' => $m['nama'],
                'lengkap' => $m['rekap']['lengkap'],
                'hasil_akhir' => $m['rekap']['hasil_akhir'],
            ])->values(),
            'versi_terakhir' => $versiTerakhir ? (int) $versiTerakhir : null,
        ]);
    }


    protected function authorizeWali(Kelas $kelas): void
    {
        abort_unless($kelas->waliKelas->pluck('id')->contains(auth()->id()), 403);
    }

    public function terbitkan(Request $request, Kelas $kelas, User $siswa, RaportService $svc): RedirectResponse
    {
        $this->authorizeWali($kelas);
        abort_unless($kelas->siswa->pluck('id')->contains($siswa->id), 404);

        $semester = (int) $request->validate(['semester' => ['required', 'in:1,2']])['semester'];

        try {
            $raport = $svc->terbitkan($kelas, $siswa, $semester, auth()->user());
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Raport {$siswa->nama_lengkap} semester {$semester} (versi {$raport->versi}) berhasil diterbitkan.");
    }

    public function terbitkanSemua(Request $request, Kelas $kelas, RaportService $svc): RedirectResponse
    {
        $this->authorizeWali($kelas);
        set_time_limit(300);

        $semester = (int) $request->validate(['semester' => ['required', 'in:1,2']])['semester'];

        $sudah = Raport::where('kelas_id', $kelas->id)->where('semester', $semester)->pluck('user_id')->unique();

        $berhasil = 0;
        $dilewati = [];

        foreach ($kelas->siswa()->whereNotIn('users.id', $sudah)->orderBy('nama_lengkap')->get() as $siswa) {
            try {
                $svc->terbitkan($kelas, $siswa, $semester, auth()->user());
                $berhasil++;
            } catch (RuntimeException) {
                $dilewati[] = $siswa->nama_lengkap;
            }
        }

        $pesan = "{$berhasil} raport diterbitkan.";
        if ($dilewati) {
            $pesan .= ' Dilewati karena nilai belum lengkap: ' . implode(', ', $dilewati) . '.';
        }

        return back()->with($berhasil ? 'success' : 'error', $pesan);
    }
}
