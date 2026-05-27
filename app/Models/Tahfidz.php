<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tahfidz extends Model
{
    protected $table = 'tahfidz';

    protected $fillable = [
        'id_user',
        'nama_surah',
        'tanggal_tes',
        'file_verifikasi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}