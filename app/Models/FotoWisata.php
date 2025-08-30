<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoWisata extends Model
{
    protected $table = 'foto_wisata';

    protected $primaryKey = 'id_foto_wisata';

    protected $fillable = [
        'id_wisata',
        'url_foto'
    ];

    public $timestamps = false;

    public function wisata()
    {
        return $this->belongsTo(Wisata::class, 'id_wisata');
    }
}
