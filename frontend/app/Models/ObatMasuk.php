<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use NumberFormatter;
class ObatMasuk extends Model
{
    protected $table = 'tb_obat_masuk';
    protected $primaryKey = 'id_obat_masuk';
    protected $fillable = [
        'kode_obat',
        'satuan_obat',
        'tanggal_masuk',
        'tanggal_kadaluarsa',
        'tanggal_keluar',
        'stok_masuk',
        'stok_keluar',
        'stok_sisa',
        'harga_obat',
        'id_obat',
        'id_kategori_obat'
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

    public function getTanggalKadaluarsaFormattedAttribute()
    {
        return $this->formatTanggalIndo($this->tanggal_kadaluarsa);
    }

    public function getTanggalKeluarFormattedAttribute()
    {
        return $this->formatTanggalIndo($this->tanggal_keluar);
    }


    // ! Status kadaluarsa
    public function getStatusKadaluarsaAttribute()
    {
        $today = Carbon::now();
        $expire = Carbon::parse($this->tanggal_kadaluarsa);
        $diffDays = $today->diffInDays($expire, false);

        if ($diffDays < 0)
            return 'Kedaluwarsa';
        if ($diffDays <= 30)
            return 'Hampir Kadaluarsa';
        return 'Aman';
    }

    // ! Badge warna untuk status kadaluarsa
    public function getStatusKadaluarsaBadgeAttribute()
    {
        return match ($this->status_kadaluarsa) {
            'Kedaluwarsa' => 'danger',
            'Hampir Kadaluarsa' => 'warning',
            'Aman' => 'success',
            default => 'secondary',
        };
    }

    // ! Accessor format mata uang Rupiah
    public function getHargaObatFormattedAttribute()
    {
        $fmt = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
        $fmt->setAttribute(NumberFormatter::FRACTION_DIGITS, 0);
        return $fmt->formatCurrency($this->harga_obat, 'IDR');
    }

    // ! presentase stok obat
    public function getPresentaseStokAttribute()
    {
        if ($this->stok_masuk == 0) {
            return 0;
        }
        $presentase_stok_keluar = (($this->stok_masuk - $this->stok_keluar) / $this->stok_masuk) * 100;
        return round($presentase_stok_keluar);
    }

    // ! Status warna untuk stok keluar dan stok sisa
    public function getStatusStokAttribute()
    {
        if ($this->presentase_stok < 20) {
            $status = 'Habis';
            $warna_status = 'danger';
        } elseif ($this->presentase_stok <= 50) {
            $status = 'Menipis';
            $warna_status = 'warning';
        } else {
            $status = 'Aman';
            $warna_status = 'success';
        }
        $status_stok = ['warna' => $warna_status, 'status' => $status];
        return $status_stok;
    }


    // ! Relasi Obat
    public function Obat()
    {
        return $this->belongsTo(Obat::class, 'id_obat', 'id_obat');
    }

    // ! Relasi Kategori Obat
    public function KategoriObat()
    {
        return $this->belongsTo(KategoriObat::class, 'id_kategori_obat', 'id_kategori_obat');
    }

}
