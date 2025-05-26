@extends('components.layout')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="bg-[#234e9a] px-6 py-4 flex justify-between items-center text-white rounded-t-xl mb-6">
        <div>
            <h1 class="text-lg font-semibold">📜 Histori Penjualan</h1>
            <p class="text-sm opacity-80">Transaksi / Histori Penjualan</p>
        </div>
    </div>

    <!-- Tabel Histori Penjualan -->
    <div class="overflow-x-auto bg-white shadow-md rounded-b-xl">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">ID Transaksi</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Tanggal</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Kasir</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Total Belanja</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Metode</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @php
                    $data = include(resource_path('data/penjualan.php'));
                @endphp

                @forelse ($data as $transaksi)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $transaksi['id'] }}</td>
                        <td class="px-4 py-2">{{ $transaksi['tanggal'] }}</td>
                        <td class="px-4 py-2">{{ $transaksi['kasir'] }}</td>
                        <td class="px-4 py-2">Rp{{ number_format($transaksi['total_belanja'], 0, ',', '.') }}</td>
                        <td class="px-4 py-2">{{ $transaksi['metode_pembayaran'] }}</td>
                        <td class="px-4 py-2 text-center">
                            <a href="#" onclick='showDetail(@json($transaksi["items"]))'
                            class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-xs">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-gray-500">Belum ada data penjualan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Detail Transaksi -->
<div id="modal-detail" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl p-6 relative">
        <button onclick="closeModal()" class="absolute top-2 right-2 text-gray-500 hover:text-red-500 text-xl">&times;</button>
        <h2 class="text-lg font-bold mb-4">Detail Transaksi</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-3 py-2 border">#</th>
                        <th class="px-3 py-2 border">Nama Barang</th>
                        <th class="px-3 py-2 border">Jumlah</th>
                        <th class="px-3 py-2 border">Harga</th>
                        <th class="px-3 py-2 border">Total</th>
                    </tr>
                </thead>
                <tbody id="detail-content" class="bg-white">
                    <!-- Diisi lewat JavaScript -->
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right font-semibold px-3 py-2 border">Total Belanja</td>
                        <td id="detail-total" class="px-3 py-2 border font-bold text-green-700 text-right"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@vite('resources/js/detailhistoripenjualan.js')



@endsection
