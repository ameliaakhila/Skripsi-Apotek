<?php

namespace App\Http\Controllers;

use App\Models\KategoriObat;
use App\Models\Obat;
use Illuminate\Http\Request;

class DataObatController extends Controller
{
    public function index()
    {
        $data = Obat::with('KategoriObat')->get();
        $kategori = KategoriObat::all();
        return view('components.main.data_obat', compact('data','kategori'));
    }
    // ! ======================= CREATE ===================================
    public function store(Request $request)
    {
        try {
            // dd($request->all());
            // ? Validasi apakah sesuai dengan type tabel
            $request->validate([
                'nama_obat' => 'required|string',
                'pabrik_obat' => 'required|string',
                'satuan_obat' => 'required|string|max:50',
                'id_kategori_obat' => 'required|exists:tb_kategori_obat,id_kategori_obat'
            ]);
            // dd($request->all());

            Obat::create([
                'nama_obat' => $request->nama_obat,
                'pabrik_obat' => $request->pabrik_obat,
                'satuan_obat' => $request->satuan_obat,
                'id_kategori_obat' => $request->id_kategori_obat,
            ]);
            // dd('Cek apakah berhasil masuk atau ga', $obat);

            // ! return
            return redirect('/data-obat')->with([
                'success' => "Data Obat {$request->nama_obat} Berhasil Dimasukan",
                'nama_obat' => $request->nama_obat
            ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal Input Data!')
                ->withInput();
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

    public function update(Request $request, $id_obat)
    {
        try {
            dd($request);
            return redirect('/data-obat')->with('success', 'Data berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal update data: ' . $e->getMessage());
        }
    }
}


