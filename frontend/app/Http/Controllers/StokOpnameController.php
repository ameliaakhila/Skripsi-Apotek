<?php

namespace App\Http\Controllers;

use App\Models\Admin\DataCabang;
use App\Models\Admin\StokOpname;
use Illuminate\Http\Request;

class StokOpnameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StokOpname::with([
            'Obat:id_obat,nama_obat,id_satuan',
            'StokBatch:id_stok_batch,tanggal_kedaluwarsa',
            'DataCabang:id_cabang,nama_cabang',
        ]);

        if (request('id_cabang')) {
            $query->where('tb_stok_opname.id_cabang', $request->id_cabang);
        }

        $query->join('tb_stok_batch', 'tb_stok_batch.id_stok_batch', '=', 'tb_stok_opname.id_stok_batch')
            ->orderBy('tb_stok_batch.tanggal_kedaluwarsa', 'asc')
            ->select('tb_stok_opname.*');

        $data = $query->simplePaginate(10);

        // dd($data->toArray());

        $dataCabang = DataCabang::all();

        // jika stok fisik kurang dari stok sistem, maka keterangan "Kurang", jika lebih maka "Lebih", jika sama maka "Sesuai"
        foreach ($data as $d) {
            if ($d->stok_fisik < $d->stok_sistem) {
                $d->keterangan = "Kurang";
            } elseif ($d->stok_fisik > $d->stok_sistem) {
                $d->keterangan = "Lebih";
            } else {
                $d->keterangan = "Sesuai";
            }
        }
        return view('components.admin.stock-opname', compact('data', 'dataCabang'));
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
    public function show(StokOpname $stokOpname)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StokOpname $stokOpname)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StokOpname $stokOpname)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StokOpname $stokOpname)
    {
        //
    }
}
