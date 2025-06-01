<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PelunasanController extends Controller
{
    // Menampilkan daftar transaksi piutang
    public function index()
    {
        $penjualan = include resource_path('data/penjualan.php');
        $pelunasan = file_exists(resource_path('data/pelunasan.php')) 
                    ? include resource_path('data/pelunasan.php') 
                    : [];

        $idYangSudahLunas = array_column($pelunasan, 'id_transaksi');

        $piutang = array_filter($penjualan, function ($trx) use ($idYangSudahLunas) {
            return $trx['metode_pembayaran'] === 'Piutang' && !in_array($trx['id'], $idYangSudahLunas);
        });

        $title = 'Pelunasan Piutang';
        return view('pelunasan', compact('piutang', 'title'));
    }

    public function getTransaksiById(Request $request)
    {
        $id = $request->query('id');
        $penjualan = include resource_path('data/penjualan.php');

        $transaksi = collect($penjualan)->firstWhere('id', $id);

        if ($transaksi) {
            return response()->json($transaksi);
        } else {
            return response()->json(['error' => 'Transaksi tidak ditemukan'], 404);
        }
    }
   
    public function store(Request $request)
    {
        $dataBaru = [
            'id_transaksi' => $request->input('id_transaksi'),
            'email' => $request->input('email'),
            'tanggal_piutang' => $request->input('tanggal_transaksi'),
            'jatuh_tempo_piutang' => $request->input('jatuh_tempo'),
            'total_piutang' => $request->input('total_belanja'),
            'tanggal_pelunasan' => $request->input('tanggal_pelunasan'),
            'dibayar' => $request->input('dibayar'),
            'kembalian' => $request->input('kembalian'),
        ];

        $file = resource_path('data/pelunasan.php');
        $pelunasan = file_exists($file) ? include $file : [];

        // Tambahkan data baru ke array
        $pelunasan[] = $dataBaru;

        // Simpan kembali ke file
        file_put_contents($file, '<?php return ' . var_export($pelunasan, true) . ';');

        return redirect()->back()->with('success', 'Pelunasan berhasil disimpan.');
    }

    public function getPelunasan()
    {
        $pelunasan = include resource_path('data/pelunasan.php');
        return response()->json($pelunasan);
    }
}
