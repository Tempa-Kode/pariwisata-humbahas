<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class Wisata extends Controller
{
    public function index() {
        $wisata = \App\Models\Wisata::with('kategori')->get();
        return view('wisata.index', compact('wisata'));
    }

    public function formTambah() {
        $kategori = Kategori::all();
        return view('wisata.tambah', compact('kategori'));
    }

    public function simpan(Request $request) {
        $data = $request->validate([
            'nama_wisata' => 'required|string|max:255',
            'id_kategori.*' => 'required|exists:kategori,id_kategori',
            'lokasi' => 'required|string|max:150',
            'deskripsi' => 'required|string',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'jam_operasional' => 'nullable|string|max:100',
            'harga_tiket' => 'nullable|string',
            'biaya_parkir' => 'nullable|string',
            'fasilitas' => 'nullable|string',
            'peraturan' => 'nullable|string',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
        ], [
            'nama_wisata.required' => 'Nama wisata harus diisi.',
            'id_kategori.*.required' => 'Kategori wisata harus dipilih.',
            'lokasi.required' => 'Lokasi wisata harus diisi.',
            'deskripsi.required' => 'Deskripsi wisata harus diisi.',
            'foto.required' => 'Foto wisata harus diunggah.',
            'jam_operasional.max' => 'Jam operasional tidak boleh lebih dari 100 karakter.',
            'longitude.required' => 'Longitude harus diisi.',
            'latitude.required' => 'Latitude harus diisi.',
        ]);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
            $data['foto'] = 'uploads/' . $filename;
        }

        $wisata = \App\Models\Wisata::create($data);
        foreach ($data['id_kategori'] as $id_kategori) {
            \App\Models\KategoriWisata::create([
                'id_kategori' => $id_kategori,
                'id_wisata' => $wisata->id_wisata,
            ]);
        }
        return redirect()->route('wisata.index')->with('success', 'Wisata berhasil ditambahkan.');
    }

    public function edit($id) {
        $wisata = \App\Models\Wisata::findOrFail($id);
        $kategori = Kategori::all();
        return view('wisata.edit', compact('wisata', 'kategori'));
    }

    public function update(Request $request, $id) {
        $wisata = \App\Models\Wisata::findOrFail($id);

        $data = $request->validate([
            'nama_wisata' => 'required|string|max:255',
            'id_kategori.*' => 'required|exists:kategori,id_kategori',
            'lokasi' => 'required|string|max:150',
            'deskripsi' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'jam_operasional' => 'nullable|string|max:100',
            'harga_tiket' => 'nullable|string',
            'biaya_parkir' => 'nullable|string',
            'fasilitas' => 'nullable|string',
            'peraturan' => 'nullable|string',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
        ]);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
            $data['foto'] = 'uploads/' . $filename;
        }

        $wisata->update($data);
        $wisata->kategori()->sync($data['id_kategori']);

        return redirect()->route('wisata.index')->with('success', 'Wisata berhasil diperbarui.');
    }
}
