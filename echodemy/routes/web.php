<?php

use App\Http\Controllers\Admin\SekolahVerificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WilayahController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::view('/registration/pending', 'auth.registration-pending')->name('registration.pending');

Route::get('/wilayah/provinsi', [WilayahController::class, 'provinsi'])->name('wilayah.provinsi');
Route::get('/wilayah/children/{kode}', [WilayahController::class, 'children'])->name('wilayah.children');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/sekolah-verification', [SekolahVerificationController::class, 'index'])->name('admin.sekolah-verification.index');
    Route::post('/sekolah-verification/{sekolah}/approve', [SekolahVerificationController::class, 'approve'])->name('admin.sekolah-verification.approve');
    Route::post('/sekolah-verification/{sekolah}/reject', [SekolahVerificationController::class, 'reject'])->name('admin.sekolah-verification.reject');
});

require __DIR__.'/auth.php';
