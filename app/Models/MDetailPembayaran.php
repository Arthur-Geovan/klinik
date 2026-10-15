<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MDetailPembayaran extends Model
{
    protected $table = 'rincian_pembayaran_obat';

    protected $fillable = [
        'pembayaran_id',
        'obat_id',
        'nama_obat',
        'jumlah',
        'harga_satuan',
        'subtotal',
    ];

    public function pembayaran()
    {
        return $this->belongsTo(MPembayaran::class, 'pembayaran_id');
    }
}
