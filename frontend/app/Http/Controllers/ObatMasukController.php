<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin\ObatMasuk;
use App\Models\Admin\DataKategori;
use App\Models\Admin\DataSatuan;
use App\Models\Admin\Obat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ObatMasukController extends Controller
{
    public function index()
    {
        $data = ObatMasuk::with([
            'Obat:id_obat,nama_obat,id_satuan',
            'DataSatuan:id_satuan,nama_satuan',
            'DataKategori:id_kategori,nama_kategori'
        ])->simplePaginate(10);

        $kodeObat = 'SF' . uniqid();

        // gunakan cache untuk master data untuk mengurangi query ke database
        $masterData = Cache::remember('master_data', now()->addHour(), function () {
            return [
                'obat' => Obat::select('id_obat', 'nama_obat')->get(),
                'kategori' => DataKategori::select('id_kategori', 'nama_kategori')->get(),
                'satuan' => DataSatuan::select('id_satuan', 'nama_satuan')->get(),
            ];
        });
        $obat = $masterData['obat'];
        $kategori = $masterData['kategori'];
        $satuan = $masterData['satuan'];

        $tanggal_hari_ini = date('dy');
        $nomor_urut_terakhir = Obat::latest('id_obat')->value('id_obat') ?? 0;
        $kodeBatch = 'BAC' . now()->format('dm') . 'SF' . str_pad($nomor_urut_terakhir, 3, '0');
        $title = "Obat Masuk";
        return view('components.admin.main.obat-masuk', compact('data', 'obat', 'satuan', 'kategori', 'kodeObat', 'kodeBatch', 'title'));
    }

    // ! ====================== CREATE =========================
    public function insert(Request $request)
    {
        try {

            // ? validasi apakah obat sesuai dengan type tabel
            $validated = $request->validate([
                'kode_obat' => 'required|string|unique:tb_obat_masuk,kode_obat',
                'kode_batch' => 'required|string',
                'tanggal_masuk' => 'required|date',
                'tanggal_kedaluwarsa' => 'required|date|after:tanggal_masuk',
                'stok_masuk' => 'required|integer|min:1',
                'stok_keluar' => 'nullable|integer|min:0',
                'harga_obat' => 'required|integer|min:1',
                'id_obat' => 'required|exists:tb_obat,id_obat',
                'id_satuan' => 'required|exists:tb_satuan,id_satuan',
                'id_kategori' => 'required|exists:tb_kategori,id_kategori',
            ]);
            // dd($request->all());

            // set default 0 kalau kosong
            $validated['stok_keluar'] = $validated['stok_keluar'] ?? 0;

            // hitung stok sisa
            $validated['stok_sisa'] = $validated['stok_masuk'] - $validated['stok_keluar'];

            // dd("MASUK INSERT", $validated);

            // ? simpan data ke database
            ObatMasuk::create($validated);


            return redirect('/obat-masuk')->with([
                'success' => 'Data berhasil ditambahkan',
                'kode' => $request->kode_obat
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
        $request->validate([
            'kode_obat' => 'required',
            'kode_batch' => 'required|string',
            'tanggal_masuk' => 'required|date',
            'tanggal_kedaluwarsa' => 'required|date|after:tanggal_masuk',
            'stok_masuk' => 'required|integer|min:1',
            'stok_keluar' => 'nullable|integer|min:0',
            'harga_obat' => 'required|integer|min:1',
            'id_obat' => 'required|exists:tb_obat,id_obat',
            'id_satuan' => 'required|exists:tb_satuan,id_satuan',
            'id_kategori' => 'required|exists:tb_kategori,id_kategori',
        ]);

        // dd("Data valudasi", $request->all());
        try {
            // Ambil data ObatMasuk
            $obatMasuk = ObatMasuk::findOrFail($id_obat_masuk);
            $obat = Obat::findOrFail($request->id_obat);
            $stok_sisa = $request->stok_masuk - $request->stok_keluar;

            // dd( $obatMasuk, $obat, $stok_sisa);

            if ($stok_sisa < 0) {
                return back()->with('error', 'Stok keluar tidak boleh lebih besar dari stok masuk!');
            }
            // Ambil data Obat yang dipilih user
            $obatMasuk->update([
                'id_obat' => $request->id_obat,
                'kode_obat' => $request->kode_obat,
                'id_kategori' => $request->id_kategori,
                'id_satuan' => $request->id_satuan,
                'tanggal_masuk' => $request->tanggal_masuk,
                'tanggal_kedaluwarsa' => $request->tanggal_kedaluwarsa,
                'stok_masuk' => $request->stok_masuk,
                'stok_keluar' => $request->stok_keluar,
                'stok_sisa' => $stok_sisa,
                'harga_obat' => $request->harga_obat,
            ]);

            $obat->update([
                'id_satuan' => $request->id_satuan,
                'id_kategori' => $request->id_kategori ?? $obat->id_kategori,
            ]);

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
            $obatMasuk->delete();

            return redirect('/obat-masuk')->with('success', 'Data berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            // Biasanya error jika ada foreign key constraint
            return redirect('/obat-masuk')->with('error', 'Data gagal dihapus: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Error umum lainnya
            return redirect('/obat-masuk')->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

}
