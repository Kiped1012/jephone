<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class MasterDataController extends Controller
{
    private $pathBarang = 'resources/data/masterdata.php';
    private $pathKategori = 'resources/data/kategori.php';
    private $pathSupplier = 'resources/data/suppliers.php';

    public function index()
    {
        return view('masterdata');
    }

    public function getBarang()
    {
        return response()->json(include base_path($this->pathBarang));
    }

    public function getKategori()
    {
        return response()->json(include base_path($this->pathKategori));
    }

    public function getSuppliers()
    {
        return response()->json(include base_path($this->pathSupplier));
    }

    public function storeBarang(Request $request)
    {
        $data = include base_path($this->pathBarang);
        $new = [
            'id_brg' => 'BRG_' . strtoupper(substr(md5(uniqid()), 0, 4)),
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'stok' => 0,
            'harga_beli' => 0,
            'harga_jual' => 0,
            'supplier' => $request->supplier,
        ];
        $data[] = $new;
        $this->writeData($this->pathBarang, $data);
        return response()->json(['success' => true]);
    }

    public function storeKategori(Request $request)
    {
        $data = include base_path($this->pathKategori);
        $data[] = $request->nama;
        $data = array_unique($data);
        $this->writeData($this->pathKategori, $data);
        return response()->json(['success' => true]);
    }

    public function storeSupplier(Request $request)
    {
        $data = include base_path($this->pathSupplier);
        $data[] = $request->nama;
        $data = array_unique($data);
        $this->writeData($this->pathSupplier, $data);
        return response()->json(['success' => true]);
    }

    private function writeData($path, $array)
    {
        $export = var_export($array, true);
        File::put(base_path($path), "<?php\n\nreturn " . $export . ";");
    }
}
