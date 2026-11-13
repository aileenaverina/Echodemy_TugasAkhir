<?php

namespace App\Http\Controllers\Sekolah;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;

class SekolahLogController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'semua');
        $search = $request->input('search');

        $sekolahId = auth()->user()->sekolah_id;

        $query = Log::with('user')
            ->whereHas('user', fn($q) => $q->where('sekolah_id', $sekolahId)->whereIn('role', ['guru', 'siswa']))
            ->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('deskripsi', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('nama_lengkap', 'like', "%{$search}%");
                    });
            });
        }

        if ($filter !== 'semua') {
            $query->whereHas('user', function ($q) use ($filter) {
                $q->where('role', $filter);
            });
        }

        $logs = $query->paginate(15)->withQueryString();

        $counts = [
            // 'admin' => Log::whereHas('user', fn($q) => $q->where('role', 'admin'))->count(),
            // 'sekolah' => Log::whereHas('user', fn($q) => $q->where('role', 'sekolah'))->count(),
            'semua' => Log::whereHas('user', fn($q) => $q->where('sekolah_id', $sekolahId)->whereIn('role', ['guru', 'siswa']))->count(),
            'guru'  => Log::whereHas('user', fn($q) => $q->where('sekolah_id', $sekolahId)->where('role', 'guru'))->count(),
            'siswa' => Log::whereHas('user', fn($q) => $q->where('sekolah_id', $sekolahId)->where('role', 'siswa'))->count(),
        ];

        return view('sekolah.log.index', compact('logs', 'counts', 'filter', 'search'));
    }
}
