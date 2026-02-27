<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataSatuan extends Model
{
    /** @use HasFactory<\Database\Factories\DataSatuanFactory> */
    use HasFactory;
    protected $table = 'tb_satuan';
    protected $primaryKey = 'id_satuan';
    protected $fillable = ['nama_satuan'];
}
