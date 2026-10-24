<?php

use App\Http\Controllers\Admin\AdminAkunController;
use App\Http\Controllers\Admin\AdminLogController;
use App\Http\Controllers\Admin\AdminMataPelajaranController;
use App\Http\Controllers\Admin\AdminSekolahController;
use App\Http\Controllers\Admin\SekolahVerificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Guru\GuruAssignmentController;
use App\Http\Controllers\Guru\GuruBobotController;
use App\Http\Controllers\Guru\GuruKelasController;
use App\Http\Controllers\Guru\GuruKelasMataPelajaranController;
use App\Http\Controllers\Guru\GuruKelasSiswaController;
use App\Http\Controllers\Guru\GuruKoreksiController;
use App\Http\Controllers\Guru\GuruLatihanController;
use App\Http\Controllers\Guru\GuruLogController;
use App\Http\Controllers\Guru\GuruMataPelajaranKelasController;
use App\Http\Controllers\Guru\GuruRaporController;
use App\Http\Controllers\Guru\GuruSectionController;
use App\Http\Controllers\Guru\GuruShareBahanAjarController;
use App\Http\Controllers\Ortu\OrtuNilaiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RaportDownloadController;
use App\Http\Controllers\Sekolah\SekolahGuruController;
use App\Http\Controllers\Sekolah\SekolahKelasController;
use App\Http\Controllers\Sekolah\SekolahLogController;
use App\Http\Controllers\Sekolah\SekolahSiswaController;
use App\Http\Controllers\Siswa\SiswaAssignmentController;
use App\Http\Controllers\Siswa\SiswaLatihanController;
use App\Http\Controllers\Siswa\SiswaMataPelajaranController;
use App\Http\Controllers\Siswa\SiswaMateriController;
use App\Http\Controllers\Siswa\SiswaNilaiController;
use App\Http\Controllers\Siswa\SiswaNotifikasiController;
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

Route::middleware('auth')->get('/raport/{raport}/unduh', [RaportDownloadController::class, 'unduh'])->name('raport.unduh');

//keperluan registrasi 
Route::view('/registration/pending', 'auth.registration-pending')->name('registration.pending');
Route::get('/wilayah/provinsi', [WilayahController::class, 'provinsi'])->name('wilayah.provinsi');
Route::get('/wilayah/children/{kode}', [WilayahController::class, 'children'])->name('wilayah.children');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Verifikasi sekolah baru daftar (pending)
    Route::get('/sekolah-verification', [SekolahVerificationController::class, 'index'])->name('sekolah-verification.index');
    Route::post('/sekolah-verification/{sekolah}/approve', [SekolahVerificationController::class, 'approve'])->name('sekolah-verification.approve');
    Route::post('/sekolah-verification/{sekolah}/reject', [SekolahVerificationController::class, 'reject'])->name('sekolah-verification.reject');

    // Manajemen sekolah (semua status)
    Route::get('/sekolah', [AdminSekolahController::class, 'index'])->name('sekolah.index');
    Route::get('/sekolah/{sekolah}', [AdminSekolahController::class, 'show'])->name('sekolah.show');
    Route::put('/sekolah/{sekolah}', [AdminSekolahController::class, 'update'])->name('sekolah.update');
    Route::delete('/sekolah/{sekolah}', [AdminSekolahController::class, 'destroy'])->name('sekolah.destroy');

    // Manajemen mata pelajaran
    Route::get('/mata-pelajaran', [AdminMataPelajaranController::class, 'index'])->name('mata-pelajaran.index');
    Route::get('/mata-pelajaran/{mataPelajaran}', [AdminMataPelajaranController::class, 'show'])->name('mata-pelajaran.show');
    Route::post('/mata-pelajaran', [AdminMataPelajaranController::class, 'store'])->name('mata-pelajaran.store');
    Route::put('/mata-pelajaran/{mataPelajaran}', [AdminMataPelajaranController::class, 'update'])->name('mata-pelajaran.update');
    Route::delete('/mata-pelajaran/{mataPelajaran}', [AdminMataPelajaranController::class, 'destroy'])->name('mata-pelajaran.destroy');

    // Verifikasi & akses (akun admin + riwayat keputusan)
    Route::get('/akun', [AdminAkunController::class, 'index'])->name('akun.index');
    Route::get('/akun/{akun}', [AdminAkunController::class, 'show'])->name('akun.show');
    Route::post('/akun', [AdminAkunController::class, 'store'])->name('akun.store');
    Route::delete('/akun/{akun}', [AdminAkunController::class, 'destroy'])->name('akun.destroy');

    Route::get('/log', [AdminLogController::class, 'index'])->name('log.index');
});

