<?php

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;

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
    $users = include resource_path('data/user.php');
    $username = $request->input('username');
    $password = $request->input('password');

    foreach ($users as $user) {
        if ($user['username'] === $username && $user['password'] === $password) {
            Session::put('user_role', $user['role']);
            Session::put('username', $username); // simpan username ke session

            if ($user['role'] === 'Admin') {
                return redirect()->route('dashboard');
            } elseif ($user['role'] === 'Kasir') {
                return redirect()->route('penjualan.index');
            }
        }
    }

    return redirect()->route('login')->with('error', 'Username atau password salah.');
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

// Store Barang
Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');

// Ambil Data User
Route::get('/manajemen-user', [UserController::class, 'index'])->name('user.index');

// Halaman Penjualan (Kasir)
Route::get('/penjualan', [OrderController::class, 'create'])->name('penjualan.index');

// Simpan Data Penjualan (Kasir)
Route::post('/penjualan', [OrderController::class, 'store'])->name('order.store');

// Histori Penjualan
Route::get('/histori-penjualan', [OrderController::class, 'history'])->name('penjualan.histori');

// Function log out
Route::get('/logout', function () {
    Session::forget('user_role');
    return redirect()->route('login');
})->name('logout');



