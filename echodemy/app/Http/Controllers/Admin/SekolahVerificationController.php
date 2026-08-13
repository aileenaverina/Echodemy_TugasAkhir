<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Mail\SekolahApprovedMail;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Str;

class SekolahVerificationController extends Controller
{
    public function index()
    {
        $pendingSekolahs = Sekolah::where('status', Sekolah::STATUS_PENDING)->latest()->get();
        return view('admin.sekolah-verification.index', compact('pendingSekolahs'));
    }

    public function approve(Sekolah $sekolah): RedirectResponse
    {
        if ($sekolah->status !== Sekolah::STATUS_PENDING) {
            return back()->with('error', 'Sekolah ini sudah diproses sebelumnya.');
        }

        $plainPassword = \Illuminate\Support\Str::password(10);

        $user = User::create([
            'role' => UserRole::Sekolah,
            'kode_user' => strtoupper($sekolah->singkatan).'-'.str_pad($sekolah->id, 4, '0', STR_PAD_LEFT),
            'email' => $sekolah->email,
            'password' => Hash::make($plainPassword),
            'sekolah_id' => $sekolah->id,
        ]);

        $sekolah->update(['status' => Sekolah::STATUS_VERIFIED]);

        Mail::to($user->email)->send(new SekolahApprovedMail($user, $plainPassword));

        return back()->with('success', "Sekolah {$sekolah->nama} berhasil diverifikasi dan email terkirim.");
    }

    public function reject(Sekolah $sekolah): RedirectResponse
    {
        $sekolah->update(['status' => Sekolah::STATUS_REJECTED]);

        return back()->with('success', "Sekolah {$sekolah->nama} ditolak.");
    }
}
