<?php

namespace App\Services\Admin;

use App\Models\Admin\DataKategori;
use App\Models\Admin\DataSatuan;
use App\Models\Admin\DataSupplier;
use App\Models\Admin\Obat;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DataObatService
{
    public function __construct(private readonly \App\Services\MasterDataCacheService $cacheService)
    {
    }

    public function ambilDataIndex(): array
    {
        $data = Cache::remember('list_obat_paginated', 600, function (): CursorPaginator {
            return Obat::with([
                'DataKategori:id_kategori,nama_kategori',
                'DataSupplier:id_supplier,nama_supplier',
                'DataSatuan:id_satuan,nama_satuan',
            ])
                ->select([
                    'id_obat',
                    'nama_obat',
                    'safety_stock',
                    'id_satuan',
                    'id_kategori',
                    'id_supplier',
                    'created_at',
                ])
                ->cursorPaginate(2000);
        });

        $masterData = Cache::remember('master_data_obat', 3600, function (): array {
            return [
                'kategori' => DataKategori::select('id_kategori', 'nama_kategori')->get(),
                'supplier' => DataSupplier::select('id_supplier', 'nama_supplier')->get(),
                'satuan' => DataSatuan::select('id_satuan', 'nama_satuan')->get(),
            ];
        });

        return [
            'data' => $data,
            'kategori' => $masterData['kategori'],
            'supplier' => $masterData['supplier'],
            'satuan' => $masterData['satuan'],
        ];
    }

    public function simpan(array $payload): Obat
    {
        return DB::transaction(function () use ($payload): Obat {
            $obat = Obat::create([
                'nama_obat' => $payload['nama_obat'],
                'safety_stock' => $payload['safety_stock'],
                'id_supplier' => $payload['id_supplier'],
                'id_satuan' => $payload['id_satuan'],
                'id_kategori' => $payload['id_kategori'],
            ]);

            $this->invalidateCache();

            return $obat;
        });
    }

    public function ubah(int $idObat, array $payload): Obat
    {
        return DB::transaction(function () use ($idObat, $payload): Obat {
            $obat = Obat::findOrFail($idObat);
            $obat->update([
                'nama_obat' => $payload['nama_obat'],
                'safety_stock' => $payload['safety_stock'],
                'id_supplier' => $payload['id_supplier'],
                'id_satuan' => $payload['id_satuan'],
                'id_kategori' => $payload['id_kategori'],
            ]);

            $this->invalidateCache();

            return $obat;
        });
    }

    public function hapus(int $idObat): string
    {
        return DB::transaction(function () use ($idObat): string {
            $obat = Obat::findOrFail($idObat);
            $namaObat = $obat->nama_obat;
            $obat->delete();

            $this->invalidateCache();

            return $namaObat;
        });
    }

    private function invalidateCache(): void
    {
        Cache::forget('list_obat_paginated');
        Cache::forget('master_data_obat');
        // ! OPTIMIZATION: Use centralized cache invalidation
        $this->cacheService->invalidateAll();
    }
}
