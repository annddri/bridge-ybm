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