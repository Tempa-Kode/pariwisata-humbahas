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

    public function halamanWisata(Request $request)
    {
        $wisata = \App\Models\Wisata::filter([
            'wisata' => $request->wisata
        ])->with('kategori')->simplePaginate(10);

        return view('pengunjung.wisata', compact('wisata'));
    }

    public function detailHalamanWisata($id)
    {
        $wisata = \App\Models\Wisata::findOrFail($id);
        return view('pengunjung.detail_wisata', compact('wisata'));
    }
}
