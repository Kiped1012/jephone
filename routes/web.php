<?php

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PelunasanController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\PurchaseController;

// Arahkan root ke halaman login
Route::get('/', function () {
    return redirect()->route('login');
});

// Halaman login
Route::get('/login', function () {
    return view('login');
})->name('login');

// Proses form login (cek username/password) - FIXED VERSION
Route::post('/login', function (Request $request) {
    $users = include resource_path('data/user.php');
    $username = $request->input('username');
    $password = $request->input('password');

    foreach ($users as $user) {
        // Gunakan Hash::check() untuk memverifikasi password yang sudah di-hash
        if ($user['username'] === $username && Hash::check($password, $user['password'])) {
            Session::put('user_role', $user['role']);
            Session::put('username', $username);
            Session::put('user_id', $user['id_usr']); // Simpan juga user ID jika diperlukan

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



