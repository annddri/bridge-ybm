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
    Schema::create('portofolio', function (Blueprint $table) {
        $table->id();

        $table->foreignId('id_user')
            ->constrained('users')
            ->onDelete('cascade');

        $table->enum('kategori', [
            'prestasi',
            'organisasi',
            'workshop/seminar'
        ])->nullable();

        $table->string('tanggal_tahun', 50)->nullable();
        $table->string('nama_kegiatan', 255)->nullable();
        $table->string('penyelenggara_jabatan', 255)->nullable();

        $table->enum('level', [
            'Lokal/Kampus',
            'Kota/Kabupaten',
            'Provinsi',
            'Nasional',
            'Internasional'
        ])->nullable();

        $table->string('status', 20)->default('Belum Lulus');
        $table->string('file_bukti', 255)->nullable();

        $table->timestamp('created_at')->useCurrent();
    });
}

public function down(): void
{
    Schema::dropIfExists('portofolio');
}
};
