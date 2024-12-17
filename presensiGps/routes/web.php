<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PresensiController;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::middleware(['guest:name'])->group(function() {
    Route::get('/', function () {
        return view('auth.login');
    })->name('login');
    Route::post('/prosesLogin', [AuthController::class, 'prosesLogin']);
});

Route::middleware(['auth:name'])->group(function() {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/prosesLogout', [AuthController::class, 'prosesLogout']);

    //presensi
    Route::get('/presensi/create', [PresensiController::class, 'create']);
    Route::post('/presensi/store', [PresensiController::class, 'store']);
});
