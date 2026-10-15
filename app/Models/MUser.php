<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class MUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'login_password',
        'role',
    ];

    protected $hidden = [
        'password',
        'login_password',
        'remember_token',
    ];

    // Relasi 1-to-1 ke Pasien
    public function pasien()
    {
        return $this->hasOne(MPasien::class, 'user_id');
    }

    // Relasi 1-to-1 ke Dokter
    public function dokter()
    {
        return $this->hasOne(MDokter::class, 'user_id');
    }

    // Relasi 1-to-Many ke Ulasan
    public function ulasan()
    {
        return $this->hasMany(MUlasan::class, 'user_id');
    }
}