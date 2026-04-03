<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Admin\DataCabang;
use App\Models\Admin\MutasiStok;
use App\Models\Admin\StokBatch;
use App\Models\Admin\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MutasiStokController extends Controller
{
    /**
     * Display a listing of the resource with optimized eager loading (no N+1)
     * Pattern: Follow ObatKeluarController structure - Controller handles data formatting
     */
    public function index()
    {
        // ! OPTIMIZATION: Paginate with eager loading + select only needed columns
        $data = MutasiStok::with([
            'stokBatch:id_stok_batch,kode_batch,id_obat,tanggal_kedaluwarsa,stok_masuk,stok_keluar,stok_sisa',
            'stokBatch.Obat:id_obat,nama_obat,id_satuan,id_kategori',
            'stokBatch.Obat.DataSatuan:id_satuan,nama_satuan',
            'stokBatch.Obat.DataKategori:id_kategori,nama_kategori',
            'cabangAsal:id_cabang,kode_cabang,nama_cabang',
            'cabangTujuan:id_cabang,kode_cabang,nama_cabang'
        ])
            ->select('id_mutasi', 'id_stok_batch', 'id_cabang_asal', 'id_cabang_tujuan', 'tanggal_mutasi', 'jumlah_mutasi', 'keterangan', 'created_at')
            ->orderBy('tanggal_mutasi', 'desc')
            ->simplePaginate(25);  // Increase per-page untuk reduce queries

        // Share data modal sekali per request agar view tidak perlu kirim atribut data berulang.
        view()->share($this->getModalMutasiStokData());

        return view('shared.mutasi-stok', compact('data'));
    }

    /**
     * Query data yang dipakai semua modal mutasi stok.
     */
    private function getModalMutasiStokData(): array
    {
        $dataCabangTujuan = Cache::remember('master_cabang_tambah_list', now()->addDay(), function () {
            return DataCabang::select('id_cabang', 'kode_cabang', 'nama_cabang')
                ->where('id_cabang', '!=', 1)
                ->orderBy('id_cabang', 'asc')
                ->get();
        });

        $dataCabangAsal = Cache::remember('master_cabang_edit_list', now()->addDay(), function () {
            return DataCabang::select('id_cabang', 'kode_cabang', 'nama_cabang')
                ->orderBy('id_cabang', 'asc')
                ->get();
        });

        $dataStokBatch = Cache::remember('master_stok_batch_available', now()->addHours(2), function () {
            return StokBatch::with('Obat:id_obat,nama_obat')
                ->select('id_stok_batch', 'kode_batch', 'id_obat', 'tanggal_kedaluwarsa', 'stok_masuk', 'stok_keluar', 'stok_sisa')
                ->where('stok_sisa', '>', 0)
                ->orderBy('tanggal_kedaluwarsa', 'asc')
                ->get();
        });

        return compact('dataCabangTujuan', 'dataCabangAsal', 'dataStokBatch');
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
     * FEFO Logic: Automatically select batch dengan tanggal kedaluwarsa paling dulu dari cabang asal
     * DEFAULT: Jika id_cabang_asal kosong, gunakan cabang dengan ID pertama (gudang pusat)
     */
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'id_stok_batch' => 'nullable|exists:tb_stok_batch,id_stok_batch',
                'id_cabang_asal' => 'nullable|exists:tb_cabang,id_cabang',
                'id_cabang_tujuan' => 'required|exists:tb_cabang,id_cabang',
                'tanggal_mutasi' => 'required|date',
                'jumlah_mutasi' => 'required|integer|min:1',
                'keterangan' => 'nullable|string',
            ], [
                'id_cabang_tujuan.required' => 'Cabang tujuan harus dipilih',
                'jumlah_mutasi.required' => 'Jumlah mutasi harus diisi',
                'jumlah_mutasi.min' => 'Jumlah mutasi minimal 1 unit',
                'tanggal_mutasi.required' => 'Tanggal mutasi harus diisi',
            ]);

            // ! DEFAULT: Jika cabang asal kosong, gunakan cabang dengan ID pertama (gudang pusat)
            if (empty($validated['id_cabang_asal'])) {
                $cabangPertama = DataCabang::orderBy('id_cabang', 'asc')->first();
                if (!$cabangPertama) {
                    return redirect()->back()
                        ->with('error', 'Tidak ada data cabang di sistem. Tambahkan cabang terlebih dahulu.')
                        ->withInput();
                }
                $validated['id_cabang_asal'] = $cabangPertama->id_cabang;
            }

            // Validasi: cabang asal dan tujuan harus berbeda
            if ($validated['id_cabang_asal'] == $validated['id_cabang_tujuan']) {
                return redirect()->back()
                    ->with('error', 'Cabang asal dan tujuan harus berbeda!')
                    ->withInput();
            }

            // Jika user tidak pilih batch spesifik, gunakan FEFO dari cabang asal
            if (empty($validated['id_stok_batch'])) {
                // FEFO: Ambil batch dengan tanggal kedaluwarsa paling dulu dari cabang asal DENGAN stok tersedia
                $batchAsal = StokBatch::where('id_cabang', $validated['id_cabang_asal'])
                    ->where('stok_sisa', '>', 0)
                    ->orderBy('tanggal_kedaluwarsa', 'asc')  // FEFO Selection
                    ->first();

                if (!$batchAsal) {
                    return redirect()->back()
                        ->with('error', 'Tidak ada stok tersedia di cabang asal untuk distribusi.')
                        ->withInput();
                }

                $validated['id_stok_batch'] = $batchAsal->id_stok_batch;
            } else {
                // User pilih batch spesifik - validasi batch ada di cabang asal dengan stok cukup
                $batchAsal = StokBatch::where('id_stok_batch', $validated['id_stok_batch'])
                    ->where('id_cabang', $validated['id_cabang_asal'])
                    ->first();

                if (!$batchAsal) {
                    return redirect()->back()
                        ->with('error', 'Batch tidak ditemukan di cabang asal atau batch sudah dihapus.')
                        ->withInput();
                }
            }

            // Reload batch untuk memastikan data terbaru
            $batchAsal = StokBatch::findOrFail($validated['id_stok_batch']);

            // Validasi: stok di cabang asal cukup untuk mutasi
            if ($validated['jumlah_mutasi'] > $batchAsal->stok_sisa) {
                return redirect()->back()
                    ->with('error', "Stok tidak cukup. Stok tersedia: {$batchAsal->stok_sisa} unit, diminta: {$validated['jumlah_mutasi']} unit")
                    ->withInput();
            }

            // ! UPDATE tb_stok_batch di cabang asal (kurangi stok keluar, hitung sisa)
            $batchAsal->update([
                'stok_keluar' => $batchAsal->stok_keluar + $validated['jumlah_mutasi'],
                'stok_sisa' => $batchAsal->stok_masuk - ($batchAsal->stok_keluar + $validated['jumlah_mutasi']),
            ]);

            // ! UPDATE atau CREATE batch di cabang tujuan (tambah stok masuk, hitung sisa)
            $batchTujuan = StokBatch::where('kode_batch', $batchAsal->kode_batch)
                ->where('id_obat', $batchAsal->id_obat)
                ->where('id_cabang', $validated['id_cabang_tujuan'])
                ->first();

            if ($batchTujuan) {
                // Batch sudah exist di cabang tujuan, update saja
                $batchTujuan->update([
                    'stok_masuk' => $batchTujuan->stok_masuk + $validated['jumlah_mutasi'],
                    'stok_sisa' => ($batchTujuan->stok_masuk + $validated['jumlah_mutasi']) - $batchTujuan->stok_keluar,
                    'status_stok' => $this->getStatusStok(($batchTujuan->stok_masuk + $validated['jumlah_mutasi']) - $batchTujuan->stok_keluar, $batchAsal->id_obat),
                ]);
            } else {
                // Batch belum exist di cabang tujuan, create batch baru dengan identitas sama
                StokBatch::create([
                    'kode_batch' => $batchAsal->kode_batch,  // Identitas batch SAMA
                    'tanggal_kedaluwarsa' => $batchAsal->tanggal_kedaluwarsa,
                    'stok_masuk' => $validated['jumlah_mutasi'],
                    'stok_keluar' => 0,
                    'stok_sisa' => $validated['jumlah_mutasi'],
                    'status_stok' => $this->getStatusStok($validated['jumlah_mutasi'], $batchAsal->id_obat),
                    'tujuan_penyesuaian' => 'Mutasi Stok',
                    'id_obat' => $batchAsal->id_obat,
                    'id_cabang' => $validated['id_cabang_tujuan'],
                ]);
            }

            // ! CREATE record di tb_mutasi (untuk audit trail)
            MutasiStok::create([
                'id_stok_batch' => $validated['id_stok_batch'],
                'id_cabang_asal' => $validated['id_cabang_asal'],
                'id_cabang_tujuan' => $validated['id_cabang_tujuan'],
                'tanggal_mutasi' => $validated['tanggal_mutasi'],
                'jumlah_mutasi' => $validated['jumlah_mutasi'],
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            // Invalidate cache
            Cache::forget('master_obat_available');
            Cache::forget('master_kodebatch_available');
            Cache::forget('master_stok_batch_available');
            Cache::forget('master_cabang_list');
            Cache::forget('master_cabang_tambah_list');
            Cache::forget('master_cabang_edit_list');

            return redirect('/mutasi-stok')->with('success', 'Mutasi stok berhasil ditambahkan');

        } catch (\Exception $e) {
            Log::error('Mutasi Stok Store Error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return redirect()->back()
                ->with('error', 'Gagal menambah mutasi stok: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MutasiStok $mutasiStok)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MutasiStok $mutasiStok)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * Memproses: REVERT mutation lama → validasi batch baru → APPLY mutation baru
     */
    public function update(Request $request, $id_mutasi)
    {
        try {
            $mutasi = MutasiStok::findOrFail($id_mutasi);

            // Validasi input
            $validated = $request->validate([
                'id_stok_batch' => 'required|exists:tb_stok_batch,id_stok_batch',
                'id_cabang_asal' => 'nullable|exists:tb_cabang,id_cabang',
                'id_cabang_tujuan' => 'required|exists:tb_cabang,id_cabang',
                'tanggal_mutasi' => 'required|date',
                'jumlah_mutasi' => 'required|integer|min:1',
                'keterangan' => 'nullable|string',
            ]);

            // ! DEFAULT: Jika cabang asal kosong, gunakan cabang dengan ID pertama
            if (empty($validated['id_cabang_asal'])) {
                $cabangPertama = DataCabang::orderBy('id_cabang', 'asc')->first();
                if (!$cabangPertama) {
                    return redirect()->back()
                        ->with('error', 'Tidak ada data cabang di sistem.')
                        ->withInput();
                }
                $validated['id_cabang_asal'] = $cabangPertama->id_cabang;
            }

            // Validasi: cabang asal dan tujuan harus berbeda
            if ($validated['id_cabang_asal'] == $validated['id_cabang_tujuan']) {
                return redirect()->back()
                    ->with('error', 'Cabang asal dan tujuan harus berbeda!')
                    ->withInput();
            }

            // Get batch dari request (batch baru yang dipilih user)
            $batchAsal = StokBatch::findOrFail($validated['id_stok_batch']);

            // Validasi batch ada di cabang asal
            if ($batchAsal->id_cabang != $validated['id_cabang_asal']) {
                return redirect()->back()
                    ->with('error', 'Batch tidak ditemukan di cabang asal yang dipilih.')
                    ->withInput();
            }

            // Get old batch untuk revert
            $oldBatchAsal = StokBatch::findOrFail($mutasi->id_stok_batch);
            $oldBatchTujuan = StokBatch::where('kode_batch', $oldBatchAsal->kode_batch)
                ->where('id_obat', $oldBatchAsal->id_obat)
                ->where('id_cabang', $mutasi->id_cabang_tujuan)
                ->first();

            if (!$oldBatchTujuan) {
                return redirect()->back()
                    ->with('error', 'Data batch tujuan tidak ditemukan di sistem.')
                    ->withInput();
            }

            // ! PHASE 1: REVERT mutation lama dari both batches
            $oldBatchAsal->update([
                'stok_keluar' => max(0, $oldBatchAsal->stok_keluar - $mutasi->jumlah_mutasi),
                'stok_sisa' => $oldBatchAsal->stok_masuk - max(0, $oldBatchAsal->stok_keluar - $mutasi->jumlah_mutasi),
            ]);

            $oldBatchTujuan->update([
                'stok_masuk' => max(0, $oldBatchTujuan->stok_masuk - $mutasi->jumlah_mutasi),
                'stok_sisa' => max(0, $oldBatchTujuan->stok_masuk - $mutasi->jumlah_mutasi) - $oldBatchTujuan->stok_keluar,
            ]);

            // ! PHASE 2: Validasi batch asal baru punya stok cukup untuk mutasi baru
            if ($validated['jumlah_mutasi'] > $batchAsal->stok_sisa) {
                return redirect()->back()
                    ->with('error', "Stok tidak cukup di batch baru. Stok tersedia: {$batchAsal->stok_sisa} unit, diminta: {$validated['jumlah_mutasi']} unit")
                    ->withInput();
            }

            // ! PHASE 3: APPLY mutation baru ke batch asal
            $batchAsal->update([
                'stok_keluar' => $batchAsal->stok_keluar + $validated['jumlah_mutasi'],
                'stok_sisa' => $batchAsal->stok_masuk - ($batchAsal->stok_keluar + $validated['jumlah_mutasi']),
            ]);

            // ! PHASE 4: Handle batch di cabang tujuan (bisa batch lama atau batch baru)
            $newBatchTujuan = StokBatch::where('kode_batch', $batchAsal->kode_batch)
                ->where('id_obat', $batchAsal->id_obat)
                ->where('id_cabang', $validated['id_cabang_tujuan'])
                ->first();

            if ($newBatchTujuan) {
                // Batch exist di cabang tujuan
                $newBatchTujuan->update([
                    'stok_masuk' => $newBatchTujuan->stok_masuk + $validated['jumlah_mutasi'],
                    'stok_sisa' => ($newBatchTujuan->stok_masuk + $validated['jumlah_mutasi']) - $newBatchTujuan->stok_keluar,
                    'status_stok' => $this->getStatusStok(($newBatchTujuan->stok_masuk + $validated['jumlah_mutasi']) - $newBatchTujuan->stok_keluar, $batchAsal->id_obat),
                ]);
            } else {
                // Batch belum exist di cabang tujuan, create batch baru
                StokBatch::create([
                    'kode_batch' => $batchAsal->kode_batch,
                    'tanggal_kedaluwarsa' => $batchAsal->tanggal_kedaluwarsa,
                    'stok_masuk' => $validated['jumlah_mutasi'],
                    'stok_keluar' => 0,
                    'stok_sisa' => $validated['jumlah_mutasi'],
                    'status_stok' => $this->getStatusStok($validated['jumlah_mutasi'], $batchAsal->id_obat),
                    'tujuan_penyesuaian' => 'Mutasi Stok',
                    'id_obat' => $batchAsal->id_obat,
                    'id_cabang' => $validated['id_cabang_tujuan'],
                ]);
            }

            // ! UPDATE record di tb_mutasi
            $mutasi->update([
                'id_stok_batch' => $validated['id_stok_batch'],
                'id_cabang_asal' => $validated['id_cabang_asal'],
                'id_cabang_tujuan' => $validated['id_cabang_tujuan'],
                'tanggal_mutasi' => $validated['tanggal_mutasi'],
                'jumlah_mutasi' => $validated['jumlah_mutasi'],
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            // Invalidate cache
            Cache::forget('master_obat_available');
            Cache::forget('master_kodebatch_available');
            Cache::forget('master_stok_batch_available');
            Cache::forget('master_cabang_list');
            Cache::forget('master_cabang_tambah_list');
            Cache::forget('master_cabang_edit_list');

            return redirect('/mutasi-stok')->with('success', 'Mutasi stok berhasil diperbarui');

        } catch (\Exception $e) {
            Log::error('Mutasi Stok Update Error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return redirect()->back()
                ->with('error', 'Gagal memperbarui mutasi stok: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     * Revert mutation: restore stok ke batch asal, kurangi dari batch tujuan
     */
    public function destroy($id_mutasi)
    {
        try {
            $mutasi = MutasiStok::findOrFail($id_mutasi);

            // Get batch asal
            $batchAsal = StokBatch::findOrFail($mutasi->id_stok_batch);

            // Get batch tujuan
            $batchTujuan = StokBatch::where('kode_batch', $batchAsal->kode_batch)
                ->where('id_obat', $batchAsal->id_obat)
                ->where('id_cabang', $mutasi->id_cabang_tujuan)
                ->first();

            if (!$batchTujuan) {
                Log::warning('Batch tujuan tidak ditemukan saat delete mutasi', ['id_mutasi' => $id_mutasi]);
                return redirect('/mutasi-stok')->with('error', 'Data batch tujuan tidak valid. Mutasi tidak dapat dihapus.');
            }

            // ! REVERT mutation: Kembalikan stok ke kondisi sebelum mutasi
            // Tambah kembali ke batch asal (kurangi stok_keluar)
            $batchAsal->update([
                'stok_keluar' => max(0, $batchAsal->stok_keluar - $mutasi->jumlah_mutasi),
                'stok_sisa' => $batchAsal->stok_masuk - max(0, $batchAsal->stok_keluar - $mutasi->jumlah_mutasi),
            ]);

            // Kurangi dari batch tujuan (kurangi stok_masuk)
            $newStokMasukTujuan = max(0, $batchTujuan->stok_masuk - $mutasi->jumlah_mutasi);
            $newStokSisaTujuan = $newStokMasukTujuan - $batchTujuan->stok_keluar;

            if ($newStokSisaTujuan > 0 || $newStokMasukTujuan > 0) {
                // Batch masih punya stok, update saja
                $batchTujuan->update([
                    'stok_masuk' => $newStokMasukTujuan,
                    'stok_sisa' => $newStokSisaTujuan,
                    'status_stok' => $this->getStatusStok($newStokSisaTujuan, $batchAsal->id_obat),
                ]);
            } else {
                // Batch stok habis (sisa <= 0), hapus batch untuk cleanup
                $batchTujuan->delete();
            }

            // ! DELETE record mutasi
            $mutasi->delete();

            // Invalidate cache
            Cache::forget('master_obat_available');
            Cache::forget('master_kodebatch_available');
            Cache::forget('master_stok_batch_available');
            Cache::forget('master_cabang_list');
            Cache::forget('master_cabang_tambah_list');
            Cache::forget('master_cabang_edit_list');

            return redirect('/mutasi-stok')->with('success', 'Mutasi stok berhasil dihapus dan stok telah dikembalikan');

        } catch (\Exception $e) {
            Log::error('Mutasi Stok Destroy Error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return redirect('/mutasi-stok')->with('error', 'Gagal menghapus mutasi stok: ' . $e->getMessage());
        }
    }

    /**
     * Helper method: Hitung status stok berdasarkan safety_stock
     */
    private function getStatusStok($stok_sisa, $id_obat)
    {
        $obat = Obat::find($id_obat);
        $safety_stock = $obat->safety_stock ?? 1;

        // Aman: stok >= 120% dari safety_stock
        if ($stok_sisa >= ($safety_stock * 1.2)) {
            return 'Aman';
        }
        // Menipis: 30% – 120% dari safety_stock
        elseif ($stok_sisa >= ($safety_stock * 0.3)) {
            return 'Menipis';
        }
        // Habis: stok <= 30% dari safety_stock
        else {
            return 'Habis';
        }
    }
}
