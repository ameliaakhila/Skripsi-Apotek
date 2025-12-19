<?php

use App\Http\Controllers\ObatController;
use App\Http\Controllers\ObatMasukController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PrediksiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\KelolaAkunController;
use App\Models\KategoriObat;
use Illuminate\Support\Facades\Route;

Route::view('/', 'components.admin.login');

Route::get('/dashboard', function () {
    $chartData = [10, 20, 15, 30, 25, 40, 35, 50, 45, 60, 55, 100];
    return view('components.admin.dashboard', compact('chartData'));
});
Route::get('/data-obat', [ObatController::class, 'index']);

Route::get('/kategori-obat', function () {
    $kategori = KategoriObat::all();
    return view('components.main.kategori_obat', compact('kategori'));
});


Route::middleware(['web'])->group(function () {
    Route::get('/obat-masuk', [ObatMasukController::class, 'index']);
    Route::post('/obat-masuk-tambah', [ObatMasukController::class, 'insert']);
    Route::put('/obat-masuk-edit/{id_obat_masuk}', [ObatMasukController::class, 'update']);
    Route::delete('/obat-masuk-hapus/{id_obat_masuk}', [ObatMasukController::class, 'destroy']);
});

Route::get('/penjualan-obat', [PenjualanController::class, 'index']);
Route::post('/penjualan-obat-tambah', [PenjualanController::class, 'insert']);

Route::get('/prediksi', [PrediksiController::class, 'index']);
// Route::get('/prediksi-upload', [PrediksiController::class, 'upload'])->name('prediksi_obat.upload');

Route::get('/obat-kadaluarsa', [PrediksiController::class,'obat_kadaluarsa']);

Route::get('/laporan', [LaporanController::class, 'index']);
Route::get('/laporan-obat', [LaporanController::class, 'index']);
Route::get('/laporan-penjualan', [LaporanController::class, 'index']);
Route::get('/laporan-prediksi', [LaporanController::class, 'index']);

Route::get('/kelola-akun', [KelolaAkunController::class, 'index']);

// Route::get('/laporan/excel', [LaporanController::class, 'excel'])->name('laporan.excel');
// Route::get('/laporan/pdf', [LaporanController::class, 'pdf'])->name('laporan.pdf');
