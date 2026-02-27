<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class StokBatch extends Model
{
    protected $table = 'tb_stok_batch';

    protected $primaryKey = 'id_stok_batch';
    protected $fillable = [
        'kode_batch',
        'tanggal_masuk',
        'tanggal_kedaluwarsa',
        'jumlah_masuk',
        'jumlah_sisa',
        'status_stok',
        'id_obat',
        'id_cabang'
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_kedaluwarsa' => 'date',
    ];

    public function getTanggalKedaluwarsaFormatAttribute()
    {
        return $this->tanggal_kedaluwarsa
            ? $this->tanggal_kedaluwarsa->translatedFormat('d F Y')
            : null;
    }

    public function Obat()
    {
        return $this->belongsTo(Obat::class, 'id_obat', 'id_obat');
    }
    public function DataCabang()
    {
        return $this->belongsTo(DataCabang::class, 'id_cabang', 'id_cabang');
    }
}
