@extends('components.layout')

@section('content')
<div class="flex-1 p-6 bg-[#f9f9f9] min-h-screen">
    <div class="text-sm breadcrumbs text-gray-600">
        <span>Master</span> <span class="mx-2">/</span> <span>Data Barang</span>
    </div>
    <div class="bg-white p-6 rounded-xl shadow mt-4">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-semibold">Data Barang</h1>
            <button class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-600">+ Entri Data</button>
        </div>
        <div class="flex justify-between mb-4 text-sm">
            <label>
                Show
                <select class="mx-2 border rounded px-2 py-1">
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                </select>
                items
            </label>
            <label>
                Cari:
                <input type="text" class="ml-2 border px-2 py-1 rounded" placeholder="Cari data...">
            </label>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-3 py-2 border">No</th>
                        <th class="px-3 py-2 border">Nama</th>
                        <th class="px-3 py-2 border">Kategori</th>
                        <th class="px-3 py-2 border">Stok Terkini</th>
                        <th class="px-3 py-2 border">Harga Beli</th>
                        <th class="px-3 py-2 border">Harga Jual</th>
                        <th class="px-3 py-2 border">Supplier</th>
                        <th class="px-3 py-2 border">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($barang as $index => $item)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-3 py-2 border">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 border">
                                <a href="{{ route('barang.show', $index+1)}}" class="text-black hover:text-blue-600">
                                    {{ $item['nama'] }}
                                </a>
                            </td>
                            <td class="px-3 py-2 border">{{ $item['kategori'] }}</td>
                            <td class="px-3 py-2 border">{{ $item['stok'] }}</td>
                            <td class="px-3 py-2 border">Rp{{ number_format($item['harga_beli']) }}</td>
                            <td class="px-3 py-2 border">Rp{{ number_format($item['harga_jual']) }}</td>
                            <td class="px-3 py-2 border">{{ $item['supplier'] }}</td>
                            <td class="px-3 py-2 border space-x-2">
                                <button class="text-blue-600 hover:underline">✏️</button>
                                <button class="text-red-600 hover:underline">🗑️</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
