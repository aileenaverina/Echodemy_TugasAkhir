<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;

class GuruLogController extends Controller
{
    public function index(Request $request)
    {
        $guru = auth()->user();
        $search = $request->input('search');

        // Kelas di mana guru ini jadi wali kelas
        $kelasWaliIds = $guru->kelas()->pluck('kelass.id');

        // Kelas di mana guru ini jadi pengajar mata pelajaran
        $kelasPengajarIds = $guru->mataPelajaranKelasDiajar()
            ->with('kelas')
            ->get()
            ->pluck('kelas.id')
            ->filter();

        $kelasIds = $kelasWaliIds->merge($kelasPengajarIds)->unique();

        // Siswa dari kelas-kelas itu
        $siswaIds = \App\Models\User::where('role', 'siswa')
            ->whereHas('kelas', fn($q) => $q->whereIn('kelass.id', $kelasIds))
            ->pluck('id');

        $query = Log::with('user')
            ->whereIn('user_id', $siswaIds)
            ->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('deskripsi', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($q2) => $q2->where('nama_lengkap', 'like', "%{$search}%"));
            });
        }

        $logs = $query->paginate(15)->withQueryString();

        return view('guru.log.index', compact('logs', 'search'));
    }
}
