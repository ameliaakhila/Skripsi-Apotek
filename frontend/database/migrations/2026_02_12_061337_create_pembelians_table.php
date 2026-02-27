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
        Schema::create('tb_pembelian', function (Blueprint $table) {
            $table->id('id_pembelian');
            $table->string('no_faktur');
            $table->integer('total_pembelian');
            $table->string('kode_batch');
            $table->date('tanggal_kedaluwarsa');
            $table->integer('harga_beli');

            $table->unsignedBigInteger('id_obat');
            $table->foreign('id_obat')->references('id_obat')->on('tb_obat')->onDelete('cascade');
            $table->unsignedBigInteger('id_supplier');
            $table->foreign('id_supplier')->references('id_supplier')->on('tb_supplier')->onDelete('cascade');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_pembelian');
    }
};
