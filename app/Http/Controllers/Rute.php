<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Rute extends Controller
{
    public function index() {
        $rute = \App\Models\Rute::all();
        return view('rute.index', compact('rute'));
    }

    public function formTambah() {
        $wisata = \App\Models\Wisata::all();
        return view('rute.tambah', compact('wisata'));
    }

    public function simpan(Request $request) {
        $validasi = $request->validate([
            'lokasi_asal' => 'required|exists:wisata,id_wisata',
            'lokasi_tujuan' => 'required|exists:wisata,id_wisata',
            'jarak' => 'required',
            'waktu_tempuh' => 'required',
        ], [
            'lokasi_asal.required' => 'Lokasi asal harus diisi.',
            'lokasi_asal.exists' => 'Lokasi asal tidak ditemukan.',
            'lokasi_tujuan.required' => 'Lokasi tujuan harus diisi.',
            'lokasi_tujuan.exists' => 'Lokasi tujuan tidak ditemukan.',
            'jarak.required' => 'Jarak harus diisi.',
            'waktu_tempuh.required' => 'Waktu tempuh harus diisi.',
        ]);

        \App\Models\Rute::create($validasi);
        return redirect()->route('rute.index')->with('success', 'Rute berhasil ditambahkan.');
    }

    public function edit($id) {
        $rute = \App\Models\Rute::findOrFail($id);
        $wisata = \App\Models\Wisata::all();
        return view('rute.edit', compact('rute', 'wisata'));
    }

    public function update(Request $request, $id) {
        $validasi = $request->validate([
            'lokasi_asal' => 'required|exists:wisata,id_wisata',
            'lokasi_tujuan' => 'required|exists:wisata,id_wisata',
            'jarak' => 'required',
            'waktu_tempuh' => 'required',
        ], [
            'lokasi_asal.required' => 'Lokasi asal harus diisi.',
            'lokasi_asal.exists' => 'Lokasi asal tidak ditemukan.',
            'lokasi_tujuan.required' => 'Lokasi tujuan harus diisi.',
            'lokasi_tujuan.exists' => 'Lokasi tujuan tidak ditemukan.',
            'jarak.required' => 'Jarak harus diisi.',
            'waktu_tempuh.required' => 'Waktu tempuh harus diisi.',
        ]);

        $rute = \App\Models\Rute::findOrFail($id);
        $rute->update($validasi);
        return redirect()->route('rute.index')->with('success', 'Rute berhasil diperbarui.');
    }

    public function hapus($id) {
        $rute = \App\Models\Rute::findOrFail($id);
        $rute->delete();
        return redirect()->route('rute.index')->with('success', 'Rute berhasil dihapus.');
    }
}
