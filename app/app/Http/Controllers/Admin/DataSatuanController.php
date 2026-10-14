<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\DataSatuan;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class DataSatuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // ! ===================== READ =====================
    public function index()
    {
        // ! OPTIMIZATION: Cache satuan list (600s = 10 min) with invalidation on mutations
        $data = Cache::remember('list_satuan', 600, function () {
            return DataSatuan::select('id_satuan', 'nama_satuan')
                ->orderBy('nama_satuan', 'asc')
                ->get();
        });
        $title = "Data Satuan";
        return view('admin.data-satuan', compact('data', 'title'));
    }

    // ! ===================== CREATE =====================
    public function store(Request $request)
    {
        try {
            // dd($request->toArray());
            $request->validate([
                'nama_satuan' => 'required|string'
            ]);
            // dd($request->toArray());

            DataSatuan::create([
                'nama_satuan' => $request->nama_satuan
            ]);

            // ! INVALIDATE cache saat ada data baru
            Cache::forget('list_satuan');
            Cache::forget('master_data_obat');  // Invalidate obat master data too

            // ! return
            return redirect('/data-satuan')->with([
                'success' => "Data Satuan {$request->nama_satuan} Berhasil Dimasukan"
            ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal Input Data!')
                ->withInput();
        }
    }

    // ! ===================== DELETE =====================
    public function destroy($id_satuan)
    {
        try {
            $dataSatuan = DataSatuan::findOrFail($id_satuan);
            $namaSatuan = $dataSatuan->nama_satuan;
            $dataSatuan->delete();

            // ! INVALIDATE cache saat delete
            Cache::forget('list_satuan');
            Cache::forget('master_data_obat');  // Invalidate obat master data too

            return redirect('/data-satuan')->with([
                'success' => "Data Obat {$namaSatuan} berhasil dihapus",
                'nama_satuan' => $namaSatuan
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Biasanya error jika ada foreign key constraint
            return redirect('/data-satuan')->with('error', 'Data gagal dihapus: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Error umum lainnya
            return redirect('/data-satuan')->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
