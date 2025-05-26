<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\File;

class OrderController extends Controller
{
    public function create()
    {
        $title = 'Penjualan';
        $barangs = include resource_path('data/barang.php'); // Ambil data dari file
        return view('penjualan', compact('barangs', 'title'));
    }

    public function store(Request $request)
    {
        $items = json_decode($request->input('items'), true);

        // Ambil data barang saat ini
        $barangPath = resource_path('data/barang.php');
        $barangs = include $barangPath;

        // Update stok
        foreach ($items as $item) {
            foreach ($barangs as &$barang) {
                if ($barang['nama'] === $item['nama']) {
                    $barang['stok'] -= $item['jumlah'];
                        if ($barang['stok'] < 0) {
                            $barang['stok'] = 0; // Cegah stok negatif
                        }
                    break;
                }
            }
        }

        // Tulis ulang file barang.php
        $phpArray = var_export($barangs, true);
        $phpFileContent = "<?php\n\nreturn $phpArray;\n";
        File::put($barangPath, $phpFileContent);

        // Simpan data transaksi (misalnya ke file `penjualan.php` sesuai sistem kamu)
        $penjualanPath = resource_path('data/penjualan.php');
        $penjualan = file_exists($penjualanPath) ? include $penjualanPath : [];

        $penjualan[] = [
            'id' => uniqid('trx_'),
            'tanggal' => $request->input('tanggal'),
            'kasir' => $request->input('kasir'),
            'total_belanja' => (int) $request->input('total_belanja'),
            'metode_pembayaran' => $request->input('metode_pembayaran'),
            'email_pelanggan' => $request->input('email_pelanggan'),
            'jatuh_tempo' => $request->input('jatuh_tempo'),
            'items' => $items,
        ];

        $phpPenjualan = var_export($penjualan, true);
        File::put($penjualanPath, "<?php\n\nreturn $phpPenjualan;\n");

        return redirect()->back()->with('success', 'Transaksi berhasil disimpan dan stok diperbarui.');
    }

    public function history()
    {
        $title = 'Histori Penjualan';
        $penjualanPath = resource_path('data/penjualan.php');
        $penjualan = file_exists($penjualanPath) ? include $penjualanPath : [];

        return view('historipenjualan', compact('penjualan', 'title'));
    }

}