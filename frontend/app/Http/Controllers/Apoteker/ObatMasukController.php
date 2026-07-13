<?php

namespace App\Http\Controllers\Apoteker;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Services\Apoteker\ObatMasukService;
use Illuminate\Http\Request;

class ObatMasukController extends Controller
{
    public function __construct(private readonly ObatMasukService $obatMasukService)
    {
    }

    // ! ====================== READ =========================
    public function index()
    {
        $indexData = $this->obatMasukService->ambilDataIndex();
        $data = $indexData['data'];
        $obat = $indexData['obat'];
        $kodeBatch = $indexData['kodeBatch'];

        return view('apoteker.obat-masuk', compact('data', 'obat', 'kodeBatch'));
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
                'kode_batch' => 'required|string',
                'tanggal_masuk' => 'required|date',
                'tanggal_kedaluwarsa' => 'required|date|after:tanggal_masuk',
                'stok_masuk' => 'required|integer|min:1',
                'id_obat' => 'required|exists:tb_obat,id_obat',
            ], [
                'tanggal_kedaluwarsa.required' => 'Tanggal kedaluwarsa harus diisi',
                'tanggal_kedaluwarsa.date' => 'Format tanggal kedaluwarsa tidak valid',
                'tanggal_kedaluwarsa.after' => 'Tanggal kedaluwarsa harus lebih besar dari tanggal masuk',
            ]);
            $obat = $this->obatMasukService->simpan($validated);

            return redirect('/obat-masuk')->with([
                'success' => 'Data Obat ' . $obat->nama_obat . ' berhasil ditambahkan',
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal Input Obat Masuk: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal Input Data: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ! ================= UPDATE =====================
    public function update(Request $request, $id_obat_masuk)
    {
        $validated = $request->validate([
            'kode_batch' => 'required|string',
            'tanggal_masuk' => 'required|date',
            'tanggal_kedaluwarsa' => 'required|date',
            'stok_masuk' => 'required|integer|min:1',
            'id_obat' => 'required|exists:tb_obat,id_obat',
        ], [
            'tanggal_masuk.required' => 'Tanggal masuk harus diisi',
            'tanggal_masuk.date' => 'Format tanggal masuk tidak valid',
            'tanggal_kedaluwarsa.required' => 'Tanggal kedaluwarsa harus diisi',
            'tanggal_kedaluwarsa.date' => 'Format tanggal kedaluwarsa tidak valid',
            // 'tanggal_kedaluwarsa.after' => 'Tanggal kedaluwarsa harus lebih besar dari tanggal masuk',
        ]);

        try {
            $namaObat = $this->obatMasukService->ubah((int) $id_obat_masuk, $validated);

            return redirect('/obat-masuk')->with('success', 'Data ' . $namaObat . ' berhasil diupdate');

        } catch (\Exception $e) {
            Log::error('Update Obat Masuk Gagal', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal update data: ' . $e->getMessage());
        }
    }

    // ! ================ DELETE ===================
    public function destroy($id_obat_masuk)
    {
        try {
            $this->obatMasukService->hapus((int) $id_obat_masuk);

            return redirect('/obat-masuk')->with('success', 'Data berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            // Biasanya error jika ada foreign key constraint
            return redirect('/obat-masuk')->with('error', 'Data gagal dihapus: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Error umum lainnya
            return redirect('/obat-masuk')->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
