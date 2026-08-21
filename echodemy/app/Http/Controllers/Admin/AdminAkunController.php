<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAkunController extends Controller
{
    public function index()
    {
        $admins = User::where('role', UserRole::Admin)->latest()->get();

        $riwayat = Log::whereNotNull('sekolah_id')
            ->where(function ($q) {
                $q->where('deskripsi', 'like', 'Memverifikasi%')
                  ->orWhere('deskripsi', 'like', 'Menolak%')
                  ->orWhere('deskripsi', 'like', 'Menonaktifkan%');
            })
            ->with(['user', 'sekolah'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.verifikasi-akses.index', compact('admins', 'riwayat'));
    }

    public function show(User $akun)
    {
        return response()->json([
            'nama_lengkap' => $akun->nama_lengkap,
            'kode_user' => $akun->kode_user,
            'email' => $akun->email,
            'last_login_at' => $akun->last_login_at?->format('d/m/y H.i.s') ?? 'Belum pernah login',
            'created_at' => $akun->created_at->format('d/m/y H.i.s'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $nextNumber = User::where('role', UserRole::Admin)->count() + 1;

        User::create([
            'role' => UserRole::Admin,
            'nama_lengkap' => $request->nama_lengkap,
            'kode_user' => 'ADMIN' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT),
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Akun admin berhasil ditambahkan.');
    }

    public function destroy(User $akun): RedirectResponse
    {
        if ($akun->id === auth()->id()) {
            return back()->with('error', 'Kamu tidak bisa menghapus akunmu sendiri.');
        }

        $akun->delete();

        return back()->with('success', "Akun {$akun->nama_lengkap} berhasil dihapus.");
    }
}
