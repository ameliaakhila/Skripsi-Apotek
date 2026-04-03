<?php

use App\Http\Controllers\Shared\AuthController;
use App\Http\Controllers\Admin\DataCabangController;
use App\Http\Controllers\Admin\DataObatController;
use App\Http\Controllers\Admin\DataKategoriController;
use App\Http\Controllers\Admin\DataSatuanController;
use App\Http\Controllers\Admin\DataSupplierController;
use App\Http\Controllers\Admin\KelolaAkunController;
use App\Http\Controllers\Shared\StokCabangController;
use App\Http\Controllers\Pharmacist\ObatMasukController;
use App\Http\Controllers\Pharmacist\ObatKeluarController;
use App\Http\Controllers\Manager\PrediksiController;
use App\Http\Controllers\Manager\LaporanController;
use App\Http\Controllers\Shared\MutasiStokController;
use App\Http\Controllers\Shared\StokBatchKedaluwarsaController;
use App\Http\Controllers\Shared\StokOpnameController;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


/**
 * PUBLIC ROUTES - Authentication
 */
Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/login-process', [AuthController::class, 'login'])->name('login.process');


/**
 * PROTECTED ROUTES - Authentication Required
 */
Route::middleware(['auth'])->group(function () {

    /**
     * SHARED DASHBOARD - All authenticated users
     */
    Route::get('/dashboard', function () {
        $user = Auth::user();
        $dashboardService = new DashboardService();

        // Return role-appropriate dashboard with real database data
        if ($user->isAdmin()) {
            $data = $dashboardService->getAdminDashboardData();
            return view('admin.dashboard', $data);
        } elseif ($user->isPharmacist()) {
            $data = $dashboardService->getPharmacistDashboardData();
            return view('pharmacist.dashboard', $data);
        } elseif ($user->isManager()) {
            $data = $dashboardService->getManagerDashboardData();
            return view('manager.dashboard', $data);
        }

        // Fallback to pharmacist dashboard
        $data = $dashboardService->getPharmacistDashboardData();
        return view('pharmacist.dashboard', $data);
    })->name('dashboard');

    /**
     * SHARED ROUTES - Accessible by multiple roles
     */



    /**
     * ADMIN ROUTES - Require admin role
     */
    Route::middleware('admin')->group(function () {
        // Data Obat
        Route::get('/data-obat', [DataObatController::class, 'index']);
        Route::post('/data-obat-tambah', [DataObatController::class, 'store']);
        Route::delete('/data-obat-hapus/{id_obat}', [DataObatController::class, 'destroy']);
        Route::put('/data-obat-edit/{id_obat}', [DataObatController::class, 'update']);

        // Data Satuan
        Route::get('/data-satuan', [DataSatuanController::class, 'index']);
        Route::post('/data-satuan-tambah', [DataSatuanController::class, 'store']);
        Route::delete('/data-satuan-hapus/{id_kategori}', [DataSatuanController::class, 'destroy']);

        // Data Kategori
        Route::get('/data-kategori', [DataKategoriController::class, 'index']);
        Route::post('/data-kategori-tambah', [DataKategoriController::class, 'store']);
        Route::delete('/data-kategori-hapus/{id_kategori}', [DataKategoriController::class, 'destroy']);

        // Data Cabang
        Route::get('/data-cabang', [DataCabangController::class, 'index']);
        Route::post('/data-cabang-tambah', [DataCabangController::class, 'store']);
        Route::delete('/data-cabang-hapus/{id_cabang}', [DataCabangController::class, 'destroy']);
        Route::put('/data-cabang-edit/{id_cabang}', [DataCabangController::class, 'update']);

        // Data Supplier
        Route::get('/data-supplier', [DataSupplierController::class, 'index']);
        Route::post('/data-supplier-tambah', [DataSupplierController::class, 'store']);
        Route::delete('/data-supplier-hapus/{id_supplier}', [DataSupplierController::class, 'destroy']);
        Route::put('/data-supplier-edit/{id_supplier}', [DataSupplierController::class, 'update']);

        // Mutasi Stok
        Route::get('/mutasi-stok', [MutasiStokController::class, 'index']);
        Route::post('/mutasi-stok-tambah', [MutasiStokController::class, 'store']);
        Route::put('/mutasi-stok-edit/{id_mutasi}', [MutasiStokController::class, 'update']);
        Route::delete('/mutasi-stok-hapus/{id_mutasi}', [MutasiStokController::class, 'destroy']);

        // Stok Cabang
        Route::get('/stok-cabang', [StokCabangController::class, 'index']);
        Route::get('/stok-cabang/{id_stok_batch}/edit', [StokCabangController::class, 'edit']);
        Route::post('/stok-cabang-tambah', [StokCabangController::class, 'store']);
        Route::put('/stok-cabang/{id_stok_batch}', [StokCabangController::class, 'update']);
        Route::delete('/stok-cabang-hapus/{id_stok_batch}', [StokCabangController::class, 'destroy']);

        // Batch & Kadaluarsa
        Route::get('/stok-batch-kedaluwarsa', [StokBatchKedaluwarsaController::class, 'index']);
        Route::post('/stok-batch-kedaluwarsa-tambah', [StokBatchKedaluwarsaController::class, 'store']);
        Route::put('/stok-batch-kedaluwarsa-edit/{id_stok_batch}', [StokBatchKedaluwarsaController::class, 'update']);
        Route::delete('/stok-batch-kedaluwarsa-hapus/{id_stok_batch}', [StokBatchKedaluwarsaController::class, 'destroy']);

        // Stok Opname
        Route::get('/stok-opname', [StokOpnameController::class, 'index']);
        Route::post('/stok-opname-tambah', [StokOpnameController::class, 'store']);
        Route::put('/stok-opname-edit/{id_stok_opname}', [StokOpnameController::class, 'update']);
        Route::delete('/stok-opname-hapus/{id_stok_opname}', [StokOpnameController::class, 'destroy']);


        // Kelola Akun
        Route::get('/kelola-akun', [KelolaAkunController::class, 'index']);
    });

    /**
     * ADMIN & PHARMACIST ROUTES - Obat Masuk & Keluar (Accessible by both roles)
     */
    Route::middleware('role:admin,apoteker')->group(function () {
        // Obat Masuk
        Route::get('/obat-masuk', [ObatMasukController::class, 'index']);
        Route::post('/obat-masuk-tambah', [ObatMasukController::class, 'insert']);
        Route::put('/obat-masuk-edit/{id_obat_masuk}', [ObatMasukController::class, 'update']);
        Route::delete('/obat-masuk-hapus/{id_obat_masuk}', [ObatMasukController::class, 'destroy']);

        // Obat Keluar
        Route::get('/obat-keluar', [ObatKeluarController::class, 'index']);
        Route::post('/obat-keluar-tambah', [ObatKeluarController::class, 'insert']);
        Route::put('/obat-keluar-edit/{id_obat_keluar}', [ObatKeluarController::class, 'update']);
        Route::delete('/obat-keluar-hapus/{id_obat_keluar}', [ObatKeluarController::class, 'destroy']);
    });

    /**
     * PHARMACIST ROUTES - Require apoteker role
     */
    Route::middleware('pharmacist')->group(function () {
        // Additional pharmacist-specific routes can be added here
    });

    /**
     * MANAGER ROUTES - Require manajer role
     */
    Route::middleware('manager')->group(function () {
        // Prediksi
        Route::get('/prediksi', [PrediksiController::class, 'index'])->name('prediksi.index');
        Route::get('/obat-kedaluwarsa', [PrediksiController::class, 'obat_kedaluwarsa'])->name('obat-kedaluwarsa.index');

        // Laporan
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan-obat', [LaporanController::class, 'index'])->name('laporan-obat.index');
        Route::get('/laporan-penjualan', [LaporanController::class, 'index'])->name('laporan-penjualan.index');
        Route::get('/laporan-prediksi', [LaporanController::class, 'index'])->name('laporan-prediksi.index');
    });

    /**
     * LOGOUT - All authenticated users
     */
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});


