<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;

class ObatKeluarExport implements FromArray, WithHeadings, ShouldAutoSize
{
    /**
     * @return array
     */
    public function array(): array
    {
        try {
            // Use raw SQL untuk avoid relationship issues
            $data = DB::table('tb_obat_keluar as ok')
                ->leftJoin('tb_stok_batch as sb', 'ok.id_stok_batch', '=', 'sb.id_stok_batch')
                ->leftJoin('tb_obat_masuk as om', 'ok.id_obat_masuk', '=', 'om.id_obat_masuk')
                ->leftJoin('tb_obat as o', 'om.id_obat', '=', 'o.id_obat')
                ->select(
                    'sb.kode_batch',
                    'o.nama_obat',
                    'ok.tanggal_keluar',
                    'ok.stok_keluar'
                )
                ->orderBy('ok.tanggal_keluar', 'desc')
                ->get();

            $rows = [];
            $no = 1;

            foreach ($data as $row) {
                try {
                    $rows[] = [
                        $no++,
                        $row->kode_batch ?? 'N/A',
                        $row->nama_obat ?? 'N/A',
                        $row->tanggal_keluar ? date('d/m/Y', strtotime($row->tanggal_keluar)) : '',
                        $row->stok_keluar ?? 0,
                    ];
                } catch (\Exception $e) {
                    $rows[] = [
                        $no++,
                        'Error baris',
                        '',
                        '',
                        '',
                    ];
                }
            }

            return $rows;
        } catch (\Exception $e) {
            \Log::error('ObatKeluarExport Error: ' . $e->getMessage());
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
            'Kode Batch',
            'Nama Obat',
            'Tanggal Keluar',
            'Stok Keluar',
        ];
    }
}
