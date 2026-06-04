<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MahasiswaProfile extends Model
{
    protected $table = 'mahasiswa_profiles';

    protected $fillable = [
        'user_id',
        'asrama_id',
        'nibs',
        'nim',
        'universitas',
        'prodi',
        'angkatan',
        'no_telp',
        'bio',
        'foto_profil',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function asrama()
    {
        return $this->belongsTo(Asrama::class);
    }
}