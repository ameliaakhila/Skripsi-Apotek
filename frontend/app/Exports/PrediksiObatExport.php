<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PrediksiObatExport implements FromArray, WithHeadings, ShouldAutoSize
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $rows = [];
        $no = 1;

        foreach ($this->data as $item) {
            if (!isset($item['error']) && !isset($item['error_message'])) {
                $rows[] = [
                    $no++,
                    $item['nama_obat'] ?? '',
                    $item['stok_awal'] ?? 0,
                    $item['stok_terjual'] ?? 0,
                    $item['stok_sisa'] ?? 0,
                    $this->getStatus($item['status'] ?? ''),
                    $item['stok_prediksi'] ?? 0,
                    $this->getConfidence($item['confidence'] ?? ''),
                ];
            }
        }

        return $rows;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'Nama Obat',
            'Stok Awal',
            'Stok Terjual',
            'Stok Sisa',
            'Status',
            'Stok Prediksi',
            'Kepercayaan',
        ];
    }

    /**
     * Get status label
     */
    private function getStatus($status)
    {
        $statusMap = [
            'menipis' => 'Menipis',
            'aman' => 'Aman',
            'habis' => 'Habis',
        ];

        return $statusMap[$status] ?? '';
    }

    /**
     * Get confidence label
     */
    private function getConfidence($confidence)
    {
        $confidenceMap = [
            'high' => 'Tinggi',
            'medium' => 'Sedang',
            'low' => 'Rendah',
        ];

        return $confidenceMap[$confidence] ?? '';
    }
}
