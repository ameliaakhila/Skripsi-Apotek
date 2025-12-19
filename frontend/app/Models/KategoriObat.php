<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriObat extends Model
{
    protected $table = 'tb_kategori_obat';
    protected $primaryKey = 'id_kategori_obat';
    protected $fillable = ['nama_kategori_obat'];

    public function Obat()
    {
        $this->hasMany(Obat::class, 'id_kategori_obat', 'id_kategori_obat');
    }
    public function ObatMasuk()
    {
        $this->hasMany(ObatMasuk::class, 'id_kategori_obat', 'id_kategori_obat');
    }
}
