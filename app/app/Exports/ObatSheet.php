<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

abstract class BaseSheet implements FromCollection, WithTitle, WithHeadings, WithEvents, WithStartRow, ShouldAutoSize
{
    protected $data;
    protected $startDate;
    protected $endDate;

    public function __construct($data, $startDate = null, $endDate = null) {
        $this->data = $data;
        $this->startDate = $startDate ?? 'Awal';
        $this->endDate = $endDate ?? now()->format('d-m-Y');
    }

    public function collection() { return $this->data; }
    public function startRow(): int { return 6; }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event) {
                $columnCount = count($this->headings());
                $lastColumn = Coordinate::stringFromColumnIndex($columnCount);

                $event->sheet->setCellValue('A1', 'APOTEK SENA FARMA');
                $event->sheet->setCellValue('A2', 'LAPORAN ' . strtoupper($this->title()));
                $event->sheet->setCellValue('A3', 'Periode: ' . $this->startDate . ' s/d ' . $this->endDate);

                $event->sheet->mergeCells("A1:{$lastColumn}1");
                $event->sheet->mergeCells("A2:{$lastColumn}2");
                $event->sheet->mergeCells("A3:{$lastColumn}3");

                $event->sheet->getStyle('A1:A3')->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'font' => ['bold' => true, 'size' => 12],
                ]);
            },
        ];
    }
}

class ObatSheet extends BaseSheet {
    public function title(): string { return 'Data Obat'; }
    public function headings(): array { return ['No', 'Nama Obat', 'Kategori', 'Supplier', 'Satuan']; }
}

class ObatMasukSheet extends BaseSheet {
    public function title(): string { return 'Obat Masuk'; }
    public function headings(): array { return ['No', 'Kode Batch', 'Nama Obat', 'Stok Masuk', 'Tgl Masuk', 'Tgl ED']; }
}

class ObatKeluarSheet extends BaseSheet {
    public function title(): string { return 'Obat Keluar'; }
    public function headings(): array { return ['No', 'Kode Batch', 'Nama Obat', 'Tgl Keluar', 'Stok Keluar']; }
}

class MutasiSheet extends BaseSheet {
    public function title(): string { return 'Mutasi Stok'; }
    public function headings(): array { return ['No', 'Tgl Mutasi', 'Nama Obat', 'Cabang Asal', 'Cabang Tujuan', 'Jumlah']; }
}

class OpnameSheet extends BaseSheet {
    public function title(): string { return 'Stok Opname'; }
    public function headings(): array { return ['No', 'Kode Opname', 'Tgl Opname', 'Nama Obat', 'Stok Fisik', 'Stok Sistem', 'Selisih', 'Cabang', 'Status']; }
}
