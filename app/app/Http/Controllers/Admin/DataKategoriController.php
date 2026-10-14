<?php

namespace App\Http\Controllers\Admin;
use App\Models\Admin\DataKategori;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class DataKategoriController extends Controller
{
    // ! ======================= READ ===================================
    public function index()
    {
        // ! OPTIMIZATION: Cache kategori list (600s = 10 min) with invalidation on mutations
        $data = Cache::remember('list_kategori', 600, function () {
            return DataKategori::select('id_kategori', 'nama_kategori')
                ->orderBy('nama_kategori', 'asc')
                ->get();
        });
        $title = "Data Kategori Obat";
        return view('admin.data-kategori', compact('data', 'title'));
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

            // ! INVALIDATE cache saat ada data baru
            Cache::forget('list_kategori');
            Cache::forget('master_data_obat');  // Invalidate obat master data too

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

            // ! INVALIDATE cache saat delete
            Cache::forget('list_kategori');
            Cache::forget('master_data_obat');  // Invalidate obat master data too

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
