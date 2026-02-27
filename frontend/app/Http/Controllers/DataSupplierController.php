<?php

namespace App\Http\Controllers;

use App\Models\Admin\DataSupplier;
use Illuminate\Http\Request;

class DataSupplierController extends Controller
{
    // ! ======================= READ ===================================
    public function index()
    {
        $data = DataSupplier::all();
        $title = "Data Supplier";
        return view('components.admin.main.data-supplier', compact('data', 'title'));
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
    public function update(Request $request, DataSupplier $dataSupplier)
    {
        //
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
