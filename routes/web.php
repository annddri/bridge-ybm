<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AmalanController;
use App\Http\Controllers\TahfidzController;

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