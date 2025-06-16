@extends('components.layout')

@section('content')
<div class="min-h-screen bg-[#f5f5f5] py-10 px-6">
    <!-- Header -->
    <div class="bg-[#234e9a] px-6 py-4 flex justify-between items-center text-white rounded-t-xl">
        <div>
            <h1 class="text-lg font-semibold">⚖️ Laporan Arus Kas</h1>
            <p class="text-sm opacity-80">Laporan / Laporan Arus Kas</p>
        </div>
        <div class="text-end">
            <small id="update-time" class="opacity-80">
                Update: {{ now()->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}
            </small>
        </div>
    </div>

    <!-- Content Container -->
    <div class="bg-white rounded-b-xl shadow-lg">
        <!-- Filter Section -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-wrap gap-4 items-center">
                <div class="flex items-center gap-2">
                    <label for="filterMonth" class="text-sm font-medium text-gray-700">Bulan:</label>
                    <select id="filterMonth" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Bulan</option>
                        <option value="01">Januari</option>
                        <option value="02">Februari</option>
                        <option value="03">Maret</option>
                        <option value="04">April</option>
                        <option value="05">Mei</option>
                        <option value="06">Juni</option>
                        <option value="07">Juli</option>
                        <option value="08">Agustus</option>
                        <option value="09">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <label for="filterYear" class="text-sm font-medium text-gray-700">Tahun:</label>
                    <select id="filterYear" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Tahun</option>
                        <option value="2025">2025</option>
                        <option value="2024">2024</option>
                    </select>
                </div>
                <button id="applyFilter" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition-colors">
                    Terapkan Filter
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-800">Total Kas Masuk</p>
                            <p id="totalKasMasuk" class="text-2xl font-bold text-green-600">Rp 0</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-red-800">Total Kas Keluar</p>
                            <p id="totalKasKeluar" class="text-2xl font-bold text-red-600">Rp 0</p>
                        </div>
                        <div class="p-3 bg-red-100 rounded-full">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-800">Saldo Akhir</p>
                            <p id="saldoAkhir" class="text-2xl font-bold text-blue-600">Rp 0</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="px-6 py-4 border-t border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Grafik Arus Kas</h2>
            <div class="bg-gray-50 rounded-lg p-4">
                <canvas id="cashFlowChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Detailed Table -->
        <div class="px-6 py-4 border-t border-gray-200">
            <!-- Header with Export/Print buttons -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Detail Arus Kas</h2>
                <div class="flex flex-wrap gap-2">
                    <button id="exportExcel" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Excel
                    </button>
                    <button id="exportPDF" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Export PDF
                    </button>
                </div>
            </div>
            
            <!-- Table Controls -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                <div class="flex items-center gap-2">
                    <label for="entriesPerPage" class="text-sm font-medium text-gray-700">Show</label>
                    <select id="entriesPerPage" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-sm text-gray-700">entries</span>
                </div>
                <div class="flex items-center gap-2">
                    <label for="searchTable" class="text-sm font-medium text-gray-700">Search:</label>
                    <input type="text" id="searchTable" placeholder="Cari transaksi..." class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="cashFlowTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Transaksi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Kas Masuk</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Kas Keluar</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo</th>
                        </tr>
                    </thead>
                    <tbody id="cashFlowTableBody" class="bg-white divide-y divide-gray-200">
                        <!-- Data akan diisi oleh JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination Controls -->
            <div class="flex flex-col sm:flex-row justify-between items-center mt-4 gap-4">
                <div class="text-sm text-gray-700">
                    Showing <span id="showingStart">0</span> to <span id="showingEnd">0</span> of <span id="totalEntries">0</span> entries
                    <span id="filteredInfo" class="hidden">(filtered from <span id="totalEntriesUnfiltered">0</span> total entries)</span>
                </div>
                <div class="flex items-center gap-2">
                    <button id="prevPage" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        Previous
                    </button>
                    <div id="pageNumbers" class="flex gap-1">
                        <!-- Page numbers will be inserted here -->
                    </div>
                    <button id="nextPage" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Modal/Template (Hidden) -->
<div id="printTemplate" class="hidden">
    <div class="print-content">
        <div class="print-header text-center mb-6">
            <h1 class="text-2xl font-bold mb-2">⚖️ LAPORAN ARUS KAS</h1>
            <p class="text-sm text-gray-600" id="printPeriod">Periode: -</p>
            <p class="text-sm text-gray-600" id="printDate">Dicetak pada: {{ now()->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}</p>
        </div>
        
        <div class="print-summary mb-6">
            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="border p-3 rounded">
                    <p class="font-semibold text-green-600">Total Kas Masuk</p>
                    <p class="text-lg font-bold" id="printTotalKasMasuk">Rp 0</p>
                </div>
                <div class="border p-3 rounded">
                    <p class="font-semibold text-red-600">Total Kas Keluar</p>
                    <p class="text-lg font-bold" id="printTotalKasKeluar">Rp 0</p>
                </div>
                <div class="border p-3 rounded">
                    <p class="font-semibold text-blue-600">Saldo Akhir</p>
                    <p class="text-lg font-bold" id="printSaldoAkhir">Rp 0</p>
                </div>
            </div>
        </div>
        
        <table class="w-full border-collapse border border-gray-300 text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 px-3 py-2 text-left">Tanggal</th>
                    <th class="border border-gray-300 px-3 py-2 text-left">Jenis</th>
                    <th class="border border-gray-300 px-3 py-2 text-left">ID Transaksi</th>
                    <th class="border border-gray-300 px-3 py-2 text-left">Keterangan</th>
                    <th class="border border-gray-300 px-3 py-2 text-right">Kas Masuk</th>
                    <th class="border border-gray-300 px-3 py-2 text-right">Kas Keluar</th>
                    <th class="border border-gray-300 px-3 py-2 text-right">Saldo</th>
                </tr>
            </thead>
            <tbody id="printTableBody">
                <!-- Data akan diisi oleh JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<!-- Loading Modal -->
<div id="loadingModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
        <span class="text-gray-700">Memproses...</span>
    </div>
</div>

<!-- CDN untuk Export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    
    .print-content, .print-content * {
        visibility: visible;
    }
    
    .print-content {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    
    .print-header {
        margin-bottom: 20px;
    }
    
    .print-summary {
        margin-bottom: 20px;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }
    
    th, td {
        border: 1px solid #000;
        padding: 5px;
        text-align: left;
    }
    
    th {
        background-color: #f0f0f0;
        font-weight: bold;
    }
    
    .text-right {
        text-align: right;
    }
    
    .text-center {
        text-align: center;
    }
}
</style>

<script>
    // Inject data ke JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize data
        window.LaporanArusKas.initializeData(
            @json(include(resource_path('data/barang.php'))),
            @json(include(resource_path('data/penjualan.php'))),
            @json(include(resource_path('data/pembelian.php'))),
            @json(include(resource_path('data/pelunasan.php')))
        );
        
        // Initialize application
        window.LaporanArusKas.initApp();
    });
</script>

@vite('resources/js/laporanaruskas.js')
@endsection