<?php

namespace App\Http\Controllers\Pharmacist;

use App\Http\Controllers\Controller;
use App\Models\Admin\ObatKeluar;
use App\Models\Admin\DataCabang;
use App\Models\Admin\ObatMasuk;
use App\Models\Admin\StokBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ObatKeluarController extends Controller
{
    /**
     * Display a listing of the resource.
     * OPTIMIZED: Remove redundant join(), use eager loading, add pagination, split cache keys
     */
    public function index()
    {
        // OPTIMIZED: Remove join() since we use with() eager loading, add pagination
        $data = ObatKeluar::with([
            'ObatMasuk:id_obat_masuk,id_obat,tanggal_kedaluwarsa,tanggal_masuk',
            'ObatMasuk.Obat:id_obat,nama_obat',
            'StokBatch:id_stok_batch,kode_batch,tanggal_kedaluwarsa,stok_sisa',
            'DataCabang:id_cabang,nama_cabang',
        ])
            ->select('tb_obat_keluar.*')
            ->orderBy('created_at', 'desc')
            ->simplePaginate(25);  // Add pagination

        $obatList = Cache::remember('master_obat_list', now()->addHours(2), function () {
            return ObatMasuk::with([
                'Obat:id_obat,nama_obat,id_kategori,id_supplier,id_satuan'
            ])
                ->leftJoin('tb_stok_batch', function ($join) {
                    $join->on('tb_obat_masuk.id_obat', '=', 'tb_stok_batch.id_obat')
                        ->on('tb_obat_masuk.kode_batch', '=', 'tb_stok_batch.kode_batch');
                })
                ->select(
                    'tb_obat_masuk.id_obat_masuk',
                    'tb_obat_masuk.id_obat',
                    'tb_obat_masuk.kode_batch',
                    'tb_obat_masuk.stok_masuk',
                    'tb_obat_masuk.tanggal_kedaluwarsa',
                    'tb_stok_batch.stok_sisa'
                )
                ->whereHas('Obat')
                ->where('tb_stok_batch.stok_sisa', '>', 0)
                ->orderBy('tb_obat_masuk.tanggal_kedaluwarsa', 'asc')
                ->distinct()
                ->get();
        });

        // OPTIMIZED: Cache with specific key for batch list with available stock only
        $kodeBatch = Cache::remember('master_kodebatch_available', now()->addHours(2), function () {
            return ObatMasuk::select('id_obat_masuk', 'kode_batch', 'tanggal_kedaluwarsa')
                ->whereHas('Obat')
                ->orderBy('tanggal_kedaluwarsa', 'asc')
                ->get();
        });

        $cabang = Cache::remember('master_cabang_list', now()->addHours(2), function () {
            return DataCabang::select('id_cabang', 'nama_cabang')->orderBy('nama_cabang')->get();
        });

        return view('pharmacist.obat-keluar', compact('data', 'kodeBatch', 'obatList', 'cabang'));
    }


    // ! ====================== CREATE =========================
    public function insert(Request $request)
    {
        try {
            // dd($request->all());
            $validasi = $request->validate([
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

            // Ambil data obat_masuk untuk mendapatkan kode_batch
            $obatMasuk = ObatMasuk::with('Obat')->find($validasi['id_obat_masuk']);

            // Cari id_stok_batch berdasarkan kode_batch dan id_cabang
            $stokBatch = StokBatch::where('kode_batch', $obatMasuk->kode_batch)
                ->where('id_cabang', $validasi['id_cabang'])
                ->firstOrFail();

            // Validasi stok_keluar tidak melebihi stok_sisa di stok_batch
            if ($validasi['stok_keluar'] > $stokBatch->stok_sisa) {
                return redirect()->back()
                    ->with('error', "Stok keluar tidak boleh melebihi stok sisa untuk cabang ini ({$stokBatch->stok_sisa} unit)")
                    ->withInput();
            }

            // Tambahkan id_stok_batch ke data yang akan disimpan
            $validasi['id_stok_batch'] = $stokBatch->id_stok_batch;

            // Create record di tb_obat_keluar
            ObatKeluar::create($validasi);

            // Update stok di tb_stok_batch ONLY
            $stokBatch->stok_keluar += $validasi['stok_keluar'];
            $stokBatch->stok_sisa = $stokBatch->stok_masuk - $stokBatch->stok_keluar;
            $stokBatch->save();

            // ! INVALIDATE cache saat ada data baru
            Cache::forget('master_obat_available');
            Cache::forget('master_kodebatch_available');

            return redirect('/obat-keluar')->with([
                'success' => 'Data Obat ' . $obatMasuk->Obat->nama_obat . ' ke ' . $validasi['id_cabang'] . ' berhasil ditambahkan',
            ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal Input Data: ' . $e->getMessage())
                ->withInput();
        }
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
            $obatKeluar = ObatKeluar::findOrFail($id_obat_keluar);

            $validasi = $request->validate([
                'id_cabang' => 'required|exists:tb_cabang,id_cabang',
                'stok_keluar' => 'required|integer|min:1',
            ], [
                'id_cabang.required' => 'Cabang harus dipilih',
                'id_cabang.exists' => 'Cabang tidak valid',
                'stok_keluar.required' => 'Stok keluar harus diisi',
                'stok_keluar.integer' => 'Stok keluar harus berupa angka',
                'stok_keluar.min' => 'Stok keluar minimal 1',
            ]);

            $stokBatch = $obatKeluar->StokBatch;

            // Hitung perubahan stok
            $selisihStok = $validasi['stok_keluar'] - $obatKeluar->stok_keluar;

            // Validasi stok tidak melebihi stok_sisa di batch
            if ($selisihStok > 0 && $selisihStok > $stokBatch->stok_sisa) {
                return redirect()->back()
                    ->with('error', "Perubahan stok keluar untuk cabang ini melebihi batas. Stok sisa cabang: {$stokBatch->stok_sisa} unit")
                    ->withInput();
            }

            // Update tb_obat_keluar
            $obatKeluar->update([
                'stok_keluar' => $validasi['stok_keluar'],
                'id_cabang' => $validasi['id_cabang'],
            ]);

            // Update tb_stok_batch ONLY
            $stokBatch->stok_keluar += $selisihStok;
            $stokBatch->stok_sisa = $stokBatch->stok_masuk - $stokBatch->stok_keluar;
            $stokBatch->save();

            // ! INVALIDATE cache saat update
            Cache::forget('master_obat_available');
            Cache::forget('master_kodebatch_available');

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
            $obatKeluar = ObatKeluar::findOrFail($id_obat_keluar);
            $stokBatch = $obatKeluar->StokBatch;
            $obatMasuk = $obatKeluar->ObatMasuk;
            $stokKeluar = $obatKeluar->stok_keluar;

            // Hapus record dari tb_obat_keluar
            $obatKeluar->delete();

            // Update tb_obat_masuk (kurangi stok_keluar)
            $obatMasuk->stok_keluar -= $stokKeluar;
            $obatMasuk->stok_sisa = $obatMasuk->stok_masuk - $obatMasuk->stok_keluar;
            $obatMasuk->save();

            // Update tb_stok_batch (kurangi stok_keluar)
            $stokBatch->stok_keluar -= $stokKeluar;
            $stokBatch->stok_sisa = $stokBatch->stok_masuk - $stokBatch->stok_keluar;
            $stokBatch->save();

            // ! INVALIDATE cache saat delete
            Cache::forget('master_obat_available');
            Cache::forget('master_kodebatch_available');

            return redirect('/obat-keluar')->with([
                'success' => 'Data Obat Keluar berhasil dihapus',
            ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal Hapus Data: ' . $e->getMessage());
        }
    }
}
