<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MPoli extends Model
{
    protected $table = 'polis';

    protected $fillable = [
        'nama_poli',
        'deskripsi',
    ];

    public function dokter()
    {
        return $this->hasMany(MDokter::class, 'poli_id');
    }
}