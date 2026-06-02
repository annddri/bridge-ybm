<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    Schema::create('kepas_profiles', function (Blueprint $table) {

        $table->id();

        $table->foreignId('user_id')
            ->constrained('users')
            ->cascadeOnDelete();

        $table->foreignId('asrama_id')
            ->constrained('asramas')
            ->cascadeOnDelete();

        $table->string('no_telp', 20)->nullable();

        $table->string('foto_profil')->nullable();

        $table->timestamps();

    });
        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
