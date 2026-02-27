<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DataCabangController;
use App\Http\Controllers\DataObatController;
use App\Http\Controllers\DataKategoriController;
use App\Http\Controllers\DataSatuanController;
use App\Http\Controllers\DataSupplierController;
use App\Http\Controllers\StokCabangController;
use App\Http\Controllers\ObatMasukController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PrediksiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\KelolaAkunController;
use App\Http\Controllers\StokBatchKedaluwarsaController;
use App\Http\Controllers\StokOpnameController;
use App\Models\Admin\DataCabang;
use App\Models\Admin\Obat;
use Illuminate\Support\Facades\Route;


Route::get('/', [AuthController::class, 'index']);
Route::post('/login-process', [AuthController::class, 'login']);


Route::middleware(['auth'])->group(function () {
    // ! Dashborad
    Route::get('/dashboard', function () {
        $chartData = [10, 20, 15, 30, 25, 40, 35, 50, 45, 60, 55, 100];
        $totalDataObat = Obat::count();
        $totalDataCabang = DataCabang::count();
        return view('components.admin.dashboard', compact('chartData', 'totalDataObat', 'totalDataCabang'));
    });


    // ! Data Obat
    Route::get('/data-obat', [DataObatController::class, 'index']);
    Route::post('/data-obat-tambah', [DataObatController::class, 'store']);
    Route::post('/data-obat-tambah', [DataObatController::class, 'store']);
    Route::delete('/data-obat-hapus/{id_obat}', [DataObatController::class, 'destroy']);
    Route::put('/data-obat-edit/{id_obat}', [DataObatController::class, 'update']);


    // ! Data Satuan
    Route::get('/data-satuan', [DataSatuanController::class, 'index']);
    Route::post('/data-satuan-tambah', [DataSatuanController::class, 'store']);
    Route::delete('/data-satuan-hapus/{id_kategori}', [DataSatuanController::class, 'destroy']);


    // ! Data Kategori
    Route::get('/data-kategori', [DataKategoriController::class, 'index']);
    Route::post('/data-kategori-tambah', [DataKategoriController::class, 'store']);
    Route::delete('/data-kategori-hapus/{id_kategori}', [DataKategoriController::class, 'destroy']);


    // ! Data Cabang
    Route::get('/data-cabang', [DataCabangController::class, 'index']);
    Route::post('/data-cabang-tambah', [DataCabangController::class, 'store']);
    Route::delete('/data-cabang-hapus/{id_cabang}', [DataCabangController::class, 'destroy']);
    Route::put('/data-cabang-edit/{id_cabang}', [DataCabangController::class, 'update']);


    // ! Data Supplier
    Route::get('/data-supplier', [DataSupplierController::class, 'index']);
    Route::post('/data-supplier-tambah', [DataSupplierController::class, 'store']);
    Route::delete('/data-supplier-hapus/{id_supplier}', [DataSupplierController::class, 'destroy']);
    Route::put('/data-supplier-edit/{id_supplier}', [DataSupplierController::class, 'update']);


    // ! Stok Cabang
    Route::get('/stok-cabang', [StokCabangController::class, 'index']);
    Route::post('/stok-cabang-tambah', [StokCabangController::class, 'store']);
    Route::put('/stok-cabang-edit/{id_stok_batch}', [StokCabangController::class, 'update']);
    Route::delete('/stok-cabang-hapus/{id_stok_batch}', [StokCabangController::class, 'destroy']);

    // ! Batch & Kadaluarsa
    Route::get('/stok-batch-kedaluwarsa', [StokBatchKedaluwarsaController::class, 'index']);
    Route::post('/stok-batch-kedaluwarsa-tambah', [StokBatchKedaluwarsaController::class, 'store']);
    Route::put('/stok-batch-kedaluwarsa-edit/{id_stok_batch}', [StokBatchKedaluwarsaController::class, 'update']);
    Route::delete('/stok-batch-kedaluwarsa-hapus/{id_stok_batch}', [StokBatchKedaluwarsaController::class, 'destroy']);


    // ! Obat Masuk
    Route::get('/obat-masuk', [ObatMasukController::class, 'index']);
    Route::post('/obat-masuk-tambah', [ObatMasukController::class, 'insert']);
    Route::put('/obat-masuk-edit/{id_obat_masuk}', [ObatMasukController::class, 'update']);
    Route::delete('/obat-masuk-hapus/{id_obat_masuk}', [ObatMasukController::class, 'destroy']);

    // ! Stok Opname
    Route::get('/stok-opname', [StokOpnameController::class, 'index']);
    Route::post('/stok-opname-tambah', [StokOpnameController::class, 'store']);
    Route::put('/stok-opname-edit/{id_stok_opname}', [StokOpnameController::class, 'update']);
    Route::delete('/stok-opname-hapus/{id_stok_opname}', [StokOpnameController::class, 'destroy']);

    Route::get('/penjualan-obat', [PenjualanController::class, 'index']);
    Route::post('/penjualan-obat-tambah', [PenjualanController::class, 'insert']);

    Route::get('/prediksi', [PrediksiController::class, 'index']);

    Route::get('/obat-kedaluwarsa', [PrediksiController::class, 'obat_kedaluwarsa']);

    Route::get('/laporan', [LaporanController::class, 'index']);
    Route::get('/laporan-obat', [LaporanController::class, 'index']);
    Route::get('/laporan-penjualan', [LaporanController::class, 'index']);
    Route::get('/laporan-prediksi', [LaporanController::class, 'index']);

    Route::get('/kelola-akun', [KelolaAkunController::class, 'index']);

    Route::post('/logout', [AuthController::class, 'logout']);
});

