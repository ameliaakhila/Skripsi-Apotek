<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataSupplier extends Model
{
    /** @use HasFactory<\Database\Factories\DataSupplierFactory> */
    // use HasFactory;
    protected $table = 'tb_supplier';
    protected $primaryKey = 'id_supplier';
    protected $fillable = [
        'nama_supplier',
        'alamat_supplier',
        'no_telp'
    ];

}
