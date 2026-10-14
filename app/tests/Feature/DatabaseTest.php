<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin\ObatMasuk;

class DatabaseTest extends TestCase
{
    public function test_obat_masuk_data_access()
    {
        $item = ObatMasuk::first();
        if ($item) {
            $this->assertIsObject($item, 'ObatMasuk should be an object');
            $this->assertObjectHasProperty('kode_batch', $item, 'ObatMasuk should have kode_batch property');
            echo "Access check: " . $item->kode_batch . "\n";
        } else {
            $this->markTestSkipped('No data in tb_obat_masuk');
        }
    }
}
