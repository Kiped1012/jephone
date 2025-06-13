<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PurchaseController extends Controller
{
    public function create()
    {
        $title = 'Pembelian';
        $barangs = include resource_path('data/barang.php'); // Ambil data barang
        return view('pembelian', compact('barangs', 'title'));
    }

    public function store(Request $request)
    {
        // Debug: cek data yang diterima
        \Log::info('Data request pembelian:', $request->all());

        $validated = $request->validate([
            'items' => 'required|string',
            'penanggung_jawab' => 'required|string',
            'tanggal' => 'required|date',
            'dibayar_input' => 'required|numeric|min:0',  // Sesuaikan dengan nama di JavaScript
            'total_belanja' => 'required|numeric|min:0',
            'kembalian_output' => 'required|numeric|min:0', // Tambahkan validasi kembalian
        ]);

        $items = json_decode($validated['items'], true);

        // Pastikan direktori data ada
        $dataDir = resource_path('data');
        if (!File::exists($dataDir)) {
            File::makeDirectory($dataDir, 0755, true);
        }

        // Ambil data barang saat ini
        $barangPath = resource_path('data/barang.php');
        $barangs = [];
        if (File::exists($barangPath)) {
            $barangs = include $barangPath;
        }

        // Update stok: TAMBAH stok karena ini pembelian
        foreach ($items as $item) {
            foreach ($barangs as &$barang) {
                if ($barang['nama'] === $item['nama']) {
                    $barang['stok'] += $item['jumlah'];
                    break;
                }
            }
        }

        // Tulis ulang file barang.php
        if (!empty($barangs)) {
            $phpArray = var_export($barangs, true);
            $phpFileContent = "<?php\n\nreturn $phpArray;\n";
            File::put($barangPath, $phpFileContent);
        }

        // Simpan data transaksi pembelian
        $pembelianPath = resource_path('data/pembelian.php');
        $pembelian = [];
        if (File::exists($pembelianPath)) {
            $pembelian = include $pembelianPath;
        }

        $pembelian[] = [
            'id_transaksi' => 'PB_' . strtoupper(uniqid()),
            'tanggal' => $validated['tanggal'],
            'penanggung_jawab' => $validated['penanggung_jawab'],
            'items' => $items,
            'total_belanja' => $validated['total_belanja'],
            'dibayar' => $validated['dibayar_input'], // Sesuaikan nama field
            'kembalian' => $validated['kembalian_output'], // Gunakan kembalian yang sudah dihitung
        ];

        // Tulis file pembelian.php
        $phpPembelian = var_export($pembelian, true);
        $phpContent = "<?php\n\nreturn $phpPembelian;\n";
        
        // Coba tulis file dan tangkap error jika ada
        try {
            File::put($pembelianPath, $phpContent);
            \Log::info('File pembelian.php berhasil ditulis di: ' . $pembelianPath);
        } catch (\Exception $e) {
            \Log::error('Gagal menulis file pembelian.php: ' . $e->getMessage());
            return redirect()->route('pembelian.create')->with('error', 'Gagal menyimpan data pembelian.');
        }

        return redirect()->route('pembelian.create')->with('success', 'Transaksi pembelian berhasil disimpan dan stok ditambah.');
    }
}