@extends('components.layout')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#234e9a] to-blue-800 p-6 rounded-lg">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Selamat Datang di Halaman Dashboard</h1>
            <p class="text-blue-100">Ringkasan keseluruhan bisnis Anda</p>
        </div>

        <?php
        // Load data dari file PHP
        $barang = include(resource_path('data/barang.php'));
        $pembelian = include(resource_path('data/pembelian.php'));
        $penjualan = include(resource_path('data/penjualan.php'));
        $pelunasan = include(resource_path('data/pelunasan.php'));

        // Hitung statistik utama
        $total_items = count($barang);
        $total_stok = array_sum(array_column($barang, 'stok'));
        $nilai_inventory = 0;
        $potensi_keuntungan = 0;
        
        foreach ($barang as $item) {
            $nilai_inventory += $item['stok'] * $item['harga_beli'];
            $potensi_keuntungan += $item['stok'] * ($item['harga_jual'] - $item['harga_beli']);
        }

        // Urutkan penjualan berdasarkan tanggal terbaru
        usort($penjualan, function($a, $b) {
            return strtotime($b['tanggal']) - strtotime($a['tanggal']);
        });

        // Ambil transaksi terbaru (sekarang sudah diurutkan)
        $transaksi_terbaru = $penjualan[0];

        // Ambil semua ID transaksi yang sudah lunas dari pelunasan.php
        $id_transaksi_lunas = array_column($pelunasan, 'id_transaksi');

        // Filter penjualan: hanya masukkan yang bukan piutang ATAU piutang yang sudah lunas
        $penjualan_valid = array_filter($penjualan, function($transaksi) use ($id_transaksi_lunas) {
            // Jika metode pembayaran bukan "Piutang", masukkan ke perhitungan
            if ($transaksi['metode_pembayaran'] !== 'Piutang') {
                return true;
            }
            
            // Jika metode pembayaran "Piutang", cek apakah sudah lunas
            return in_array($transaksi['id'], $id_transaksi_lunas);
        });

        // Hitung total penjualan dari transaksi yang valid (tanpa piutang belum lunas)
        $total_penjualan = array_sum(array_column($penjualan_valid, 'total_belanja'));
        
        // Hitung total pembelian
        $total_pembelian = array_sum(array_column($pembelian, 'total_belanja'));
        
        // Filter Transaksi Piutang
        $transaksi_piutang = array_filter($penjualan, function($transaksi) {
            return $transaksi['metode_pembayaran'] === 'Piutang';
        });

        // Hitung total piutang dari semua transaksi piutang
        $total_piutang = array_sum(array_column($transaksi_piutang, 'total_belanja'));
        $total_terlunasi = array_sum(array_column($pelunasan, 'total_piutang'));
        
        // Hitung piutang yang belum lunas (opsional - untuk informasi tambahan)
        $piutang_belum_lunas = array_filter($penjualan, function($transaksi) use ($id_transaksi_lunas) {
            return $transaksi['metode_pembayaran'] === 'Piutang' && 
                   !in_array($transaksi['id'], $id_transaksi_lunas);
        });
        $total_piutang_belum_lunas = array_sum(array_column($piutang_belum_lunas, 'total_belanja'));
        
        // Analisis stok rendah (contoh: stok < 1000)
        $stok_rendah = array_filter($barang, function($item) {
            return $item['stok'] < 500;
        });

        // Pagination untuk stok barang
        $items_per_page = 4;
        $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $total_pages = ceil(count($barang) / $items_per_page);
        $offset = ($current_page - 1) * $items_per_page;
        $barang_paginated = array_slice($barang, $offset, $items_per_page);

        // Fungsi untuk format mata uang dalam jutaan
        function formatCurrencyInJuta($amount) {
            $juta = $amount / 1000000;
            return number_format($juta, 2) . ' Jt';
        }
        ?>

        <!-- Cards Statistik Utama -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Items -->
            <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Items</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1"><?= $total_items ?></p>
                    </div>
                    <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center ml-4">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Stok -->
            <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Stok</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1"><?= number_format($total_stok) ?></p>
                    </div>
                    <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center ml-4">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Nilai Inventory -->
            <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-600">Nilai Inventory</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1 truncate">Rp<?= formatCurrencyInJuta($nilai_inventory) ?></p>
                    </div>
                    <div class="h-12 w-12 bg-purple-100 rounded-lg flex items-center justify-center ml-4 flex-shrink-0">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Potensi Keuntungan -->
            <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-600">Potensi Profit</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1 truncate">Rp<?= formatCurrencyInJuta($potensi_keuntungan) ?></p>
                    </div>
                    <div class="h-12 w-12 bg-yellow-100 rounded-lg flex items-center justify-center ml-4 flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Arus Kas dan Piutang -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Arus Kas -->
            <div class="lg:col-span-2 bg-white/95 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-white/20">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Arus Kas</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="h-3 w-3 bg-green-500 rounded-full mr-3"></div>
                            <span class="text-sm font-medium text-gray-700">Total Penjualan</span>
                        </div>
                        <span class="text-lg font-bold text-green-600">+Rp<?= number_format($total_penjualan) ?></span>
                    </div>
                    <div class="flex justify-between items-center p-4 bg-red-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="h-3 w-3 bg-red-500 rounded-full mr-3"></div>
                            <span class="text-sm font-medium text-gray-700">Total Pembelian</span>
                        </div>
                        <span class="text-lg font-bold text-red-600">-Rp<?= number_format($total_pembelian) ?></span>
                    </div>
                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Net Cash Flow</span>
                            <span class="text-xl font-bold <?= ($total_penjualan - $total_pembelian) > 0 ? 'text-green-600' : 'text-red-600' ?>">
                                <?= ($total_penjualan - $total_pembelian) > 0 ? '+' : '' ?>Rp<?= number_format($total_penjualan - $total_pembelian) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Piutang -->
            <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-white/20">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Status Piutang</h3>
                <div class="space-y-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-900">Rp<?= number_format($total_piutang) ?></p>
                        <p class="text-sm text-gray-600">Total Piutang</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-600">Rp<?= number_format($total_terlunasi) ?></p>
                        <p class="text-sm text-gray-600">Terlunasi</p>
                    </div>
                    <div class="bg-green-100 rounded-lg p-3 text-center">
                        <p class="text-sm text-green-800">
                            <?= $total_piutang > 0 ? round(($total_terlunasi / $total_piutang) * 100, 1) : 0 ?>% Terlunasi
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 3: Inventory dan Alerts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Stok Barang dengan Pagination -->
            <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-white/20">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Status Stok Barang</h3>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600" id="pagination-info">
                            <?= $offset + 1 ?> - <?= min($offset + $items_per_page, count($barang)) ?> dari <?= count($barang) ?>
                        </span>
                    </div>
                </div>
                
                <div class="space-y-4 mb-4" id="stock-content">
                    <?php foreach ($barang_paginated as $item): ?>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-800"><?= $item['nama'] ?></p>
                            <p class="text-sm text-gray-600"><?= $item['supplier'] ?? 'N/A' ?></p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold <?= $item['stok'] < 500 ? 'text-red-600' : 'text-green-600' ?>">
                                <?= number_format($item['stok']) ?>
                            </p>
                            <p class="text-xs text-gray-500"><?= $item['kategori'] ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination Controls -->
                <div class="flex justify-between items-center" id="pagination-controls">
                    <button onclick="changePage(<?= max(1, $current_page - 1) ?>)" 
                            class="<?= $current_page <= 1 ? 'cursor-not-allowed opacity-50' : 'hover:bg-gray-100' ?> 
                                   px-3 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 bg-white transition-colors">
                        < Sebelumnya
                    </button>
                    
                    <div class="flex items-center space-x-2">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <button onclick="changePage(<?= $i ?>)" 
                                    class="<?= $i == $current_page ? 'bg-[#234e9a] text-white' : 'bg-white text-gray-700 hover:bg-gray-100' ?> 
                                           px-3 py-2 rounded-lg border border-gray-300 text-sm font-medium transition-colors">
                                <?= $i ?>
                            </button>
                        <?php endfor; ?>
                    </div>
                    
                    <button onclick="changePage(<?= min($total_pages, $current_page + 1) ?>)" 
                            class="<?= $current_page >= $total_pages ? 'cursor-not-allowed opacity-50' : 'hover:bg-gray-100' ?> 
                                   px-3 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 bg-white transition-colors">
                        Selanjutnya >
                    </button>
                </div>
            </div>

            <!-- Alert & Notifications -->
            <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-white/20">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Notifikasi & Peringatan</h3>
                <div class="space-y-4">
                    <?php if (count($stok_rendah) > 0): ?>
                    <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-red-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            <div>
                                <p class="font-medium text-red-800">Stok Rendah!</p>
                                <p class="text-sm text-red-700">
                                    <?= count($stok_rendah) ?> item memiliki stok di bawah 500 unit
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Transaksi Terbaru -->
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="font-medium text-blue-800">Transaksi Terbaru</p>
                                <p class="text-sm text-blue-700">
                                    Penjualan terakhir: <?= date('d/m/Y', strtotime($transaksi_terbaru['tanggal'])) ?> 
                                    (Rp<?= number_format($transaksi_terbaru['total_belanja']) ?>)
                                </p>
                                <p class="text-xs text-blue-600">
                                    ID: <?= $transaksi_terbaru['id'] ?> | <?= $transaksi_terbaru['metode_pembayaran'] ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Margin Keuntungan -->
                    <?php 
                    $total_margin = 0;
                    $total_harga_beli = 0;
                    foreach ($barang as $item) {
                        $margin = $item['harga_jual'] - $item['harga_beli'];
                        $total_margin += $margin;
                        $total_harga_beli += $item['harga_beli'];
                    }
                    $avg_margin_percent = $total_harga_beli > 0 ? ($total_margin / $total_harga_beli) * 100 : 0;
                    ?>
                    <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-green-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <div>
                                <p class="font-medium text-green-800">Rata-rata Margin</p>
                                <p class="text-sm text-green-700">
                                    <?= round($avg_margin_percent, 1) ?>% margin keuntungan rata-rata
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-white/20">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Ringkasan Aktivitas</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Update terakhir: <?= date('d F Y, H:i') ?>
                    </p>
                </div>
                <div class="mt-4 md:mt-0 flex space-x-4">
                    <div class="text-center">
                        <p class="text-lg font-bold text-gray-900"><?= count($pembelian) ?></p>
                        <p class="text-xs text-gray-600">Transaksi Beli</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-bold text-gray-900"><?= count($penjualan) ?></p>
                        <p class="text-xs text-gray-600">Transaksi Jual</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-bold text-gray-900"><?= count($pelunasan) ?></p>
                        <p class="text-xs text-gray-600">Pelunasan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Data untuk pagination AJAX
