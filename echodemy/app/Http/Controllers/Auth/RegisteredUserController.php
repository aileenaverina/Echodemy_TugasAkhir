<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register',  [
            'wilayahs' => \App\Models\Wilayah::orderBy('nama')->get(),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'nama' => ['required', 'string', 'max:500'],
            'npsn' => ['required', 'string', 'size:8', 'unique:sekolahs,npsn'],
            'singkatan' => ['required', 'string', 'max:5'],
            'jenjang' => ['required', 'in:SD,SMP,SMA,MK,MA,SMK'],
            'wilayah_kode' => [
                'required',
                Rule::exists('wilayahs', 'kode'),
                function ($attribute, $value, $fail) {
                    if (substr_count($value, '.') !== 3) {
                        $fail('Wilayah yang dipilih harus level kelurahan.');
                    }
                },
            ],
            'detail_alamat' => ['required', 'string', 'max:500'],
            'nomor_telepon' => ['required', 'string', 'max:45'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:sekolahs,email'],
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('sekolah-logos', 'public');
        }

        Sekolah::create([
            'nama' => $request->nama,
            'npsn' => $request->npsn,
            'detail_alamat' => $request->detail_alamat,
            'nomor_telepon' => $request->nomor_telepon,
            'email' => $request->email,
            'logo' => $logoPath,
            'singkatan' => strtoupper($request->singkatan),
            'jenjang' => $request->jenjang,
            'wilayah_kode' => $request->wilayah_kode,
            'is_active' => true,
            'status' => Sekolah::STATUS_PENDING,
        ]);

        return redirect()->route('registration.pending');
    }
}
