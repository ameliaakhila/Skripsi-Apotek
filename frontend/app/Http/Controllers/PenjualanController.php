<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ObatMasuk;
use App\Models\KategoriObat;
use App\Models\Obat;
use App\Models\Penjualan;
use Illuminate\Support\Facades\Log;

class PenjualanController extends Controller
{
    private $data;
    public function __construct()
    {
        $data = [
            ["kode_obat" => "OB001", "nama_obat" => "Paracetamol 500mg", "pabrik_obat" => "Kalbe", "kategori_obat" => "Analgesik", "stok_obat" => 120, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 2000],
            ["kode_obat" => "OB002", "nama_obat" => "Amoxicillin 500mg", "pabrik_obat" => "Dexa", "kategori_obat" => "Antibiotik", "stok_obat" => 80, "satuan_obat" => "Kapsul", "opsi" => "", "harga_obat" => 5000],
            ["kode_obat" => "OB003", "nama_obat" => "Cefixime 200mg", "pabrik_obat" => "Novell", "kategori_obat" => "Antibiotik", "stok_obat" => 50, "satuan_obat" => "Kapsul", "opsi" => "", "harga_obat" => 7000],
            ["kode_obat" => "OB004", "nama_obat" => "Vitamin C 500mg", "pabrik_obat" => "Kimia Farma", "kategori_obat" => "Vitamin", "stok_obat" => 200, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 3000],
            ["kode_obat" => "OB005", "nama_obat" => "Ibuprofen 400mg", "pabrik_obat" => "Kalbe", "kategori_obat" => "Analgesik", "stok_obat" => 90, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 4000],
            ["kode_obat" => "OB006", "nama_obat" => "Cetirizine 10mg", "pabrik_obat" => "Dexa", "kategori_obat" => "Antihistamin", "stok_obat" => 70, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 3500],
            ["kode_obat" => "OB007", "nama_obat" => "Ranitidine 150mg", "pabrik_obat" => "Novell", "kategori_obat" => "Antasida", "stok_obat" => 60, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 4500],
            ["kode_obat" => "OB008", "nama_obat" => "Metformin 500mg", "pabrik_obat" => "Kalbe", "kategori_obat" => "Diabetes", "stok_obat" => 100, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 6000],
            ["kode_obat" => "OB009", "nama_obat" => "Amlodipine 5mg", "pabrik_obat" => "Dexa", "kategori_obat" => "Hipertensi", "stok_obat" => 75, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 5500],
            ["kode_obat" => "OB010", "nama_obat" => "Simvastatin 10mg", "pabrik_obat" => "Kimia Farma", "kategori_obat" => "Kolesterol", "stok_obat" => 85, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 5000],
            ["kode_obat" => "OB011", "nama_obat" => "Loratadine 10mg", "pabrik_obat" => "Novell", "kategori_obat" => "Antihistamin", "stok_obat" => 90, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 3600],
            ["kode_obat" => "OB012", "nama_obat" => "Prednisone 5mg", "pabrik_obat" => "Kalbe", "kategori_obat" => "Kortikosteroid", "stok_obat" => 55, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 8000],
            ["kode_obat" => "OB013", "nama_obat" => "Omeprazole 20mg", "pabrik_obat" => "Dexa", "kategori_obat" => "Antasida", "stok_obat" => 120, "satuan_obat" => "Kapsul", "opsi" => "", "harga_obat" => 6500],
            ["kode_obat" => "OB014", "nama_obat" => "Salbutamol 2mg", "pabrik_obat" => "Kimia Farma", "kategori_obat" => "Bronkodilator", "stok_obat" => 70, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 7000],
            ["kode_obat" => "OB015", "nama_obat" => "Furosemide 40mg", "pabrik_obat" => "Novell", "kategori_obat" => "Diuretik", "stok_obat" => 65, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 5000],
            ["kode_obat" => "OB016", "nama_obat" => "Captopril 25mg", "pabrik_obat" => "Kalbe", "kategori_obat" => "Hipertensi", "stok_obat" => 80, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 5500],
            ["kode_obat" => "OB017", "nama_obat" => "Hydrochlorothiazide 25mg", "pabrik_obat" => "Dexa", "kategori_obat" => "Diuretik", "stok_obat" => 55, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 4800],
            ["kode_obat" => "OB018", "nama_obat" => "Clindamycin 300mg", "pabrik_obat" => "Novell", "kategori_obat" => "Antibiotik", "stok_obat" => 50, "satuan_obat" => "Kapsul", "opsi" => "", "harga_obat" => 7500],
            ["kode_obat" => "OB019", "nama_obat" => "Dexamethasone 0.5mg", "pabrik_obat" => "Kalbe", "kategori_obat" => "Kortikosteroid", "stok_obat" => 60, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 8200],
            ["kode_obat" => "OB020", "nama_obat" => "Metronidazole 500mg", "pabrik_obat" => "Dexa", "kategori_obat" => "Antibiotik", "stok_obat" => 70, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 6000],
            ["kode_obat" => "OB021", "nama_obat" => "Aspirin 100mg", "pabrik_obat" => "Kimia Farma", "kategori_obat" => "Analgesik", "stok_obat" => 90, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 2200],
            ["kode_obat" => "OB022", "nama_obat" => "Naproxen 250mg", "pabrik_obat" => "Novell", "kategori_obat" => "Analgesik", "stok_obat" => 85, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 4200],
            ["kode_obat" => "OB023", "nama_obat" => "Levocetirizine 5mg", "pabrik_obat" => "Kalbe", "kategori_obat" => "Antihistamin", "stok_obat" => 60, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 3600],
            ["kode_obat" => "OB024", "nama_obat" => "Montelukast 10mg", "pabrik_obat" => "Dexa", "kategori_obat" => "Antiasma", "stok_obat" => 50, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 5000],
            ["kode_obat" => "OB025", "nama_obat" => "Diltiazem 60mg", "pabrik_obat" => "Kimia Farma", "kategori_obat" => "Hipertensi", "stok_obat" => 75, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 5800],
            ["kode_obat" => "OB026", "nama_obat" => "Pantoprazole 40mg", "pabrik_obat" => "Novell", "kategori_obat" => "Antasida", "stok_obat" => 95, "satuan_obat" => "Kapsul", "opsi" => "", "harga_obat" => 6700],
            ["kode_obat" => "OB027", "nama_obat" => "Tramadol 50mg", "pabrik_obat" => "Kalbe", "kategori_obat" => "Analgesik", "stok_obat" => 65, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 6000],
            ["kode_obat" => "OB028", "nama_obat" => "Gabapentin 100mg", "pabrik_obat" => "Dexa", "kategori_obat" => "Neuropati", "stok_obat" => 55, "satuan_obat" => "Kapsul", "opsi" => "", "harga_obat" => 7000],
            ["kode_obat" => "OB029", "nama_obat" => "Captopril 12.5mg", "pabrik_obat" => "Kimia Farma", "kategori_obat" => "Hipertensi", "stok_obat" => 80, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 5400],
            ["kode_obat" => "OB030", "nama_obat" => "Simvastatin 20mg", "pabrik_obat" => "Novell", "kategori_obat" => "Kolesterol", "stok_obat" => 70, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 5100],
            ["kode_obat" => "OB031", "nama_obat" => "Lisinopril 10mg", "pabrik_obat" => "Kalbe", "kategori_obat" => "Hipertensi", "stok_obat" => 65, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 5600],
            ["kode_obat" => "OB032", "nama_obat" => "Atenolol 50mg", "pabrik_obat" => "Dexa", "kategori_obat" => "Hipertensi", "stok_obat" => 75, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 5500],
            ["kode_obat" => "OB033", "nama_obat" => "Losartan 50mg", "pabrik_obat" => "Kimia Farma", "kategori_obat" => "Hipertensi", "stok_obat" => 80, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 5800],
            ["kode_obat" => "OB034", "nama_obat" => "Clarithromycin 250mg", "pabrik_obat" => "Novell", "kategori_obat" => "Antibiotik", "stok_obat" => 50, "satuan_obat" => "Kapsul", "opsi" => "", "harga_obat" => 7500],
            ["kode_obat" => "OB035", "nama_obat" => "Azithromycin 500mg", "pabrik_obat" => "Kalbe", "kategori_obat" => "Antibiotik", "stok_obat" => 70, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 7200],
            ["kode_obat" => "OB036", "nama_obat" => "Metformin 1000mg", "pabrik_obat" => "Dexa", "kategori_obat" => "Diabetes", "stok_obat" => 90, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 6500],
            ["kode_obat" => "OB037", "nama_obat" => "Glimepiride 2mg", "pabrik_obat" => "Kimia Farma", "kategori_obat" => "Diabetes", "stok_obat" => 60, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 6000],
            ["kode_obat" => "OB038", "nama_obat" => "Insulin NPH", "pabrik_obat" => "Novell", "kategori_obat" => "Diabetes", "stok_obat" => 40, "satuan_obat" => "Ampul", "opsi" => "", "harga_obat" => 10000],
            ["kode_obat" => "OB039", "nama_obat" => "Insulin Regular", "pabrik_obat" => "Kalbe", "kategori_obat" => "Diabetes", "stok_obat" => 45, "satuan_obat" => "Ampul", "opsi" => "", "harga_obat" => 10000],
            ["kode_obat" => "OB040", "nama_obat" => "Prednisolone 5mg", "pabrik_obat" => "Dexa", "kategori_obat" => "Kortikosteroid", "stok_obat" => 55, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 8200],
            ["kode_obat" => "OB041", "nama_obat" => "Hydrocortisone Cream", "pabrik_obat" => "Kimia Farma", "kategori_obat" => "Kortikosteroid", "stok_obat" => 60, "satuan_obat" => "Tube", "opsi" => "", "harga_obat" => 9000],
            ["kode_obat" => "OB042", "nama_obat" => "Fluticasone Nasal Spray", "pabrik_obat" => "Novell", "kategori_obat" => "Kortikosteroid", "stok_obat" => 70, "satuan_obat" => "Botol", "opsi" => "", "harga_obat" => 9500],
            ["kode_obat" => "OB043", "nama_obat" => "Salbutamol Inhaler", "pabrik_obat" => "Kalbe", "kategori_obat" => "Bronkodilator", "stok_obat" => 80, "satuan_obat" => "Botol", "opsi" => "", "harga_obat" => 7500],
            ["kode_obat" => "OB044", "nama_obat" => "Montelukast 5mg", "pabrik_obat" => "Dexa", "kategori_obat" => "Antiasma", "stok_obat" => 50, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 5000],
            ["kode_obat" => "OB045", "nama_obat" => "Ranitidine Syrup 150ml", "pabrik_obat" => "Kimia Farma", "kategori_obat" => "Antasida", "stok_obat" => 65, "satuan_obat" => "Botol", "opsi" => "", "harga_obat" => 7000],
            ["kode_obat" => "OB046", "nama_obat" => "Omeprazole 40mg", "pabrik_obat" => "Novell", "kategori_obat" => "Antasida", "stok_obat" => 75, "satuan_obat" => "Kapsul", "opsi" => "", "harga_obat" => 6800],
            ["kode_obat" => "OB047", "nama_obat" => "Acetaminophen 500mg", "pabrik_obat" => "Kalbe", "kategori_obat" => "Analgesik", "stok_obat" => 120, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 2000],
            ["kode_obat" => "OB048", "nama_obat" => "Ibuprofen 200mg", "pabrik_obat" => "Dexa", "kategori_obat" => "Analgesik", "stok_obat" => 90, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 3500],
            ["kode_obat" => "OB049", "nama_obat" => "Diclofenac 50mg", "pabrik_obat" => "Kimia Farma", "kategori_obat" => "Analgesik", "stok_obat" => 85, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 4000],
            ["kode_obat" => "OB050", "nama_obat" => "Loperamide 2mg", "pabrik_obat" => "Novell", "kategori_obat" => "Antidiare", "stok_obat" => 60, "satuan_obat" => "Tablet", "opsi" => "", "harga_obat" => 3000]
        ];
    }
    public function index()
    {
        $data = ObatMasuk::all();
        $obat = Obat::all();
        $kategori = KategoriObat::all();
        $penjualan = Penjualan::all();
        // dump(Penjualan::all());

        // dd($data->all());
        return view('components.main.penjualan_obat', compact('data', 'obat', 'kategori', 'penjualan'));
    }


