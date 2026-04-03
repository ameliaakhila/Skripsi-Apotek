<?php

namespace App\Http\Controllers\Pharmacist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\ObatMasuk;
use App\Models\Admin\Obat;
use App\Models\Admin\StokBatch;
use App\Helpers\StokStatusHelper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ObatMasukController extends Controller
{
    // ! ====================== READ =========================
    public function index()
    {
        // OPTIMIZED: Nested eager loading dengan relationships dari Obat
        $data = ObatMasuk::with([
            'Obat:id_obat,nama_obat,id_kategori,id_supplier,id_satuan',
            'Obat.DataKategori:id_kategori,nama_kategori',
            'Obat.DataSupplier:id_supplier,nama_supplier',
            'Obat.DataSatuan:id_satuan,nama_satuan'
        ])
            ->orderBy('created_at', 'desc')
            ->simplePaginate(25);  // Add pagination

        // Master obat list dengan eager loading relationships
        $obat = Cache::remember('master_obat_list', now()->addHours(2), function () {
            return Obat::with([
                'DataKategori:id_kategori,nama_kategori',
                'DataSupplier:id_supplier,nama_supplier',
                'DataSatuan:id_satuan,nama_satuan'
            ])
                ->select('id_obat', 'nama_obat', 'id_kategori', 'id_supplier', 'id_satuan')
                ->whereHas('DataKategori')  // Filter: Only include Obat with valid kategori
                ->whereHas('DataSupplier')  // Filter: Only include Obat with valid supplier
                ->whereHas('DataSatuan')    // Filter: Only include Obat with valid satuan
                ->orderBy('created_at', 'desc')
                ->get();
        });

        // Generate next batch code more efficiently
        $nomor_urut_terakhir = Obat::query()->max('id_obat') ?? 0;
        $kodeBatch = 'BAC' . now()->format('dmy') . 'SF' . str_pad($nomor_urut_terakhir + 1, 3, '0', STR_PAD_LEFT);
        return view('pharmacist.obat-masuk', compact('data', 'obat', 'kodeBatch'));
    }

    // ! ====================== CREATE =========================
    public function insert(Request $request)
    {
        try {
            // dd($request->all());

            // ? validasi apakah obat sesuai dengan type tabel
            $validated = $request->validate([
                'kode_batch' => 'required|string',
                'tanggal_masuk' => 'required|date',
                'tanggal_kedaluwarsa' => 'required|date|after:tanggal_masuk',
                'stok_masuk' => 'required|integer|min:1',
                'id_obat' => 'required|exists:tb_obat,id_obat',
            ], [
                'tanggal_kedaluwarsa.required' => 'Tanggal kedaluwarsa harus diisi',
                'tanggal_kedaluwarsa.date' => 'Format tanggal kedaluwarsa tidak valid',
                'tanggal_kedaluwarsa.after' => 'Tanggal kedaluwarsa harus lebih besar dari tanggal masuk',
            ]);
            // dd('After validated', $request->all());

            // set default 0 kalau kosong
            $validated['stok_keluar'] = $validated['stok_keluar'] ?? 0;

            // hitung stok sisa
            $validated['stok_sisa'] = $validated['stok_masuk'] - $validated['stok_keluar'];

            // dd("Data Validasi", $validated);



            // ? simpan data ke database
            ObatMasuk::create($validated);

            // set id cabang kedalam default cabang 1 karna gudangnya ada disana
            $validated['id_cabang'] = 1;

            // dd("Data Yang akan masuk kedalam stok batch", $validated);

            // ? simpan ke tb_stok_batch
            StokBatch::create([
                'kode_batch' => $validated['kode_batch'],
                'tanggal_kedaluwarsa' => $validated['tanggal_kedaluwarsa'],
                'stok_masuk' => $validated['stok_masuk'],
                'stok_sisa' => $validated['stok_sisa'],
                'status_stok' => $this->getStatusStok($validated['stok_sisa'], $validated['id_obat']),
                'id_obat' => $validated['id_obat'],
                'id_cabang' => $validated['id_cabang'],
            ]);

            $obat = Obat::find($validated['id_obat']);

            // ! INVALIDATE cache saat ada data baru
            Cache::forget('master_obat_available');
            Cache::forget('master_kodebatch_available');
            Cache::forget('master_obat_list');

            return redirect('/obat-masuk')->with([
                'success' => 'Data Obat ' . $obat->nama_obat . ' berhasil ditambahkan',
            ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal Input Data!')
                ->withInput();
        }
    }

    // ! ================= UPDATE =====================
    public function update(Request $request, $id_obat_masuk)
    {
        // dd($request->all());
        // Validasi input sebelum update
        $validated = $request->validate([
            'kode_batch' => 'required|string',
            'tanggal_masuk' => 'required|date',
            'tanggal_kedaluwarsa' => 'required|date|after:tanggal_masuk',
            'stok_masuk' => 'required|integer|min:1',
            'stok_keluar' => 'nullable|integer|min:0',
            'id_obat' => 'required|exists:tb_obat,id_obat',
        ], [
            'tanggal_masuk.required' => 'Tanggal masuk harus diisi',
            'tanggal_masuk.date' => 'Format tanggal masuk tidak valid',
            'tanggal_kedaluwarsa.required' => 'Tanggal kedaluwarsa harus diisi',
            'tanggal_kedaluwarsa.date' => 'Format tanggal kedaluwarsa tidak valid',
            'tanggal_kedaluwarsa.after' => 'Tanggal kedaluwarsa harus lebih besar dari tanggal masuk',
        ]);

        // dd("Data valudasi", $request->all());
        try {
            // Ambil data ObatMasuk
            $obatMasuk = ObatMasuk::findOrFail($id_obat_masuk);
            $obat = Obat::findOrFail($validated['id_obat']);
            $stok_sisa = $validated['stok_masuk'] - ($validated['stok_keluar'] ?? 0);

            // dd( $obatMasuk, $obat, $stok_sisa);

            if ($stok_sisa < 0) {
                return back()->with('error', 'Stok keluar tidak boleh lebih besar dari stok masuk!');
            }
            // Ambil data Obat yang dipilih user
            $obatMasuk->update([
                'kode_batch' => $validated['kode_batch'],
                'id_obat' => $validated['id_obat'],
                'tanggal_masuk' => $validated['tanggal_masuk'],
                'tanggal_kedaluwarsa' => $validated['tanggal_kedaluwarsa'],
                'stok_masuk' => $validated['stok_masuk'],
                'stok_keluar' => $validated['stok_keluar'],
                'stok_sisa' => $stok_sisa,
            ]);

            // ? update tb_stok_batch yang terkait dengan obat_masuk ini
            // Cari stok_batch berdasarkan kode_batch lama (anggap relasi 1:1)
            $stokBatch = StokBatch::where('kode_batch', $obatMasuk->getOriginal('kode_batch'))
                ->where('id_obat', $obatMasuk->getOriginal('id_obat'))
                ->first();

            if ($stokBatch) {
                $stokBatch->update([
                    'kode_batch' => $validated['kode_batch'],
                    'tanggal_kedaluwarsa' => $validated['tanggal_kedaluwarsa'],
                    'stok_masuk' => $validated['stok_masuk'],
                    'stok_sisa' => $stok_sisa,
                    'status_stok' => $this->getStatusStok($stok_sisa, $validated['id_obat']),
                    'id_cabang' => $validated['id_cabang'],
                ]);
            }

            // ! INVALIDATE cache saat update
            Cache::forget('master_obat_available');
            Cache::forget('master_kodebatch_available');
            Cache::forget('master_obat_list');

            // ? jika berhasil alert sukses akan muncul
            return redirect('/obat-masuk')->with('success', 'Data ' . $obatMasuk->nama_obat . ' berhasil diupdate');

        } catch (\Exception $e) {
            Log::error('Update Obat Masuk Gagal', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal update data: ' . $e->getMessage());
        }
    }

    // ! ================ DELETE ===================
    public function destroy($id_obat_masuk)
    {
        try {
            $obatMasuk = ObatMasuk::findOrFail($id_obat_masuk);

            // ? hapus entry di tb_stok_batch yang terkait
            StokBatch::where('kode_batch', $obatMasuk->kode_batch)
                ->where('id_obat', $obatMasuk->id_obat)
                ->delete();

            // ? hapus entry di tb_obat_masuk
            $obatMasuk->delete();

            // ! INVALIDATE cache saat delete
            Cache::forget('master_obat_available');
            Cache::forget('master_kodebatch_available');
            Cache::forget('master_obat_list');

            return redirect('/obat-masuk')->with('success', 'Data berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            // Biasanya error jika ada foreign key constraint
            return redirect('/obat-masuk')->with('error', 'Data gagal dihapus: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Error umum lainnya
            return redirect('/obat-masuk')->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    // ! ================ HELPER METHOD ===================
    /**
     * Tentukan status stok berdasarkan safety_stock dari Obat
     * Aman: stok >= 120% dari safety_stock
     * Menipis: 30% – 120% dari safety_stock
     * Habis: stok <= 30% dari safety_stock
     */
    private function getStatusStok($stok_sisa, $id_obat)
    {
        // Fetch obat untuk mendapatkan safety_stock
        $obat = Obat::find($id_obat);
        $safety_stock = $obat->safety_stock ?? 1;

        return StokStatusHelper::getStatus($stok_sisa, $safety_stock);
    }

}
