<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wisata extends Model
{
    protected $table = 'wisata';

    protected $primaryKey = 'id_wisata';

    protected $fillable = [
        'nama_wisata',
        'destinasi_unggulan',
        'lokasi',
        'deskripsi',
        'foto',
        'jam_operasional',
        'harga_tiket',
        'biaya_parkir',
        'fasilitas',
        'peraturan',
        'transportasi',
        'longitude',
        'latitude'
    ];

    public $timestamps = false;

    public function kategori()
    {
        return $this->belongsToMany(Kategori::class, 'kategori_wisata', 'id_wisata', 'id_kategori');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['wisata'] ?? false, function($query, $wisata) {
            return $query->where('nama_wisata', 'like', '%' . $wisata . '%');
        });

        return $query;
    }

    public function fotoWisata()
    {
        return $this->hasMany(FotoWisata::class, 'id_wisata');
    }
}
