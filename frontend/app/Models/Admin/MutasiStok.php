<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class MutasiStok extends Model
{
    protected $table = 'tb_mutasi_stok';

    protected $primaryKey = 'id_mutasi';

    protected $fillable = [
        'id_stok_batch',
        'id_cabang_asal',
        'id_cabang_tujuan',
        'tanggal_mutasi',
        'jumlah_mutasi',
        'keterangan'
    ];

    public function getTanggalMutasiFormattedAttribute()
    {
        return \Carbon\Carbon::parse($this->tanggal_mutasi)->locale('id')->isoFormat('D MMMM Y');
    }

    public function StokBatch()
    {
        return $this->belongsTo(StokBatch::class, 'id_stok_batch', 'id_stok_batch');
    }

    public function CabangAsal()
    {
        return $this->belongsTo(DataCabang::class, 'id_cabang_asal', 'id_cabang');
    }

    public function CabangTujuan()
    {
        return $this->belongsTo(DataCabang::class, 'id_cabang_tujuan', 'id_cabang');
    }

    // Relasi ke Obat melalui StokBatch untuk easier access di view
    public function Obat()
    {
        return $this->belongsTo(Obat::class, 'id_obat', 'id_obat');
    }
}
