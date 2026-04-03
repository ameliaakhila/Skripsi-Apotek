<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\DataSupplier;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class DataSupplierController extends Controller
{
    // ! ======================= READ ===================================
    public function index()
    {
        // ! OPTIMIZATION: Cache supplier list (600s = 10 min) with invalidation on mutations
        $data = Cache::remember('list_supplier', 600, function () {
            return DataSupplier::select('id_supplier', 'nama_supplier', 'alamat_supplier', 'no_telp')
                ->orderBy('nama_supplier', 'asc')
                ->get();
        });
        return view('admin.data-supplier', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    // ! ====================== CREATE ===================================
    public function store(Request $request)
    {
        // dd($request->all());
        try {
            // ? Validasi apakah sesuai dengan type tabel
            $request->validate([
                'nama_supplier' => 'required|string|max:100',
                'alamat_supplier' => 'required|string|max:200',
                'no_telp' => 'required|string|max:15',
            ]);
            // dd($request->all());

            DataSupplier::create([
                'nama_supplier' => $request->nama_supplier,
                'alamat_supplier' => $request->alamat_supplier,
                'no_telp' => $request->no_telp,
            ]);

            // ! INVALIDATE cache saat ada data baru
            Cache::forget('list_supplier');
            Cache::forget('master_data_obat');  // Invalidate obat master data too

            // ! return
            return redirect('/data-supplier')->with([
                'success' => "Data Supplier {$request->nama_supplier} Berhasil Dimasukan"
            ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal Input Data!')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DataSupplier $dataSupplier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DataSupplier $dataSupplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // ! ======================= UPDATE ===================================
    public function update(Request $request, $id_supplier)
    {
        try {
            // ? Validasi apakah sesuai dengan type tabel
            $request->validate([
                'nama_supplier' => 'required|string|max:100',
                'alamat_supplier' => 'required|string|max:200',
                'no_telp' => 'required|string|max:15',
            ], [
                'nama_supplier.required' => 'Nama Supplier harus diisi',
                'nama_supplier.max' => 'Nama Supplier maksimal 100 karakter',
                'alamat_supplier.required' => 'Alamat Supplier harus diisi',
                'alamat_supplier.max' => 'Alamat Supplier maksimal 200 karakter',
                'no_telp.required' => 'No Telpon harus diisi',
                'no_telp.max' => 'No Telpon maksimal 15 karakter',
            ]);

            $dataSupplier = DataSupplier::findOrFail($id_supplier);

            $dataSupplier->update([
                'nama_supplier' => $request->nama_supplier,
                'alamat_supplier' => $request->alamat_supplier,
                'no_telp' => $request->no_telp,
            ]);

            // ! INVALIDATE cache saat update
            Cache::forget('list_supplier');
            Cache::forget('master_data_obat');  // Invalidate obat master data too

            // ! return
            return redirect('/data-supplier')->with([
                'success' => "Data Supplier {$request->nama_supplier} Berhasil Diupdate"
            ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal Update Data!')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    // ! ======================= DELETE ===================================
    public function destroy($id_supplier)
    {
        // dd($id_supplier);
        try {
            $dataSupplier = DataSupplier::findOrFail($id_supplier);
            $dataSupplier->delete();

            // ! INVALIDATE cache saat delete
            Cache::forget('list_supplier');
            Cache::forget('master_data_obat');  // Invalidate obat master data too

            return redirect('/data-supplier')->with([
                'success' => "Data Supplier {$dataSupplier->nama_supplier} Berhasil Dihapus"
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
