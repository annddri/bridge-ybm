<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portofolio extends Model
{
    protected $table = 'portofolio';

    const UPDATED_AT = null;

    protected $fillable = [
        'id_user',
        'kategori',
        'tanggal_tahun',
        'nama_kegiatan',
        'penyelenggara_jabatan',
        'level',
        'file_bukti',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}