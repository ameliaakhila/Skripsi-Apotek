<?php

use App\Http\Controllers\Shared\AuthController;
use App\Http\Controllers\Admin\DataCabangController;
use App\Http\Controllers\Admin\DataObatController;
use App\Http\Controllers\Admin\DataKategoriController;
use App\Http\Controllers\Admin\DataSatuanController;
use App\Http\Controllers\Admin\DataSupplierController;
use App\Http\Controllers\Admin\KelolaAkunController;
use App\Http\Controllers\Shared\StokCabangController;
use App\Http\Controllers\Apoteker\ObatMasukController;
use App\Http\Controllers\Apoteker\ObatKeluarController;
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
Route::get('/login-process', function () {
    return redirect()->route('login');
});
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
        } elseif ($user->isApoteker()) {
            $data = $dashboardService->getApotekerDashboardData();
            return view('apoteker.dashboard', $data);
        } elseif ($user->isManager()) {
            $data = $dashboardService->getManagerDashboardData();
            return view('manager.dashboard', $data);
        }

        // Fallback to Apoteker dashboard
        $data = $dashboardService->getApotekerDashboardData();
        return view('apoteker.dashboard', $data);
    })->name('dashboard');

    /**
     * SHARED ROUTES - Accessible by multiple roles
     */



    /**
     * ADMIN ROUTES - Require admin role (System Management)
     */
    Route::middleware('admin')->as('admin.')->group(function () {
        // Data Obat (RESTful)
        Route::resource('data-obat', DataObatController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['data-obat' => 'id_obat']);

        // Legacy endpoints agar view lama tetap jalan
        Route::post('/data-obat-tambah', [DataObatController::class, 'store'])->name('data-obat.tambah-legacy');
        Route::put('/data-obat-edit/{id_obat}', [DataObatController::class, 'update'])->name('data-obat.edit-legacy');
        Route::delete('/data-obat-hapus/{id_obat}', [DataObatController::class, 'destroy'])->name('data-obat.hapus-legacy');

        // Data Satuan (RESTful)
        Route::resource('data-satuan', DataSatuanController::class)
            ->only(['index', 'store', 'destroy'])
            ->parameters(['data-satuan' => 'id_satuan']);

        Route::post('/data-satuan-tambah', [DataSatuanController::class, 'store'])->name('data-satuan.tambah-legacy');
        Route::delete('/data-satuan-hapus/{id_satuan}', [DataSatuanController::class, 'destroy'])->name('data-satuan.hapus-legacy');

        // Data Kategori (RESTful)
        Route::resource('data-kategori', DataKategoriController::class)
            ->only(['index', 'store', 'destroy'])
            ->parameters(['data-kategori' => 'id_kategori']);

        Route::post('/data-kategori-tambah', [DataKategoriController::class, 'store'])->name('data-kategori.tambah-legacy');
        Route::delete('/data-kategori-hapus/{id_kategori}', [DataKategoriController::class, 'destroy'])->name('data-kategori.hapus-legacy');

        // Data Cabang (RESTful)
        Route::resource('data-cabang', DataCabangController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['data-cabang' => 'id_cabang']);

        Route::post('/data-cabang-tambah', [DataCabangController::class, 'store'])->name('data-cabang.tambah-legacy');
        Route::put('/data-cabang-edit/{id_cabang}', [DataCabangController::class, 'update'])->name('data-cabang.edit-legacy');
        Route::delete('/data-cabang-hapus/{id_cabang}', [DataCabangController::class, 'destroy'])->name('data-cabang.hapus-legacy');

        // Data Supplier (RESTful)
        Route::resource('data-supplier', DataSupplierController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['data-supplier' => 'id_supplier']);

        Route::post('/data-supplier-tambah', [DataSupplierController::class, 'store'])->name('data-supplier.tambah-legacy');
        Route::put('/data-supplier-edit/{id_supplier}', [DataSupplierController::class, 'update'])->name('data-supplier.edit-legacy');
        Route::delete('/data-supplier-hapus/{id_supplier}', [DataSupplierController::class, 'destroy'])->name('data-supplier.hapus-legacy');

        // Kelola Akun
        Route::get('/kelola-akun', [KelolaAkunController::class, 'index'])->name('kelola-akun.index');
        Route::post('/kelola-akun', [KelolaAkunController::class, 'store'])->name('kelola-akun.store');
        Route::put('/kelola-akun/{id}', [KelolaAkunController::class, 'update'])->name('kelola-akun.update');
        Route::delete('/kelola-akun/{id}', [KelolaAkunController::class, 'destroy'])->name('kelola-akun.destroy');
        Route::put('/kelola-akun/{id}/reset-password', [KelolaAkunController::class, 'resetPassword'])->name('kelola-akun.reset-password');
    });

    /**
     * SHARED OPERATIONAL ROUTES - Admin & Apoteker (Stock Transactions & Management)
     */
    Route::middleware('role:admin,apoteker')->group(function () {
        // Obat Masuk (RESTful)
        Route::resource('obat-masuk', ObatMasukController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['obat-masuk' => 'id_obat_masuk']);

        Route::post('/obat-masuk-tambah', [ObatMasukController::class, 'store'])->name('obat-masuk.store-legacy');
        Route::put('/obat-masuk-edit/{id_obat_masuk}', [ObatMasukController::class, 'update'])->name('obat-masuk.update-legacy');
        Route::delete('/obat-masuk-hapus/{id_obat_masuk}', [ObatMasukController::class, 'destroy'])->name('obat-masuk.destroy-legacy');

        // Obat Keluar (RESTful)
        Route::resource('obat-keluar', ObatKeluarController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['obat-keluar' => 'id_obat_keluar']);

        Route::post('/obat-keluar-tambah', [ObatKeluarController::class, 'store'])->name('obat-keluar.store-legacy');
        Route::put('/obat-keluar-edit/{id_obat_keluar}', [ObatKeluarController::class, 'update'])->name('obat-keluar.update-legacy');
        Route::delete('/obat-keluar-hapus/{id_obat_keluar}', [ObatKeluarController::class, 'destroy'])->name('obat-keluar.destroy-legacy');

        // Mutasi Stok
        Route::get('/mutasi-stok', [MutasiStokController::class, 'index'])->name('mutasi-stok.index');
        Route::post('/mutasi-stok-tambah', [MutasiStokController::class, 'store'])->name('mutasi-stok.store-legacy');
        Route::put('/mutasi-stok-edit/{id_mutasi}', [MutasiStokController::class, 'update'])->name('mutasi-stok.update-legacy');
        Route::delete('/mutasi-stok-hapus/{id_mutasi}', [MutasiStokController::class, 'destroy'])->name('mutasi-stok.destroy-legacy');

        // Stok Opname
        Route::get('/stok-opname', [StokOpnameController::class, 'index'])->name('stok-opname.index');
        Route::post('/stok-opname-tambah', [StokOpnameController::class, 'store'])->name('stok-opname.store-legacy');
        Route::put('/stok-opname-edit/{id_stok_opname}', [StokOpnameController::class, 'update'])->name('stok-opname.update-legacy');
        Route::delete('/stok-opname-hapus/{id_stok_opname}', [StokOpnameController::class, 'destroy'])->name('stok-opname.destroy-legacy');
    });

    /**
     * SHARED MONITORING ROUTES - Admin, Apoteker, & Manager (View Only for Manager)
     */
    Route::middleware('role:admin,apoteker,manajer')->group(function () {
        // Stok Cabang
        Route::get('/stok-cabang', [StokCabangController::class, 'index'])->name('stok-cabang.index');
        Route::get('/stok-cabang/{id_stok_batch}/edit', [StokCabangController::class, 'edit'])->name('stok-cabang.edit');
        Route::post('/stok-cabang-tambah', [StokCabangController::class, 'store'])->name('stok-cabang.store-legacy');
        Route::put('/stok-cabang/{id_stok_batch}', [StokCabangController::class, 'update'])->name('stok-cabang.update');
        Route::delete('/stok-cabang-hapus/{id_stok_batch}', [StokCabangController::class, 'destroy'])->name('stok-cabang.destroy-legacy');

        // Batch & Kadaluarsa
        Route::get('/stok-batch-kedaluwarsa', [StokBatchKedaluwarsaController::class, 'index'])->name('stok-batch-kedaluwarsa.index');
        Route::post('/stok-batch-kedaluwarsa-tambah', [StokBatchKedaluwarsaController::class, 'store'])->name('stok-batch-kedaluwarsa.store-legacy');
        Route::put('/stok-batch-kedaluwarsa-edit/{id_stok_batch}', [StokBatchKedaluwarsaController::class, 'update'])->name('stok-batch-kedaluwarsa.update-legacy');
        Route::delete('/stok-batch-kedaluwarsa-hapus/{id_stok_batch}', [StokBatchKedaluwarsaController::class, 'destroy'])->name('stok-batch-kedaluwarsa.destroy-legacy');

        // Obat Kedaluwarsa (Warning Info)
        Route::get('/obat-kedaluwarsa', [PrediksiController::class, 'obat_kedaluwarsa'])->name('obat-kedaluwarsa.index');
    });

    /**
     * ANALYTICS & REPORTING ROUTES - Admin & Manager
     */
    Route::middleware('role:admin,manajer')->group(function () {
        // Prediksi
        Route::get('/prediksi', [PrediksiController::class, 'index'])->name('prediksi.index');
        Route::post('/prediksi/generate', [PrediksiController::class, 'generatePrediction'])->name('prediksi.generate');
        Route::get('/prediksi/drug/{id_obat}', [PrediksiController::class, 'getDrugPrediction'])->name('prediksi.drug');
        Route::get('/prediksi/summary/{id_obat}', [PrediksiController::class, 'getLastMonthSummary'])->name('prediksi.summary');
        Route::get('/prediksi/export-excel', [PrediksiController::class, 'exportExcel'])->name('prediksi.export-excel');
        Route::get('/prediksi/export-pdf', [PrediksiController::class, 'exportPdf'])->name('prediksi.export-pdf');

        // Laporan
        Route::get('/dashboard/refresh', [App\Http\Controllers\Manager\DashboardRefreshController::class, '__invoke'])->name('dashboard.refresh');
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan-obat', [LaporanController::class, 'index'])->name('laporan-obat.index');
        Route::get('/laporan-penjualan', [LaporanController::class, 'index'])->name('laporan-penjualan.index');
        Route::get('/laporan-prediksi', [LaporanController::class, 'index'])->name('laporan-prediksi.index');
        Route::get('/laporan/export-obat', [LaporanController::class, 'exportObat'])->name('laporan.export-obat');
        Route::get('/laporan/export-obat-masuk', [LaporanController::class, 'exportObatMasuk'])->name('laporan.export-obat-masuk');
        Route::get('/laporan/export-obat-keluar', [LaporanController::class, 'exportObatKeluar'])->name('laporan.export-obat-keluar');
        Route::get('/laporan/export-mutasi-stok', [LaporanController::class, 'exportMutasiStok'])->name('laporan.export-mutasi-stok');
        Route::get('/laporan/export-stok-opname', [LaporanController::class, 'exportStokOpname'])->name('laporan.export-stok-opname');
        Route::get('/laporan/export-full', [LaporanController::class, 'exportFull'])->name('laporan.export-full');
    });

    /**
     * LOGOUT - All authenticated users
     */
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});


