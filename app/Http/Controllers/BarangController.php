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
            'title' => 'Daftar Barang'
        ]);
    }

    public function show($id)
    {
        $barangList = include resource_path('data/barang.php');

        // Sesuaikan index array: $id 1 berarti index 0
        $index = (int)$id - 1;

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

    public function create()
    {
       return view('entribarang', [
        'title' => 'Entri Barang'
        ]); 
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_brg' => 'required|string',
            'nama' => 'required|string',
            'kategori' => 'required|string',
            'supplier' => 'required|string',
            'stok' => 'required|integer|min:0',
            'harga_beli' => 'required|integer|min:0',
            'harga_jual' => 'required|integer|min:0',
        ]);

        $barangBaru = [
            'id_brg' => $validated['id_brg'],
            'nama' => $validated['nama'],
            'kategori' => $validated['kategori'],
            'stok' => $validated['stok'],
            'harga_beli' => $validated['harga_beli'],
            'harga_jual' => $validated['harga_jual'],
            'supplier' => $validated['supplier'],
        ];

        $filePath = resource_path('data/barang.php');
        $data = include($filePath);

        $data[] = $barangBaru;

        file_put_contents($filePath, '<?php return ' . var_export($data, true) . ';');

        return redirect()->route('barang.index')->with('success', 'Barang berhasil disimpan!');
    }

    public function edit($id)
    {
        $barangList = include resource_path('data/barang.php');
        $index = (int)$id;

        if (!isset($barangList[$index])) {
            abort(404);
        }

        return view('entribarang', [
            'barang' => $barangList[$index],
            'index' => $index,
            'title' => 'Edit Barang'
        ]);
    }

    public function update(Request $request, $index)
    {
        $validated = $request->validate([
            'id_brg' => 'required|string',
            'nama' => 'required|string',
            'kategori' => 'required|string',
            'supplier' => 'required|string',
            'stok' => 'required|integer|min:0',
            'harga_beli' => 'required|integer|min:0',
            'harga_jual' => 'required|integer|min:0',
        ]);

        $filePath = resource_path('data/barang.php');
        $data = include($filePath);

        $data[$index] = $validated;

        file_put_contents($filePath, '<?php return ' . var_export($data, true) . ';');

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $filePath = resource_path('data/barang.php');
        $data = include($filePath);

        array_splice($data, (int)$id, 1);

        file_put_contents($filePath, '<?php return ' . var_export($data, true) . ';');

        return response()->json(['success' => true, 'message' => 'Barang berhasil dihapus!']);
    }
}
