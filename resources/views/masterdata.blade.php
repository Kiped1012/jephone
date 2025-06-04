@extends('components.layout')

{{-- Notifikasi error --}}
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
<div class="flex-1 p-6 bg-[#f4f6f8] min-h-screen space-y-6">

    {{-- Section 1: Data Barang --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="bg-[#234e9a] px-6 py-4 flex justify-between items-center text-white">
            <div>
                <h1 class="text-lg font-semibold">📦 Data Barang</h1>
                <p class="text-sm opacity-80">Master / Data Barang</p>
            </div>
            <button id="btn-open-barang" class="bg-white text-[#234e9a] font-medium px-4 py-2 text-sm rounded hover:bg-gray-100">
                + Entri Data
            </button>
        </div>
        <div class="p-4 overflow-x-auto">
            <table class="w-full text-sm border">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-2 border">ID</th>
                        <th class="px-4 py-2 border">Nama Barang</th>
                        <th class="px-4 py-2 border">Kategori</th>
                        <th class="px-4 py-2 border">Supplier</th>
                    </tr>
                </thead>
                <tbody id="barangTable">
                    {{-- Diisi JavaScript --}}
                </tbody>
            </table>
        </div>
    </div>

    {{-- Section 2: Data Kategori --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="bg-[#234e9a] px-6 py-4 flex justify-between items-center text-white">
            <div>
                <h1 class="text-lg font-semibold">📂 Data Kategori</h1>
                <p class="text-sm opacity-80">Master / Data Kategori</p>
            </div>
            <button id="btn-open-kategori" class="bg-white text-[#234e9a] font-medium px-4 py-2 text-sm rounded hover:bg-gray-100">
                + Entri Kategori
            </button>
        </div>
        <div class="p-4 overflow-x-auto">
            <table class="w-full text-sm border">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-2 border">Nama Kategori</th>
                    </tr>
                </thead>
                <tbody id="kategoriTable">
                    {{-- Diisi JavaScript --}}
                </tbody>
            </table>
        </div>
    </div>

    {{-- Section 3: Data Supplier --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="bg-[#234e9a] px-6 py-4 flex justify-between items-center text-white">
            <div>
                <h1 class="text-lg font-semibold">🏷️ Data Supplier</h1>
                <p class="text-sm opacity-80">Master / Data Supplier</p>
            </div>
            <button id="btn-open-supplier" class="bg-white text-[#234e9a] font-medium px-4 py-2 text-sm rounded hover:bg-gray-100">
                + Entri Supplier
            </button>
        </div>
        <div class="p-4 overflow-x-auto">
            <table class="w-full text-sm border">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-2 border">Nama Supplier</th>
                    </tr>
                </thead>
                <tbody id="supplierTable">
                    {{-- Diisi JavaScript --}}
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Form Popup: Data Barang --}}
<div id="formBarang" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center hidden z-[999]">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h2 class="text-lg font-semibold mb-4">Tambah Data Barang</h2>
        <div class="space-y-3">
            <input type="text" id="namaBarang" class="w-full border px-3 py-2 rounded" placeholder="Nama Barang">
            <select id="kategoriBarang" class="w-full border px-3 py-2 rounded">
                <option value="">Pilih Kategori</option>
            </select>
            <select id="supplierBarang" class="w-full border px-3 py-2 rounded">
                <option value="">Pilih Supplier</option>
            </select>
        </div>
        <div class="mt-4 flex justify-end space-x-2">
            <button id="simpanBarang" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
            <button class="btn-close-modal px-4 py-2 border rounded" data-target="formBarang">Tutup</button>
        </div>
    </div>
</div>

{{-- Form Popup: Kategori --}}
<div id="formKategori" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center hidden z-[999]">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h2 class="text-lg font-semibold mb-4">Tambah Kategori</h2>
        <input type="text" id="namaKategori" class="w-full border px-3 py-2 rounded" placeholder="Nama Kategori">
        <div class="mt-4 flex justify-end space-x-2">
            <button id="simpanKategori" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
            <button class="btn-close-modal px-4 py-2 border rounded" data-target="formKategori">Tutup</button>
        </div>
    </div>
</div>

{{-- Form Popup: Supplier --}}
<div id="formSupplier" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center hidden z-[999]">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h2 class="text-lg font-semibold mb-4">Tambah Supplier</h2>
        <input type="text" id="namaSupplier" class="w-full border px-3 py-2 rounded" placeholder="Nama Supplier">
        <div class="mt-4 flex justify-end space-x-2">
            <button id="simpanSupplier" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
            <button class="btn-close-modal px-4 py-2 border rounded" data-target="formSupplier">Tutup</button>
        </div>
    </div>
</div>

@vite(['resources/js/masterdata.js'])
@endsection