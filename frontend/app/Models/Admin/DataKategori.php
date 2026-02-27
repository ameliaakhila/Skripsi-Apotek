<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class DataKategori extends Model
{
    protected $table = 'tb_kategori';
    protected $primaryKey = 'id_kategori';
    protected $fillable = ['nama_kategori'];

    public function Obat()
    {
        $this->hasMany(Obat::class, 'id_kategori', 'id_kategori');
    }
    public function ObatMasuk()
    {
        $this->hasMany(ObatMasuk::class, 'id_kategori', 'id_kategori');
    }
}
