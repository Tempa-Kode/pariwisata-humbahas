<?php

namespace App\Http\Controllers;

use App\Models\FotoWisata;
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
            'destinasi_unggulan' => 'nullable|boolean',
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

        $data['destinasi_unggulan'] = $request->has('destinasi_unggulan') ? 1 : 0;

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
            'destinasi_unggulan' => 'nullable|boolean',
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

        $data['destinasi_unggulan'] = $request->has('destinasi_unggulan') ? 1 : 0;

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

    public function hapus($id) {
        $wisata = \App\Models\Wisata::findOrFail($id);
        $wisata->kategori()->detach();
        $wisata->delete();
        return redirect()->route('wisata.index')->with('success', 'Wisata berhasil dihapus.');
    }

    public function tambahFotoWisata(Request $request) {
        $request->validate([
            'id_wisata' => 'required|exists:wisata,id_wisata',
            'foto.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'foto_ganti.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $id_wisata = $request->input('id_wisata');

        // 1. Ganti foto lama jika ada input
        if ($request->has('foto_ganti')) {
            foreach ($request->file('foto_ganti') ?? [] as $id_foto => $file) {
                if ($file && $file->isValid()) {
                    $foto = FotoWisata::find($id_foto);
                    if ($foto) {
                        if (file_exists(public_path($foto->url_foto))) {
                            unlink(public_path($foto->url_foto));
                        }
                        $filename = time() . '_' . $id_foto . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('uploads'), $filename);
                        $foto->url_foto = 'uploads/' . $filename;
                        $foto->save();
                    }
                }
            }
        }

        // 2. Tambah foto baru
        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $file) {
                if ($file && $file->isValid()) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads'), $filename);
                    FotoWisata::create([
                        'id_wisata' => $id_wisata,
                        'url_foto' => 'uploads/' . $filename,
                    ]);
                }
            }
        }

        return redirect()->route('wisata.edit', $id_wisata)->with('success', 'Foto wisata berhasil diperbarui.');
    }

    public function hapusFoto($id_foto_wisata)
    {
        $foto = FotoWisata::findOrFail($id_foto_wisata);
        $id_wisata = $foto->id_wisata;
        // Hapus file fisik
        if (file_exists(public_path($foto->url_foto))) {
            unlink(public_path($foto->url_foto));
        }
        $foto->delete();
        return redirect()->route('wisata.edit', $id_wisata)->with('success', 'Foto berhasil dihapus.');
    }
}
