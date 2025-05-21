<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        $barang = include resource_path('data/barang.php');
        return view('databarang', [
            'barang' => $barang,
            'title' => 'Data Barang'
        ]);
    }

    public function show($id)
    {
        $barangList = include resource_path('data/barang.php');

        // Sesuaikan index array: $id 1 berarti index 0
        $index = $id - 1;

        // Cek apakah data dengan index itu ada
        if (!isset($barangList[$index])) {
            abort(404);
        }

        $barang = $barangList[$index];

       return view('detailbarang', [
            'barang' => $barang,
            'title' => 'Detail Barang'
        ]);
    }

}
