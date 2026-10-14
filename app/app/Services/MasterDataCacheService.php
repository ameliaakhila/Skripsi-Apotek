<?php

namespace App\Services;

use App\Models\Admin\Obat;
use App\Models\Admin\DataKategori;
use App\Models\Admin\DataSatuan;
use App\Models\Admin\DataSupplier;
use App\Models\Admin\DataCabang;
use App\Models\Admin\ObatMasuk;
use Illuminate\Support\Facades\Cache;

/**
 * MasterDataCacheService
 *
 * Centralized service for managing master data caching
 * Benefits:
 * - Single point of cache management
 * - Easy cache invalidation
 * - Consistent TTL across application
 * - Prevents N+1 queries
 */
class MasterDataCacheService
{
    // Cache TTL constants (in seconds or datetime objects)
    // ! OPTIMIZATION: Reduced TTL to 5 minutes (300 seconds) for near real-time consistency
    private const CACHE_TTL_MASTER = 300; 
    private const CACHE_TTL_OBAT = 300; 
    private const CACHE_TTL_AVAILABILITY = 60; // 1 minute for availability lists

    /**
     * Get all obat with caching
     */
    public function getObatList($orderBy = 'nama_obat')
    {
        return Cache::remember('master_obat_list', now()->addSeconds(self::CACHE_TTL_OBAT), function () use ($orderBy) {
            return Obat::select('id_obat', 'nama_obat', 'safety_stock')
                ->orderBy($orderBy)
                ->get();
        });
    }

    /**
     * Get all obat with relations for forms
     */
    public function getObatListWithRelations($orderBy = 'created_at', $direction = 'desc')
    {
        return Cache::remember('master_obat_list_with_relations', now()->addSeconds(self::CACHE_TTL_OBAT), function () use ($orderBy, $direction) {
            return Obat::with([
                'DataKategori:id_kategori,nama_kategori',
                'DataSupplier:id_supplier,nama_supplier',
                'DataSatuan:id_satuan,nama_satuan',
            ])
                ->select('id_obat', 'nama_obat', 'id_kategori', 'id_supplier', 'id_satuan')
                ->whereHas('DataKategori')
                ->whereHas('DataSupplier')
                ->whereHas('DataSatuan')
                ->orderBy($orderBy, $direction)
                ->get();
        });
    }

    /**
     * Get all kategori with caching
     */
    public function getKategoriList()
    {
        return Cache::remember('master_kategori_list', now()->addSeconds(self::CACHE_TTL_MASTER), function () {
            return DataKategori::select('id_kategori', 'nama_kategori')
                ->orderBy('nama_kategori')
                ->get();
        });
    }

    /**
     * Get all satuan with caching
     */
    public function getSatuanList()
    {
        return Cache::remember('master_satuan_list', now()->addSeconds(self::CACHE_TTL_MASTER), function () {
            return DataSatuan::select('id_satuan', 'nama_satuan')
                ->orderBy('nama_satuan')
                ->get();
        });
    }

    /**
     * Get all supplier with caching
     */
    public function getSupplierList()
    {
        return Cache::remember('master_supplier_list', now()->addSeconds(self::CACHE_TTL_MASTER), function () {
            return DataSupplier::select('id_supplier', 'nama_supplier')
                ->orderBy('nama_supplier')
                ->get();
        });
    }

    /**
     * Get all cabang with caching
     */
    public function getCabangList()
    {
        return Cache::remember('master_cabang_list', now()->addSeconds(self::CACHE_TTL_MASTER), function () {
            return DataCabang::select('id_cabang', 'nama_cabang')
                ->orderBy('nama_cabang')
                ->get();
        });
    }

    /**
     * Get obat masuk with available stock (stok_masuk > stok_keluar)
     * Used for dropdowns where only items with stock are displayed
     */
    public function getObatMasukAvailable()
    {
        return Cache::remember('master_obat_available', now()->addSeconds(self::CACHE_TTL_AVAILABILITY), function () {
            return ObatMasuk::select('id_obat_masuk', 'id_obat', 'kode_batch', 'stok_masuk', 'stok_keluar', 'tanggal_kedaluwarsa')
                ->whereColumn('stok_masuk', '>', 'stok_keluar')
                ->with('Obat:id_obat,nama_obat')
                ->orderBy('tanggal_kedaluwarsa', 'asc')
                ->get();
        });
    }

    /**
     * Get batch codes for available obat
     */
    public function getKodeBatchAvailable()
    {
        return Cache::remember('master_kodebatch_available', now()->addSeconds(self::CACHE_TTL_AVAILABILITY), function () {
            return ObatMasuk::select('id_obat_masuk', 'kode_batch', 'tanggal_kedaluwarsa', 'stok_masuk')
                ->withSum('obatKeluar as total_keluar', 'stok_keluar')
                ->havingRaw('stok_masuk > COALESCE(total_keluar, 0)')
                ->orderBy('tanggal_kedaluwarsa', 'asc')
                ->get();
        });
    }

    /**
     * Get master data bundle - reduces multiple cache hits to single call
     */
    public function getMasterDataBundle()
    {
        return [
            'obat' => $this->getObatList(),
            'kategori' => $this->getKategoriList(),
            'satuan' => $this->getSatuanList(),
            'supplier' => $this->getSupplierList(),
            'cabang' => $this->getCabangList(),
        ];
    }

    /**
     * Clear all master data caches
     * Call this when data is created/updated/deleted
     */
    public function invalidateAll()
    {
        $keys = [
            'master_obat_list',
            'master_obat_list_with_relations',
            'master_kategori_list',
            'master_satuan_list',
            'master_supplier_list',
            'master_cabang_list',
            'master_obat_available',
            'master_kodebatch_available',
            'obat_keluar_list_available',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Invalidate specific cache
     */
    public function invalidate($cacheKey)
    {
        Cache::forget($cacheKey);
    }

    /**
     * Clear availability caches (frequently change)
     */
    public function invalidateAvailability()
    {
        Cache::forget('master_obat_available');
        Cache::forget('master_kodebatch_available');
    }
}
