<?php

use App\Http\Controllers\AlternatifController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KriteriaController;
use Illuminate\Support\Facades\Route;

// Halaman Utama: Tabel A (Nilai Alternatif terhadap Kriteria)
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::put('/tabel-a', [DashboardController::class, 'updateTabelA'])->name('tabel-a.update');
Route::post('/tabel-a/alternatif', [DashboardController::class, 'quickAddAlternatif'])->name('tabel-a.quick-add');
Route::post('/tabel-a/seed', [DashboardController::class, 'seedInitialData'])->name('tabel-a.seed');

// Input & Kelola Kriteria + Bobot Kriteria
Route::get('/kriterias', [KriteriaController::class, 'index'])->name('kriterias.index');
Route::post('/kriterias', [KriteriaController::class, 'store'])->name('kriterias.store');
Route::put('/kriterias/{kriteria}', [KriteriaController::class, 'update'])->name('kriterias.update');
Route::delete('/kriterias/{kriteria}', [KriteriaController::class, 'destroy'])->name('kriterias.destroy');

// Input & Kelola Alternatif Supplier
Route::get('/alternatifs', [AlternatifController::class, 'index'])->name('alternatifs.index');
Route::post('/alternatifs', [AlternatifController::class, 'store'])->name('alternatifs.store');
Route::put('/alternatifs/{alternatif}', [AlternatifController::class, 'update'])->name('alternatifs.update');
Route::delete('/alternatifs/{alternatif}', [AlternatifController::class, 'destroy'])->name('alternatifs.destroy');
