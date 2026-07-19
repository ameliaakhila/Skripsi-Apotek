<?php

namespace App\Http\Controllers\Apoteker;

use App\Http\Controllers\Controller;
use App\Models\Admin\StokBatch;
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
        // Hapus $kodeBatch dari compact, karena tidak lagi digunakan untuk memuat semua data
        // Ini akan mencegah error "entry too large" karena data besar tidak lagi dilempar ke view
        // dan tidak akan di-cache.
        return view('apoteker.obat-keluar', compact('data', 'obatList', 'cabang'));
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
                'id_stok_batch' => 'required|exists:tb_stok_batch,id_stok_batch',
                'id_cabang' => 'required|exists:tb_cabang,id_cabang',
                'tanggal_keluar' => 'required|date',
                'stok_keluar' => 'required|integer|min:1',
            ], [
                'id_stok_batch.required' => 'Obat dan Batch harus dipilih',
                'id_stok_batch.exists' => 'Data stok tidak valid',
                'id_cabang.required' => 'Cabang asal harus dipilih',
                'id_cabang.exists' => 'Cabang tidak valid',
                'tanggal_keluar.required' => 'Tanggal keluar harus diisi',
                'tanggal_keluar.date' => 'Tanggal keluar harus berupa tanggal yang valid',
                'stok_keluar.required' => 'Stok keluar harus diisi',
                'stok_keluar.integer' => 'Stok keluar harus berupa angka',
                'stok_keluar.min' => 'Stok keluar minimal 1',
            ]);
            $result = $this->obatKeluarService->simpan($validated);

            return redirect('/obat-keluar')->with([
                'success' => 'Data Obat Keluar ' . $result['nama_obat'] . ' di ' . $result['nama_cabang'] . ' berhasil ditambahkan',
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
                'id_stok_batch' => 'required|exists:tb_stok_batch,id_stok_batch',
                'id_cabang' => 'required|exists:tb_cabang,id_cabang',
                'stok_keluar' => 'required|integer|min:1',
                'tanggal_keluar' => 'required|date',
            ], [
                'id_stok_batch.required' => 'Obat dan Batch harus dipilih',
                'id_stok_batch.exists' => 'Data stok tidak valid',
                'id_cabang.required' => 'Cabang asal harus dipilih',
                'id_cabang.exists' => 'Cabang tidak valid',
                'stok_keluar.required' => 'Stok keluar harus diisi',
                'stok_keluar.integer' => 'Stok keluar harus berupa angka',
                'stok_keluar.min' => 'Stok keluar minimal 1',
                'tanggal_keluar.required' => 'Tanggal keluar harus diisi',
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

    /**
     * Search batch for Select2 AJAX
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchBatch(Request $request)
    {
        $search = $request->input('q');
        $idCabang = $request->input('id_cabang');

        $query = StokBatch::query()
            ->with('Obat:id_obat,nama_obat')
            ->where('stok_sisa', '>', 0) // Hanya tampilkan batch yang masih ada stok
            ->select('id_stok_batch', 'kode_batch', 'id_obat', 'stok_sisa', 'tanggal_kedaluwarsa');

        // Filter berdasarkan cabang jika diberikan
        if ($idCabang) {
            $query->where('id_cabang', $idCabang);
        }

        // Filter berdasarkan input pencarian user
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_batch', 'LIKE', "%{$search}%")
                  ->orWhereHas('Obat', function ($subq) use ($search) {
                      $subq->where('nama_obat', 'LIKE', "%{$search}%");
                  });
            });
        }

        $batches = $query->limit(50)->get()->map(function ($batch) {
            return [
                'id' => $batch->id_stok_batch,
                'text' => ($batch->Obat->nama_obat ?? 'N/A') . ' - ' . $batch->kode_batch . ' (Sisa: ' . $batch->stok_sisa . ')',
                'exp' => $batch->tanggal_kedaluwarsa,
                'stok' => $batch->stok_sisa,
            ];
        });

        return response()->json(['results' => $batches]);
    }
}
