<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Log;
use App\Models\MataPelajaran;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return match (auth()->user()->role?->value) {
            'admin' => $this->admin(),
            'sekolah' => $this->sekolah(),
            default => view('dashboard'), // placeholder role lain, dibuat menyusul
        };
    }

    protected function admin(): View
    {
        $stats = [
            'sekolah_terverifikasi' => Sekolah::where('status', Sekolah::STATUS_VERIFIED)->count(),
            'menunggu_verifikasi' => Sekolah::where('status', Sekolah::STATUS_PENDING)->count(),
            'mata_pelajaran' => MataPelajaran::count(),
            'total_pengguna' => User::count(),
        ];

        $permintaanRegistrasi = Sekolah::latest()->take(4)->get();

        $logAktivitas = Log::with('user')->latest()->take(4)->get();

        return view('admin.dashboard', compact('stats', 'permintaanRegistrasi', 'logAktivitas'));
    }

    protected function sekolah(): View
    {
        $sekolahId = auth()->user()->sekolah_id;

        $stats = [
            'kelas_aktif' => Kelas::where('sekolah_id', $sekolahId)->where('status', 'aktif')->count(),
            'guru_terdaftar' => User::where('sekolah_id', $sekolahId)->where('role', 'guru')->count(),
            'siswa_aktif' => User::where('sekolah_id', $sekolahId)->where('role', 'siswa')->count(),
            'mata_pelajaran' => MataPelajaran::whereHas('penugasanKelas.kelas', function ($q) use ($sekolahId) {
                $q->where('sekolah_id', $sekolahId);
            })->distinct()->count(),
        ];

        $daftarKelas = Kelas::where('sekolah_id', $sekolahId)
            ->with('waliKelas')
            ->withCount('users as jumlah_siswa')
            ->latest()
            ->take(4)
            ->get();

        $logAktivitas = Log::where('sekolah_id', $sekolahId)
            ->whereHas('user', function ($q) {
                $q->whereIn('role', ['guru', 'siswa']);
            })
            ->with('user')
            ->latest()
            ->take(4)
            ->get();

        return view('sekolah.dashboard', compact('stats', 'daftarKelas', 'logAktivitas'));
    }
}
