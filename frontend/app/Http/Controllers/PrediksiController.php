<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembelian;
use Phpml\Classification\DecisionTree;
use Phpml\ModelManager;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Component;

class PrediksiController extends Controller
{
    public function index()
    {
        $data = [
            ["nama_obat" => "ALLOPURINOL 100 MG LANDSON", "stok_awal" => 184, "stok_terjual" => 112, "stok_prediksi" => 180, "stok_sisa" => 72, "status" => "aman"],
            ["nama_obat" => "SANGOBION BABY SYR", "stok_awal" => 132, "stok_terjual" => 98, "stok_prediksi" => 180, "stok_sisa" => 34, "status" => "menipis"],
            ["nama_obat" => "ACETIN 200 MG KAPSUL", "stok_awal" => 215, "stok_terjual" => 209, "stok_prediksi" => 250, "stok_sisa" => 6, "status" => "habis"],
            ["nama_obat" => "ACETYLCYSTEINE 200 MG CAPS DEXA", "stok_awal" => 178, "stok_terjual" => 95, "stok_prediksi" => 180, "stok_sisa" => 83, "status" => "aman"],
            ["nama_obat" => "ACETYLCYSTEINE 200 MG KAPSUL INDOFARMA", "stok_awal" => 156, "stok_terjual" => 107, "stok_prediksi" => 180, "stok_sisa" => 49, "status" => "menipis"],
            ["nama_obat" => "ACETYLCYSTEINE 200 MG KAPSUL INFION", "stok_awal" => 143, "stok_terjual" => 140, "stok_prediksi" => 220, "stok_sisa" => 3, "status" => "habis"],
            ["nama_obat" => "ACETYLCYSTEINE 200 MG KAPSUL NULAB", "stok_awal" => 199, "stok_terjual" => 148, "stok_prediksi" => 220, "stok_sisa" => 51, "status" => "aman"],
            ["nama_obat" => "ACETYLCYSTEINE 200 MG MBF", "stok_awal" => 120, "stok_terjual" => 54, "stok_prediksi" => 150, "stok_sisa" => 66, "status" => "aman"],
            ["nama_obat" => "ACETYLCYSTEINE 200 MG TAB YARINDO", "stok_awal" => 220, "stok_terjual" => 171, "stok_prediksi" => 250, "stok_sisa" => 49, "status" => "menipis"],
            ["nama_obat" => "ACETYLCYTEINE 200 MG NOVELL", "stok_awal" => 95, "stok_terjual" => 90, "stok_prediksi" => 180, "stok_sisa" => 5, "status" => "habis"],
            ["nama_obat" => "ACIFAR 200 MG TAB", "stok_awal" => 188, "stok_terjual" => 130, "stok_prediksi" => 220, "stok_sisa" => 58, "status" => "aman"],
            ["nama_obat" => "ACIFAR 400 MG TAB", "stok_awal" => 137, "stok_terjual" => 125, "stok_prediksi" => 220, "stok_sisa" => 12, "status" => "menipis"],
            ["nama_obat" => "ACIFAR CREAM", "stok_awal" => 92, "stok_terjual" => 88, "stok_prediksi" => 180, "stok_sisa" => 4, "status" => "habis"],
            ["nama_obat" => "ACITRAL SIRUP 120 ML", "stok_awal" => 160, "stok_terjual" => 86, "stok_prediksi" => 180, "stok_sisa" => 74, "status" => "aman"],
            ["nama_obat" => "ACNOL GEL FOR ACNE 10 GR", "stok_awal" => 145, "stok_terjual" => 90, "stok_prediksi" => 180, "stok_sisa" => 55, "status" => "aman"],
            ["nama_obat" => "ACNOL LOTION", "stok_awal" => 134, "stok_terjual" => 105, "stok_prediksi" => 180, "stok_sisa" => 29, "status" => "menipis"],
            ["nama_obat" => "ACTIFED HIJAU SYR", "stok_awal" => 170, "stok_terjual" => 162, "stok_prediksi" => 250, "stok_sisa" => 8, "status" => "habis"],
            ["nama_obat" => "ACTIFED KUNING SYR", "stok_awal" => 125, "stok_terjual" => 47, "stok_prediksi" => 150, "stok_sisa" => 78, "status" => "aman"],
            ["nama_obat" => "ACTIFED MERAH SYP K", "stok_awal" => 198, "stok_terjual" => 167, "stok_prediksi" => 250, "stok_sisa" => 31, "status" => "menipis"],
            ["nama_obat" => "ACYCLOVIR 200 MG TAB NOVELL", "stok_awal" => 176, "stok_terjual" => 170, "stok_prediksi" => 250, "stok_sisa" => 6, "status" => "habis"]
        ];
        return view('components.admin.prediksi_obat', compact('data'));
    }

