<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Dijkstra extends Controller
{
    public function halamanCariRute() {
        $wisata = \App\Models\Wisata::all();
        return view('pengunjung.cari-rute', compact('wisata'));
    }
}
