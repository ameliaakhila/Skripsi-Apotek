<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin\ObatMasuk;
use App\Models\Admin\DataKategori;
use App\Models\Admin\Obat;
use App\Models\Admin\Penjualan;

class PenjualanController extends Controller
{
    private $data;
    public function index()
    {
        $data = ObatMasuk::all();
        $obat = Obat::all();
        $kategori = DataKategori::all();
        $penjualan = Penjualan::all();
        return view('components.admin.main.penjualan_obat', compact('data', 'obat', 'kategori', 'penjualan'));
    }


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
                    'id_satuan' => $obat_masuk->id_satuan,
                    'harga_obat' => $obat_masuk->harga_obat,
                    'id_obat' => $obat_masuk->id_obat,
                    'id_obat_masuk' => $obat_masuk->id_obat_masuk,
                    'id_kategori' => $obat_masuk->id_kategori,
                ]);
            }

            // kembalikan JSON sukses
            return response()->json(['success' => true, 'message' => 'Obat berhasil ditambahkan kedatabase!', 'data' => $penjualan]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
