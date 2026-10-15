<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MLampiran extends Model
{
    protected $table = 'lampirans';

    protected $fillable = [
        'lampiranable_id',
        'lampiranable_type',
        'file_path',
        'keterangan',
    ];

    public function lampiranable()
    {
        return $this->morphTo();
    }
}