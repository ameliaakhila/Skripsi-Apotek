<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\Admin\Obat;
use App\Models\Admin\ObatMasuk;
use App\Models\Admin\ObatKeluar;
use App\Models\Admin\MutasiStok;
use App\Models\Admin\StokOpname;

class FullReportExport implements WithMultipleSheets
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate ?? 'Awal';
        $this->endDate = $endDate ?? now()->format('d-m-Y');
    }

    public function sheets(): array
    {
        return [
            new ObatSheet(Obat::with(['DataKategori', 'DataSupplier', 'DataSatuan'])->get()->map(fn($i, $k) => [
                $k + 1, $i->nama_obat, $i->DataKategori->nama_kategori ?? '-', $i->DataSupplier->nama_supplier ?? '-', $i->DataSatuan->nama_satuan ?? '-'
            ]), $this->startDate, $this->endDate),
            new ObatMasukSheet(ObatMasuk::with('Obat')->get()->map(fn($i, $k) => [
                $k + 1, $i->kode_batch, $i->Obat->nama_obat ?? '-', $i->stok_masuk, $i->tanggal_masuk, $i->tanggal_kedaluwarsa
            ]), $this->startDate, $this->endDate),
            new ObatKeluarSheet(ObatKeluar::with(['ObatMasuk.Obat', 'StokBatch'])->get()->map(fn($i, $k) => [
                $k + 1, $i->StokBatch->kode_batch ?? '-', $i->ObatMasuk->Obat->nama_obat ?? '-', $i->tanggal_keluar, $i->stok_keluar
            ]), $this->startDate, $this->endDate),
            new MutasiSheet(MutasiStok::with(['StokBatch.Obat', 'CabangAsal', 'CabangTujuan'])->get()->map(fn($i, $k) => [
                $k + 1, $i->tanggal_mutasi, $i->StokBatch->Obat->nama_obat ?? '-', $i->CabangAsal->nama_cabang, $i->CabangTujuan->nama_cabang, $i->jumlah_mutasi
            ]), $this->startDate, $this->endDate),
            new OpnameSheet(StokOpname::with(['Obat', 'DataCabang'])->get()->map(fn($i, $k) => [
                $k + 1, $i->kode_opname, $i->tanggal_opname, $i->Obat->nama_obat ?? '-', $i->stok_fisik, $i->stok_sistem, $i->selisih, $i->DataCabang->nama_cabang, $i->keterangan
            ]), $this->startDate, $this->endDate),
        ];
    }
}
