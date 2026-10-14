<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MutasiStokExport implements FromArray, WithHeadings, ShouldAutoSize
{
    /**
     * @return array
     */
    public function array(): array
    {
        try {
            // Use raw SQL untuk avoid relationship issues
            $data = DB::table('tb_mutasi_stok as ms')
                ->leftJoin('tb_stok_batch as sb', 'ms.id_stok_batch', '=', 'sb.id_stok_batch')
                ->leftJoin('tb_obat as o', 'sb.id_obat', '=', 'o.id_obat')
                ->leftJoin('tb_cabang as ca', 'ms.id_cabang_asal', '=', 'ca.id_cabang')
                ->leftJoin('tb_cabang as ct', 'ms.id_cabang_tujuan', '=', 'ct.id_cabang')
                ->select(
                    'ms.tanggal_mutasi',
                    'o.nama_obat',
                    'ca.nama_cabang as cabang_asal',
                    'ct.nama_cabang as cabang_tujuan',
                    'ms.jumlah_mutasi',
                )
                ->orderBy('ms.tanggal_mutasi', 'desc')
                ->get();

            $rows = [];
            $no = 1;

            foreach ($data as $row) {
                try {
                    $rows[] = [
                        $no++,
                        $row->tanggal_mutasi ? date('d/m/Y', strtotime($row->tanggal_mutasi)) : '',
                        $row->nama_obat ?? 'N/A',
                        $row->cabang_asal ?? 'N/A',
                        $row->cabang_tujuan ?? 'N/A',
                        $row->jumlah_mutasi ?? 0,
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
                    ];
                }
            }

            return $rows;
        } catch (\Exception $e) {
            Log::error('MutasiStokExport Error: ' . $e->getMessage());
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
            'Tanggal Mutasi',
            'Nama Obat',
            'Cabang Asal',
            'Cabang Tujuan',
            'Jumlah Mutasi',
            'Keterangan',
        ];
    }
}
