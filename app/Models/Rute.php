<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rute extends Model
{
    protected $table = 'rute';
    protected $primaryKey = 'id_rute';
    public $timestamps = false;

    protected $fillable = [
        'lokasi_asal',
        'lokasi_tujuan',
        'jarak',
        'waktu_tempuh',
    ];

    public function lokasiAsal()
    {
        return $this->belongsTo(Wisata::class, 'lokasi_asal', 'id_wisata');
    }

    public function lokasiTujuan()
    {
        return $this->belongsTo(Wisata::class, 'lokasi_tujuan', 'id_wisata');
    }
}
