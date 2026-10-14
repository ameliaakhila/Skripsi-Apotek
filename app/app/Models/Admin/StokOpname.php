<?php

namespace App\Models\Admin;

use App\Models\Admin\DataCabang;
use App\Models\Admin\Obat;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class StokOpname extends Model
{
    protected $table = 'tb_stok_opname';

    protected $primaryKey = 'id_stok_opname';
    protected $fillable = [
        'kode_opname',
        'tanggal_opname',
        'stok_fisik',
        'stok_sistem',
        'selisih',
        'keterangan',
        'id_obat',
        'id_stok_batch',
        'id_cabang',
        'id_user',
    ];

    protected $casts = [
        'tanggal_opname' => 'date',
    ];

    private function formatTanggalIndo($tanggal)
    {
        return Carbon::parse($tanggal)
            ->locale('id')
            ->isoFormat('D MMMM Y');
    }
    public function getTanggalOpnameFormattedAttribute()
    {
        return $this->formatTanggalIndo($this->tanggal_opname);
    }

    // Buat format atribut keterangan warna berdasarkan keteranan selisih
    public function getKeteranganIconAttribute()
    {
        return match ($this->keterangan) {
            'Menipis' => 'fas fa-circle text-danger',
            'Lebih' => 'fas fa-circle text-warning',
            'Sesuai' => 'fas fa-circle text-success',
            default => 'fas fa-circle text-secondary',
        };
    }

    public function Obat()
    {
        return $this->belongsTo(Obat::class, 'id_obat', 'id_obat');
    }
    public function StokBatch()
    {
        return $this->belongsTo(StokBatch::class, 'id_stok_batch', 'id_stok_batch');
    }
    public function DataCabang()
    {
        return $this->belongsTo(DataCabang::class, 'id_cabang', 'id_cabang');
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
