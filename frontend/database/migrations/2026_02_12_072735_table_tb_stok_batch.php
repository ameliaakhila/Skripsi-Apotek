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
        Schema::create('tb_stok_batch', function (Blueprint $table) {
            $table->id('id_stok_batch');
            $table->string('kode_batch');
            $table->date('tanggal_kedaluwarsa');
            $table->integer('stok_masuk');
            $table->integer('stok_keluar')->default(0);
            $table->integer('stok_sisa')->default(0);
            $table->enum('status_stok', ['Aman', 'Menipis', 'Habis']);
            $table->string('tujuan_penyesuaian')->default('Tidak ada penyesuaian');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreignId('id_obat')->constrained('tb_obat')->references('id_obat')->onDelete('cascade');
            $table->foreignId('id_cabang')->constrained('tb_cabang')->references('id_cabang')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_stok_batch', function (Blueprint $table) {
            
        });
    }
};
