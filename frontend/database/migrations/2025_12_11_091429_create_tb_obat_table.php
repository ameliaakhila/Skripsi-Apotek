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
            $table->string('pabrik_obat');
            $table->string('satuan_obat');
            $table->timestamps();

            $table->unsignedBigInteger('id_kategori_obat');
            $table->foreign('id_kategori_obat')->references('id_kategori_obat')->on('tb_kategori_obat')->onDelete('cascade');
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
