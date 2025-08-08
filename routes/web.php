<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [App\Http\Controllers\Login::class, 'halamanLogin'])->name('login');
Route::post('/login', [App\Http\Controllers\Login::class, 'prosesLogin'])->name('login.proses');
Route::get('beranda', [\App\Http\Controllers\Login::class, 'halamanBeranda'])->middleware('auth')->name('dashboard');

Route::prefix('kategori-wisata')->middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\KategoriWisata::class, 'index'])->name('kategori-wisata.index');
    Route::get('/tambah', [\App\Http\Controllers\KategoriWisata::class, 'formTambah'])->name('kategori-wisata.tambah');
    Route::post('/simpan', [\App\Http\Controllers\KategoriWisata::class, 'simpan'])->name('kategori-wisata.simpan');
    Route::get('/edit/{id}', [\App\Http\Controllers\KategoriWisata::class, 'edit'])->name('kategori-wisata.edit');
    Route::put('/update/{id}', [\App\Http\Controllers\KategoriWisata::class, 'update'])->name('kategori-wisata.update');
    Route::delete('/hapus/{id}', [\App\Http\Controllers\KategoriWisata::class, 'hapus'])->name('kategori-wisata.hapus');
});

Route::prefix('wisata')->middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Wisata::class, 'index'])->name('wisata.index');
    Route::get('/tambah', [\App\Http\Controllers\Wisata::class, 'formTambah'])->name('wisata.tambah');
    Route::post('/simpan', [\App\Http\Controllers\Wisata::class, 'simpan'])->name('wisata.simpan');
    Route::get('/edit/{id}', [\App\Http\Controllers\Wisata::class, 'edit'])->name('wisata.edit');
    Route::put('/update/{id}', [\App\Http\Controllers\Wisata::class, 'update'])->name('wisata.update');
    Route::delete('/hapus/{id}', [\App\Http\Controllers\Wisata::class, 'hapus'])->name('wisata.hapus');
});
