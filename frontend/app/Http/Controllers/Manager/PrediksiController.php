<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Admin\StokBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PrediksiController extends Controller
{
    /**
     * Determine stock status based on quantity
     */
    private function getStokStatus($stok_sisa, $safety_stock)
    {
        if ($stok_sisa <= 0) {
            return 'habis';
        } elseif ($stok_sisa <= $safety_stock) {
            return 'menipis';
        }
        return 'aman';
    }

    public function index()
    {
        // OPTIMIZED: Use select() with specific columns instead of raw select,
        // Add pagination, cache results for better performance
        $cacheKey = 'prediksi_stok_data';

        $data = Cache::remember($cacheKey, now()->addHours(1), function () {
            return StokBatch::with('Obat:id_obat,nama_obat,safety_stock')
                ->select(
                    'id_stok_batch',
                    'id_obat',
                    DB::raw('SUM(stok_masuk - stok_keluar) as stok_sisa'),
                    DB::raw('COUNT(*) as batch_count')
                )
                ->groupBy('id_obat')
                ->get()
                ->map(function ($batch) {
                    $stok_sisa = $batch->stok_sisa ?? 0;
                    $safety_stock = $batch->Obat->safety_stock ?? 10;

                    return [
                        'id_stok_batch' => $batch->id_stok_batch,
                        'id_obat' => $batch->id_obat,
                        'nama_obat' => $batch->Obat->nama_obat,
                        'stok_sisa' => $stok_sisa,
                        'safety_stock' => $safety_stock,
                        'batch_count' => $batch->batch_count,
                        'status' => $this->getStokStatus($stok_sisa, $safety_stock)
                    ];
                })
                ->sortBy('stok_sisa')
                ->values();
        });

        return view('manager.prediksi-obat', compact('data'));
    }

    public function upload(Request $request)
    {
        // Validasi file
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $data = [];

        // Baca file CSV
        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $data[] = $row;
            }
            fclose($handle);
        }
        return view('manager.prediksi-obat', compact('data'));
    }

    public function obat_kedaluwarsa()
    {
        // ! OPTIMIZATION: Cache expired/near-expiry medicine data (3600s = 1 hour)
        $cacheKey = 'obat_kedaluwarsa_report';
        $data = Cache::remember($cacheKey, 3600, function () {
            // Query actual expired/near-expiry medicine data from database
            $today = now()->format('Y-m-d');

            return StokBatch::with('Obat', 'ObatMasuk')
                ->leftJoin('tb_obat_masuk', 'tb_stok_batch.id_obat_masuk', '=', 'tb_obat_masuk.id_obat_masuk')
                ->where('tb_obat_masuk.tanggal_kedaluwarsa', '<=', now()->addMonths(3)->format('Y-m-d'))
                ->orderBy('tb_obat_masuk.tanggal_kedaluwarsa', 'asc')
                ->select('tb_stok_batch.*', 'tb_obat_masuk.tanggal_kedaluwarsa')
                ->get()
                ->map(function ($stok) use ($today) {
                    $expiry_date = \Carbon\Carbon::parse($stok->tanggal_kedaluwarsa);
                    $status_terjual = $stok->status == 'keluar' ? 'Terjual' : 'Belum Terjual';
                    $kondisi = $expiry_date->isPast() ? 'Sudah Kadaluarsa' : ($expiry_date->diffInDays(now()) <= 30 ? 'Kurang Baik' : 'Baik');

                    return [
                        'nama_obat' => $stok->Obat->nama_obat,
                        'jumlah_obat' => $stok->jumlah_batch,
                        'tanggal_kedaluwarsa' => $expiry_date->format('d F Y'),
                        'status_terjual' => $status_terjual,
                        'kondisi' => $kondisi
                    ];
                });
        });

        // Fallback: If no expiry data, show empty collection
        if ($data->isEmpty()) {
            $data = collect([]);
        }

        return view('manager.obat-kedaluwarsa', compact('data'));
    }
}
