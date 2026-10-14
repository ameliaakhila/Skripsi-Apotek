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
        Schema::create('tb_mutasi_stok', function (Blueprint $table) {
            $table->id('id_mutasi');
            $table->foreignId('id_stok_batch')->constrained('tb_stok_batch')->references('id_stok_batch')->onDelete('cascade');

            $table->unsignedBigInteger('id_cabang_asal')->index();
            $table->foreign('id_cabang_asal')->references('id_cabang')->on('tb_cabang')->onDelete('cascade');

            $table->unsignedBigInteger('id_cabang_tujuan')->index();
            $table->foreign('id_cabang_tujuan')->references('id_cabang')->on('tb_cabang')->onDelete('cascade');

            $table->date('tanggal_mutasi');
            $table->integer('jumlah_mutasi');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_mutasi_stok', function (Blueprint $table) {
            //
        });
    }
};
