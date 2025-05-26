<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create()
    {
        $barangs = include resource_path('data/barang.php'); // Ambil data dari file
        return view('penjualan', compact('barangs'));
    }
}