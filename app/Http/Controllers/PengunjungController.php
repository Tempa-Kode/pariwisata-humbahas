<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PengunjungController extends Controller
{
    public function index()
    {
        $wisataUnggulan = \App\Models\Wisata::where('destinasi_unggulan', true)
            ->take(4)
            ->get();
        return view('pengunjung.index', compact('wisataUnggulan'));
    }
}
