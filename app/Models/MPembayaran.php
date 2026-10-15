<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MPembayaran extends Model
{
    protected $table = 'pembayarans';

    protected $fillable = [
        'reservasi_id',
        'total_bayar',
        'metode_bayar',
        'status',
        'bukti_bayar',
        'catatan_admin',
        'biaya_konsultasi',
        'biaya_pengecekan',
        'total_obat',
        'nomor_kuitansi',
        'kuitansi_path',
        'dikonfirmasi_oleh',
        'dikonfirmasi_pada',
    ];

    protected function casts(): array
    {
        return [
            'dikonfirmasi_pada' => 'datetime',
        ];
    }

    public function reservasi()
    {
        return $this->belongsTo(MReservasi::class, 'reservasi_id');
    }

    public function rincianObat()
    {
        return $this->hasMany(MDetailPembayaran::class, 'pembayaran_id');
    }

    public function dikonfirmasiOleh()
    {
        return $this->belongsTo(MUser::class, 'dikonfirmasi_oleh');
    }
}