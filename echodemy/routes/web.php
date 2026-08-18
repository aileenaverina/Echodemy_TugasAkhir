<?php

use App\Http\Controllers\Admin\AdminMataPelajaranController;
use App\Http\Controllers\Admin\AdminSekolahController;
use App\Http\Controllers\Admin\SekolahVerificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WilayahController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
     return redirect()->route('login');
});

//?
// Route::get('/dashboard', function () {
//     return view('dashboard');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//keperluan registrasi 
Route::view('/registration/pending', 'auth.registration-pending')->name('registration.pending');
Route::get('/wilayah/provinsi', [WilayahController::class, 'provinsi'])->name('wilayah.provinsi');
Route::get('/wilayah/children/{kode}', [WilayahController::class, 'children'])->name('wilayah.children');



Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/sekolah-verification', [SekolahVerificationController::class, 'index'])->name('admin.sekolah-verification.index');
    Route::post('/sekolah-verification/{sekolah}/approve', [SekolahVerificationController::class, 'approve'])->name('admin.sekolah-verification.approve');
    Route::post('/sekolah-verification/{sekolah}/reject', [SekolahVerificationController::class, 'reject'])->name('admin.sekolah-verification.reject');

    Route::get('/sekolah', [AdminSekolahController::class, 'index'])->name('sekolah.index');
    Route::get('/sekolah/{sekolah}', [AdminSekolahController::class, 'show'])->name('sekolah.show');
    Route::put('/sekolah/{sekolah}', [AdminSekolahController::class, 'update'])->name('sekolah.update');
    Route::delete('/sekolah/{sekolah}', [AdminSekolahController::class, 'destroy'])->name('sekolah.destroy');

      Route::get('/mata-pelajaran', [AdminMataPelajaranController::class, 'index'])->name('mata-pelajaran.index');
    Route::get('/mata-pelajaran/{mataPelajaran}', [AdminMataPelajaranController::class, 'show'])->name('mata-pelajaran.show');
    Route::post('/mata-pelajaran', [AdminMataPelajaranController::class, 'store'])->name('mata-pelajaran.store');
    Route::put('/mata-pelajaran/{mataPelajaran}', [AdminMataPelajaranController::class, 'update'])->name('mata-pelajaran.update');
    Route::delete('/mata-pelajaran/{mataPelajaran}', [AdminMataPelajaranController::class, 'destroy'])->name('mata-pelajaran.destroy');
});
require __DIR__.'/auth.php';
