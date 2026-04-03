<?php

namespace App\Models\Admin;

use App\Helpers\StokStatusHelper;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use NumberFormatter;
class ObatMasuk extends Model
{
    protected $table = 'tb_obat_masuk';
    protected $primaryKey = 'id_obat_masuk';
    protected $fillable = [
        'kode_batch',
        'tanggal_masuk',
        'tanggal_kedaluwarsa',
        'stok_masuk',
        'id_obat',
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
        // Tentukan status menggunakan helper berdasarkan safety_stock
        $status = StokStatusHelper::getStatus($this->stok_sisa, $this->Obat->safety_stock ?? 1);

        // Map status ke warna
        $warna_status = match ($status) {
            'Habis' => 'danger',
            'Menipis' => 'warning',
            'Aman' => 'success',
            default => 'secondary',
        };

        return ['warna' => $warna_status, 'status' => $status];
    }


    // ! ================= RELASI =====================

    // ! Relasi Obat
    public function Obat()
    {
        return $this->belongsTo(Obat::class, 'id_obat', 'id_obat');
    }

    // ! Relasi StokBatch - satu batch obat masuk bisa jadi multiple stok batch di berbagai cabang
    // Filter by id_obat dan kode_batch sama untuk track batch yang sama across cabang
    public function stokBatches()
    {
        return $this->hasMany(StokBatch::class, 'id_obat', 'id_obat')
            ->where('kode_batch', $this->kode_batch);
    }

    // ! Alias untuk camelCase convention
    public function StokBatch()
    {
        return $this->stokBatches();
    }
}
