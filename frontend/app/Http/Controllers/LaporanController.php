<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class LaporanController extends Controller
{
    public function index()
    {
        $data = [
            [
                "kode_transaksi" => "TRX001",
                "tanggal" => "2025-12-15",
                "nama_obat" => "Paracetamol 500mg",
                "jumlah_obat" => 20,
                "tanggal_kadaluarsa" => "2026-06-30"
            ],
            [
                "kode_transaksi" => "TRX002",
                "tanggal" => "2025-12-14",
                "nama_obat" => "Amoxicillin 250mg",
                "jumlah_obat" => 15,
                "tanggal_kadaluarsa" => "2026-03-15"
            ],
            [
                "kode_transaksi" => "TRX003",
                "tanggal" => "2025-12-13",
                "nama_obat" => "Vitamin C 1000mg",
                "jumlah_obat" => 30,
                "tanggal_kadaluarsa" => "2027-01-01"
            ],
            [
                "kode_transaksi" => "TRX004",
                "tanggal" => "2025-12-12",
                "nama_obat" => "Ibuprofen 400mg",
                "jumlah_obat" => 25,
                "tanggal_kadaluarsa" => "2026-12-31"
            ],
            [
                "kode_transaksi" => "TRX005",
                "tanggal" => "2025-12-11",
                "nama_obat" => "Cetirizine 10mg",
                "jumlah_obat" => 10,
                "tanggal_kadaluarsa" => "2026-09-30"
            ]
        ];

        return view('components.main.laporan', compact('data'));
    }
}
