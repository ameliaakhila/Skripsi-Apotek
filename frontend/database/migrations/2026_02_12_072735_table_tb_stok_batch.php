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
            $table->string('kode_batch')->unique();
            $table->date('tanggal_kedaluwarsa');
            $table->integer('jumlah_masuk');
            $table->integer('jumlah_sisa')->default(0);
            $table->enum('status_stok', ['Aman', 'Menipis', 'Habis']);
            $table->timestamps();

            $table->unsignedBigInteger('id_obat');
            $table->foreign('id_obat')->references('id_obat')->on('tb_obat')->onDelete('cascade');
            $table->unsignedBigInteger('id_cabang')->default(1);
            $table->foreign('id_cabang')->references('id_cabang')->on('tb_cabang')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_stok_batch', function (Blueprint $table) {
            //
        });
    }
};
