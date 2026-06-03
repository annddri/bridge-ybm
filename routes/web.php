<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AmalanController;
use App\Http\Controllers\TahfidzController;
use App\Http\Controllers\AkademikController;
use App\Http\Controllers\PortofolioController;
use App\Http\Controllers\MasyarakatController;
use App\Http\Controllers\InventarisController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\KepasController;
use App\Http\Controllers\ProfileKepasController;
use App\Http\Controllers\DataMahasiswaController;
use App\Http\Controllers\LeaderboardController;

Route::get('/', function () {
    if (session()->has('id_user')) {
        return redirect('/dashboard');
    }

    return redirect('/login');
});
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/profile', [ProfileController::class, 'index']);
Route::post('/profile/update-whatsapp', [ProfileController::class, 'updateWhatsapp'])
    ->name('profile.updateWhatsapp');

Route::get('/amalan', [AmalanController::class, 'index'])->name('amalan.index');
Route::post('/amalan/update', [AmalanController::class, 'update'])->name('amalan.update');

Route::get('/tahfidz', [TahfidzController::class, 'index'])->name('tahfidz');
Route::post('/tahfidz', [TahfidzController::class, 'store'])->name('tahfidz.store');
Route::delete('/tahfidz/{id}', [TahfidzController::class, 'destroy'])->name('tahfidz.destroy');

Route::get('/akademik', [AkademikController::class, 'index'])->name('akademik');

Route::post('/akademik/ip', [AkademikController::class, 'storeIp'])->name('akademik.ip.store');
Route::post('/akademik/toefl', [AkademikController::class, 'storeToefl'])->name('akademik.toefl.store');
Route::delete('/akademik/ip/{id}', [AkademikController::class, 'destroyIp'])->name('akademik.ip.destroy');
Route::delete('/akademik/toefl/{id}', [AkademikController::class, 'destroyToefl'])->name('akademik.toefl.destroy');


Route::get('/portofolio', [PortofolioController::class, 'index'])->name('portofolio');
Route::post('/portofolio', [PortofolioController::class, 'store'])->name('portofolio.store');
Route::delete('/portofolio/{id}', [PortofolioController::class, 'destroy'])->name('portofolio.destroy');

Route::get('/masyarakat', [MasyarakatController::class, 'index'])->name('masyarakat');
Route::post('/masyarakat', [MasyarakatController::class, 'store'])->name('masyarakat.store');
Route::delete('/masyarakat/{id}', [MasyarakatController::class, 'destroy'])->name('masyarakat.destroy');

Route::get('/inventaris', [InventarisController::class, 'index'])->name('inventaris.index');
Route::post('/inventaris', [InventarisController::class, 'store'])->name('inventaris.store');
Route::put('/inventaris/{id_barang}', [InventarisController::class, 'update'])->name('inventaris.update');
Route::delete('/inventaris/{id_barang}', [InventarisController::class, 'destroy'])->name('inventaris.destroy');

Route::get('/keuangan', [KeuanganController::class, 'index'])->name('keuangan.index');
Route::post('/keuangan/store', [KeuanganController::class, 'store'])->name('keuangan.store');
Route::put('/keuangan/update/{id}', [KeuanganController::class, 'update'])->name('keuangan.update');
Route::delete('/keuangan/delete/{id}', [KeuanganController::class, 'destroy'])->name('keuangan.destroy');

// KEPALA ASRAMA
Route::get('/kepas', [KepasController::class, 'index'])->name('kepas');


Route::get('/profile-kepas', [ProfileKepasController::class, 'index']);
Route::post('/profile-kepas/update-whatsapp', [ProfileKepasController::class, 'updateWhatsapp'])
    ->name('profileKepas.updateWhatsapp');

Route::get('/data-mahasiswa', [
    DataMahasiswaController::class,
    'index'
])->name('data-mahasiswa');

Route::get('/data-mahasiswa/{id}', [
    DataMahasiswaController::class,
    'detail'
])->name('mahasiswa.detail');   

Route::get(
    '/data-mahasiswa/{id}/amalan',
    [DataMahasiswaController::class, 'detailAmalan']
)->name('mahasiswa.amalan');

Route::get(
    '/data-mahasiswa/{id}/tahfidz',
    [DataMahasiswaController::class, 'detailTahfidz']
)->name('mahasiswa.tahfidz');

Route::get(
    '/data-mahasiswa/{id}/akademik',
    [DataMahasiswaController::class, 'detailAkademik']
)->name('mahasiswa.akademik');

Route::get(
    '/data-mahasiswa/{id}/portofolio',
    [DataMahasiswaController::class, 'detailPortofolio']
)->name('mahasiswa.portofolio');

Route::get(
    '/data-mahasiswa/{id}/masyarakat',
    [DataMahasiswaController::class, 'detailMasyarakat']
)->name('mahasiswa.masyarakat');


Route::get(
    '/leaderboard',
    [LeaderboardController::class, 'index']
)->name('leaderboard');

Route::get(
    '/keuangan-monitoring',
    [KepasController::class, 'keuangan']
)->name('keuangan.monitoring');