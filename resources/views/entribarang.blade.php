@extends('components.layout')
<div x-data="{ show: false, message: '' }"
    x-show="show"
    x-on:show-error.window="message = $event.detail; show = true; setTimeout(() => show = false, 3000)"
    class="fixed right-4 top-4 z-50 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-md max-w-sm w-full"
    x-cloak>
    <div class="flex justify-between items-center">
        <span class="text-sm" x-text="message"></span>
        <button @click="show = false" class="ml-4 text-red-700 hover:text-red-900 font-bold">&times;</button>
    </div>
</div>
@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <!-- Header -->
    <div class="bg-blue-900 text-white rounded-t-lg px-6 py-4">
        <h2 class="text-xl font-semibold">🗂️ Entri Barang</h2>
        <p class="text-sm mt-1">Master / Data Barang / Entri Data</p>
    </div>

    <!-- Form -->
    <form id="formBarang" action="{{ isset($index) ? route('barang.update', $index) : route('barang.store') }}" method="POST" class="p-6 space-y-6">
        @csrf
        @if(isset($index)) @method('PUT') @endif

        <!-- Baris 1 -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block font-semibold mb-1">Nama Barang</label>
                <select name="nama" id="namaBarang" class="w-full border border-gray-300 rounded-md px-4 py-2 bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-400" required>
                    <option value="">-- Pilih Barang --</option>
                </select>
                <input type="hidden" name="id_brg" id="idBarang" value="{{ $barang['id_brg'] ?? '' }}">
            </div>
            <div>
                <label class="block font-semibold mb-1">Supplier</label>
                <input type="text" name="supplier" id="supplier" class="w-full border border-gray-300 rounded-md px-4 py-2 bg-gray-100" readonly required value="{{ $barang['supplier'] ?? '' }}">
            </div>
        </div>

        <!-- Baris 2 -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t pt-6">
            <div>
                <label class="block font-semibold mb-1">Kategori</label>
                <input type="text" name="kategori" id="kategori" class="w-full border border-gray-300 rounded-md px-4 py-2 bg-gray-100" readonly required value="{{ $barang['kategori'] ?? '' }}">
            </div>
            <div>
                <label class="block font-semibold mb-1">Stok Terkini</label>
                <input type="number" name="stok" class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" required value="{{ $barang['stok'] ?? '' }}">
            </div>
        </div>

        <!-- Baris 3 -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block font-semibold mb-1">Harga Beli</label>
                <input type="number" name="harga_beli" class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" required value="{{ $barang['harga_beli'] ?? '' }}">
            </div>
            <div>
                <label class="block font-semibold mb-1">Harga Jual</label>
                <input type="number" name="harga_jual" class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" required value="{{ $barang['harga_jual'] ?? '' }}">
            </div>
        </div>

        <!-- Tombol -->
        <div class="flex justify-end gap-4 pt-4">
            <button id="btnSimpan" type="submit" class="bg-blue-800 hover:bg-blue-900 text-white font-semibold px-6 py-2 rounded-md shadow-md">Simpan</button>
            <a href="{{ route('barang.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-6 py-2 rounded-md shadow-md">Batal</a>
        </div>
    </form>
</div>

<script>
    window.masterBarang = @json(include(resource_path('data/masterdata.php')));
    window.editBarang = @json($barang ?? null);
    window.dataBarangTersimpan = @json(include(resource_path('data/barang.php')));
</script>

@vite(['resources/js/entribarang.js'])

@endsection
