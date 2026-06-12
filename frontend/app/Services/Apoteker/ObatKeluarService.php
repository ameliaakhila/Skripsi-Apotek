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

        $obatList = Cache::remember('obat_keluar_list_available', now()->addHours(2), function () {
            return ObatMasuk::with(['Obat:id_obat,nama_obat'])
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


        $kodeBatch = $this->cacheService->getKodeBatchAvailable();

        $cabang = $this->cacheService->getCabangList();

        return compact('data', 'obatList', 'kodeBatch', 'cabang');
    }

    public function simpan(array $validated): array
    {
        return DB::transaction(function () use ($validated): array {
            $obatMasuk = ObatMasuk::with('Obat')->findOrFail($validated['id_obat_masuk']);

            $stokBatch = StokBatch::where('kode_batch', $obatMasuk->kode_batch)
                ->where('id_cabang', $validated['id_cabang'])
                ->firstOrFail();

            if ($validated['stok_keluar'] > $stokBatch->stok_sisa) {
                throw new \RuntimeException("Stok keluar tidak boleh melebihi stok sisa untuk cabang ini ({$stokBatch->stok_sisa} unit)");
            }

            $validated['id_stok_batch'] = $stokBatch->id_stok_batch;

            ObatKeluar::create($validated);

            $stokBatch->stok_keluar += $validated['stok_keluar'];
            $stokBatch->stok_sisa = $stokBatch->stok_masuk - $stokBatch->stok_keluar;
            $stokBatch->save();

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Menambah Stok Keluar: ' . $obatMasuk->Obat->nama_obat
            ]);

            $this->sinkronkanStokObatMasuk($obatMasuk->id_obat_masuk);
            $this->invalidateCache();

            $namaCabang = DataCabang::where('id_cabang', $validated['id_cabang'])->value('nama_cabang') ?? 'cabang';

            return [
                'nama_obat' => $obatMasuk->Obat->nama_obat ?? 'obat',
                'nama_cabang' => $namaCabang,
            ];
        });
    }

    public function ubah(int $idObatKeluar, array $validated): void
    {
        DB::transaction(function () use ($idObatKeluar, $validated): void {
            $obatKeluar = ObatKeluar::with('ObatMasuk.Obat')->findOrFail($idObatKeluar);
            $stokBatch = $obatKeluar->StokBatch;

            $selisihStok = $validated['stok_keluar'] - $obatKeluar->stok_keluar;

            if ($selisihStok > 0 && $selisihStok > $stokBatch->stok_sisa) {
                throw new \RuntimeException("Perubahan stok keluar untuk cabang ini melebihi batas. Stok sisa cabang: {$stokBatch->stok_sisa} unit");
            }

            $obatKeluar->update([
                'stok_keluar' => $validated['stok_keluar'],
                'id_cabang' => $validated['id_cabang'],
            ]);

            $stokBatch->stok_keluar += $selisihStok;
            $stokBatch->stok_sisa = $stokBatch->stok_masuk - $stokBatch->stok_keluar;
            $stokBatch->save();
            
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Mengubah Stok Keluar: ' . $obatKeluar->ObatMasuk->Obat->nama_obat
            ]);

            $this->sinkronkanStokObatMasuk($obatKeluar->id_obat_masuk);
            $this->invalidateCache();
        });
    }

    public function hapus(int $idObatKeluar): void
    {
        DB::transaction(function () use ($idObatKeluar): void {
            $obatKeluar = ObatKeluar::with('ObatMasuk.Obat')->findOrFail($idObatKeluar);
            $stokBatch = $obatKeluar->StokBatch;
            $obatMasuk = $obatKeluar->ObatMasuk;
            $stokKeluar = $obatKeluar->stok_keluar;

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Menghapus Stok Keluar: ' . $obatKeluar->ObatMasuk->Obat->nama_obat
            ]);

            $obatKeluar->delete();

            $stokBatch->stok_keluar -= $stokKeluar;
            $stokBatch->stok_sisa = $stokBatch->stok_masuk - $stokBatch->stok_keluar;
            $stokBatch->save();

            if ($obatMasuk) {
                $this->sinkronkanStokObatMasuk($obatMasuk->id_obat_masuk);
            }

            $this->invalidateCache();
        });
    }

    private function sinkronkanStokObatMasuk(int $idObatMasuk): void
    {
        $obatMasuk = ObatMasuk::find($idObatMasuk);

        if (!$obatMasuk) {
            return;
        }

        $totalKeluar = (int) ObatKeluar::where('id_obat_masuk', $idObatMasuk)->sum('stok_keluar');
        $stokSisa = max(0, (int) $obatMasuk->stok_masuk - $totalKeluar);

        $obatMasuk->update([
            'stok_keluar' => $totalKeluar,
            'stok_sisa' => $stokSisa,
        ]);
    }

    private function invalidateCache(): void
    {
        $this->cacheService->invalidateAll();
    }
}
