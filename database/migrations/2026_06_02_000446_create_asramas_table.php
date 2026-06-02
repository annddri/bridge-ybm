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
        Schema::create('asramas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('kode_asrama', 30)->unique();
            $table->string('nama_asrama', 100);
            $table->string('regional', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asramas');
    }
};
