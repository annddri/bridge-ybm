<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Masyarakat extends Model
{
    protected $table = 'masyarakat';

    const UPDATED_AT = null;

    protected $fillable = [
        'id_user',
        'kategori',
        'waktu',
        'lokasi_sasaran_peserta',
        'kunjungan_sospro_materi',
        'link_laporan',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}