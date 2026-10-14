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
        Schema::create('tb_stok_opname', function (Blueprint $table) {
            $table->id(column: 'id_stok_opname');
            $table->string('kode_opname', 10);
            $table->date('tanggal_opname');
            $table->integer('stok_sistem');
            $table->integer('stok_fisik');
            $table->integer('selisih');
            $table->text('keterangan')->nullable();

            $table->foreignId('id_stok_batch')->constrained('tb_stok_batch', 'id_stok_batch')->onDelete('cascade');
            $table->foreignId('id_cabang')->constrained('tb_cabang', 'id_cabang')->onDelete('cascade');
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
            $table->foreignId('id_obat')->constrained('tb_obat', 'id_obat')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_stok_opname', function (Blueprint $table) {
            //
        });
    }
};
