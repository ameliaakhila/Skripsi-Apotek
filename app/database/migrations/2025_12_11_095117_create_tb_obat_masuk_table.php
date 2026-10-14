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
        Schema::create('tb_obat_masuk', function (Blueprint $table) {
            $table->id('id_obat_masuk');
            $table->string('kode_batch');
            $table->date('tanggal_masuk');
            $table->date('tanggal_kedaluwarsa');
            $table->integer('stok_masuk');

            $table->foreignId('id_obat')->constrained('tb_obat', 'id_obat')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_obat_masuk');
    }
};
