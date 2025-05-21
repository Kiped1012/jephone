<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\UserController;

// Arahkan root ke halaman login
Route::get('/', function () {
    return redirect()->route('login');
});

// Halaman login
Route::get('/login', function () {
    return view('login');
})->name('login');

// Proses form login (cek username/password)
Route::post('/login', function (Request $request) {
    $username = $request->input('username');
    $password = $request->input('password');

    if ($username === 'Kiped' && $password === '1234') {
        Session::put('user_role', 'admin');
        return redirect()->route('dashboard');
    } elseif ($username === 'Hasby' && $password === '5678') {
        Session::put('user_role', 'kasir');
        return redirect()->route('dashboard');
    } else {
        return redirect()->route('login')->with('error', 'Username atau password salah.');
    }
})->name('login.submit');

// Halaman dashboard setelah login berhasil
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Ambil Data Barang
Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');

//Entri Barang
Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');

// Detail Barang
Route::get('/barang/{id}', [BarangController::class, 'show'])->name('barang.show');

//Store Barang
Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');

//Ambil Data User
Route::get('/manajemen-user', [UserController::class, 'index'])->name('user.index');

// Function log out
Route::get('/logout', function () {
    Session::forget('user_role');
    return redirect()->route('login');
})->name('logout');



