<?php

namespace App\Services\Apoteker;

use App\Helpers\StokStatusHelper;
use App\Models\Admin\Obat;
use App\Models\Admin\ObatKeluar;
use App\Models\Admin\ObatMasuk;
use App\Models\Admin\StokBatch;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ObatMasukService
{
    public function __construct(private readonly \App\Services\MasterDataCacheService $cacheService)
    {
    }

    public function ambilDataIndex(): array
    {
        $data = ObatMasuk::with([
            'Obat:id_obat,nama_obat,id_kategori,id_supplier,id_satuan',
            'Obat.DataKategori:id_kategori,nama_kategori',
            'Obat.DataSupplier:id_supplier,nama_supplier',
            'Obat.DataSatuan:id_satuan,nama_satuan',
        ])
            ->orderBy('created_at', 'desc')
            ->simplePaginate(10);

        // ! OPTIMIZATION: Use centralized cache service
        $obat = $this->cacheService->getObatListWithRelations();

        $nomorUrutTerakhir = Obat::query()->max('id_obat') ?? 0;
        $kodeBatch = 'BAC' . now()->format('dmy') . 'SF' . str_pad($nomorUrutTerakhir + 1, 3, '0', STR_PAD_LEFT);

        return compact('data', 'obat', 'kodeBatch');
    }

    public function simpan(array $validated): Obat
    {
        return DB::transaction(function () use ($validated): Obat {
            $stokKeluar = $validated['stok_keluar'] ?? 0;
            $stokSisa = $validated['stok_masuk'] - $stokKeluar;

            ObatMasuk::create([
                'kode_batch' => $validated['kode_batch'],
                'tanggal_masuk' => $validated['tanggal_masuk'],
                'tanggal_kedaluwarsa' => $validated['tanggal_kedaluwarsa'],
                'stok_masuk' => $validated['stok_masuk'],
                'stok_keluar' => $stokKeluar,
                'stok_sisa' => $stokSisa,
                'id_obat' => $validated['id_obat'],
            ]);

            StokBatch::create([
                'kode_batch' => $validated['kode_batch'],
                'tanggal_kedaluwarsa' => $validated['tanggal_kedaluwarsa'],
                'stok_masuk' => $validated['stok_masuk'],
                'stok_sisa' => $stokSisa, 'status_stok' => $this->getStatusStok($stokSisa, $validated['id_obat']),
                'id_obat' => $validated['id_obat'],
                'id_cabang' => 1,
            ]);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Menambah Stok Masuk: ' . $validated['kode_batch']
            ]);

            $this->invalidateCache();

            return Obat::findOrFail($validated['id_obat']);
        });
    }

    public function ubah(int $idObatMasuk, array $validated): string
    {
        return DB::transaction(function () use ($idObatMasuk, $validated): string {
            $obatMasuk = ObatMasuk::findOrFail($idObatMasuk);
            $oldKodeBatch = $obatMasuk->kode_batch;
            $oldIdObat = $obatMasuk->id_obat;

            $rekapStokKeluar = ObatKeluar::where('id_obat_masuk', $obatMasuk->id_obat_masuk)
                ->selectRaw('SUM(stok_keluar) as total_stok_keluar, COUNT(*) as total_transaksi')
                ->first();

            $stokKeluar = ((int) $rekapStokKeluar->total_transaksi > 0)
                ? (int) $rekapStokKeluar->total_stok_keluar
                : 0;

            $stokSisa = $validated['stok_masuk'] - $stokKeluar;

            if ($stokSisa < 0) {
                throw new \RuntimeException('Stok keluar tidak boleh lebih besar dari stok masuk!');
            }

            $obatMasuk->update([
                'kode_batch' => $validated['kode_batch'],
                'id_obat' => $validated['id_obat'],
                'tanggal_masuk' => $validated['tanggal_masuk'],
                'tanggal_kedaluwarsa' => $validated['tanggal_kedaluwarsa'],
                'stok_masuk' => $validated['stok_masuk'],
                'stok_keluar' => $stokKeluar,
                'stok_sisa' => $stokSisa,
            ]);

            $stokBatch = StokBatch::where('kode_batch', $oldKodeBatch)
                ->where('id_obat', $oldIdObat)
                ->first();

            if ($stokBatch) {
                $stokBatch->update([
                    'kode_batch' => $validated['kode_batch'],
                    'tanggal_kedaluwarsa' => $validated['tanggal_kedaluwarsa'],
                    'stok_masuk' => $validated['stok_masuk'],
                    'stok_sisa' => $stokSisa,
                    'status_stok' => $this->getStatusStok($stokSisa, $validated['id_obat']),
                    'id_cabang' => $validated['id_cabang'] ?? $stokBatch->id_cabang,
                ]);
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Mengubah Stok Masuk: ' . $validated['kode_batch']
            ]);

            $this->invalidateCache();

            return (string) (Obat::find($validated['id_obat'])->nama_obat ?? 'obat');
        });
    }

    public function hapus(int $idObatMasuk): void
    {
        DB::transaction(function () use ($idObatMasuk): void {
            $obatMasuk = ObatMasuk::findOrFail($idObatMasuk);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Menghapus Stok Masuk: ' . $obatMasuk->kode_batch
            ]);

            StokBatch::where('kode_batch', $obatMasuk->kode_batch)
                ->where('id_obat', $obatMasuk->id_obat)
                ->delete();

            $obatMasuk->delete();

            $this->invalidateCache();
        });
    }
// ...


    private function getStatusStok(int $stokSisa, int $idObat): string
    {
        $obat = Obat::find($idObat);
        $safetyStock = $obat->safety_stock ?? 1;

        return StokStatusHelper::getStatus($stokSisa, $safetyStock);
    }

    private function invalidateCache(): void
    {
        // ! OPTIMIZATION: Use centralized cache invalidation
        $this->cacheService->invalidateAll();
        // Also invalidate local/specific caches used in other services
        Cache::forget('obat_keluar_list_available');
    }
}
