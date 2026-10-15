<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MJadwalPraktik extends Model
{
    protected $table = 'jadwal_praktiks';

    protected $fillable = [
        'dokter_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'kuota',
    ];

    public function dokter()
    {
        return $this->belongsTo(MDokter::class, 'dokter_id');
    }

    public function reservasi()
    {
        return $this->hasMany(MReservasi::class, 'jadwal_praktik_id');
    }
}