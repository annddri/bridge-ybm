<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembinaan extends Model
{
    protected $table = 'pembinaans';

    protected $fillable = [
        'id_user',
        'tanggal',
        'nama_pemateri',
        'judul_materi',
        'resume',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}
