<?php

namespace App\Services\Apoteker;

use App\Models\Admin\DataCabang;
use App\Models\Admin\ObatKeluar;
use App\Models\Admin\ObatMasuk;
use App\Models\Admin\StokBatch;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ObatKeluarService
{
    public function __construct(private readonly \App\Services\MasterDataCacheService $cacheService)
    {
    }

    public function ambilDataIndex(): array
    {
        $data = ObatKeluar::with([
            'ObatMasuk:id_obat_masuk,id_obat,tanggal_kedaluwarsa,tanggal_masuk',
            'ObatMasuk.Obat:id_obat,nama_obat',
            'StokBatch:id_stok_batch,kode_batch,tanggal_kedaluwarsa,stok_sisa',
            'DataCabang:id_cabang,nama_cabang',
        ])
            ->select('tb_obat_keluar.*')
            ->orderBy('created_at', 'desc')
            ->simplePaginate(25);

        // Get all batches with stock > 0 for all branches
        $obatList = Cache::remember('obat_keluar_list_all_batches', now()->addHours(2), function () {
            return StokBatch::with(['Obat:id_obat,nama_obat', 'DataCabang:id_cabang,nama_cabang'])
                ->select(
                    'id_stok_batch',
                    'id_obat',
                    'id_cabang',
                    'kode_batch',
                    'stok_sisa',
                    'tanggal_kedaluwarsa'
                )
                ->where('stok_sisa', '>', 0)
                ->orderBy('tanggal_kedaluwarsa', 'asc')
                ->get();
        });

        $kodeBatch = $this->cacheService->getKodeBatchAvailable();

        // Get list of branches that have stock
        $cabang = Cache::remember('cabang_list_with_stock', now()->addHours(2), function () {
            return DataCabang::whereHas('stokBatches', function($query) {
                $query->where('stok_sisa', '>', 0);
            })->select('id_cabang', 'nama_cabang')->get();
        });

        return compact('data', 'obatList', 'kodeBatch', 'cabang');
    }

    public function simpan(array $validated): array
    {
        return DB::transaction(function () use ($validated): array {
            $stokBatch = StokBatch::with('Obat')->findOrFail($validated['id_stok_batch']);
            
            // Validasi tambahan: pastikan batch ada di cabang yang dipilih
            if ($stokBatch->id_cabang != $validated['id_cabang']) {
                throw new \RuntimeException("Batch obat tidak ditemukan di cabang yang dipilih.");
            }

            $obatMasuk = ObatMasuk::where('id_obat', $stokBatch->id_obat)
                ->where('kode_batch', $stokBatch->kode_batch)
                ->firstOrFail();

            if ($validated['stok_keluar'] > $stokBatch->stok_sisa) {
                throw new \RuntimeException("Stok keluar melebihi stok tersedia ({$stokBatch->stok_sisa} unit)");
            }

            $validated['id_obat_masuk'] = $obatMasuk->id_obat_masuk;

            ObatKeluar::create($validated);

            $stokBatch->stok_keluar += $validated['stok_keluar'];
            $stokBatch->stok_sisa = $stokBatch->stok_masuk - $stokBatch->stok_keluar;
            $stokBatch->save();

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Menambah Stok Keluar: ' . ($stokBatch->Obat->nama_obat ?? 'Obat')
            ]);

            $this->sinkronkanStokObatMasuk($obatMasuk->id_obat_masuk);
            $this->invalidateCache();

            return [
                'nama_obat' => $stokBatch->Obat->nama_obat ?? 'obat',
                'nama_cabang' => DataCabang::where('id_cabang', $validated['id_cabang'])->value('nama_cabang') ?? 'cabang',
            ];
        });
    }

    public function ubah(int $idObatKeluar, array $validated): void
    {
        DB::transaction(function () use ($idObatKeluar, $validated): void {
            $obatKeluar = ObatKeluar::with(['ObatMasuk.Obat', 'StokBatch'])->findOrFail($idObatKeluar);
            $oldStokBatch = $obatKeluar->StokBatch;
            $newStokBatch = StokBatch::findOrFail($validated['id_stok_batch']);
            
            // Validasi cabang
            if ($newStokBatch->id_cabang != $validated['id_cabang']) {
                throw new \RuntimeException("Batch obat tidak sesuai dengan cabang yang dipilih.");
            }

            if ($oldStokBatch->id_stok_batch !== $newStokBatch->id_stok_batch) {
                // Revert stok lama
                $oldStokBatch->stok_keluar -= $obatKeluar->stok_keluar;
                $oldStokBatch->stok_sisa = $oldStokBatch->stok_masuk - $oldStokBatch->stok_keluar;
                $oldStokBatch->save();
                
                // Update stok baru
                if ($validated['stok_keluar'] > $newStokBatch->stok_sisa) {
                    throw new \RuntimeException("Stok tidak cukup di cabang terpilih.");
                }
                $newStokBatch->stok_keluar += $validated['stok_keluar'];
                $newStokBatch->stok_sisa = $newStokBatch->stok_masuk - $newStokBatch->stok_keluar;
                $newStokBatch->save();
            } else {
                $selisihStok = $validated['stok_keluar'] - $obatKeluar->stok_keluar;
                if ($selisihStok > 0 && $selisihStok > $oldStokBatch->stok_sisa) {
                    throw new \RuntimeException("Stok tidak cukup.");
                }
                $oldStokBatch->stok_keluar += $selisihStok;
                $oldStokBatch->stok_sisa = $oldStokBatch->stok_masuk - $oldStokBatch->stok_keluar;
                $oldStokBatch->save();
            }

            $obatMasuk = ObatMasuk::where('id_obat', $newStokBatch->id_obat)
                ->where('kode_batch', $newStokBatch->kode_batch)
                ->firstOrFail();

            $obatKeluar->update([
                'id_stok_batch' => $newStokBatch->id_stok_batch,
                'id_obat_masuk' => $obatMasuk->id_obat_masuk,
                'id_cabang' => $validated['id_cabang'],
                'stok_keluar' => $validated['stok_keluar'],
                'tanggal_keluar' => $validated['tanggal_keluar'] ?? $obatKeluar->tanggal_keluar,
            ]);
            
            $this->sinkronkanStokObatMasuk($obatMasuk->id_obat_masuk);
            $this->invalidateCache();
        });
    }

    public function hapus(int $idObatKeluar): void
    {
        DB::transaction(function () use ($idObatKeluar): void {
            $obatKeluar = ObatKeluar::findOrFail($idObatKeluar);
            $stokBatch = $obatKeluar->StokBatch;
            $obatMasuk = $obatKeluar->ObatMasuk;
            $stokKeluar = $obatKeluar->stok_keluar;

            $obatKeluar->delete();

            if ($stokBatch) {
                $stokBatch->stok_keluar -= $stokKeluar;
                $stokBatch->stok_sisa = $stokBatch->stok_masuk - $stokBatch->stok_keluar;
                $stokBatch->save();
            }

            if ($obatMasuk) {
                $this->sinkronkanStokObatMasuk($obatMasuk->id_obat_masuk);
            }

            $this->invalidateCache();
        });
    }

    private function sinkronkanStokObatMasuk(int $idObatMasuk): void
    {
        $obatMasuk = ObatMasuk::find($idObatMasuk);
        if ($obatMasuk) {
            $totalKeluar = (int) ObatKeluar::where('id_obat_masuk', $idObatMasuk)->sum('stok_keluar');
            $obatMasuk->update([
                'stok_keluar' => $totalKeluar,
                'stok_sisa' => max(0, $obatMasuk->stok_masuk - $totalKeluar),
            ]);
        }
    }

    private function invalidateCache(): void
    {
        $this->cacheService->invalidateAll();
    }
}
