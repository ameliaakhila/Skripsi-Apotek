<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Admin\ObatKeluar;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        // OPTIMIZED: Use eager loading, only select needed columns, use pagination at DB level
        // Remove redundant leftJoin since we're using eager loading
        // Process at database level instead of PHP for efficiency
        $data = ObatKeluar::with([
            'ObatMasuk:id_obat_masuk,id_obat,tanggal_kedaluwarsa',  // Only select needed columns
            'ObatMasuk.Obat:id_obat,nama_obat',
            'DataCabang:id_cabang,nama_cabang'
        ])
            ->select(
                'id_obat_keluar',
                'id_obat_masuk',
                'id_cabang',
                'stok_keluar',
                'keterangan',
                'created_at',
                'tanggal_keluar'
            )
            ->orderBy('created_at', 'desc')
            ->simplePaginate(20);  // Use pagination for better performance

        return view('manager.laporan', compact('data'));
    }
}
