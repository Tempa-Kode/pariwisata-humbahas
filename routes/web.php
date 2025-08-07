<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [App\Http\Controllers\Login::class, 'halamanLogin'])->name('login');
Route::post('/login', [App\Http\Controllers\Login::class, 'prosesLogin'])->name('login.proses');
Route::get('beranda', [\App\Http\Controllers\Login::class, 'halamanBeranda'])->middleware('auth')->name('dashboard');
