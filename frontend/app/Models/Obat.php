<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    protected $table = 'tb_obat';

    protected $primaryKey = 'id_obat';
    protected $fillable = [
        'nama_obat',
        'pabrik_obat',
        'satuan_obat',
        'id_kategori_obat'
    ];
    public function KategoriObat()
    {
        return $this->belongsTo(KategoriObat::class, 'id_kategori_obat', 'id_kategori_obat');
    }
    public function ObatMasuk()
    {
        $this->hasMany(ObatMasuk::class, 'id_obat', 'id_obat');
    }
}
