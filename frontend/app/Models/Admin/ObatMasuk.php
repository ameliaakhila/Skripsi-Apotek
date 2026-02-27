<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use NumberFormatter;
class ObatMasuk extends Model
{
    protected $table = 'tb_obat_masuk';
    protected $primaryKey = 'id_obat_masuk';
    protected $fillable = [
        'kode_obat',
        'kode_batch',
        'tanggal_masuk',
        'tanggal_kedaluwarsa',
        'stok_masuk',
        'stok_keluar',
        'stok_sisa',
        'harga_obat',
        'id_satuan',
        'id_obat',
        'id_kategori'
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

    public function getTanggalKeluarFormattedAttribute()
    {
        return $this->formatTanggalIndo($this->tanggal_keluar);
    }


    // ! Status kedaluwarsa
    public function getStatuskedaluwarsaAttribute()
    {
        $today = Carbon::now();
        $expire = Carbon::parse($this->getAttribute('tanggal_kedaluwarsa'));
        $diffDays = $today->diffInDays($expire, false);

        if ($diffDays < 0)
            return 'Kedaluwarsa';
        if ($diffDays <= 30)
            return 'Hampir kedaluwarsa';
        return 'Aman';
    }

    // Method untuk DOM looping
    public function getStatusKedaluwarsaData()
    {
        return [
            'status' => $this->status_kedaluwarsa,
            'badge' => $this->status_kedaluwarsa_badge,
            'tanggal_formatted' => $this->tanggal_kedaluwarsa_formatted,
        ];
    }

    // ! Badge warna untuk status kedaluwarsa
    public function getStatuskedaluwarsaBadgeAttribute()
    {
        return match ($this->status_kedaluwarsa) {
            'Kedaluwarsa' => 'danger',
            'Hampir kedaluwarsa' => 'warning',
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

    // ! Relasi Satuan
    public function DataSatuan()
    {
        return $this->belongsTo(DataSatuan::class, 'id_satuan', 'id_satuan');
    }

    // ! Relasi Kategori Obat
    public function DataKategori()
    {
        return $this->belongsTo(DataKategori::class, 'id_kategori', 'id_kategori');
    }

}
