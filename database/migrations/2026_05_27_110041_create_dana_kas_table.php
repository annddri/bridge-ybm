<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dana_kas', function (Blueprint $table) {
            $table->id('id_kas');

            $table->date('tanggal');
            $table->enum('jenis_transaksi', ['Masuk', 'Keluar']);
            $table->decimal('nominal', 12, 2);
            $table->text('keterangan')->nullable();

            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->string('created_by_nibs', 50)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dana_kas');
    }
};