const barangData = <?= json_encode($barang) ?>;
const itemsPerPage = <?= $items_per_page ?>;
const totalPages = <?= $total_pages ?>;
let currentPage = <?= $current_page ?>;

function changePage(page) {
    if (page < 1 || page > totalPages || page === currentPage) {
        return;
    }
    
    currentPage = page;
    const offset = (page - 1) * itemsPerPage;
    const endIndex = Math.min(offset + itemsPerPage, barangData.length);
    const pageData = barangData.slice(offset, endIndex);
    
    // Update konten dengan animasi fade
    const stockContent = document.getElementById('stock-content');
    const paginationInfo = document.getElementById('pagination-info');
    const paginationControls = document.getElementById('pagination-controls');
    
    // Fade out
    stockContent.style.opacity = '0.5';
    stockContent.style.transform = 'translateY(10px)';
    
    setTimeout(() => {
        // Update konten stok
        let html = '';
        pageData.forEach(item => {
            const stockColor = item.stok < 500 ? 'text-red-600' : 'text-green-600';
            html += `
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-800">${item.nama}</p>
                        <p class="text-sm text-gray-600">${item.supplier || 'N/A'}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold ${stockColor}">
                            ${new Intl.NumberFormat('id-ID').format(item.stok)}
                        </p>
                        <p class="text-xs text-gray-500">${item.kategori}</p>
                    </div>
                </div>
            `;
        });
        stockContent.innerHTML = html;
        
        // Update pagination info
        paginationInfo.textContent = `${offset + 1} - ${endIndex} dari ${barangData.length}`;
        
        // Update pagination controls
        updatePaginationControls();
        
        // Fade in
        stockContent.style.opacity = '1';
        stockContent.style.transform = 'translateY(0)';
    }, 200);
}

