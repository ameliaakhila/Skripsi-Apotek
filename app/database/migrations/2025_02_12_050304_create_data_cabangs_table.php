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
        Schema::create('tb_cabang', function (Blueprint $table) {
            $table->id('id_cabang');
            $table->string('kode_cabang')->unique();
            $table->string('nama_cabang');
            $table->enum('tipe', ['pusat', 'cabang']);
            $table->string('alamat_cabang');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_cabangs');
    }
};
