<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ObatKeluar extends Model
{
    protected $table = 'tb_obat_keluar';
    protected $primaryKey = 'id_obat_keluar';
    protected $fillable = [
        'tanggal_keluar',
        'stok_keluar',
        'id_stok_batch',
        'id_obat_masuk',
        'id_cabang',
    ];

    // ! Accessor tanggal dalam bahasa Indonesia
    private function formatTanggalIndo($tanggal)
    {
        return Carbon::parse($tanggal)
            ->locale('id')
            ->isoFormat('D MMMM Y');
    }

    public function getTanggalKeluarFormattedAttribute()
    {
        return $this->formatTanggalIndo($this->tanggal_keluar);
    }

    public function getTanggalKedaluwarsaFormattedAttribute()
    {
        return $this->formatTanggalIndo($this->tanggal_kedaluwarsa);
    }

    // ! =============== Relasi Eloquent ===============
    // ? Relasi dengan StokBatch dan ObatMasuk untuk mendapatkan informasi obat yang keluar
    public function StokBatch()
    {
        return $this->belongsTo(StokBatch::class, 'id_stok_batch', 'id_stok_batch');
    }
    // ? Relasi dengan ObatMasuk untuk mendapatkan informasi obat yang keluar
    public function ObatMasuk()
    {
        return $this->belongsTo(ObatMasuk::class, 'id_obat_masuk', 'id_obat_masuk');
    }
    // ? Relasi dengan DataCabang untuk mendapatkan informasi cabang tempat obat keluar
    public function DataCabang()
    {
        return $this->belongsTo(DataCabang::class, 'id_cabang', 'id_cabang');
    }
    // ? Relasi dengan Obat untuk mendapatkan informasi obat yang keluar
    public function Obat()
    {
        return $this->belongsTo(Obat::class, 'id_obat_masuk', 'id_obat_masuk')->with('Obat:id_obat,nama_obat');
    }
}

