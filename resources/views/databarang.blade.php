@extends('components.layout')

@section('content')
<div class="flex-1 p-6 bg-[#f4f6f8] min-h-screen">
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="bg-[#234e9a] px-6 py-4 flex justify-between items-center text-white">
            <div>
                <h1 class="text-lg font-semibold">📦 Daftar Barang</h1>
                <p class="text-sm opacity-80">Master / Daftar Barang</p>
            </div>
            <a href="{{ route('barang.create') }}">
                <button class="bg-white text-[#234e9a] font-medium px-4 py-2 text-sm rounded hover:bg-gray-100">
                    + Entri Data
                </button>
            </a>
        </div>

        <div class="p-6">
            <div class="flex justify-between items-center mb-4 text-sm text-gray-700">
                <div>
                    Show
                    <select class="mx-2 border rounded px-2 py-1">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                    </select>
                    items
                </div>
                <div>
                    Cari:
                    <input type="text" class="ml-2 border px-2 py-1 rounded" placeholder="Search...">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border border-gray-200">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 border">No</th>
                            <th class="px-4 py-3 border">Nama</th>
                            <th class="px-4 py-3 border">Kategori</th>
                            <th class="px-4 py-3 border">Stok</th>
                            <th class="px-4 py-3 border">Harga Beli</th>
                            <th class="px-4 py-3 border">Harga Jual</th>
                            <th class="px-4 py-3 border">Supplier</th>
                            <th class="px-4 py-3 border">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-800">
                        @foreach ($barang as $index => $item)
                            <tr class="hover:bg-gray-50 border-b">
                                <td class="px-4 py-2 border">{{ $index + 1 }}</td>
                                <td class="px-4 py-2 border">
                                    <a href="{{ route('barang.show', $index + 1) }}" class="text-black hover:text-blue-600">
                                        {{ $item['nama'] }}
                                    </a>
                                </td>
                                <td class="px-4 py-2 border">{{ $item['kategori'] }}</td>
                                <td class="px-4 py-2 border">{{ $item['stok'] }}</td>
                                <td class="px-4 py-2 border">Rp{{ number_format($item['harga_beli']) }}</td>
                                <td class="px-4 py-2 border">Rp{{ number_format($item['harga_jual']) }}</td>
                                <td class="px-4 py-2 border">{{ $item['supplier'] }}</td>
                                <td class="px-4 py-2 border text-center space-x-1">
                                    <button class="text-blue-500 hover:text-blue-700">✏️</button>
                                    <button class="text-red-500 hover:text-red-700">🗑️</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@vite(['resources/js/databarang.js'])
@endsection
