<?php

namespace App\Models\Admin;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class StokBatch extends Model
{
    protected $table = 'tb_stok_batch';

    protected $primaryKey = 'id_stok_batch';
    protected $fillable = [
        'kode_batch',
        'tanggal_masuk',
        'tanggal_kedaluwarsa',
        'stok_masuk',
        'stok_keluar',
        'stok_sisa',
        'status_stok',
        'tujuan_penyesuaian',
        'catatan',
        'id_obat',
        'id_cabang',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_kedaluwarsa' => 'date',
    ];

    // ! Accessor tanggal dalam bahasa Indonesia
    private function formatTanggalIndo($tanggal)
    {
        return Carbon::parse($tanggal)
            ->locale('id')
            ->isoFormat('D MMMM Y');
    }

    public function getTanggalMasukFormattedAttribute()
    {
        return $this->formatTanggalIndo($this->tanggal_masuk);
    }

    public function getTanggalkedaluwarsaFormattedAttribute()
    {
        return $this->formatTanggalIndo($this->tanggal_kedaluwarsa);
    }

    public function getStatusKedaluwarsaAttribute()
    {
        $today = Carbon::today();
        $diffDays = $today->diffInDays($this->tanggal_kedaluwarsa, false);

        if ($diffDays < 0) {
            return 'Kedaluwarsa';
        }

        if ($diffDays <= 30) {
            return 'Mendekati Kedaluwarsa';
        }

        return 'Aman';
    }

    public function Obat()
    {
        return $this->belongsTo(Obat::class, 'id_obat', 'id_obat');
    }
    public function DataCabang()
    {
        return $this->belongsTo(DataCabang::class, 'id_cabang', 'id_cabang');
    }

    public function ObatMasuk()
    {
        return $this->hasOne(ObatMasuk::class, 'id_obat', 'id_obat')
            ->whereColumn('tb_obat_masuk.kode_batch', 'tb_stok_batch.kode_batch');
    }

    public function MutasiStok()
    {
        return $this->hasMany(MutasiStok::class, 'id_stok_batch', 'id_stok_batch');
    }

    public function StokOpname()
    {
        return $this->hasMany(StokOpname::class, 'id_stok_batch', 'id_stok_batch');
    }
}
