<?php

namespace App\Http\Controllers\Shared;

use App\Helpers\StokStatusHelper;
use App\Http\Controllers\Controller;
use App\Models\Admin\StokBatch;
use App\Models\Admin\DataCabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class StokCabangController extends Controller
{
    /**
     * Monitoring global stok per obat per cabang (tanpa detail batch).
     */
    public function index(Request $request)
    {
        $dataCabang = Cache::remember('cabang_list', now()->addDay(), function () {
            return DataCabang::select('id_cabang', 'nama_cabang')->orderBy('nama_cabang')->get();
        });

        $selectedCabangId = $request->input('id_cabang');

        $query = StokBatch::query()
            ->join('tb_obat', 'tb_obat.id_obat', '=', 'tb_stok_batch.id_obat')
            ->join('tb_cabang', 'tb_cabang.id_cabang', '=', 'tb_stok_batch.id_cabang')
            ->select(
                'tb_stok_batch.id_obat',
                'tb_stok_batch.id_cabang',
                'tb_obat.nama_obat',
                'tb_obat.safety_stock',
                'tb_cabang.nama_cabang',
                DB::raw('COALESCE(SUM(tb_stok_batch.stok_sisa), 0) as total_stok_sisa'),
                DB::raw('COUNT(tb_stok_batch.id_stok_batch) as total_batch')
            );

        if ($selectedCabangId) {
            $query->where('tb_stok_batch.id_cabang', $selectedCabangId);
        }

        $stok = $query
            ->groupBy(
                'tb_stok_batch.id_obat',
                'tb_stok_batch.id_cabang',
                'tb_obat.nama_obat',
                'tb_obat.safety_stock',
                'tb_cabang.nama_cabang'
            )
            ->orderBy('tb_cabang.nama_cabang', 'asc')
            ->orderBy('tb_obat.nama_obat', 'asc')
            ->get()
            ->map(function ($item) {
                $item->status_stok = StokStatusHelper::getStatus(
                    (int) $item->total_stok_sisa,
                    (int) ($item->safety_stock ?? 0)
                );

                return $item;
            });

        $title = $selectedCabangId
            ? 'Monitoring Stok Cabang: ' . ($dataCabang->firstWhere('id_cabang', $selectedCabangId)->nama_cabang ?? 'Tidak Ditemukan')
            : 'Monitoring Stok Semua Cabang';

        return view(
            'shared.stok-cabang',
            compact('stok', 'title', 'dataCabang', 'selectedCabangId')
        );
    }

    public function update(Request $request, $id_stok_batch)
    {
        return redirect('/stok-cabang')
            ->with('error', 'Penyesuaian stok tidak dilakukan dari menu Stok Cabang. Gunakan menu Stok Opname.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_stok_batch)
    {
        return redirect('/stok-cabang')
            ->with('error', 'Penghapusan data batch tidak diizinkan dari menu Stok Cabang.');
    }
}
