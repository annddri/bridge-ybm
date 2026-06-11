<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\MahasiswaProfile;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public $timestamps = true;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function mahasiswaProfile()
    {
        return $this->hasOne(MahasiswaProfile::class, 'user_id', 'id');
    }

    public function kepasProfile()
    {
        return $this->hasOne(KepasProfile::class, 'user_id', 'id');
    }

    public function amalan()
    {
        return $this->hasMany(Amalan::class, 'id_user', 'id');
    }

    public function tahfidz()
    {
        return $this->hasMany(Tahfidz::class, 'id_user', 'id');
    }

    public function portofolio()
    {
        return $this->hasMany(Portofolio::class, 'id_user', 'id');
    }

    public function masyarakat()
    {
        return $this->hasMany(Masyarakat::class, 'id_user', 'id');
    }
}
