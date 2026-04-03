<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\DataKategori;
use App\Models\Admin\DataSatuan;
use App\Models\Admin\DataSupplier;
use App\Models\Admin\Obat;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class DataObatController extends Controller
{
    // ! ======================= READ ===================================
    public function index()
    {
        // ! OPTIMIZATION 1: Cache seluruh obat data result (600s = 10 min)
        // ! Invalidate saat create/update/delete
        $data = Cache::remember('list_obat_paginated', 600, function () {
            return Obat::with([
                'DataKategori:id_kategori,nama_kategori',  // Select spesifik
                'DataSupplier:id_supplier,nama_supplier',  // Kurangi kolom
                'DataSatuan:id_satuan,nama_satuan'         // Yang dipakai di view
            ])
                ->select([
                    'id_obat',
                    'nama_obat',
                    'safety_stock',
                    'id_satuan',
                    'id_kategori',
                    'id_supplier',
                    'created_at'
                    // Exclude kolom yang tidak dipakai di view
                ])
                ->cursorPaginate(2000);  // OPTIMIZATION 2: Tidak ada COUNT query
        });

        // ! OPTIMIZATION 3: Cache master data lebih lama (1 jam)
        $masterData = Cache::remember('master_data_obat', 3600, function () {
            return [
                'kategori' => DataKategori::select('id_kategori', 'nama_kategori')->get(),
                'supplier' => DataSupplier::select('id_supplier', 'nama_supplier')->get(),
                'satuan' => DataSatuan::select('id_satuan', 'nama_satuan')->get(),
            ];
        });
        $kategori = $masterData['kategori'];
        $supplier = $masterData['supplier'];
        $satuan = $masterData['satuan'];
        return view('admin.data-obat', compact('data', 'kategori', 'supplier', 'satuan'));
    }
    // ! ======================= CREATE ===================================
    public function store(Request $request)
    {
        try {
            // ? Validasi sesuai dengan schema tb_obat
            $request->validate([
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

            Obat::create([
                'nama_obat' => $request->nama_obat,
                'safety_stock' => $request->safety_stock,
                'id_supplier' => $request->id_supplier,
                'id_satuan' => $request->id_satuan,
                'id_kategori' => $request->id_kategori,
            ]);

            // ! INVALIDATE cache saat ada data baru
            Cache::forget('list_obat_paginated');

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
            // ? Validasi sesuai dengan schema tb_obat
            $request->validate([
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

            $dataObat = Obat::findOrFail($id_obat);
            $dataObat->update([
                'nama_obat' => $request->nama_obat,
                'safety_stock' => $request->safety_stock,
                'id_supplier' => $request->id_supplier,
                'id_satuan' => $request->id_satuan,
                'id_kategori' => $request->id_kategori,
            ]);

            // ! INVALIDATE cache saat update
            Cache::forget('list_obat_paginated');

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
            $dataObat = Obat::findOrFail($id_obat);
            $namaObat = $dataObat->nama_obat;
            $dataObat->delete();

            // ! INVALIDATE cache saat delete
            Cache::forget('list_obat_paginated');

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


