<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MPasien extends Model
{
    protected $table = 'pasiens';

    protected $fillable = [
        'user_id',
        'no_rm',
        'nik',
        'tanggal_lahir',
        'alamat',
        'no_telp',
    ];

    public function user()
    {
        return $this->belongsTo(MUser::class, 'user_id');
    }

    public function reservasi()
    {
        return $this->hasMany(MReservasi::class, 'pasien_id');
    }
}