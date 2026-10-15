<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MObat extends Model
{
    protected $table = 'obats';

    protected $fillable = [
        'nama_obat',
        'satuan',
        'stok',
        'harga',
    ];

    public function rekamMedis()
    {
        return $this->belongsToMany(MRekamMedis::class, 'resep_obat', 'obat_id', 'rekam_medis_id')
                    ->withPivot('dosis', 'jumlah')
                    ->withTimestamps();
    }
}