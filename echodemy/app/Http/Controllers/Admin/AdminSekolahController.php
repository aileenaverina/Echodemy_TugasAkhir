<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\Sekolah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminSekolahController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'semua');
        $search = $request->input('search');

        $query = Sekolah::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('npsn', 'like', "%{$search}%");
            });
        }

        match ($filter) {
            'menunggu' => $query->where('status', Sekolah::STATUS_PENDING),
            'terverifikasi' => $query->where('status', Sekolah::STATUS_VERIFIED)->where('is_active', true),
            'nonaktif' => $query->where('is_active', false),
            default => null,
        };

        $sekolahs = $query->latest()->paginate(10)->withQueryString();

        $counts = [
            'semua' => Sekolah::count(),
            'menunggu' => Sekolah::where('status', Sekolah::STATUS_PENDING)->count(),
            'terverifikasi' => Sekolah::where('status', Sekolah::STATUS_VERIFIED)->where('is_active', true)->count(),
            'nonaktif' => Sekolah::where('is_active', false)->count(),
        ];

        return view('admin.sekolah.index', compact('sekolahs', 'counts', 'filter', 'search'));
    }

    public function show(Sekolah $sekolah)
    {
        return response()->json([
            'nama' => $sekolah->nama,
            'npsn' => $sekolah->npsn,
            'jenjang' => $sekolah->jenjang,
            'singkatan' => $sekolah->singkatan,
            'email' => $sekolah->email,
            'nomor_telepon' => $sekolah->nomor_telepon,
            'detail_alamat' => $sekolah->detail_alamat,
            'logo' => $sekolah->logo ? Storage::url($sekolah->logo) : null,
            'kota' => $sekolah->kotaKabupaten(),
            'jumlah_siswa' => $sekolah->users()->where('role', 'siswa')->count(),
            'jumlah_guru' => $sekolah->users()->where('role', 'guru')->count(),
        ]);
    }

    public function update(Request $request, Sekolah $sekolah): RedirectResponse
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:500'],
            'npsn' => ['required', 'string', 'size:8', Rule::unique('sekolahs', 'npsn')->ignore($sekolah->id)],
            'nomor_telepon' => ['required', 'string', 'max:45'],
            'detail_alamat' => ['required', 'string', 'max:500'],
            'email' => ['required', 'email', Rule::unique('sekolahs', 'email')->ignore($sekolah->id)],
        ]);

        $sekolah->update($request->only(['nama', 'npsn', 'nomor_telepon', 'detail_alamat', 'email']));

        Log::create([
            'deskripsi' => "Mengupdate sekolah {$sekolah->nama}",
            'user_id' => auth()->id(),
            'sekolah_id' => $sekolah->id,
        ]);

        return back()->with('success', 'Data sekolah berhasil diperbarui.');
    }

    public function destroy(Sekolah $sekolah): RedirectResponse
    {
        $sekolah->update(['is_active' => false]);

        Log::create([
            'deskripsi' => "Menonaktifkan sekolah {$sekolah->nama}",
            'user_id' => auth()->id(),
            'sekolah_id' => $sekolah->id,
        ]);

        return back()->with('success', "Sekolah {$sekolah->nama} berhasil dinonaktifkan.");
    }
}