Route::middleware(['auth', 'role:sekolah'])->prefix('sekolah')->name('sekolah.')->group(function () {

    Route::get('/kelas', [SekolahKelasController::class, 'index'])->name('kelas.index');
    Route::get('/kelas/{kelas}', [SekolahKelasController::class, 'show'])->name('kelas.show');
    Route::post('/kelas', [SekolahKelasController::class, 'store'])->name('kelas.store');
    Route::put('/kelas/{kelas}', [SekolahKelasController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{kelas}', [SekolahKelasController::class, 'destroy'])->name('kelas.destroy');

    Route::get('/guru', [SekolahGuruController::class, 'index'])->name('guru.index');
    Route::get('/guru/{guru}', [SekolahGuruController::class, 'show'])->name('guru.show');
    Route::post('/guru', [SekolahGuruController::class, 'store'])->name('guru.store');
    Route::put('/guru/{guru}', [SekolahGuruController::class, 'update'])->name('guru.update');
    Route::delete('/guru/{guru}', [SekolahGuruController::class, 'destroy'])->name('guru.destroy');

    Route::get('/siswa', [SekolahSiswaController::class, 'index'])->name('siswa.index');
    Route::get('/siswa/{siswa}', [SekolahSiswaController::class, 'show'])->name('siswa.show');
    Route::post('/siswa', [SekolahSiswaController::class, 'store'])->name('siswa.store');
    Route::put('/siswa/{siswa}', [SekolahSiswaController::class, 'update'])->name('siswa.update');
    Route::delete('/siswa/{siswa}', [SekolahSiswaController::class, 'destroy'])->name('siswa.destroy');
    Route::get('/siswa/{siswa}/ortu', [SekolahSiswaController::class, 'showOrtu'])->name('siswa.ortu.show');
    Route::post('/siswa/{siswa}/ortu/reset-password', [SekolahSiswaController::class, 'resetOrtuPassword'])->name('siswa.ortu.reset-password');

    Route::get('/log', [SekolahLogController::class, 'index'])->name('log.index');
});

Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/kelas', [GuruKelasController::class, 'index'])->name('kelas.index');
    Route::get('/kelas/{kelas}', [GuruKelasController::class, 'show'])->name('kelas.show');

    Route::get('/kelas/{kelas}/mata-pelajaran', [GuruKelasMataPelajaranController::class, 'index'])->name('kelas.mata-pelajaran.index');
    Route::post('/kelas/{kelas}/mata-pelajaran', [GuruKelasMataPelajaranController::class, 'store'])->name('kelas.mata-pelajaran.store');
    Route::put('/kelas/{kelas}/mata-pelajaran/{mataPelajaranKelas}', [GuruKelasMataPelajaranController::class, 'update'])->name('kelas.mata-pelajaran.update');
    Route::delete('/kelas/{kelas}/mata-pelajaran/{mataPelajaranKelas}', [GuruKelasMataPelajaranController::class, 'destroy'])->name('kelas.mata-pelajaran.destroy');

    Route::get('/kelas/{kelas}/siswa', [GuruKelasSiswaController::class, 'index'])->name('kelas.siswa.index');
    Route::post('/kelas/{kelas}/siswa', [GuruKelasSiswaController::class, 'store'])->name('kelas.siswa.store');
    Route::delete('/kelas/{kelas}/siswa/{siswa}', [GuruKelasSiswaController::class, 'destroy'])->name('kelas.siswa.destroy');

    Route::get('/mata-pelajaran-kelas/{mataPelajaranKelas}', [GuruMataPelajaranKelasController::class, 'show'])->name('mata-pelajaran-kelas.show');
    Route::post('/mata-pelajaran-kelas/{mataPelajaranKelas}/section', [GuruMataPelajaranKelasController::class, 'storeSection'])->name('mata-pelajaran-kelas.section.store');
    Route::put('/mata-pelajaran-kelas/{mataPelajaranKelas}/section/{bahanAjar}', [GuruMataPelajaranKelasController::class, 'updateSection'])->name('mata-pelajaran-kelas.section.update');
    Route::delete('/mata-pelajaran-kelas/{mataPelajaranKelas}/section/{bahanAjar}', [GuruMataPelajaranKelasController::class, 'destroySection'])->name('mata-pelajaran-kelas.section.destroy');
    Route::post('/mata-pelajaran-kelas/{mataPelajaranKelas}/guru', [GuruMataPelajaranKelasController::class, 'addGuru'])->name('mata-pelajaran-kelas.guru.store');
    Route::get('/mata-pelajaran-kelas/{mataPelajaranKelas}/rekap-nilai', [GuruMataPelajaranKelasController::class, 'rekapNilai'])->name('mata-pelajaran-kelas.rekap');
    Route::delete('/mata-pelajaran-kelas/{mataPelajaranKelas}/guru/{guru}', [GuruMataPelajaranKelasController::class, 'removeGuru'])->name('mata-pelajaran-kelas.guru.destroy');
    Route::post('/section/{bahanAjar}/toggle-lock', [GuruMataPelajaranKelasController::class, 'toggleLockSection'])->name('section.toggle-lock');

    Route::get('/share-targets', [GuruShareBahanAjarController::class, 'targets'])->name('share.targets');
    Route::post('/section/{bahanAjar}/share', [GuruShareBahanAjarController::class, 'shareSection'])->name('section.share');

    Route::get('/section/{bahanAjar}/create', [GuruSectionController::class, 'create'])->name('section.create');
    Route::post('/section/{bahanAjar}/materi', [GuruSectionController::class, 'storeMateri'])->name('section.materi.store');
    Route::post('/section/{bahanAjar}/assignment', [GuruSectionController::class, 'storeAssignment'])->name('section.assignment.store');
    Route::post('/section/{bahanAjar}/latihan', [GuruSectionController::class, 'storeLatihan'])->name('section.latihan.store');

    Route::get('/latihan/{latihan}', [GuruLatihanController::class, 'show'])->name('latihan.show');
    Route::get('/latihan/{latihan}/soal', [GuruLatihanController::class, 'soal'])->name('latihan.soal');
    Route::put('/latihan/{latihan}/submission/{submission}', [GuruLatihanController::class, 'updateNilai'])->name('latihan.submission.update');
    Route::put('/latihan/{latihan}/koreksi/{submission}', [GuruKoreksiController::class, 'update'])->name('guru.koreksi.update');
    Route::get('/latihan/{latihan}/koreksi/{submission}', [GuruKoreksiController::class, 'show'])->name('guru.koreksi.show');

    Route::get('/assignment/{assignment}', [GuruAssignmentController::class, 'show'])->name('assignment.show');

    Route::get('/rapor', [GuruRaporController::class, 'index'])->name('rapor.index');
    Route::get('/rapor/kelas/{kelas}', [GuruRaporController::class, 'kelas'])->name('rapor.kelas');
    Route::get('/rapor/kelas/{kelas}/siswa/{siswa}', [GuruRaporController::class, 'siswa'])->name('rapor.siswa');
    Route::get('/rapor/kelas/{kelas}/siswa/{siswa}/cek', [GuruRaporController::class, 'cek'])->name('rapor.cek');
    Route::post('/rapor/kelas/{kelas}/siswa/{siswa}/terbitkan', [GuruRaporController::class, 'terbitkan'])->name('rapor.terbitkan');
    Route::post('/rapor/kelas/{kelas}/terbitkan-semua', [GuruRaporController::class, 'terbitkanSemua'])->name('rapor.terbitkan-semua');

    Route::get('/log', [GuruLogController::class, 'index'])->name('log.index');
});

Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/mata-pelajaran-kelas/{mataPelajaranKelas}', [SiswaMataPelajaranController::class, 'show'])->name('mata-pelajaran-kelas.show');
    Route::post('/materi/{materi}/toggle-progress', [SiswaMataPelajaranController::class, 'toggleProgress'])->name('materi.toggle-progress');
    Route::get('/materi/{materi}', [SiswaMateriController::class, 'show'])->name('materi.show');

    Route::get('/latihan/{latihan}', [SiswaLatihanController::class, 'show'])->name('latihan.show');
    Route::post('/latihan/{latihan}/mulai', [SiswaLatihanController::class, 'mulai'])->name('latihan.mulai');
    Route::get('/latihan/{latihan}/kerjakan/{submission}', [SiswaLatihanController::class, 'kerjakan'])->name('latihan.kerjakan');
    Route::post('/latihan/{latihan}/kerjakan/{submission}/jawab', [SiswaLatihanController::class, 'jawab'])->name('latihan.jawab');
    Route::post('/latihan/{latihan}/kerjakan/{submission}/selesai', [SiswaLatihanController::class, 'selesai'])->name('latihan.selesai');
    Route::get('/latihan/{latihan}/hasil/{submission}', [SiswaLatihanController::class, 'hasil'])->name('latihan.hasil');

    Route::get('/assignment/{assignment}', [SiswaAssignmentController::class, 'show'])->name('assignment.show');
    Route::post('/assignment/{assignment}/kumpul', [SiswaAssignmentController::class, 'kumpul'])->name('assignment.kumpul');

    Route::get('/nilai', [SiswaNilaiController::class, 'index'])->name('nilai.index');

    Route::get('/notifikasi', [SiswaNotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{notif}/toggle', [SiswaNotifikasiController::class, 'toggle'])->name('notifikasi.toggle');

    Route::get('/mata-pelajaran-kelas/{mataPelajaranKelas}/bobot', [GuruBobotController::class, 'index'])->name('bobot.index');
    Route::put('/mata-pelajaran-kelas/{mataPelajaranKelas}/bobot', [GuruBobotController::class, 'update'])->name('bobot.update');
});

Route::middleware(['auth', 'role:orang_tua'])->prefix('ortu')->name('ortu.')->group(function () {
    Route::get('/nilai', [OrtuNilaiController::class, 'index'])->name('nilai.index');
    Route::get('/nilai/{mataPelajaranKelas}', [OrtuNilaiController::class, 'show'])->name('nilai.show');
});
require __DIR__ . '/auth.php';
