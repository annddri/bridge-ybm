<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('amalan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_user')
                ->constrained('users')
                ->onDelete('cascade');

            $table->date('tanggal')->nullable();

            $table->unsignedTinyInteger('shalat_5_waktu')->default(0);
            $table->unsignedTinyInteger('shalat_malam')->default(0);
            $table->unsignedTinyInteger('dzikir_pagi')->default(0);
            $table->unsignedTinyInteger('mendoakan_orang')->default(0);
            $table->unsignedTinyInteger('shalat_dhuha')->default(0);
            $table->unsignedTinyInteger('membaca_alquran')->default(0);
            $table->unsignedTinyInteger('shaum_sunnah')->default(0);
            $table->unsignedTinyInteger('berinfak')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amalan');
    }
};
