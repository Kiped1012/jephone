<?php

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PelunasanController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AuthController;

// Login routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// Forgot Password routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot.password');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot.password.submit');

// Reset Password routes
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('reset.password');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset.password.submit');

// Logout route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard route (example - protected route)
Route::get('/dashboard', function () {
    if (!session('user_id')) {
        return redirect()->route('login');
    }
    return view('dashboard');
})->name('dashboard');

// Arahkan root ke halaman login
Route::get('/', function () {
    return redirect()->route('login');
});

// Ambil Data Barang
Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');

// Entri Barang
Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');

Route::get('/barang/{id}/edit', [BarangController::class, 'edit'])->name('barang.edit');
Route::put('/barang/{id}', [BarangController::class, 'update'])->name('barang.update');
Route::delete('/barang/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');

// Master Data
Route::get('/masterdata', [MasterDataController::class, 'index'])->name('masterdata');

Route::get('/masterdata/barang', [MasterDataController::class, 'getBarang'])->name('masterdata.barang.get');
Route::get('/masterdata/kategori', [MasterDataController::class, 'getKategori'])->name('masterdata.kategori.get');
Route::get('/masterdata/suppliers', [MasterDataController::class, 'getSuppliers'])->name('masterdata.suppliers.get');

Route::post('/masterdata/barang', [MasterDataController::class, 'storeBarang'])->name('masterdata.barang.store');
Route::post('/masterdata/kategori', [MasterDataController::class, 'storeKategori'])->name('masterdata.kategori.store');
Route::post('/masterdata/suppliers', [MasterDataController::class, 'storeSupplier'])->name('masterdata.suppliers.store');

Route::post('/masterdata/barang/delete', [MasterDataController::class, 'deleteBarang'])->name('masterdata.barang.delete');
Route::post('/masterdata/kategori/delete', [MasterDataController::class, 'deleteKategori'])->name('masterdata.kategori.delete');
Route::post('/masterdata/suppliers/delete', [MasterDataController::class, 'deleteSupplier'])->name('masterdata.suppliers.delete');

// Detail Barang
Route::get('/barang/{id}', [BarangController::class, 'show'])->name('barang.show');

// Store Barang
Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');

// Ambil Data User
Route::get('/manajemen-user', [UserController::class, 'index'])->name('user.index');

// Pembelian
Route::get('/pembelian', [PurchaseController::class, 'create'])->name('pembelian.create');
Route::post('/pembelian', [PurchaseController::class, 'store'])->name('pembelian.store');
Route::get('/histori/pembelian', [PurchaseController::class, 'historyPembelian'])->name('histori.pembelian');

// Daftar Piutang
Route::get('/piutang/daftar', function () {
    return view('daftarpiutang');
})->name('piutang.daftar');

// Route untuk laporan stok
Route::get('/laporan-stok', function () {
    return view('laporanstok');
})->name('laporan.stok');

// Route untuk laporan arus kas
Route::get('/laporan-arus-kas', function () {
    return view('laporanaruskas');
})->name('laporan.aruskas');

// Halaman Penjualan (Kasir)
Route::get('/penjualan', [OrderController::class, 'create'])->name('penjualan.index');

// Simpan Data Penjualan (Kasir)
Route::post('/penjualan', [OrderController::class, 'store'])->name('order.store');

// Histori Penjualan
Route::get('/histori-penjualan', [OrderController::class, 'history'])->name('penjualan.histori');

// Halaman Pelunasan
Route::get('/pelunasan', [PelunasanController::class, 'index'])->name('pelunasan.index');

// Ambil data transaksi piutang berdasarkan ID (untuk modal pelunasan)
Route::get('/pelunasan/get-transaksi', [PelunasanController::class, 'getTransaksiById'])->name('pelunasan.getTransaksi');

// Form Pelunasan
Route::post('/pelunasan', [PelunasanController::class, 'store'])->name('pelunasan.store');

Route::get('/data/pelunasan', [PelunasanController::class, 'getPelunasan']);

// Manage User
Route::prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('user.index');
    Route::get('/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/store', [UserController::class, 'store'])->name('user.store');
    Route::get('/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/{id}', [UserController::class, 'destroy'])->name('user.destroy');
});

// Function log out
Route::get('/logout', function () {
    Session::forget('user_role');
    return redirect()->route('login');
})->name('logout');



