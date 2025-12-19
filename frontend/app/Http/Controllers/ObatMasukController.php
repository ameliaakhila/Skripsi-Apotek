<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ObatMasuk;
use App\Models\KategoriObat;
use App\Models\Obat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ObatMasukController extends Controller
{
    public function index()
    {
        $data = ObatMasuk::all();
        $obat = Obat::all();
        $kategori = KategoriObat::all();
        $satuanList = Obat::select('satuan_obat')->distinct()->get();

        $obatMasukList = ObatMasuk::with('Obat')
            ->select('id_obat')
            ->distinct()
            ->get();

        // Buat kode obat otomatis
        $tanggal_hari_ini = date('ymd');
        $nomor_urut_terakhir = DB::table('tb_obat_masuk')->max('id_obat_masuk') ?? 0;
        $nomor_urut_terakhir++;
        $nomor_urut_format = str_pad($nomor_urut_terakhir, 2, '1');
        $kodeObat = 'SF' . $tanggal_hari_ini . $nomor_urut_format;
        $type = 'tambah';
        return view('components.main.obat_masuk', compact('data', 'obatMasukList', 'obat', 'satuanList', 'kategori', 'kodeObat', 'type'));
    }

    // ! ====================== CREATE =========================
    public function insert(Request $request)
    {
        try {
            // dd($request->all());
            // ? validasi apakah obat sesuai dengan type tabel
            $request->validate([
                'kode_obat' => 'required|string|unique:tb_obat_masuk,kode_obat',
                'id_obat' => 'required|exists:tb_obat,id_obat',
                'id_kategori_obat' => 'required|exists:tb_kategori_obat,id_kategori_obat',
                'tanggal_masuk' => 'required|date',
                'tanggal_kadaluarsa' => 'required|date|after:tanggal_masuk',
                'tanggal_keluar' => 'required|date|before:tanggal_kadaluarsa',
                'satuan_obat' => 'required|string|max:50',
                'stok_masuk' => 'required|integer|min:1',
                'harga_obat' => 'required|integer|min:1',
            ]);
            // dd($request->all());

            // ? Hitung stok sisa obat dari (stok_masuk - stok_keluar)
            $stok_sisa = $request->stok_masuk - $request->stok_keluar;

            ObatMasuk::create([
                'kode_obat' => $request->kode_obat,
                'id_obat' => $request->id_obat,
                'id_kategori_obat' => $request->id_kategori_obat,
                'tanggal_masuk' => $request->tanggal_masuk,
                'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa,
                'tanggal_keluar' => $request->tanggal_keluar,
                'satuan_obat' => $request->satuan_obat,
                'stok_masuk' => $request->stok_masuk,
                'stok_sisa' => $stok_sisa,
                'harga_obat' => $request->harga_obat,
            ]);


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
            'id_obat' => 'required|exists:tb_obat,id_obat',
            'id_kategori_obat' => 'required|exists:tb_kategori_obat,id_kategori_obat',
            'kode_obat' => 'required|string|max:10',
            'tanggal_masuk' => 'required|date',
            'tanggal_kadaluarsa' => 'required|date',
            'tanggal_keluar' => 'required|date',
            'satuan_obat' => 'required|string|max:50',
            'stok_masuk' => 'required|integer|min:1',
            'stok_keluar' => 'required|integer|min:0',
            'harga_obat' => 'required|integer|min:1',
        ]);

        // dd("MASUK UPDATE", $id_obat_masuk, $request->all());
        try {
            // Ambil data ObatMasuk
            $obatMasuk = ObatMasuk::findOrFail($id_obat_masuk);
            // dd("DEBUG OBATMASUK BEFORE", $obatMasuk);

            // Ambil data id_obat dan id_kategori_obat dari tabel terkait berdasarkan nama
            $obat = Obat::findOrFail($request->id_obat);
            $kategori = KategoriObat::findOrFail($request->id_kategori_obat);


            $stok_sisa = $request->stok_masuk - $request->stok_keluar;
            if ($stok_sisa < 0) {
                return back()->with('error', 'Stok keluar tidak boleh lebih besar dari stok masuk!');
            }
            // Ambil data Obat yang dipilih user
            $obatMasuk->update([
                'id_obat' => $request->id_obat,
                'kode_obat' => $request->kode_obat,
                'satuan_obat' => $request->satuan_obat,
                'id_kategori_obat' => $request->id_kategori_obat ?? $kategori->id_kategori_obat,
                'tanggal_masuk' => $request->tanggal_masuk,
                'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa,
                'tanggal_keluar' => $request->tanggal_keluar,
                'stok_masuk' => $request->stok_masuk,
                'stok_keluar' => $request->stok_keluar,
                'stok_sisa' => $stok_sisa,
                'harga_obat' => $request->harga_obat,
            ]);

            // dd("DEBUG OBATMASUK AFTER", $obatMasuk);
            $obat->update([
                'satuan_obat' => $request->satuan_obat,
                'id_kategori_obat' => $request->id_kategori_obat ?? $obat->id_kategori_obat,
            ]);

            // session()->flash('success', 'Data berhasil diupdate');
            // dd(session()->all());

            // ? jika berhasil alert sukses akan muncul
            return redirect('/obat-masuk')->with('success', 'Data berhasil diupdate');

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
