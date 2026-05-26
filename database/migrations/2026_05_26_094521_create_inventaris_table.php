<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventaris', function (Blueprint $table) {
            $table->increments('id_barang');
            $table->string('nama_barang', 100);
            $table->string('kode_barang', 20);
            $table->integer('jumlah')->default(1);
            $table->enum('kondisi', [
                'Baik',
                'Rusak Ringan',
                'Rusak Berat'
            ])->default('Baik');
            $table->text('keterangan')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->string('created_by_nibs', 50)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->string('updated_by_nibs', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventaris');
    }
};