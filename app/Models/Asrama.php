<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asrama extends Model
{
    protected $table = 'asramas';

    protected $fillable = [
        'kode_asrama',
        'nama_asrama',
        'regional',
    ];

    public function mahasiswa()
    {
        return $this->hasMany(MahasiswaProfile::class);
    }

    public function kepas()
    {
        return $this->hasMany(KepasProfile::class);
    }
}