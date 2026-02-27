<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    /** @use HasFactory<\Database\Factories\PembelianFactory> */
    use HasFactory;
    protected $table = 'tb_pembelian';
    protected $primaryKey = 'id_pembelian';
    protected $fillable = [
        'no_faktur',
        'tanggal_pembelian',
        'id_supplier',
        'total_pembelian'
    ];
}
