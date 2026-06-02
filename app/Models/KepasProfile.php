<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KepasProfile extends Model
{
    protected $table = 'kepas_profiles';

    protected $fillable = [
        'user_id',
        'asrama_id',
        'no_telp',
        'foto_profil',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function asrama()
    {
        return $this->belongsTo(Asrama::class);
    }
}