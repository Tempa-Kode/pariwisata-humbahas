<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriWisata extends Model
{
    protected $table = 'kategori_wisata';
    protected $primaryKey = 'id_kategori_wisata';
    protected $fillable = [
        'id_kategori',
        'id_wisata',
    ];

    public $timestamps = false;
}
