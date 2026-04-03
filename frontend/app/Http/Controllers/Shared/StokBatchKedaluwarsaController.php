<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Admin\DataCabang;
use App\Models\Admin\Obat;
use App\Models\Admin\StokBatch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StokBatchKedaluwarsaController extends Controller
{
    /**
     * Halaman operasional detail stok per batch (FEFO view).
     */
    public function index(Request $request)
    {
        $dataCabang = Cache::remember('cabang_list_for_batch_expiry', now()->addDay(), function () {
            return DataCabang::select('id_cabang', 'nama_cabang')
                ->orderBy('nama_cabang', 'asc')
                ->get();
        });

        $dataObat = Cache::remember('obat_list_for_batch_expiry', now()->addHours(2), function () {
            return Obat::select('id_obat', 'nama_obat')
                ->orderBy('nama_obat', 'asc')
                ->get();
        });

        $query = StokBatch::with([
            'Obat:id_obat,nama_obat,safety_stock',
            'DataCabang:id_cabang,nama_cabang',
        ])
            ->select('id_stok_batch', 'kode_batch', 'id_obat', 'id_cabang', 'stok_masuk', 'stok_keluar', 'stok_sisa', 'tanggal_kedaluwarsa', 'status_stok')
            ->orderBy('tanggal_kedaluwarsa', 'asc');

        if ($request->filled('id_cabang')) {
            $query->where('id_cabang', $request->id_cabang);
        }

        if ($request->filled('id_obat')) {
            $query->where('id_obat', $request->id_obat);
        }

        if ($request->filled('status_expiry')) {
            $today = Carbon::today();

            if ($request->status_expiry === 'expired') {
                $query->whereDate('tanggal_kedaluwarsa', '<', $today);
            } elseif ($request->status_expiry === 'near') {
                $query->whereBetween('tanggal_kedaluwarsa', [$today, $today->copy()->addDays(30)]);
            } elseif ($request->status_expiry === 'safe') {
                $query->whereDate('tanggal_kedaluwarsa', '>', $today->copy()->addDays(30));
            }
        }

        $stok = $query->simplePaginate(25)->withQueryString();

        $today = Carbon::today();
        $stok->getCollection()->transform(function ($item) use ($today) {
            $sisaHari = $today->diffInDays($item->tanggal_kedaluwarsa, false);

            if ($sisaHari < 0) {
                $item->indikator_kedaluwarsa = ['label' => 'Kedaluwarsa', 'badge' => 'danger'];
            } elseif ($sisaHari <= 30) {
                $item->indikator_kedaluwarsa = ['label' => 'Mendekati Kedaluwarsa', 'badge' => 'warning'];
            } else {
                $item->indikator_kedaluwarsa = ['label' => 'Aman', 'badge' => 'success'];
            }

            $item->sisa_hari = $sisaHari;

            return $item;
        });

        $title = "Data Batch & Kedaluwarsa";
        return view('shared.stok-batch-kedaluwarsa', compact('stok', 'title', 'dataCabang', 'dataObat'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return redirect('/stok-batch-kedaluwarsa')
            ->with('error', 'Menu Batch & Kedaluwarsa hanya untuk monitoring operasional. Gunakan menu transaksi untuk mengubah stok.');
    }

    /**
     * Display the specified resource.
     */
    public function show($stokBatchKedaluwarsa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($stokBatchKedaluwarsa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id_stok_batch)
    {
        return redirect('/stok-batch-kedaluwarsa')
            ->with('error', 'Perubahan stok batch tidak dilakukan dari menu ini.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_stok_batch)
    {
        return redirect('/stok-batch-kedaluwarsa')
            ->with('error', 'Penghapusan batch tidak diizinkan dari menu ini.');
    }
}
