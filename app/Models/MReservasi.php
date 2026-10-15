<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MReservasi extends Model
{
    protected $table = 'reservasis';

    protected $fillable = [
        'pasien_id',
        'dokter_id',
        'jadwal_praktik_id',
        'tanggal_reservasi',
        'status',
    ];

    public function pasien()
    {
        return $this->belongsTo(MPasien::class, 'pasien_id');
    }

    public function dokter()
    {
        return $this->belongsTo(MDokter::class, 'dokter_id');
    }

    public function jadwalPraktik()
    {
        return $this->belongsTo(MJadwalPraktik::class, 'jadwal_praktik_id');
    }

    public function rekamMedis()
    {
        return $this->hasOne(MRekamMedis::class, 'reservasi_id');
    }

    public function pembayaran()
    {
        return $this->hasOne(MPembayaran::class, 'reservasi_id');
    }

    public function lampiran()
    {
        return $this->morphMany(MLampiran::class, 'lampiranable');
    }
}