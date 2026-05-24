<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AmalanController;
use App\Http\Controllers\TahfidzController;
use App\Http\Controllers\AkademikController;
use App\Http\Controllers\PortofolioController;

Route::get('/login', function () {
    return view('login');
});

Route::post('/login', [AuthController::class, 'login'])->name('login.process');

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index']);

Route::get('/profile', [ProfileController::class, 'index']);

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