<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategori';

    protected $primaryKey = 'id_kategori';

    protected $fillable = [
        'nama_kategori',
    ];

    public function wisata()
    {
        return $this->hasMany(Wisata::class, 'id_kategori', 'id_kategori');
    }
}
