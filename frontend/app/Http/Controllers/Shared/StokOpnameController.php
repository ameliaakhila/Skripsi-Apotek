<?php

namespace App\Http\Controllers\Shared;

use App\Helpers\StokStatusHelper;
use App\Http\Controllers\Controller;
use App\Models\Admin\DataCabang;
use App\Models\Admin\StokBatch;
use App\Models\Admin\StokOpname;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StokOpnameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StokOpname::with([
            'Obat:id_obat,nama_obat,id_satuan',
            'StokBatch:id_stok_batch,kode_batch,tanggal_kedaluwarsa,stok_sisa,id_cabang,id_obat',
            'DataCabang:id_cabang,nama_cabang',
        ])->select('tb_stok_opname.*');

        if ($request->filled('id_cabang')) {
            $query->where('tb_stok_opname.id_cabang', $request->id_cabang);
        }

        if ($request->filled('id_obat')) {
            $query->where('tb_stok_opname.id_obat', $request->id_obat);
        }

        $data = $query->orderBy('tb_stok_opname.tanggal_opname', 'desc')
            ->orderBy('tb_stok_opname.created_at', 'desc')
            ->simplePaginate(15)
            ->withQueryString();

        $dataCabang = Cache::remember('cabang_list_for_opname', now()->addDay(), function () {
            return DataCabang::select('id_cabang', 'nama_cabang')
                ->orderBy('nama_cabang', 'asc')
                ->get();
        });

        $batchQuery = StokBatch::with([
            'Obat:id_obat,nama_obat,safety_stock',
            'DataCabang:id_cabang,nama_cabang',
        ])
            ->select('id_stok_batch', 'kode_batch', 'id_obat', 'id_cabang', 'stok_sisa', 'tanggal_kedaluwarsa')
            ->orderBy('tanggal_kedaluwarsa', 'asc');

        if ($request->filled('id_cabang')) {
            $batchQuery->where('id_cabang', $request->id_cabang);
        }

        $dataStokBatch = $batchQuery->get();

        return view('shared.stok-opname', compact('data', 'dataCabang', 'dataStokBatch'));
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
        $validated = $request->validate([
            'id_stok_batch' => 'required|exists:tb_stok_batch,id_stok_batch',
            'tanggal_opname' => 'required|date',
            'stok_fisik' => 'required|integer|min:0',
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect('/stok-opname')->with('error', 'Sesi pengguna tidak ditemukan. Silakan login kembali.');
        }

        try {
            $result = DB::transaction(function () use ($validated, $user) {
                $stokBatch = StokBatch::with('Obat')
                    ->lockForUpdate()
                    ->findOrFail($validated['id_stok_batch']);

                $stokSistem = (int) $stokBatch->stok_sisa;
                $stokFisik = (int) $validated['stok_fisik'];
                $selisih = $stokFisik - $stokSistem;
                $keterangan = $this->determineKeterangan($selisih);
                $kodeOpname = $this->generateKodeOpname();

                StokOpname::create([
                    'kode_opname' => $kodeOpname,
                    'tanggal_opname' => $validated['tanggal_opname'],
                    'stok_sistem' => $stokSistem,
                    'stok_fisik' => $stokFisik,
                    'selisih' => $selisih,
                    'keterangan' => $keterangan,
                    'id_obat' => $stokBatch->id_obat,
                    'id_stok_batch' => $stokBatch->id_stok_batch,
                    'id_cabang' => $stokBatch->id_cabang,
                    'id_user' => $user->id_user,
                ]);

                $stokBatch->stok_sisa = $stokFisik;
                $stokBatch->status_stok = StokStatusHelper::getStatus(
                    $stokFisik,
                    (int) ($stokBatch->Obat->safety_stock ?? 0)
                );
                $stokBatch->tujuan_penyesuaian = 'Stok Opname';
                $stokBatch->catatan = 'Penyesuaian via opname ' . $kodeOpname . ' pada ' . Carbon::parse($validated['tanggal_opname'])->format('Y-m-d');
                $stokBatch->save();

                return $kodeOpname;
            });

            Cache::forget('stok_batch_kedaluwarsa_list');
            Cache::forget('stok_agregat_all_branches');
            Cache::forget('master_stok_batch_available');

            return redirect('/stok-opname')->with('success', 'Sesi opname ' . $result . ' berhasil disimpan dan stok batch sudah diperbarui.');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan stok opname: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StokOpname $stokOpname)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StokOpname $stokOpname)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id_stok_opname)
    {
        return redirect('/stok-opname')
            ->with('error', 'Histori stok opname bersifat audit trail dan tidak dapat diubah. Buat sesi opname baru jika diperlukan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_stok_opname)
    {
        return redirect('/stok-opname')
            ->with('error', 'Histori stok opname bersifat audit trail dan tidak dapat dihapus.');
    }

    private function determineKeterangan(int $selisih): string
    {
        if ($selisih < 0) {
            return 'Menipis';
        }

        if ($selisih > 0) {
            return 'Lebih';
        }

        return 'Sesuai';
    }

    private function generateKodeOpname(): string
    {
        $prefix = 'OP' . now()->format('dmyy');

        do {
            $suffix = strtoupper(str_pad(base_convert(random_int(0, 1295), 10, 36), 2, '0', STR_PAD_LEFT));
            $kode = $prefix . $suffix;
        } while (StokOpname::where('kode_opname', $kode)->exists());

        return $kode;
    }
}
