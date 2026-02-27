<?php

namespace App\Http\Controllers;

use App\Models\Admin\DataKategori;
use App\Models\Admin\DataSatuan;
use App\Models\Admin\DataSupplier;
use App\Models\Admin\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DataObatController extends Controller
{
    // ! ======================= READ ===================================
    public function index()
    {
        $data = Obat::with([
            'DataKategori',
            'DataSupplier',
            'DataSatuan'
        ])->simplePaginate(10);

        $masterData = Cache::remember('master_data_obat', 60, function () {
            return [
                'kategori' => DataKategori::all(),
                'supplier' => DataSupplier::all(),
                'satuan' => DataSatuan::all(),
            ];
        });
        $kategori = $masterData['kategori'];
        $supplier = $masterData['supplier'];
        $satuan = $masterData['satuan'];
        return view('components.admin.main.data-obat', compact('data', 'kategori', 'supplier', 'satuan'));
    }
    // ! ======================= CREATE ===================================
    public function store(Request $request)
    {
        try {
            // ? Validasi apakah sesuai dengan type tabel
            $request->validate([
                'nama_obat' => 'required|string',
                'id_supplier' => 'required|exists:tb_supplier,id_supplier',
                'id_satuan' => 'required|exists:tb_satuan,id_satuan',
                'id_kategori' => 'required|exists:tb_kategori,id_kategori'
            ]);
            // dd($request->toArray());

            Obat::create([
                'nama_obat' => $request->nama_obat,
                'id_supplier' => $request->id_supplier,
                'id_satuan' => $request->id_satuan,
                'id_kategori' => $request->id_kategori,
            ]);

            // ! return
            return redirect('/data-obat')->with([
                'success' => "Data Obat {$request->nama_obat} Berhasil Dimasukan"
            ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal Input Data!')
                ->withInput();
        }
    }

    public function update(Request $request, $id_obat)
    {
        // ? Validasi apakah sesuai dengan type tabel
        $request->validate([
            'nama_obat' => 'required|string',
            'id_supplier' => 'required|exists:tb_supplier,id_supplier',
            'id_satuan' => 'required|exists:tb_satuan,id_satuan',
            'id_kategori' => 'required|exists:tb_kategori,id_kategori'
        ]);

        $dataObat = Obat::findOrFail($id_obat);
        $dataObat->nama_obat = $request->nama_obat;
        $dataObat->id_supplier = $request->id_supplier;
        $dataObat->id_satuan = $request->id_satuan;
        $dataObat->id_kategori = $request->id_kategori;
        $dataObat->save();

        try {
            return redirect('/data-obat')->with('success', "Data Obat {$request->nama_obat} berhasil diupdate");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal update data: ' . $e->getMessage());
        }
    }

    // ! ====================================== DELETE =========================================
    public function destroy($id_obat)
    {
        try {
            $dataObat = Obat::findOrFail($id_obat);
            $namaObat = $dataObat->nama_obat;
            $dataObat->delete();

            return redirect('/data-obat')->with([
                'success' => "Data Obat {$namaObat} berhasil dihapus",
                'nama_obat' => $namaObat
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Biasanya error jika ada foreign key constraint
            return redirect('/data-obat')->with('error', 'Data gagal dihapus: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Error umum lainnya
            return redirect('/data-obat')->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }


}


