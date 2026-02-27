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
        Schema::create('tb_penjualan', function (Blueprint $table) {
            $table->id('id_penjualan');
            $table->integer('jumlah_penjualan');
            $table->timestamps();

            $table->foreignId('id_cabang')->constrained('tb_cabang', 'id_cabang')->onDelete('cascade');
            $table->foreignId('id_obat')->constrained('tb_obat_masuk', 'id_obat')->onDelete('cascade');
            $table->foreignId('id_obat_masuk')->constrained('tb_obat_masuk', 'id_obat_masuk')->onDelete('cascade');
            $table->foreignId('id_kategori')->constrained('tb_kategori', 'id_kategori')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_penjualan');
    }
};
