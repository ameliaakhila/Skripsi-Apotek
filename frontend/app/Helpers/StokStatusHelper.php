<?php

namespace App\Helpers;

/**
 * ! StokStatusHelper - Helper untuk menentukan status stok secara konsisten
 * * Berdasarkan persentase: safety_stock >= 120% (Aman), 30%-120% (Menipis), <= 30% (Habis)
 */
class StokStatusHelper
{
    /**
     * ! Tentukan status stok berdasarkan safety_stock
     *
     * * Aman: stok >= 120% dari safety_stock
     * * Menipis: 30% – 120% dari safety_stock
     * * Habis: stok <= 30% dari safety_stock
     *
     * @param int|float $stok_sekarang Jumlah stok saat ini
     * @param int|float $safety_stock Minimum stok yang aman (dari tb_obat)
     * @return string Status: 'Aman', 'Menipis', atau 'Habis'
     */
    public static function getStatus($stok_sekarang, $safety_stock)
    {
        if ($safety_stock == 0) {
            return $stok_sekarang == 0 ? 'Habis' : 'Aman';
        }

        // ! Hitung persentase: (stok_sekarang / safety_stock) * 100
        $presentase = ($stok_sekarang / $safety_stock) * 100;

        if ($presentase >= 120) {
            return 'Aman';
        } elseif ($presentase >= 30) {
            return 'Menipis';
        } else {
            return 'Habis';
        }
    }

    /**
     * ! Tentukan status stok dengan data array (untuk seeder compatibility)
     *
     * @param array $data Array dengan keys: 'stok_sekarang', 'safety_stock'
     * @return string Status stok
     */
    public static function getStatusFromArray($data)
    {
        return self::getStatus(
            $data['stok_sekarang'] ?? 0,
            $data['safety_stock'] ?? 1
        );
    }
}
