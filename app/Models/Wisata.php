<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wisata extends Model
{
    protected $table = 'wisata';

    protected $primaryKey = 'id_wisata';

    protected $fillable = [
        'nama_wisata',
        'lokasi',
        'deskripsi',
        'foto',
        'jam_operasional',
        'harga_tiket',
        'biaya_parkir',
        'fasilitas',
        'peraturan',
        'longitude',
        'latitude'
    ];

    public $timestamps = false;

    public function kategori()
    {
        return $this->belongsToMany(Kategori::class, 'kategori_wisata', 'id_wisata', 'id_kategori');
    }

}
