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
        'lokasi_sasaran',
        'nama_kegiatan_materi',
        'keterangan_tambahan',
        'link_laporan',
        'status',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}