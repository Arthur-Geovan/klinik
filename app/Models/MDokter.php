<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MDokter extends Model
{
    protected $table = 'dokters';

    protected $fillable = [
        'user_id',
        'poli_id',
        'no_str',
        'spesialisasi',
        'foto',
        'tarif_konsultasi',
        'tarif_pengecekan',
    ];

    public function user()
    {
        return $this->belongsTo(MUser::class, 'user_id');
    }

    public function poli()
    {
        return $this->belongsTo(MPoli::class, 'poli_id');
    }

    public function jadwalPraktik()
    {
        return $this->hasMany(MJadwalPraktik::class, 'dokter_id');
    }

    public function reservasi()
    {
        return $this->hasMany(MReservasi::class, 'dokter_id');
    }

    // Polymorphic Relationship untuk Ulasan
    public function ulasan()
    {
        return $this->morphMany(MUlasan::class, 'ulasanable');
    }

    // Polymorphic Relationship untuk Lampiran
    public function lampiran()
    {
        return $this->morphMany(MLampiran::class, 'lampiranable');
    }
}