<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tahfidz', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_user')
                ->constrained('users')
                ->onDelete('cascade');

            $table->string('nama_surah', 100);
            $table->date('tanggal_tes');
            $table->string('file_verifikasi')->nullable();
            $table->enum('status', ['Lulus', 'Belum Lulus'])->default('Belum Lulus');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tahfidz');
    }
};
