@extends('components.layout')

@section('content')
<div class="p-6">
    <!-- Tabel Histori Penjualan dengan Header Tergabung -->
    <div class="bg-white shadow-md rounded-xl">
        <!-- Header -->
        <div class="bg-[#234e9a] px-6 py-4 flex justify-between items-center text-white rounded-t-xl">
            <div>
                <h1 class="text-lg font-semibold">📜 Histori Penjualan</h1>
                <p class="text-sm opacity-80">Transaksi / Histori Penjualan</p>
            </div>
        </div>

        <!-- Controls Section -->
        <div class="p-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <!-- Show Entries -->
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600">Show</label>
                    <select id="entries-select" class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <label class="text-sm text-gray-600">entries</label>
                </div>

                <!-- Search -->
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600">Search:</label>
                    <input type="text" id="search-input" placeholder="Cari transaksi..." 
                           class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
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
                <tbody id="table-body" class="divide-y divide-gray-200">
                    <!-- Data akan diisi oleh JavaScript -->
                </tbody>
            </table>
            
            <!-- No Data Message -->
            <div id="no-data" class="text-center py-8 text-gray-500 hidden">
                <p>Tidak ada data yang sesuai dengan pencarian.</p>
            </div>
        </div>

        <!-- Pagination and Info -->
        <div class="p-4 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <!-- Info -->
                <div class="text-sm text-gray-600">
                    <span id="info-text">Showing 0 to 0 of 0 entries</span>
                </div>

                <!-- Pagination -->
                <div class="flex items-center gap-2">
                    <button id="prev-btn" class="px-3 py-2 text-sm border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Previous
                    </button>
                    
                    <div id="pagination-numbers" class="flex gap-1">
                        <!-- Page numbers akan diisi oleh JavaScript -->
                    </div>
                    
                    <button id="next-btn" class="px-3 py-2 text-sm border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Next
                    </button>
                </div>
            </div>
        </div>
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

<!-- Hidden data for JavaScript -->
<script>
    window.penjualanData = @json(include(resource_path('data/penjualan.php')));
</script>

@vite('resources/js/detailhistoripenjualan.js')

@endsection