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
        Schema::create('tb_obat_keluar', function (Blueprint $table) {
            $table->id('id_obat_keluar');
            $table->date('tanggal_keluar');
            $table->integer('stok_keluar');

            $table->foreignId('id_obat_masuk')->constrained('tb_obat_masuk', 'id_obat_masuk')->onDelete('cascade');
            $table->foreignId('id_stok_batch')->constrained('tb_stok_batch', 'id_stok_batch')->onDelete('cascade');
            $table->foreignId('id_cabang')->constrained('tb_cabang', 'id_cabang')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_obat_keluar');
    }
};