    public function dashboard()
    {
        $chartData = [10, 20, 15, 30, 25, 40, 35, 50, 45, 60, 55, 100];
        $prediksi = [
            ["nama_obat" => "Paracetamol", "status" => "aman", "stok" => 120],
            ["nama_obat" => "Amoxicillin", "status" => "menipis", "stok" => 14],
            ["nama_obat" => "Vitamin C 500mg", "status" => "habis", "stok" => 0],
            ["nama_obat" => "Ibuprofen", "status" => "aman", "stok" => 95],
            ["nama_obat" => "Cetirizine", "status" => "menipis", "stok" => 9],
            ["nama_obat" => "Antasida DOEN", "status" => "aman", "stok" => 77],
            ["nama_obat" => "Oralit", "status" => "aman", "stok" => 110],
            ["nama_obat" => "Metformin", "status" => "menipis", "stok" => 12],
            ["nama_obat" => "Amlodipine", "status" => "aman", "stok" => 140],
            ["nama_obat" => "Omeprazole", "status" => "habis", "stok" => 0],
            ["nama_obat" => "Ranitidine", "status" => "menipis", "stok" => 6],
            ["nama_obat" => "Captopril", "status" => "aman", "stok" => 98],
            ["nama_obat" => "Simvastatin", "status" => "aman", "stok" => 81],
            ["nama_obat" => "CTM", "status" => "menipis", "stok" => 10],
            ["nama_obat" => "Betadine", "status" => "aman", "stok" => 150],
            ["nama_obat" => "Salbutamol", "status" => "habis", "stok" => 0],
            ["nama_obat" => "Zinc Tablet", "status" => "menipis", "stok" => 13],
            ["nama_obat" => "Asam Mefenamat", "status" => "aman", "stok" => 60],
            ["nama_obat" => "Diazepam", "status" => "aman", "stok" => 45],
            ["nama_obat" => "Amphicillin", "status" => "habis", "stok" => 0]
        ];
        return view('components.admin.dashboard', compact('chartData', 'prediksi'));
    }

    public function upload(Request $request)
    {
        // Validasi file
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $data = [];

        // Baca file CSV
        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $data[] = $row;
            }
            fclose($handle);
        }
        return view('components.admin.prediksi_obat', compact('data'));
    }

    public function obat_kadaluarsa()
    {
        $data = [
            [
                "nama_obat" => "Paracetamol 500mg",
                "jumlah_obat" => 20,
                "tanggal_kadaluarsa" => "1 Januari 2027",
                "status_terjual" => "Belum Terjual",
                "kondisi" => "Baik"
            ],
            [
                "nama_obat" => "Amoxicillin 250mg",
                "jumlah_obat" => 15,
                "tanggal_kadaluarsa" => "15 Desember 2025",
                "status_terjual" => "Terjual",
                "kondisi" => "Baik"
            ],
            [
                "nama_obat" => "Vitamin C 1000mg",
                "jumlah_obat" => 30,
                "tanggal_kadaluarsa" => "12 Januari 2027",
                "status_terjual" => "Belum Terjual",
                "kondisi" => "Baik"
            ],
            [
                "nama_obat" => "Ibuprofen 400mg",
                "jumlah_obat" => 25,
                "tanggal_kadaluarsa" => "10 Januari 2026",
                "status_terjual" => "Terjual",
                "kondisi" => "Baik"
            ],
            [
                "nama_obat" => "Cetirizine 10mg",
                "jumlah_obat" => 10,
                "tanggal_kadaluarsa" => "22 Januari 2027",
                "status_terjual" => "Belum Terjual",
                "kondisi" => "Kurang Baik"
            ]
        ];


        return view('components.admin.obat_kadaluarsa', compact('data'));
    }
}
