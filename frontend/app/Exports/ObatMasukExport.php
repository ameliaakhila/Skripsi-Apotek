<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;

class ObatMasukExport implements FromArray, WithHeadings, ShouldAutoSize
{
    /**
     * @return array
     */
    public function array(): array
    {
        try {
            // Use raw SQL untuk avoid relationship issues
            $data = DB::table('tb_obat_masuk as om')
                ->leftJoin('tb_obat as o', 'om.id_obat', '=', 'o.id_obat')
                ->select(
                    'om.kode_batch',
                    'o.nama_obat',
                    'om.stok_masuk',
                    'om.tanggal_masuk',
                    'om.tanggal_kedaluwarsa'
                )
                ->orderBy('om.tanggal_masuk', 'desc')
                ->get();

            $rows = [];
            $no = 1;

            foreach ($data as $row) {
                try {
                    $rows[] = [
                        $no++,
                        $row->kode_batch ?? '',
                        $row->nama_obat ?? 'N/A',
                        $row->stok_masuk ?? 0,
                        $row->tanggal_masuk ? date('d/m/Y', strtotime($row->tanggal_masuk)) : '',
                        $row->tanggal_kedaluwarsa ? date('d/m/Y', strtotime($row->tanggal_kedaluwarsa)) : '',
                    ];
                } catch (\Exception $e) {
                    $rows[] = [
                        $no++,
                        'Error baris: ' . $e->getMessage(),
                        '',
                        '',
                        '',
                        '',
                    ];
                }
            }

            return $rows;
        } catch (\Exception $e) {
            \Log::error('ObatMasukExport Error: ' . $e->getMessage());
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
            'Stok Masuk',
            'Tanggal Masuk',
            'Tanggal Kedaluwarsa',
        ];
    }
}
