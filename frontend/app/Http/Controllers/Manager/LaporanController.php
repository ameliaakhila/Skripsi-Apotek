<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Admin\Obat;
use App\Models\Admin\ObatKeluar;
use App\Models\Admin\ObatMasuk;
use App\Models\Admin\MutasiStok;
use App\Models\Admin\StokOpname;
use App\Exports\ObatMasukExport;
use App\Exports\ObatKeluarExport;
use App\Exports\MutasiStokExport;
use App\Exports\StokOpnameExport;
use App\Exports\ObatSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
// use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        // Ambil data dari semua tabel dengan pagination
        $obat = Obat::with(['DataKategori', 'DataSupplier', 'DataSatuan'])
            ->orderBy('nama_obat', 'asc')
            ->get();

        $obatMasuk = ObatMasuk::with([
            'Obat:id_obat,nama_obat',
        ])
            ->select(
                'id_obat_masuk',
                'kode_batch',
                'tanggal_masuk',
                'tanggal_kedaluwarsa',
                'stok_masuk',
                'id_obat'
            )
            ->orderBy('tanggal_masuk', 'desc')
            ->simplePaginate(15, ['*'], 'obat_masuk');

        $obatKeluar = ObatKeluar::with([
            'StokBatch:id_stok_batch,kode_batch,tanggal_kedaluwarsa,stok_sisa',
            'ObatMasuk:id_obat_masuk,id_obat,tanggal_kedaluwarsa',
            'ObatMasuk.Obat:id_obat,nama_obat',
            'DataCabang:id_cabang,nama_cabang'
        ])
            ->select(
                'id_obat_keluar',
                'id_obat_masuk',
                'id_stok_batch',
                'id_cabang',
                'stok_keluar',
                'created_at',
                'tanggal_keluar'
            )
            ->orderBy('tanggal_keluar', 'desc')
            ->simplePaginate(15, ['*'], 'obat_keluar');

        $mutasiStok = MutasiStok::with([
            'StokBatch:id_stok_batch,id_obat',
            'StokBatch.Obat:id_obat,nama_obat',
            'CabangAsal:id_cabang,nama_cabang',
            'CabangTujuan:id_cabang,nama_cabang'
        ])
            ->select(
                'id_mutasi',
                'id_stok_batch',
                'id_cabang_asal',
                'id_cabang_tujuan',
                'tanggal_mutasi',
                'jumlah_mutasi',
            )
            ->orderBy('tanggal_mutasi', 'desc')
            ->simplePaginate(15, ['*'], 'mutasi_stok');

        $stokOpname = StokOpname::with([
            'Obat:id_obat,nama_obat',
            'StokBatch:id_stok_batch,id_obat',
            'DataCabang:id_cabang,nama_cabang'
        ])
            ->select(
                'id_stok_opname',
                'kode_opname',
                'tanggal_opname',
                'stok_fisik',
                'stok_sistem',
                'selisih',
                'keterangan',
                'id_obat',
                'id_stok_batch',
                'id_cabang'
            )
            ->orderBy('tanggal_opname', 'desc')
            ->simplePaginate(15, ['*'], 'stok_opname');

        return view('manager.laporan', compact(
            'obat',
            'obatMasuk',
            'obatKeluar',
            'mutasiStok',
            'stokOpname'
        ));
    }

    /**
     * Export Data Obat ke Excel
     */
    public function exportObat()
    {
        try {
            return Excel::download(
                new \App\Exports\ObatSheet(\App\Models\Admin\Obat::with(['DataKategori', 'DataSupplier', 'DataSatuan'])->get()->map(fn($i, $k) => [
                    $k + 1, $i->nama_obat, $i->DataKategori->nama_kategori ?? '-', $i->DataSupplier->nama_supplier ?? '-', $i->DataSatuan->nama_satuan ?? '-'
                ])),
                'Laporan_Data_Obat_' . now()->format('d-m-Y') . '.xlsx'
            );
        } catch (\Exception $e) {
            Log::error('Export Data Obat Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export Full Report (Gabungan per sheet)
     */
    public function exportFull(Request $request)
    {
        try {
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            return Excel::download(
                new \App\Exports\FullReportExport($startDate, $endDate),
                'Laporan_Full_Manajemen_Stok_' . now()->format('d-m-Y') . '.xlsx'
            );
        } catch (\Exception $e) {
            Log::error('Export Full Report Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export Obat Masuk ke Excel
     */
    public function exportObatMasuk()
    {
        try {
            return Excel::download(
                new ObatMasukExport(),
                'Laporan_Obat_Masuk_' . now()->format('d-m-Y') . '.xlsx'
            );
        } catch (\Exception $e) {
            Log::error('Export Obat Masuk Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export Obat Keluar ke Excel
     */
    public function exportObatKeluar()
    {
        try {
            return Excel::download(
                new ObatKeluarExport(),
                'Laporan_Obat_Keluar_' . now()->format('d-m-Y') . '.xlsx'
            );
        } catch (\Exception $e) {
            Log::error('Export Obat Keluar Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export Mutasi Stok ke Excel
     */
    public function exportMutasiStok()
    {
        try {
            return Excel::download(
                new MutasiStokExport(),
                'Laporan_Mutasi_Stok_' . now()->format('d-m-Y') . '.xlsx'
            );
        } catch (\Exception $e) {
            Log::error('Export Mutasi Stok Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export Stok Opname ke Excel
     */
    public function exportStokOpname()
    {
        try {
            return Excel::download(
                new StokOpnameExport(),
                'Laporan_Stok_Opname_' . now()->format('d-m-Y') . '.xlsx'
            );
        } catch (\Exception $e) {
            Log::error('Export Stok Opname Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export Excel: ' . $e->getMessage());
        }
    }
}
