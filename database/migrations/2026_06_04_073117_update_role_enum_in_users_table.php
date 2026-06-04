<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ubah enum role di tabel users:
     * - Sebelum : ['mahasiswa', 'pengurus', 'administrator']
     * - Sesudah : ['mahasiswa', 'kepas', 'administrator']
     *
     * Menggunakan raw SQL karena MySQL ENUM tidak bisa diubah
     * lewat Blueprint Laravel secara langsung.
     */
    public function up(): void
    {
        // 1. Ganti nilai 'pengurus' yang sudah ada menjadi 'kepas'
        DB::statement("UPDATE users SET role = 'kepas' WHERE role = 'pengurus'");

        // 2. Ubah definisi ENUM kolom role
        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN role ENUM('mahasiswa', 'kepas', 'administrator') NOT NULL
        ");
    }

    /**
     * Rollback: kembalikan ke definisi semula.
     */
    public function down(): void
    {
        // Kembalikan nilai 'kepas' ke 'pengurus' dulu
        DB::statement("UPDATE users SET role = 'pengurus' WHERE role = 'kepas'");

        // Kembalikan definisi ENUM
        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN role ENUM('mahasiswa', 'pengurus', 'administrator') NOT NULL
        ");
    }
};
