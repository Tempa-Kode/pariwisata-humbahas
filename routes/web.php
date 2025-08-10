<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\PengunjungController::class, 'index'])->name('pengunjung.index');
Route::get('/destinasi-wisata', [App\Http\Controllers\PengunjungController::class, 'halamanWisata'])->name('pengunjung.wisata');
Route::get('/destinasi-wisata/{id}', [App\Http\Controllers\PengunjungController::class, 'detailHalamanWisata'])->name('pengunjung.wisata.detail');

Route::get('/login', [App\Http\Controllers\Login::class, 'halamanLogin'])->name('login');
Route::post('/login', [App\Http\Controllers\Login::class, 'prosesLogin'])->name('login.proses');
Route::post('/logout', [\App\Http\Controllers\Login::class, 'logout'])->name('logout');
Route::get('/dashboard', [\App\Http\Controllers\Login::class, 'halamanDashboard'])->middleware('auth')->name('dashboard');

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

Route::prefix('rute')->middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Rute::class, 'index'])->name('rute.index');
    Route::get('/tambah', [\App\Http\Controllers\Rute::class, 'formTambah'])->name('rute.tambah');
    Route::post('/simpan', [\App\Http\Controllers\Rute::class, 'simpan'])->name('rute.simpan');
    Route::get('/edit/{id}', [\App\Http\Controllers\Rute::class, 'edit'])->name('rute.edit');
    Route::put('/update/{id}', [\App\Http\Controllers\Rute::class, 'update'])->name('rute.update');
    Route::delete('/hapus/{id}', [\App\Http\Controllers\Rute::class, 'hapus'])->name('rute.hapus');
});
