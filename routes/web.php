<?php

use App\Http\Controllers\AlternatifController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\RiwayatPerhitunganController;
use App\Http\Controllers\SesiKeputusanController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/proposal', [DashboardController::class, 'proposal'])->name('proposal');

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/demo-login', [AuthController::class, 'demoLogin'])->name('demo-login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Master Kriteria
Route::get('/kriterias', [KriteriaController::class, 'index'])->name('kriterias.index');
Route::post('/kriterias', [KriteriaController::class, 'store'])->name('kriterias.store');
Route::put('/kriterias/{kriteria}', [KriteriaController::class, 'update'])->name('kriterias.update');
Route::delete('/kriterias/{kriteria}', [KriteriaController::class, 'destroy'])->name('kriterias.destroy');

// Master Alternatif
Route::get('/alternatifs', [AlternatifController::class, 'index'])->name('alternatifs.index');
Route::post('/alternatifs', [AlternatifController::class, 'store'])->name('alternatifs.store');
Route::put('/alternatifs/{alternatif}', [AlternatifController::class, 'update'])->name('alternatifs.update');
Route::delete('/alternatifs/{alternatif}', [AlternatifController::class, 'destroy'])->name('alternatifs.destroy');

// Sesi Keputusan & Matriks Penilaian & Hitung TOPSIS
Route::get('/sesi-keputusans', [SesiKeputusanController::class, 'index'])->name('sesi-keputusans.index');
Route::post('/sesi-keputusans', [SesiKeputusanController::class, 'store'])->name('sesi-keputusans.store');
Route::get('/sesi-keputusans/{sesiKeputusan}', [SesiKeputusanController::class, 'show'])->name('sesi-keputusans.show');
Route::put('/sesi-keputusans/{sesiKeputusan}', [SesiKeputusanController::class, 'update'])->name('sesi-keputusans.update');
Route::delete('/sesi-keputusans/{sesiKeputusan}', [SesiKeputusanController::class, 'destroy'])->name('sesi-keputusans.destroy');

Route::put('/sesi-keputusans/{sesiKeputusan}/kriterias', [SesiKeputusanController::class, 'updateKriterias'])->name('sesi-keputusans.kriterias.update');
Route::post('/sesi-keputusans/{sesiKeputusan}/kriterias', [SesiKeputusanController::class, 'addKriteria'])->name('sesi-keputusans.kriterias.add');
Route::delete('/sesi-keputusans/{sesiKeputusan}/kriterias/{sesiKriteria}', [SesiKeputusanController::class, 'removeKriteria'])->name('sesi-keputusans.kriterias.remove');

Route::post('/sesi-keputusans/{sesiKeputusan}/alternatifs/existing', [SesiKeputusanController::class, 'addExistingAlternatif'])->name('sesi-keputusans.alternatifs.add-existing');
Route::post('/sesi-keputusans/{sesiKeputusan}/alternatifs/new', [SesiKeputusanController::class, 'addNewAlternatif'])->name('sesi-keputusans.alternatifs.add-new');
Route::delete('/sesi-keputusans/{sesiKeputusan}/alternatifs/{sesiAlternatif}', [SesiKeputusanController::class, 'removeAlternatif'])->name('sesi-keputusans.alternatifs.remove');

Route::put('/sesi-keputusans/{sesiKeputusan}/penilaians', [SesiKeputusanController::class, 'updatePenilaian'])->name('sesi-keputusans.penilaians.update');
Route::post('/sesi-keputusans/{sesiKeputusan}/hitung', [SesiKeputusanController::class, 'hitung'])->name('sesi-keputusans.hitung');

// Riwayat Perhitungan & Detail TOPSIS
Route::get('/riwayat-perhitungans', [RiwayatPerhitunganController::class, 'index'])->name('riwayat-perhitungans.index');
Route::get('/riwayat-perhitungans/{riwayatPerhitungan}', [RiwayatPerhitunganController::class, 'show'])->name('riwayat-perhitungans.show');
Route::delete('/riwayat-perhitungans/{riwayatPerhitungan}', [RiwayatPerhitunganController::class, 'destroy'])->name('riwayat-perhitungans.destroy');