    // if ($obat_masuk) {
    //     return response()->json(
    //         [
    //             'success' => true,
    //             'message' => 'Obat Ditemukan',
    //             'data' => [
    //                 'id_obat_masuk' => $obat_masuk->id_obat_masuk,
    //                 'nama_obat' => $obat_masuk->Obat->nama_obat,
    //                 'kode_obat' => $obat_masuk->kode_obat,
    //                 'id_kategori_obat' => $obat_masuk->id_kategori_obat,
    //                 'id_obat' => $obat_masuk->id_obat,
    //             ]
    //         ]
    //     );
    // }
    // else {
    //     return response()->json(
    //         [
    //             'error' => false,
    //             'message' => 'Obat Tidak Ditemukan'
    //         ]
    //     );
    // }
    public function insert(Request $request)
    {
        $request->validate([
            'id_obat_masuk' => 'required|exists:tb_obat_masuk,id_obat_masuk'
        ]);
        $obat_masuk = ObatMasuk::find($request->id_obat_masuk);

        try {
            // cek apakah obat sudah ada di tb_penjualan
            $penjualan = Penjualan::where('id_obat_masuk', $obat_masuk->id_obat_masuk)->first();

            if ($penjualan) {
                // jika sudah ada, tambah stok_keluar 1
                $penjualan->increment('stok_keluar', 1);
            } else {
                $penjualan = Penjualan::create([
                    'kode_obat' => $obat_masuk->kode_obat,
                    'stok_keluar' => 1, // mulai dengan 1
                    'satuan_obat' => $obat_masuk->satuan_obat,
                    'harga_obat' => $obat_masuk->harga_obat,
                    'id_obat' => $obat_masuk->id_obat,
                    'id_obat_masuk' => $obat_masuk->id_obat_masuk,
                    'id_kategori_obat' => $obat_masuk->id_kategori_obat,
                ]);
            }

            // kembalikan JSON sukses
            return response()->json(['success' => true, 'message' => 'Obat berhasil ditambahkan kedatabase!', 'data' => $penjualan]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
