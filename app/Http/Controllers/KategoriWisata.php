<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriWisata extends Controller
{
//    Menampilkan daftar kategori wisata
    public function index() {
        $kategori = Kategori::all();
        return view('kategori-wisata.index', compact('kategori'));
    }

//    menampilkan form tambah kategori wisata
    public function formTambah() {
        return view('kategori-wisata.tambah');
    }

//    menyimpan kategori wisata baru
    public function simpan(Request $request) {
        $validasi = $request->validate(
            ['nama_kategori' => 'required|string|max:255'],
            ['nama_kategori.required' => 'Nama kategori harus diisi.']
        );

        Kategori::create($validasi);
        return redirect()->route('kategori-wisata.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

//    menampilkan form edit kategori wisata
    public function edit($id) {
        $kategori = Kategori::findOrFail($id);
        return view('kategori-wisata.edit', compact('kategori'));
    }

//    memperbarui kategori wisata
    public function update(Request $request, $id) {
        $validasi = $request->validate(
            ['nama_kategori' => 'required|string|max:255'],
            ['nama_kategori.required' => 'Nama kategori harus diisi.']
        );

        $kategori = Kategori::findOrFail($id);
        $kategori->update($validasi);
        return redirect()->route('kategori-wisata.index')->with('success', 'Kategori berhasil diperbarui.');
    }

//    menghapus kategori wisata
    public function hapus($id) {
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();
        return redirect()->route('kategori-wisata.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
