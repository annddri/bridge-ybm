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
        Schema::create('masyarakat', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_user')
                ->constrained('users')
                ->onDelete('cascade');

            $table->enum('kategori', [
                'kunjungan',
                'social_project',
                'narasumber'
            ])->nullable();

            $table->string('waktu', 50)->nullable();
            $table->string('lokasi_sasaran', 255)->nullable();
            $table->string('nama_kegiatan_materi', 255)->nullable();
            $table->text('keterangan_tambahan')->nullable();
            $table->string('link_laporan', 255)->nullable();

            $table->string('status', 20)->default('Belum Lulus');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('masyarakat');
    }
};
