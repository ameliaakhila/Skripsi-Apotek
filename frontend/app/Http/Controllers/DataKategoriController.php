<?php

namespace App\Http\Controllers;
use App\Models\Admin\DataKategori;

use Illuminate\Http\Request;

class DataKategoriController extends Controller
{
    // ! ======================= READ ===================================
    public function index()
    {
        $data = DataKategori::all();
        $title = "Data Kategori Obat";
        return view('components.admin.main.data_kategori', compact('data', 'title'));
    }

    // ! ======================= CREATE ===================================
    public function store(Request $request)
    {
        // dd($request->all());
        try {
            // ? Validasi apakah sesuai dengan type tabel
            $request->validate([
                'nama_kategori' => 'required|string|max:100',
            ]);
            // dd($request->all());

            DataKategori::create([
                'nama_kategori' => $request->nama_kategori,
            ]);

            // ! return
            return redirect('/data-kategori')->with([
                'success' => "Data Kategori Obat {$request->nama_kategori} Berhasil Dimasukan"
            ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal Input Data!')
                ->withInput();
        }
    }

    // ! ======================= DELETE ===================================
    public function destroy($id_kategori)
    {
        // dd($id_kategori);
        try {
            $dataKategori = DataKategori::findOrFail($id_kategori);
            $dataKategori->delete();

            return redirect('/data-kategori')->with([
                'success' => "Data Kategori {$dataKategori->nama_kategori} Berhasil Dihapus"
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()
                ->with('error', 'Gagal Hapus Data! Pastikan tidak ada obat yang menggunakan kategori ini.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal Hapus Data!');
        }
    }
}
