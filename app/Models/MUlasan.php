<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MUlasan extends Model
{
    protected $table = 'ulasans';

    protected $fillable = [
        'ulasanable_id',
        'ulasanable_type',
        'user_id',
        'rating',
        'komentar',
    ];

    public function ulasanable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(MUser::class, 'user_id');
    }
}