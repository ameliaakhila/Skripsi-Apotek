<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    protected $table = 'tb_obat';

    protected $primaryKey = 'id_obat';

    protected $fillable = [
        'nama_obat',
        'id_satuan',
        'id_kategori',
        'id_supplier',
    ];

    public function DataSatuan()
    {
        return $this->belongsTo(DataSatuan::class, 'id_satuan', 'id_satuan');
    }
    public function DataSupplier()
    {
        return $this->belongsTo(DataSupplier::class, 'id_supplier', 'id_supplier');
    }
    public function DataKategori()
    {
        return $this->belongsTo(DataKategori::class, 'id_kategori', 'id_kategori');
    }
    public function ObatMasuk()
    {
        return $this->hasMany(ObatMasuk::class, 'id_obat', 'id_obat');
    }
}
