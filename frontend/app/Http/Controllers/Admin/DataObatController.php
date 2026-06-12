<?php

namespace App\Http\Controllers\Admin;

use App\Services\Admin\DataObatService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DataObatController extends Controller
{
    public function __construct(private readonly DataObatService $dataObatService)
    {
    }

    // ! ======================= READ ===================================
    public function index()
    {
        $indexData = $this->dataObatService->ambilDataIndex();

        $data = $indexData['data'];
        $kategori = $indexData['kategori'];
        $supplier = $indexData['supplier'];
        $satuan = $indexData['satuan'];

        return view('admin.data-obat', compact('data', 'kategori', 'supplier', 'satuan'));
    }

    // ! ======================= CREATE ===================================
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_obat' => 'required|string|max:255',
                'safety_stock' => 'required|integer|min:1',
                'id_supplier' => 'required|exists:tb_supplier,id_supplier',
                'id_satuan' => 'required|exists:tb_satuan,id_satuan',
                'id_kategori' => 'required|exists:tb_kategori,id_kategori'
            ], [
                'nama_obat.required' => 'Nama obat harus diisi',
                'safety_stock.required' => 'Safety stock harus diisi',
                'safety_stock.min' => 'Safety stock minimal 1',
                'id_supplier.required' => 'Supplier harus dipilih',
                'id_satuan.required' => 'Satuan harus dipilih',
                'id_kategori.required' => 'Kategori harus dipilih'
            ]);

            $this->dataObatService->simpan($validated);

            return redirect('/data-obat')->with([
                'success' => "Data Obat {$request->nama_obat} berhasil ditambahkan"
            ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambah data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, $id_obat)
    {
        try {
            $validated = $request->validate([
                'nama_obat' => 'required|string|max:255',
                'safety_stock' => 'required|integer|min:1',
                'id_supplier' => 'required|exists:tb_supplier,id_supplier',
                'id_satuan' => 'required|exists:tb_satuan,id_satuan',
                'id_kategori' => 'required|exists:tb_kategori,id_kategori'
            ], [
                'nama_obat.required' => 'Nama obat harus diisi',
                'safety_stock.required' => 'Safety stock harus diisi',
                'safety_stock.min' => 'Safety stock minimal 1',
                'id_supplier.required' => 'Supplier harus dipilih',
                'id_satuan.required' => 'Satuan harus dipilih',
                'id_kategori.required' => 'Kategori harus dipilih'
            ]);

            $this->dataObatService->ubah((int) $id_obat, $validated);

            return redirect('/data-obat')->with('success', "Data Obat {$request->nama_obat} berhasil diupdate");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal update data: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ! ====================================== DELETE =========================================
    public function destroy($id_obat)
    {
        try {
            $namaObat = $this->dataObatService->hapus((int) $id_obat);

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
}


