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
        Schema::create('tb_mutasi', function (Blueprint $table) {
            $table->id('id_mutasi');
            $table->unsignedBigInteger('id_stok_batch');
            $table->foreign('id_stok_batch')->references('id_stok_batch')->on('tb_stok_batch')->onDelete('cascade');
            $table->unsignedBigInteger('id_cabang_asal');
            $table->foreign('id_cabang_asal')->references('id_cabang')->on('tb_cabang')->onDelete('cascade');
            $table->unsignedBigInteger('id_cabang_tujuan');
            $table->foreign('id_cabang_tujuan')->references('id_cabang')->on('tb_cabang')->onDelete('cascade');
            $table->date('tanggal_mutasi');
            $table->integer('jumlah_mutasi');
            $table->text('keterangan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_mutasi', function (Blueprint $table) {
            //
        });
    }
};
