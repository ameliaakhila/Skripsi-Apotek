<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataCabang extends Model
{
    /** @use HasFactory<\Database\Factories\DataCabangFactory> */
    use HasFactory;
    protected $table = 'tb_cabang';
    protected $primaryKey = 'id_cabang';
    protected $fillable = [
        'kode_cabang',
        'nama_cabang',
        'alamat_cabang'
    ];
}
