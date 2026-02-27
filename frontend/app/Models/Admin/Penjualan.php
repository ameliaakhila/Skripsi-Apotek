<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use NumberFormatter;

class Penjualan extends Model
{
    protected $table = 'tb_penjualan';

    protected $primaryKey = 'id_penjualan';
    public $incrementing = true;
    protected $fillable = [
        'id_cabang',
        'tanggal_penjualan',
        'jumlah_penjualan',
        'id_obat',
        'id_obat_masuk',
        'id_kategori'
    ];

    public function getHargaObatFormattedAttribute()
    {
        $fmt = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
        $fmt->setAttribute(NumberFormatter::FRACTION_DIGITS, 0);
        return $fmt->formatCurrency($this->harga_obat, 'IDR');
    }

    public function DataKategori()
    {
        return $this->belongsTo(DataKategori::class, 'id_kategori', 'id_kategori');
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
