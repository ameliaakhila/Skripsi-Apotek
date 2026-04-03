<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\DataCabang;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class DataCabangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // ! ===================== READ =====================
    public function index()
    {
        // ! OPTIMIZATION 1: Cache cabang list (600s = 10 min) with cache invalidation
        $data = Cache::remember('list_cabang', 600, function () {
            return DataCabang::select('id_cabang', 'kode_cabang', 'nama_cabang', 'alamat_cabang')
                ->orderBy('id_cabang', 'asc')
                ->get();
        });

        $title = "Data Cabang";
        // ! OPTIMIZATION 2: Cache kode cabang generation (avoid recalculation)
        $kodeCabang = Cache::remember('kode_cabang_next', 600, function () {
            return 'SF' . str_pad(DataCabang::max('id_cabang') + 1, 3, '0', STR_PAD_LEFT);
        });
        return view('admin.data-cabang', compact('data', 'title', 'kodeCabang'));
    }


    // ! ===================== CREATE =====================
    public function store(Request $request)
    {
        try {
            // dd($request->all());
            // ? Validasi apakah sesuai dengan type tabel
            $request->validate([
                'kode_cabang' => 'required|string|max:10|unique:tb_cabang,kode_cabang',
                'nama_cabang' => 'required|string|max:100',
                'alamat_cabang' => 'required|string|max:255',
            ]);
            // dd($request->all());

            DataCabang::create([
                'kode_cabang' => $request->kode_cabang,
                'nama_cabang' => $request->nama_cabang,
                'alamat_cabang' => $request->alamat_cabang,
            ]);

            // ! INVALIDATE cache saat ada data baru
            Cache::forget('list_cabang');
            Cache::forget('kode_cabang_next');

            // ! return
            return redirect('/data-cabang')->with([
                'success' => "Data Cabang {$request->nama_cabang} Berhasil Dimasukan"
            ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal Input Data!')
                ->withInput();
        }
    }

    // ! ===================== UPDATE =====================
    public function update(Request $request, $id_cabang)
    {
        try {
            // dd($request->all(), $id_cabang);
            // ? Validasi apakah sesuai dengan type tabel
            $request->validate([
                'kode_cabang' => 'required|string|max:10|unique:tb_cabang,kode_cabang,' . $id_cabang . ',id_cabang',
                'nama_cabang' => 'required|string|max:100',
                'alamat_cabang' => 'required|string|max:255',
            ]);
            // dd($request->all(), $id_cabang);
            $dataCabang = DataCabang::findOrFail($id_cabang);
            $dataCabang->update([
                'kode_cabang' => $request->kode_cabang,
                'nama_cabang' => $request->nama_cabang,
                'alamat_cabang' => $request->alamat_cabang,
            ]);

            // ! INVALIDATE cache saat update
            Cache::forget('list_cabang');
            Cache::forget('kode_cabang_next');

            return redirect('/data-cabang')->with('success', "Data Cabang {$request->nama_cabang} berhasil diupdate");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal Update Data!')
                ->withInput();
        }
    }

    // ! ===================== DELETE =====================
    public function destroy($id_cabang)
    {
        try {
            $dataCabang = DataCabang::findOrFail($id_cabang);
            $dataCabang->delete();

            // ! INVALIDATE cache saat delete
            Cache::forget('list_cabang');
            Cache::forget('kode_cabang_next');

            return redirect('/data-cabang')->with('success', "Data Cabang {$dataCabang->nama_cabang} berhasil dihapus");
        } catch (\Illuminate\Database\QueryException $e) {
            // Biasanya error jika ada foreign key constraint
            return redirect('/data-cabang')->with('error', 'Data gagal dihapus: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Error umum lainnya
            return redirect('/data-cabang')->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
