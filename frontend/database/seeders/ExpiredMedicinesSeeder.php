<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\StokBatch;
use Carbon\Carbon;

class ExpiredMedicinesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update 5 random batches to expire within the next 30 days
        $batches = StokBatch::where('stok_sisa', '>', 0)
            ->inRandomOrder()
            ->limit(5)
            ->get();

        foreach ($batches as $batch) {
            $batch->update([
                'tanggal_kedaluwarsa' => Carbon::now()->addDays(rand(1, 25))
            ]);
        }
    }
}
