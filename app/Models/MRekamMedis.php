<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MRekamMedis extends Model
{
    protected $table = 'rekam_medis';

    protected $fillable = [
        'reservasi_id',
        'keluhan',
        'diagnosa',
        'catatan',
        'status_resep',
        'disiapkan_oleh',
        'disiapkan_pada',
        'diserahkan_oleh',
        'diserahkan_pada',
    ];

    public function reservasi()
    {
        return $this->belongsTo(MReservasi::class, 'reservasi_id');
    }

    // Pivot Many-to-Many ke MObat
    public function obat()
    {
        return $this->belongsToMany(MObat::class, 'resep_obat', 'rekam_medis_id', 'obat_id')
                    ->withPivot('dosis', 'jumlah')
                    ->withTimestamps();
    }

    public function lampiran()
    {
        return $this->morphMany(MLampiran::class, 'lampiranable');
    }

    public function disiapkanOleh()
    {
        return $this->belongsTo(MUser::class, 'disiapkan_oleh');
    }

    public function diserahkanOleh()
    {
        return $this->belongsTo(MUser::class, 'diserahkan_oleh');
    }
}