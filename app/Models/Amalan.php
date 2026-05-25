<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Amalan extends Model
{
    protected $table = 'amalan';

    protected $fillable = [
        'id_user',
        'tanggal',
        'shalat_5_waktu',
        'shalat_malam',
        'dzikir_pagi',
        'mendoakan_orang',
        'shalat_dhuha',
        'membaca_alquran',
        'shaum_sunnah',
        'berinfak',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}