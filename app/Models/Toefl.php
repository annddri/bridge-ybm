<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Toefl extends Model
{
    protected $table = 'toefl';

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'score',
        'jenis_tes',
        'file_sertifikat',
        'status',
        'tanggal_upload',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}