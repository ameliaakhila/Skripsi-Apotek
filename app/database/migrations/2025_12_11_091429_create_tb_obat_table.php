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
        Schema::create('tb_obat', function (Blueprint $table) {
            $table->id('id_obat');
            $table->string('nama_obat');
            $table->integer('safety_stock');

            $table->unsignedBigInteger('id_kategori');
            $table->foreign('id_kategori')->references('id_kategori')->on('tb_kategori')->onDelete('cascade');

            $table->unsignedBigInteger(column: 'id_supplier');
            $table->foreign('id_supplier')->references('id_supplier')->on('tb_supplier')->onDelete('cascade');

            $table->unsignedBigInteger('id_satuan');
            $table->foreign('id_satuan')->references('id_satuan')->on('tb_satuan')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_obat');
    }
};
