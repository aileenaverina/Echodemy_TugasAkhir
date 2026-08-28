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

        //$query = Log::with('user')->latest();
        $query = Log::with('user')
            ->whereHas('user', function ($q) {
                $q->whereIn('role', ['guru', 'siswa']);
            })
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
             'semua' => Log::whereHas('user', fn($q) => $q->whereIn('role', ['guru', 'siswa']))->count(),
            // 'admin' => Log::whereHas('user', fn($q) => $q->where('role', 'admin'))->count(),
            // 'sekolah' => Log::whereHas('user', fn($q) => $q->where('role', 'sekolah'))->count(),
            'guru' => Log::whereHas('user', fn($q) => $q->where('role', 'guru'))->count(),
            'siswa' => Log::whereHas('user', fn($q) => $q->where('role', 'siswa'))->count(),
        ];

        return view('sekolah.log.index', compact('logs', 'counts', 'filter', 'search'));
    }
}
