<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * ADD INDEXES FOR PERFORMANCE OPTIMIZATION
     * These indexes improve query performance for frequently filtered columns
     */
    public function up(): void
    {
        // Index on tanggal_kedaluwarsa - frequently used in WHERE and ORDER BY clauses
        if (Schema::hasTable('tb_stok_batch')) {
            Schema::table('tb_stok_batch', function (Blueprint $table) {
                // Check if index doesn't exist before adding
                $table->index('tanggal_kedaluwarsa');
                $table->index('id_obat');
                $table->index('id_cabang');
                $table->index(['id_cabang', 'tanggal_kedaluwarsa']); // Composite index for queries filtering by both
            });
        }

        // Indexes on foreign keys in tb_obat for faster joins
        if (Schema::hasTable('tb_obat')) {
            Schema::table('tb_obat', function (Blueprint $table) {
                $table->index('id_kategori');
                $table->index('id_supplier');
                $table->index('id_satuan');
            });
        }

        // Indexes in tb_obat_masuk for frequent filtering
        if (Schema::hasTable('tb_obat_masuk')) {
            Schema::table('tb_obat_masuk', function (Blueprint $table) {
                $table->index('tanggal_kedaluwarsa');
                $table->index('id_obat');
            });
        }

        // Indexes in tb_obat_keluar
        if (Schema::hasTable('tb_obat_keluar')) {
            Schema::table('tb_obat_keluar', function (Blueprint $table) {
                $table->index('id_obat_masuk');
                $table->index('id_stok_batch');
                $table->index('id_cabang');
                $table->index('created_at'); // For ordering by date
            });
        }

        // Indexes for users table
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('email');
                $table->index('role'); // For role-based access control
            });
        }

        // Indexes in tb_mutasi_stok
        if (Schema::hasTable('tb_mutasi_stok')) {
            Schema::table('tb_mutasi_stok', function (Blueprint $table) {
                $table->index('id_stok_batch');
                $table->index('created_at');
            });
        }

        // Indexes in tb_stok_opname
        if (Schema::hasTable('tb_stok_opname')) {
            Schema::table('tb_stok_opname', function (Blueprint $table) {
                $table->index('id_stok_batch');
                $table->index('id_cabang');
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes if they exist
        if (Schema::hasTable('tb_stok_batch')) {
            Schema::table('tb_stok_batch', function (Blueprint $table) {
                $table->dropIndex(['tanggal_kedaluwarsa']);
                $table->dropIndex(['id_obat']);
                $table->dropIndex(['id_cabang']);
                $table->dropIndex(['id_cabang', 'tanggal_kedaluwarsa']);
            });
        }

        if (Schema::hasTable('tb_obat')) {
            Schema::table('tb_obat', function (Blueprint $table) {
                $table->dropIndex(['id_kategori']);
                $table->dropIndex(['id_supplier']);
                $table->dropIndex(['id_satuan']);
            });
        }

        if (Schema::hasTable('tb_obat_masuk')) {
            Schema::table('tb_obat_masuk', function (Blueprint $table) {
                $table->dropIndex(['tanggal_kedaluwarsa']);
                $table->dropIndex(['id_obat']);
            });
        }

        if (Schema::hasTable('tb_obat_keluar')) {
            Schema::table('tb_obat_keluar', function (Blueprint $table) {
                $table->dropIndex(['id_obat_masuk']);
                $table->dropIndex(['id_stok_batch']);
                $table->dropIndex(['id_cabang']);
                $table->dropIndex(['created_at']);
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['email']);
                $table->dropIndex(['role']);
            });
        }

        if (Schema::hasTable('tb_mutasi_stok')) {
            Schema::table('tb_mutasi_stok', function (Blueprint $table) {
                $table->dropIndex(['id_stok_batch']);
                $table->dropIndex(['created_at']);
            });
        }

        if (Schema::hasTable('tb_stok_opname')) {
            Schema::table('tb_stok_opname', function (Blueprint $table) {
                $table->dropIndex(['id_stok_batch']);
                $table->dropIndex(['id_cabang']);
                $table->dropIndex(['created_at']);
            });
        }
    }
};
