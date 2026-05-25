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

Route::get('/amalan', [AmalanController::class, 'index']);
Route::post('/amalan/update', [AmalanController::class, 'update'])->name('amalan.update');

Route::get('/tahfidz', [TahfidzController::class, 'index'])->name('tahfidz');
Route::post('/tahfidz', [TahfidzController::class, 'store'])->name('tahfidz.store');
Route::get('/tahfidz/status/{id}/{status}', [TahfidzController::class, 'updateStatus'])->name('tahfidz.status');


Route::get('/akademik', [AkademikController::class, 'index'])->name('akademik');

Route::post('/akademik/ip', [AkademikController::class, 'storeIp'])->name('akademik.ip.store');
Route::post('/akademik/toefl', [AkademikController::class, 'storeToefl'])->name('akademik.toefl.store');

Route::get('/akademik/ip/status/{id}/{status}', [AkademikController::class, 'updateIpStatus'])->name('akademik.ip.status');
Route::get('/akademik/toefl/status/{id}/{status}', [AkademikController::class, 'updateToeflStatus'])->name('akademik.toefl.status');


Route::get('/portofolio', [PortofolioController::class, 'index'])->name('portofolio');
Route::post('/portofolio', [PortofolioController::class, 'store'])->name('portofolio.store');
Route::get('/portofolio/status/{id}/{status}', [PortofolioController::class, 'updateStatus'])->name('portofolio.status');


Route::get('/masyarakat', [MasyarakatController::class, 'index'])->name('masyarakat');
Route::post('/masyarakat', [MasyarakatController::class, 'store'])->name('masyarakat.store');
Route::get('/masyarakat/status/{id}/{status}', [MasyarakatController::class, 'updateStatus'])->name('masyarakat.status');
