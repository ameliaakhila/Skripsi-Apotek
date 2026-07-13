<?php

/**
 * ! ObatKeluarSeeder - Generate stok_keluar per cabang ke tb_stok_batch (OPTIMIZED)
 * * ALUR OPTIMIZED:
 * * 1. Ambil stok_batch dengan chunk processing
 * * 2. Generate & insert stok_keluar dengan batch insert (1000 rows per chunk)
 * * 3. UPDATE tb_stok_batch dengan raw SQL & batch processing
 * * BENEFIT: Reduced N+1 queries, faster bulk operations, efficient memory usage
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helpers\StokStatusHelper;

class ObatKeluarSeeder extends Seeder
{
    private const CHUNK_SIZE = 1000; // Batch insert size

    public function run(): void
    {
        // ! STEP 1: Clear old data
        DB::table('tb_obat_keluar')->truncate();

        // ! STEP 2: Get batch → obat_masuk mapping ONCE (tidak perlu loop)
        $batch_to_obat_masuk = DB::table('tb_obat_masuk')
            ->pluck('id_obat_masuk', 'kode_batch')
            ->toArray();

        // ! STEP 3: Ambil stok_batch dengan pagination untuk memory efficiency
        $perPage = 2000;
        $lastId = 0;
        $totalProcessed = 0;

        do {
            // ! Chunk processing dengan offset cursor
            $stok_batches = DB::table('tb_stok_batch')
                ->where('id_stok_batch', '>', $lastId)
                ->join('tb_obat', 'tb_stok_batch.id_obat', '=', 'tb_obat.id_obat')
                ->select(
                    'tb_stok_batch.id_stok_batch',
                    'tb_stok_batch.kode_batch',
                    'tb_stok_batch.id_cabang',
                    'tb_stok_batch.id_obat',
                    'tb_stok_batch.stok_masuk',
                    'tb_obat.safety_stock'
                )
                ->orderBy('tb_stok_batch.id_stok_batch')
                ->limit($perPage)
                ->get();

            // ! Tentukan range tanggal untuk stok_keluar (misal: 1 Jan 2025 - sekarang)
            $startDate = \Carbon\Carbon::create(2025, 1, 1);
            $endDate = now();

            $startTimeStamp = $startDate->timestamp;
            $endTimeStamp = $endDate->timestamp;

            if ($stok_batches->isEmpty()) {
                break;
            }

            $data_keluar = [];

            // ! STEP 4: Generate stok_keluar untuk chunk saat ini
            foreach ($stok_batches as $batch) {
                $qty_keluar = rand(1, max(1, $batch->stok_masuk));

                $id_obat_masuk = $batch_to_obat_masuk[$batch->kode_batch] ?? null;
                if (!$id_obat_masuk) {
                    continue;
                }

                $randomTimestamp = rand($startTimeStamp, $endTimeStamp);
                $randomDate = \Carbon\Carbon::createFromTimestamp($randomTimestamp);

                $data_keluar[] = [
                    'id_stok_batch' => $batch->id_stok_batch,
                    'id_obat_masuk' => $id_obat_masuk,
                    'id_cabang' => $batch->id_cabang,
                    'tanggal_keluar' => $randomDate,
                    'stok_keluar' => $qty_keluar,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // ! STEP 5: Insert data_keluar in larger chunks (1000 per batch)
            foreach (array_chunk($data_keluar, self::CHUNK_SIZE) as $chunk) {
                DB::table('tb_obat_keluar')->insert($chunk);
            }

            $totalProcessed += $stok_batches->count();
            $lastId = $stok_batches->last()->id_stok_batch;

            // ! Force garbage collection untuk memory efficiency
            unset($stok_batches, $data_keluar);

        } while (true);

        // ! STEP 6: UPDATE tb_stok_batch dengan CASE WHEN (1 query vs N queries)
        $this->updateStokBatchBatch();

        echo "\n✓ ObatKeluarSeeder completed: {$totalProcessed} stok_batch processed\n";
    }

    /**
     * ! Update tb_stok_batch menggunakan CASE WHEN (bulk update dalam 1 query)
     */
    private function updateStokBatchBatch(): void
    {
        // ! Get mapping dari id_stok_batch -> (stok_masuk, safety_stock)
        $stok_batch_data = DB::table('tb_stok_batch')
            ->join('tb_obat', 'tb_stok_batch.id_obat', '=', 'tb_obat.id_obat')
            ->select('tb_stok_batch.id_stok_batch', 'tb_stok_batch.stok_masuk', 'tb_obat.safety_stock')
            ->get()
            ->keyBy('id_stok_batch');

        // ! Get semua stok_keluar per batch
        $keluar_data = DB::table('tb_obat_keluar')
            ->select('id_stok_batch', DB::raw('SUM(stok_keluar) as total_keluar'))
            ->groupBy('id_stok_batch')
            ->get();

        // ! Prepare update data dengan batch processing
        $update_data = [];
        foreach ($keluar_data as $keluar) {
            $batch = $stok_batch_data->get($keluar->id_stok_batch);
            if (!$batch)
                continue;

            $stok_sisa = max(0, $batch->stok_masuk - $keluar->total_keluar);
            $status_stok = StokStatusHelper::getStatus($stok_sisa, $batch->safety_stock);

            $update_data[] = [
                'id' => $keluar->id_stok_batch,
                'stok_keluar' => $keluar->total_keluar,
                'stok_sisa' => $stok_sisa,
                'status_stok' => $status_stok,
            ];
        }

        // ! Lakukan batch update dengan CASE WHEN
        if (!empty($update_data)) {
            $this->batchUpdateStokBatch($update_data);
        }
    }

    /**
     * ! Batch update menggunakan CASE WHEN untuk performa lebih baik
     */
    private function batchUpdateStokBatch(array $updates): void
    {
        if (empty($updates))
            return;

        // ! Build CASE WHEN statements
        $ids = array_column($updates, 'id');
        $ids_sql = implode(',', $ids);

        // ! Build CASE statements untuk setiap kolom
        $stok_keluar_case = "CASE id_stok_batch\n";
        $stok_sisa_case = "CASE id_stok_batch\n";
        $status_stok_case = "CASE id_stok_batch\n";

        foreach ($updates as $update) {
            $stok_keluar_case .= "WHEN {$update['id']} THEN {$update['stok_keluar']}\n";
            $stok_sisa_case .= "WHEN {$update['id']} THEN {$update['stok_sisa']}\n";
            $status_stok_case .= "WHEN {$update['id']} THEN '{$update['status_stok']}'\n";
        }

        $stok_keluar_case .= "ELSE stok_keluar END";
        $stok_sisa_case .= "ELSE stok_sisa END";
        $status_stok_case .= "ELSE status_stok END";

        // ! Execute single update query
        DB::statement("
            UPDATE tb_stok_batch
            SET
                stok_keluar = {$stok_keluar_case},
                stok_sisa = {$stok_sisa_case},
                status_stok = {$status_stok_case},
                updated_at = NOW()
            WHERE id_stok_batch IN ({$ids_sql})
        ");
    }
}

