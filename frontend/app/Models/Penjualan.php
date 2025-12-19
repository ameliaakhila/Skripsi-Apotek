<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use NumberFormatter;

class Penjualan extends Model
{
    protected $table = 'tb_penjualan_obat';

    protected $primaryKey = 'id_penjualan_obat';
    public $incrementing = true;
    protected $fillable = ['kode_obat', 'stok_keluar', 'satuan_obat', 'harga_obat', 'id_obat', 'id_obat_masuk', 'id_kategori_obat'];

    public function getHargaObatFormattedAttribute()
    {
        $fmt = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
        $fmt->setAttribute(NumberFormatter::FRACTION_DIGITS, 0);
        return $fmt->formatCurrency($this->harga_obat, 'IDR');
    }

    public function KategoriObat()
    {
        return $this->belongsTo(KategoriObat::class, 'id_kategori_obat', 'id_kategori_obat');
    }
    public function Obat()
    {
        return $this->belongsTo(Obat::class, 'id_obat', 'id_obat');
    }
    public function ObatMasuk()
    {
        return $this->belongsTo(ObatMasuk::class, 'id_obat_masuk', 'id_obat_masuk');
    }
}
