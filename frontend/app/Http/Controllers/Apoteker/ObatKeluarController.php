<?php

namespace App\Http\Controllers\Apoteker;

use App\Http\Controllers\Controller;
use App\Models\Admin\ObatKeluar;
use App\Services\Apoteker\ObatKeluarService;
use Illuminate\Http\Request;

class ObatKeluarController extends Controller
{
    public function __construct(private readonly ObatKeluarService $obatKeluarService)
    {
    }

    /**
     * Display a listing of the resource.
     * OPTIMIZED: Remove redundant join(), use eager loading, add pagination, split cache keys
     */
    public function index()
    {
        $indexData = $this->obatKeluarService->ambilDataIndex();
        $data = $indexData['data'];
        $obatList = $indexData['obatList'];
        $kodeBatch = $indexData['kodeBatch'];
        $cabang = $indexData['cabang'];
        return view('apoteker.obat-keluar', compact('data', 'kodeBatch', 'obatList', 'cabang'));
    }


    // ! ====================== CREATE =========================
    public function insert(Request $request)
    {
        return $this->store($request);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_obat_masuk' => 'required|exists:tb_obat_masuk,id_obat_masuk',
                'id_cabang' => 'required|exists:tb_cabang,id_cabang',
                'tanggal_keluar' => 'required|date',
                'stok_keluar' => 'required|integer|min:1',
            ], [
                'id_obat_masuk.required' => 'Obat harus dipilih',
                'id_obat_masuk.exists' => 'Obat tidak valid',
                'id_cabang.required' => 'Cabang harus dipilih',
                'id_cabang.exists' => 'Cabang tidak valid',
                'tanggal_keluar.required' => 'Tanggal keluar harus diisi',
                'tanggal_keluar.date' => 'Tanggal keluar harus berupa tanggal yang valid',
                'stok_keluar.required' => 'Stok keluar harus diisi',
                'stok_keluar.integer' => 'Stok keluar harus berupa angka',
                'stok_keluar.min' => 'Stok keluar minimal 1',
            ]);
            $result = $this->obatKeluarService->simpan($validated);

            return redirect('/obat-keluar')->with([
                'success' => 'Data Obat Keluar ' . $result['nama_obat'] . ' ke ' . $result['nama_cabang'] . ' berhasil ditambahkan',
            ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal Input Data: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ObatKeluar $obatKeluar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ObatKeluar $obatKeluar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id_obat_keluar)
    {
        try {
            $validated = $request->validate([
                'id_cabang' => 'required|exists:tb_cabang,id_cabang',
                'stok_keluar' => 'required|integer|min:1',
            ], [
                'id_cabang.required' => 'Cabang harus dipilih',
                'id_cabang.exists' => 'Cabang tidak valid',
                'stok_keluar.required' => 'Stok keluar harus diisi',
                'stok_keluar.integer' => 'Stok keluar harus berupa angka',
                'stok_keluar.min' => 'Stok keluar minimal 1',
            ]);

            $this->obatKeluarService->ubah((int) $id_obat_keluar, $validated);

            return redirect('/obat-keluar')->with([
                'success' => 'Data Obat Keluar berhasil diperbarui',
            ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal Update Data: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_obat_keluar)
    {
        try {
            $this->obatKeluarService->hapus((int) $id_obat_keluar);

            return redirect('/obat-keluar')->with([
                'success' => 'Data Obat Keluar berhasil dihapus',
            ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal Hapus Data: ' . $e->getMessage());
        }
    }
}
