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
        Schema::create('toefl', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_user')
                ->constrained('users')
                ->onDelete('cascade');

            $table->unsignedInteger('score')->nullable();

            $table->enum('jenis_tes', [
                'Pre-Test',
                'Post-Test',
                'Real Test'
            ])->nullable();

            $table->string('file_sertifikat', 255)->nullable();

            $table->string('status', 20)->default('Belum Lulus');

            $table->timestamp('tanggal_upload')->useCurrent();
        });
    }
};
