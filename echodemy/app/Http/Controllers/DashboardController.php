<?php

namespace App\Http\Controllers;

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
}
