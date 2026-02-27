<?php

namespace App\Http\Controllers;

use App\Models\Admin\StokBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StokCabangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        // Mengambil data StokBatch beserta relasi DataCabang dan Obat sekaligus
        $stok = StokBatch::with(['DataCabang', 'Obat']);

        // Filter hanya jika benar-benar dipilih
        if ($request->filled('id_cabang')) {
            $stok->where('id_cabang', $request->id_cabang);
        }

        // penerapan FEFO
        $stok = $stok->orderBy('tanggal_kedaluwarsa', 'asc')
            ->get();


        $dataCabang = DB::table('tb_cabang')->get();

        $title = "Data Stok Cabang";

        return view(
            'components.admin.stok-cabang',
            compact('stok', 'title', 'dataCabang')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(StokBatch $StokCabang)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StokBatch $StokCabang)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StokBatch $StokCabang)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StokBatch $StokCabang)
    {
        //
    }
}
