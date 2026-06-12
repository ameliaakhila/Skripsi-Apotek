<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;

class StokOpnameExport implements FromArray, WithHeadings, ShouldAutoSize
{
    /**
     * @return array
     */
    public function array(): array
    {
        try {
            // Use raw SQL untuk avoid relationship issues
            $data = DB::table('tb_stok_opname as so')
                ->leftJoin('tb_obat as o', 'so.id_obat', '=', 'o.id_obat')
                ->leftJoin('tb_cabang as c', 'so.id_cabang', '=', 'c.id_cabang')
                ->select(
                    'so.kode_opname',
                    'so.tanggal_opname',
                    'o.nama_obat',
                    'so.stok_fisik',
                    'so.stok_sistem',
                    'so.selisih',
                    'c.nama_cabang',
                    'so.keterangan'
                )
                ->orderBy('so.tanggal_opname', 'desc')
                ->get();

            $rows = [];
            $no = 1;

            foreach ($data as $row) {
                try {
                    $rows[] = [
                        $no++,
                        $row->kode_opname ?? '',
                        $row->tanggal_opname ? date('d/m/Y', strtotime($row->tanggal_opname)) : '',
                        $row->nama_obat ?? 'N/A',
                        $row->stok_fisik ?? 0,
                        $row->stok_sistem ?? 0,
                        $row->selisih ?? 0,
                        $row->nama_cabang ?? 'N/A',
                        $row->keterangan ?? '-',
                    ];
                } catch (\Exception $e) {
                    $rows[] = [
                        $no++,
                        'Error baris',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                    ];
                }
            }

            return $rows;
        } catch (\Exception $e) {
            \Log::error('StokOpnameExport Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'Kode Opname',
            'Tanggal Opname',
            'Nama Obat',
            'Stok Fisik',
            'Stok Sistem',
            'Selisih',
            'Cabang',
            'Status',
        ];
    }
}
