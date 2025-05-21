@extends('components.layout')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <!-- Header -->
    <div class="bg-blue-900 text-white rounded-t-lg px-6 py-4">
        <h2 class="text-xl font-semibold">🗂️ Entri Barang</h2>
        <p class="text-sm mt-1">Master / Data Barang / Entri Data</p>
    </div>

    <!-- Form -->
    <form action="{{ route('barang.store') }}" method="POST" class="p-6 space-y-6">
        @csrf

        <!-- Baris 1 -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block font-semibold mb-1">Nama Barang</label>
                <input type="text" name="nama" class="w-full border border-gray-300 rounded-md px-4 py-2 bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
            </div>
            <div>
                <label class="block font-semibold mb-1">Supplier</label>
                <input type="text" name="supplier" class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
            </div>
        </div>

        <!-- Baris 2 -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t pt-6">
            <div>
                <label class="block font-semibold mb-1">Kategori</label>
                <select name="kategori" class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
                    <option value="">--pilih--</option>
                    <option value="Komponen">Komponen</option>
                    <option value="IC">IC</option>
                    <option value="Konektor">Konektor</option>
                    <!-- Tambahkan sesuai kebutuhan -->
                </select>
            </div>
            <div>
                <label class="block font-semibold mb-1">Stok Terkini</label>
                <input type="number" name="stok" class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
            </div>
        </div>

        <!-- Baris 3 -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block font-semibold mb-1">Harga Beli</label>
                <input type="number" name="harga_beli" class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
            </div>
            <div>
                <label class="block font-semibold mb-1">Harga Jual</label>
                <input type="number" name="harga_jual" class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
            </div>
        </div>

        <!-- Tombol -->
        <div class="flex justify-end gap-4 pt-4">
            <button type="submit" class="bg-blue-800 hover:bg-blue-900 text-white font-semibold px-6 py-2 rounded-md shadow-md">Simpan</button>
            <a href="{{ route('barang.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-6 py-2 rounded-md shadow-md">Batal</a>
        </div>
    </form>
</div>
@endsection
