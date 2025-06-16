@extends('components.layout')

@section('content')
<div class="min-h-screen bg-[#f5f5f5] py-10 px-6">
    <!-- Header -->
    <div class="bg-[#234e9a] px-6 py-4 flex justify-between items-center text-white rounded-t-xl">
        <div>
            <h1 class="text-lg font-semibold">📊 Laporan Stok</h1>
            <p class="text-sm opacity-80">Laporan / Laporan Stok</p>
        </div>
        <div class="text-end">
            <small id="update-time" class="opacity-80">
                Update: {{ now()->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}
            </small>
        </div>
    </div>

    <div class="bg-white rounded-b-xl shadow-lg p-6">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white border-l-4 border-blue-500 shadow rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-1">
                        <div class="text-xs font-semibold text-blue-600 uppercase mb-1">Total Barang</div>
                        <div class="text-2xl font-bold text-gray-800" id="totalBarang">0</div>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-boxes text-2xl text-gray-300"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border-l-4 border-green-500 shadow rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-1">
                        <div class="text-xs font-semibold text-green-600 uppercase mb-1">Total Stok Masuk</div>
                        <div class="text-2xl font-bold text-gray-800" id="totalStokMasuk">0</div>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-arrow-up text-2xl text-gray-300"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border-l-4 border-yellow-500 shadow rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-1">
                        <div class="text-xs font-semibold text-yellow-600 uppercase mb-1">Total Stok Keluar</div>
                        <div class="text-2xl font-bold text-gray-800" id="totalStokKeluar">0</div>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-arrow-down text-2xl text-gray-300"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border-l-4 border-cyan-500 shadow rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-1">
                        <div class="text-xs font-semibold text-cyan-600 uppercase mb-1">Nilai Total Stok</div>
                        <div class="text-2xl font-bold text-gray-800" id="nilaiTotalStok">Rp 0</div>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-dollar-sign text-2xl text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Pergerakan Stok (Full Width) -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h3 class="text-lg font-semibold text-gray-800">Pergerakan Stok Harian</h3>
                <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                    <!-- Filter Periode untuk Chart -->
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700">Periode:</label>
                        <select class="px-3 py-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" id="filterPeriode">
                            <option value="7">7 Hari</option>
                            <option value="30" selected>30 Hari</option>
                            <option value="90">90 Hari</option>
                        </select>
                    </div>
                    <!-- Chart Type Toggle -->
                    <div class="flex space-x-2">
                        <input type="radio" class="sr-only" name="chartType" id="line" checked>
                        <label class="px-3 py-1 text-sm border border-blue-500 text-blue-500 rounded cursor-pointer hover:bg-blue-50 transition-colors" for="line">Line</label>
                        <input type="radio" class="sr-only" name="chartType" id="bar">
                        <label class="px-3 py-1 text-sm border border-blue-500 text-blue-500 rounded cursor-pointer hover:bg-blue-50 transition-colors" for="bar">Bar</label>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="chart-container">
                    <canvas id="stockMovementChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Distribusi Stok Chart -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h3 class="text-lg font-semibold text-gray-800">Distribusi Stok per Kategori</h3>
                <!-- Filter Kategori untuk Donut Chart -->
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-700">Kategori:</label>
                    <select class="px-3 py-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" id="filterKategori">
                        <option value="">Semua Kategori</option>
                        @foreach(include(resource_path('data/kategori.php')) as $kategori)
                            <option value="{{ $kategori }}">{{ $kategori }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="p-6">
                <div class="max-w-md mx-auto">
                    <canvas id="stockDistributionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Moving Items -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-green-600">🔥 Top 5 Barang Terlaris</h3>
                </div>
                <div class="p-6">
                    <div id="topSellingItems"></div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-red-600">⚠️ Stok Menipis</h3>
                </div>
                <div class="p-6">
                    <div id="lowStockItems"></div>
                </div>
            </div>
        </div>

        <!-- Detailed Stock Table -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h3 class="text-lg font-semibold text-gray-800">Detail Pergerakan Stok</h3>
                <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                    <!-- Filter Status Stok untuk Tabel -->
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700">Status Stok:</label>
                        <select class="px-3 py-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" id="filterStok">
                            <option value="">Semua</option>
                            <option value="rendah">Stok Rendah (&lt;500)</option>
                            <option value="normal">Stok Normal</option>
                            <option value="tinggi">Stok Tinggi (&gt;700)</option>
                        </select>
                    </div>
                    <!-- Export Buttons -->
                    <div class="flex space-x-2">
                        <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm" onclick="exportToExcel()">
                            <i class="fas fa-file-excel mr-2"></i>Export Excel
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table Controls -->
            <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-700">Show</label>
                    <select class="px-3 py-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" id="entriesPerPage">
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">All</option>
                    </select>
                    <span class="text-sm text-gray-700">entries</span>
                </div>
                
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-700">Search:</label>
                    <input type="text" id="searchInput" placeholder="Cari nama barang atau kode..." 
                           class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm min-w-[250px]">
                </div>
            </div>

            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-blue-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Kode</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Nama Barang</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Kategori</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Stok Awal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Stok Masuk</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Stok Keluar</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Stok Akhir</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Nilai Stok</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Status</th>
                            </tr>
                        </thead>
                        <tbody id="stockTableBody" class="bg-white divide-y divide-gray-200">
                            <!-- Data akan dimuat via JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Controls -->
                <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-sm text-gray-700" id="tableInfo">
                        Showing 0 to 0 of 0 entries
                    </div>
                    <div class="flex space-x-2" id="paginationControls">
                        <button id="prevBtn" class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            Previous
                        </button>
                        <div id="pageNumbers" class="flex space-x-1">
                            <!-- Page numbers will be generated dynamically -->
                        </div>
                        <button id="nextBtn" class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-rendah { background-color: #fee2e2; color: #dc2626; }
.status-normal { background-color: #dcfce7; color: #16a34a; }
.status-tinggi { background-color: #dbeafe; color: #2563eb; }

.item-card {
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 0.75rem;
    transition: all 0.2s;
}

.item-card:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    transform: translateY(-1px);
}

.chart-container {
    position: relative;
    height: 400px;
    width: 100%;
}

/* Radio button styling for chart type */
input[type="radio"]:checked + label {
    background-color: #3b82f6;
    color: white;
}

/* Page number button styling */
.page-btn {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    background-color: white;
    color: #6b7280;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.page-btn:hover {
    background-color: #f9fafb;
}

.page-btn.active {
    background-color: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

/* Responsive chart height */
@media (max-width: 768px) {
    .chart-container {
        height: 300px;
    }
}

@media print {
    .no-print { display: none !important; }
    .chart-container {
        height: 300px;
    }
}
</style>

<script>
    const barang = @json(include(resource_path('data/barang.php')));
    const penjualan = @json(include(resource_path('data/penjualan.php')));
    const pembelian = @json(include(resource_path('data/pembelian.php')));
    const kategori = @json(include(resource_path('data/kategori.php')));
    // Set timezone offset ke UTC+7 (WIB)
    function updateClock() {
        const now = new Date();
        // Dapatkan UTC, lalu tambahkan offset WIB (+7 jam)
        const utc = now.getTime() + (now.getTimezoneOffset() * 60000);
        const wib = new Date(utc + (7 * 60 * 60000));

        const day = String(wib.getDate()).padStart(2, '0');
        const month = String(wib.getMonth() + 1).padStart(2, '0');
        const year = wib.getFullYear();
        const hour = String(wib.getHours()).padStart(2, '0');
        const minute = String(wib.getMinutes()).padStart(2, '0');

        document.getElementById('update-time').textContent = 
            `Update: ${day}/${month}/${year} ${hour}:${minute}`;
    }

    // Update setiap detik
    updateClock();
    setInterval(updateClock, 1000);
</script>

@vite('resources/js/laporanstok.js')
@endsection