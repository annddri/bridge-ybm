<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MahasiswaProfile extends Model
{
    protected $table = 'mahasiswa_profiles';

    protected $fillable = [
        'user_id',
        'nibs',
        'nim',
        'universitas',
        'prodi',
        'angkatan',
        'no_telp',
        'bio',
        'foto_profil',
    ];
}