function updatePaginationControls() {
    const paginationControls = document.getElementById('pagination-controls');
    
    let html = `
        <button onclick="changePage(${Math.max(1, currentPage - 1)})" 
                class="${currentPage <= 1 ? 'cursor-not-allowed opacity-50' : 'hover:bg-gray-100'} 
                       px-3 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 bg-white transition-colors">
            < Sebelumnya
        </button>
        
        <div class="flex items-center space-x-2">
    `;
    
    for (let i = 1; i <= totalPages; i++) {
        const activeClass = i === currentPage ? 'bg-[#234e9a] text-white' : 'bg-white text-gray-700 hover:bg-gray-100';
        html += `
            <button onclick="changePage(${i})" 
                    class="${activeClass} px-3 py-2 rounded-lg border border-gray-300 text-sm font-medium transition-colors">
                ${i}
            </button>
        `;
    }
    
    html += `
        </div>
        
        <button onclick="changePage(${Math.min(totalPages, currentPage + 1)})" 
                class="${currentPage >= totalPages ? 'cursor-not-allowed opacity-50' : 'hover:bg-gray-100'} 
                       px-3 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 bg-white transition-colors">
            Selanjutnya >
        </button>
    `;
    
    paginationControls.innerHTML = html;
}

// Tambahkan CSS untuk transisi yang smooth
document.head.insertAdjacentHTML('beforeend', `
    <style>
        #stock-content {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        
        .pagination-button {
            transition: all 0.2s ease;
        }
        
        .pagination-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
    </style>
`);
</script>
@endsection