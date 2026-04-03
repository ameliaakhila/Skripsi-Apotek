<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ! Add indexes pada foreign keys untuk JOIN performance
        Schema::table('tb_obat', function (Blueprint $table) {
            try {
                $table->index('id_kategori', 'idx_obat_kategori');
            } catch (\Exception $e) {
                // Index sudah ada
            }

            try {
                $table->index('id_supplier', 'idx_obat_supplier');
            } catch (\Exception $e) {
                // Index sudah ada
            }

            try {
                $table->index('id_satuan', 'idx_obat_satuan');
            } catch (\Exception $e) {
                // Index sudah ada
            }
        });

        // ! Add index pada tb_kategori, tb_supplier, tb_satuan untuk cepat di-query
        Schema::table('tb_kategori', function (Blueprint $table) {
            if (Schema::hasColumn('tb_kategori', 'nama_kategori')) {
                try {
                    $table->index('nama_kategori', 'idx_kategori_nama');
                } catch (\Exception $e) {
                    // Index sudah ada
                }
            }
        });

        Schema::table('tb_supplier', function (Blueprint $table) {
            if (Schema::hasColumn('tb_supplier', 'nama_supplier')) {
                try {
                    $table->index('nama_supplier', 'idx_supplier_nama');
                } catch (\Exception $e) {
                    // Index sudah ada
                }
            }
        });

        Schema::table('tb_satuan', function (Blueprint $table) {
            if (Schema::hasColumn('tb_satuan', 'nama_satuan')) {
                try {
                    $table->index('nama_satuan', 'idx_satuan_nama');
                } catch (\Exception $e) {
                    // Index sudah ada
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_obat', function (Blueprint $table) {
            $table->dropIndex('idx_obat_kategori');
            $table->dropIndex('idx_obat_supplier');
            $table->dropIndex('idx_obat_satuan');
        });

        Schema::table('tb_kategori', function (Blueprint $table) {
            $table->dropIndex('idx_kategori_nama');
        });

        Schema::table('tb_supplier', function (Blueprint $table) {
            $table->dropIndex('idx_supplier_nama');
        });

        Schema::table('tb_satuan', function (Blueprint $table) {
            $table->dropIndex('idx_satuan_nama');
        });
    }
};
