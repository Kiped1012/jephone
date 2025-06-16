// Laporan Stok JavaScript - DOM Based with Real Data Charts
class LaporanStok {
    constructor() {
        this.data = {
            barang: [],
            penjualan: [],
            pembelian: []
        };
        this.filteredData = [];
        
        // Pagination and table properties
        this.currentPage = 1;
        this.entriesPerPage = 10;
        this.searchQuery = '';
        this.displayedData = []; // Data yang akan ditampilkan setelah filter dan search
        
        this.init();
    }

    async init() {
        try {
            await this.loadData();
            this.setupEventListeners();
            this.initializeCharts();
            this.renderAll();
        } catch (error) {
            console.error('Error initializing:', error);
            this.showError('Gagal memuat data');
        }
    }

    async loadData() {
        try {
            // Ambil data langsung dari variabel global yang di-embed di Blade
            this.data.barang = barang;
            this.data.penjualan = penjualan;
            this.data.pembelian = pembelian;

            // Proses data untuk laporan
            this.processStockData();
        } catch (error) {
            // Fallback data untuk development
            this.loadMockData();
        }
    }

    processStockData() {
        const today = new Date();
        const filterPeriode = document.getElementById('filterPeriode')?.value || 'all';
        
        // Tentukan tanggal filter berdasarkan periode yang dipilih
        let filterDate = null;
        
        if (filterPeriode !== 'all') {
            let filterDays;
            switch (filterPeriode) {
                case '7':
                    filterDays = 7;
                    break;
                case '30':
                    filterDays = 30;
                    break;
                case '90':
                    filterDays = 90;
                    break;
                default:
                    filterDays = null;
            }
            
            if (filterDays) {
                filterDate = new Date(today.getTime() - (filterDays * 24 * 60 * 60 * 1000));
            }
        }

        // Hitung pergerakan stok untuk setiap barang
        this.filteredData = this.data.barang.map(barang => {
            
            // HITUNG stok masuk dari pembelian.items
            const stokMasuk = this.data.pembelian
                .filter(p => {
                    if (!filterDate) return true; // Jika 'all', ambil semua data
                    
                    // Standardize date format - handle both formats
                    let tanggalPembelian;
                    if (p.tanggal.includes('-')) {
                        // Format YYYY-MM-DD
                        tanggalPembelian = new Date(p.tanggal);
                    } else {
                        // Format lainnya
                        tanggalPembelian = new Date(p.tanggal);
                    }
                    return tanggalPembelian >= filterDate;
                })
                .reduce((sum, pembelian) => {
                    const item = pembelian.items.find(item => item.nama === barang.nama);
                    return sum + (item ? parseInt(item.jumlah) || 0 : 0);
                }, 0);

            // Hitung stok keluar dari penjualan
            const stokKeluar = this.data.penjualan
                .filter(p => {
                    if (!filterDate) return true; // Jika 'all', ambil semua data
                    
                    const tanggalPenjualan = new Date(p.tanggal);
                    return tanggalPenjualan >= filterDate;
                })
                .reduce((sum, penjualan) => {
                    const item = penjualan.items.find(item => item.nama === barang.nama);
                    return sum + (item ? parseInt(item.jumlah) || 0 : 0);
                }, 0);

            // Hitung stok awal - pastikan tidak ada perhitungan yang menghasilkan nilai aneh
            const stokAkhir = parseInt(barang.stok) || 0;
            const stokAwal = stokAkhir + stokKeluar - stokMasuk;
            
            const nilaiStok = stokAkhir * (parseFloat(barang.harga_beli) || 0);

            // Tentukan status stok
            let status = 'normal';
            if (stokAkhir < 500) status = 'rendah';
            else if (stokAkhir > 700) status = 'tinggi';

            return {
                ...barang,
                stok_awal: Math.max(0, stokAwal), // Pastikan tidak negatif
                stok_masuk: stokMasuk,
                stok_keluar: stokKeluar,
                stok_akhir: stokAkhir,
                nilai_stok: nilaiStok,
                status: status
            };
        });
    }

    setupEventListeners() {
        // Export Excel button
        const excelBtn = document.querySelector('button[onclick="exportToExcel()"]');
        if (excelBtn) {
            excelBtn.onclick = () => this.exportToExcel();
        }

        // Print button
        const printBtn = document.querySelector('button[onclick="printReport()"]');
        if (printBtn) {
            printBtn.onclick = () => this.printReport();
        }

        // Chart type radio buttons
        const chartRadios = document.querySelectorAll('input[name="chartType"]');
        chartRadios.forEach(radio => {
            radio.addEventListener('change', () => this.updateStockMovementChart());
        });

        // Filter untuk Chart Distribusi Kategori (hanya mempengaruhi donut chart)
        const filterKategori = document.getElementById('filterKategori');
        if (filterKategori) {
            filterKategori.addEventListener('change', () => this.updateStockDistributionChart());
        }

        // Filter Periode untuk Pergerakan Stok (mempengaruhi chart line/bar dan summary cards)
        const filterPeriode = document.getElementById('filterPeriode');
        if (filterPeriode) {
            filterPeriode.addEventListener('change', () => {
                this.processStockData(); // Reprocess data dengan periode baru
                this.renderSummaryCards();
                this.renderTopSellingItems();
                this.updateStockMovementChart();
                this.renderStockTable(); // Update table juga
            });
        }

        // Filter Status Stok untuk Tabel Detail (hanya mempengaruhi tabel dan low stock items)
        const filterStok = document.getElementById('filterStok');
        if (filterStok) {
            filterStok.addEventListener('change', () => {
                this.currentPage = 1; // Reset ke halaman pertama
                this.renderStockTable();
                this.renderLowStockItems();
            });
        }

        // Table controls
        this.setupTableControls();
    }

    setupTableControls() {
        // Show entries per page
        const entriesPerPageSelect = document.getElementById('entriesPerPage');
        if (entriesPerPageSelect) {
            entriesPerPageSelect.addEventListener('change', (e) => {
                this.entriesPerPage = e.target.value === 'all' ? 'all' : parseInt(e.target.value);
                this.currentPage = 1; // Reset ke halaman pertama
                this.renderStockTable();
            });
        }

        // Search input
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.searchQuery = e.target.value.toLowerCase();
                this.currentPage = 1; // Reset ke halaman pertama
                this.renderStockTable();
            });
        }

        // Pagination controls
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                if (this.currentPage > 1) {
                    this.currentPage--;
                    this.renderStockTable();
                }
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                const totalPages = this.getTotalPages();
                if (this.currentPage < totalPages) {
                    this.currentPage++;
                    this.renderStockTable();
                }
            });
        }
    }

    getFilteredDataForTable() {
        const statusFilter = document.getElementById('filterStok')?.value || '';
        
        let filteredData = this.filteredData.filter(item => {
            // Filter berdasarkan status
            if (statusFilter && item.status !== statusFilter) return false;
            
            // Filter berdasarkan search query
            if (this.searchQuery) {
                const searchLower = this.searchQuery.toLowerCase();
                return item.nama.toLowerCase().includes(searchLower) || 
                       item.id_brg.toLowerCase().includes(searchLower) ||
                       item.kategori.toLowerCase().includes(searchLower);
            }
            
            return true;
        });

        return filteredData;
    }

    getPaginatedData() {
        const filteredData = this.getFilteredDataForTable();
        
        if (this.entriesPerPage === 'all') {
            return filteredData;
        }

        const startIndex = (this.currentPage - 1) * this.entriesPerPage;
        const endIndex = startIndex + this.entriesPerPage;
        
        return filteredData.slice(startIndex, endIndex);
    }

    getTotalPages() {
        if (this.entriesPerPage === 'all') return 1;
        
        const totalItems = this.getFilteredDataForTable().length;
        return Math.ceil(totalItems / this.entriesPerPage);
    }

    renderAll() {
        this.renderSummaryCards();
        this.renderStockTable();
        this.renderTopSellingItems();
        this.renderLowStockItems();
        this.updateCharts();
    }

    renderSummaryCards() {
        const totalBarang = this.filteredData.length;
        const totalStokMasuk = this.filteredData.reduce((sum, item) => sum + item.stok_masuk, 0);
        const totalStokKeluar = this.filteredData.reduce((sum, item) => sum + item.stok_keluar, 0);
        const nilaiTotalStok = this.filteredData.reduce((sum, item) => sum + item.nilai_stok, 0);

        this.updateElement('totalBarang', totalBarang.toLocaleString());
        this.updateElement('totalStokMasuk', totalStokMasuk.toLocaleString());
        this.updateElement('totalStokKeluar', totalStokKeluar.toLocaleString());
        this.updateElement('nilaiTotalStok', `Rp ${nilaiTotalStok.toLocaleString()}`);
    }

    renderStockTable() {
        const tbody = document.getElementById('stockTableBody');
        if (!tbody) return;

        tbody.innerHTML = '';

        // Get paginated data
        const paginatedData = this.getPaginatedData();
        const totalFilteredData = this.getFilteredDataForTable();

        // Render table rows
        paginatedData.forEach(item => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';
            
            row.innerHTML = `
                <td class="px-4 py-3 text-sm font-medium text-gray-900">${item.id_brg}</td>
                <td class="px-4 py-3 text-sm text-gray-900">${item.nama}</td>
                <td class="px-4 py-3 text-sm text-gray-500">${item.kategori}</td>
                <td class="px-4 py-3 text-sm text-gray-900">${item.stok_awal.toLocaleString()}</td>
                <td class="px-4 py-3 text-sm text-green-600 font-medium">+${item.stok_masuk.toLocaleString()}</td>
                <td class="px-4 py-3 text-sm text-red-600 font-medium">-${item.stok_keluar.toLocaleString()}</td>
                <td class="px-4 py-3 text-sm font-bold text-gray-900">${item.stok_akhir.toLocaleString()}</td>
                <td class="px-4 py-3 text-sm text-gray-900">Rp ${item.nilai_stok.toLocaleString()}</td>
                <td class="px-4 py-3">
                    <span class="status-badge status-${item.status}">
                        ${item.status === 'rendah' ? 'Rendah' : 
                          item.status === 'tinggi' ? 'Tinggi' : 'Normal'}
                    </span>
                </td>
            `;
            
            tbody.appendChild(row);
        });

        // Update table info and pagination
        this.updateTableInfo(totalFilteredData.length, paginatedData.length);
        this.updatePaginationControls();
    }

    updateTableInfo(totalEntries, displayedEntries) {
        const tableInfo = document.getElementById('tableInfo');
        if (!tableInfo) return;

        if (this.entriesPerPage === 'all') {
            tableInfo.textContent = `Showing ${totalEntries} entries`;
        } else {
            const startEntry = totalEntries === 0 ? 0 : (this.currentPage - 1) * this.entriesPerPage + 1;
            const endEntry = Math.min(startEntry + displayedEntries - 1, totalEntries);
            tableInfo.textContent = `Showing ${startEntry} to ${endEntry} of ${totalEntries} entries`;
        }
    }

    updatePaginationControls() {
        const totalPages = this.getTotalPages();
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const pageNumbers = document.getElementById('pageNumbers');

        // Update previous button
        if (prevBtn) {
            prevBtn.disabled = this.currentPage <= 1;
        }

        // Update next button
        if (nextBtn) {
            nextBtn.disabled = this.currentPage >= totalPages;
        }

        // Update page numbers
        if (pageNumbers) {
            pageNumbers.innerHTML = '';
            
            if (totalPages <= 1) return;

            // Calculate page range to show
            let startPage = Math.max(1, this.currentPage - 2);
            let endPage = Math.min(totalPages, this.currentPage + 2);

            // Adjust range if we're near the beginning or end
            if (endPage - startPage < 4) {
                if (startPage === 1) {
                    endPage = Math.min(totalPages, startPage + 4);
                } else if (endPage === totalPages) {
                    startPage = Math.max(1, endPage - 4);
                }
            }

            // Add first page and ellipsis if needed
            if (startPage > 1) {
                this.addPageButton(pageNumbers, 1);
                if (startPage > 2) {
                    this.addEllipsis(pageNumbers);
                }
            }

            // Add page numbers
            for (let i = startPage; i <= endPage; i++) {
                this.addPageButton(pageNumbers, i);
            }

            // Add ellipsis and last page if needed
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    this.addEllipsis(pageNumbers);
                }
                this.addPageButton(pageNumbers, totalPages);
            }
        }
    }

    addPageButton(container, pageNumber) {
        const button = document.createElement('button');
        button.className = `page-btn ${pageNumber === this.currentPage ? 'active' : ''}`;
        button.textContent = pageNumber;
        button.addEventListener('click', () => {
            this.currentPage = pageNumber;
            this.renderStockTable();
        });
        container.appendChild(button);
    }

    addEllipsis(container) {
        const ellipsis = document.createElement('span');
        ellipsis.className = 'px-2 py-2 text-gray-500';
        ellipsis.textContent = '...';
        container.appendChild(ellipsis);
    }

    renderTopSellingItems() {
        const container = document.getElementById('topSellingItems');
        if (!container) return;

        // Dapatkan periode filter untuk menghitung penjualan
        const filterPeriode = document.getElementById('filterPeriode')?.value || 'all';
        const today = new Date();
        let filterDate = null;
        
        if (filterPeriode !== 'all') {
            let filterDays;
            switch (filterPeriode) {
                case '7':
                    filterDays = 7;
                    break;
                case '30':
                    filterDays = 30;
                    break;
                case '90':
                    filterDays = 90;
                    break;
                default:
                    filterDays = null;
            }
            
            if (filterDays) {
                filterDate = new Date(today.getTime() - (filterDays * 24 * 60 * 60 * 1000));
            }
        }

        // Hitung penjualan per item berdasarkan periode
        const salesData = {};
        this.data.penjualan
            .filter(penjualan => {
                if (!filterDate) return true;
                const tanggalPenjualan = new Date(penjualan.tanggal);
                return tanggalPenjualan >= filterDate;
            })
            .forEach(penjualan => {
                penjualan.items.forEach(item => {
                    if (!salesData[item.nama]) {
                        salesData[item.nama] = { nama: item.nama, jumlah: 0, total: 0 };
                    }
                    salesData[item.nama].jumlah += item.jumlah;
                    salesData[item.nama].total += item.total;
                });
            });

        const topItems = Object.values(salesData)
            .sort((a, b) => b.jumlah - a.jumlah)
            .slice(0, 5);

        container.innerHTML = topItems.map((item, index) => `
            <div class="item-card">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="font-medium text-gray-900">#${index + 1} ${item.nama}</div>
                        <div class="text-sm text-gray-500">Terjual: ${item.jumlah} unit</div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-green-600">Rp ${item.total.toLocaleString()}</div>
                    </div>
                </div>
            </div>
        `).join('') || '<p class="text-gray-500 text-center py-4">Belum ada data penjualan</p>';
    }

    renderLowStockItems() {
        const container = document.getElementById('lowStockItems');
        if (!container) return;

        // Gunakan data yang difilter berdasarkan status stok
        const tableData = this.getFilteredDataForTable();
        
        const lowStockItems = tableData
            .filter(item => item.status === 'rendah')
            .sort((a, b) => a.stok_akhir - b.stok_akhir)
            .slice(0, 5);

        container.innerHTML = lowStockItems.map(item => `
            <div class="item-card border-l-4 border-red-500">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="font-medium text-gray-900">${item.nama}</div>
                        <div class="text-sm text-gray-500">${item.kategori}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-red-600">${item.stok_akhir} unit</div>
                        <div class="text-xs text-gray-500">Minimum: 500</div>
                    </div>
                </div>
            </div>
        `).join('') || '<p class="text-gray-500 text-center py-4">Semua stok dalam kondisi baik</p>';
    }

    initializeCharts() {
        // Initialize Chart.js charts
        this.stockMovementChart = null;
        this.stockDistributionChart = null;
        this.updateCharts();
    }

    updateCharts() {
        this.updateStockMovementChart();
        this.updateStockDistributionChart();
    }

    // Generate real movement data from transactions based on selected period
    generateRealMovementData() {
        const filterPeriode = document.getElementById('filterPeriode')?.value || 'all';
        const today = new Date();
        let days = 7; // Default untuk chart
        
        // Tentukan jumlah hari untuk chart berdasarkan periode
        switch (filterPeriode) {
            case '7':
                days = 7;
                break;
            case '30':
                days = 30;
                break;
            case '90':
                days = 90;
                break;
            case 'all':
                // Untuk 'all', gunakan 30 hari terakhir untuk chart agar tidak terlalu padat
                days = 30;
                break;
            default:
                days = 7;
        }

        const labels = [];
        const dailyData = {};

        // Generate days based on selected period
        for (let i = days - 1; i >= 0; i--) {
            const date = new Date(today.getTime() - (i * 24 * 60 * 60 * 1000));
            const dateStr = date.toISOString().split('T')[0]; // YYYY-MM-DD format
            const displayDate = date.toLocaleDateString('id-ID', { month: 'short', day: 'numeric' });
            
            labels.push(displayDate);
            dailyData[dateStr] = { stokMasuk: 0, stokKeluar: 0 };
        }

        // Calculate stock in (pembelian) for each day
        this.data.pembelian.forEach(pembelian => {
            const tanggal = pembelian.tanggal; // Already in YYYY-MM-DD format
            if (dailyData[tanggal]) {
                pembelian.items.forEach(item => {
                    dailyData[tanggal].stokMasuk += parseInt(item.jumlah) || 0;
                });
            }
        });

        // Calculate stock out (penjualan) for each day
        this.data.penjualan.forEach(penjualan => {
            const tanggal = penjualan.tanggal; // Already in YYYY-MM-DD format
            if (dailyData[tanggal]) {
                penjualan.items.forEach(item => {
                    dailyData[tanggal].stokKeluar += parseInt(item.jumlah) || 0;
                });
            }
        });

        // Extract arrays for chart
        const stokMasukData = [];
        const stokKeluarData = [];
        
        Object.keys(dailyData).sort().forEach(date => {
            stokMasukData.push(dailyData[date].stokMasuk);
            stokKeluarData.push(dailyData[date].stokKeluar);
        });

        return {
            labels: labels,
            stokMasukData,
            stokKeluarData
        };
    }

    updateStockMovementChart() {
        const canvas = document.getElementById('stockMovementChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        
        // Destroy existing chart
        if (this.stockMovementChart) {
            this.stockMovementChart.destroy();
        }

        // Get real data instead of mock data
        const realData = this.generateRealMovementData();

        const chartType = document.querySelector('input[name="chartType"]:checked')?.id || 'line';

        this.stockMovementChart = new Chart(ctx, {
            type: chartType,
            data: {
                labels: realData.labels,
                datasets: [{
                    label: 'Stok Masuk',
                    data: realData.stokMasukData,
                    borderColor: '#10b981',
                    backgroundColor: chartType === 'bar' ? '#10b981' : 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Stok Keluar',
                    data: realData.stokKeluarData,
                    borderColor: '#ef4444',
                    backgroundColor: chartType === 'bar' ? '#ef4444' : 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + ' unit';
                            }
                        }
                    }
                }
            }
        });
    }

    updateStockDistributionChart() {
        const canvas = document.getElementById('stockDistributionChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        
        // Destroy existing chart
        if (this.stockDistributionChart) {
            this.stockDistributionChart.destroy();
        }

        // Filter berdasarkan kategori yang dipilih
        const filterKategori = document.getElementById('filterKategori')?.value || '';
        
        let dataForChart = this.filteredData;
        if (filterKategori) {
            dataForChart = this.filteredData.filter(item => item.kategori === filterKategori);
        }

        // Calculate stock distribution by category from filtered data
        const categoryData = {};
        dataForChart.forEach(item => {
            if (!categoryData[item.kategori]) {
                categoryData[item.kategori] = 0;
            }
            categoryData[item.kategori] += item.stok_akhir;
        });

        const labels = Object.keys(categoryData);
        const data = Object.values(categoryData);
        const colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'];

        this.stockDistributionChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.slice(0, labels.length),
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + context.parsed + ' unit (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    updateElement(id, value) {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = value;
        }
    }

    exportToExcel() {
        // Simple CSV export menggunakan data yang difilter
        let csv = 'Kode,Nama Barang,Kategori,Stok Awal,Stok Masuk,Stok Keluar,Stok Akhir,Nilai Stok,Status\n';
        
        const tableData = this.getFilteredDataForTable();
        tableData.forEach(item => {
            csv += `${item.id_brg},${item.nama},${item.kategori},${item.stok_awal},${item.stok_masuk},${item.stok_keluar},${item.stok_akhir},${item.nilai_stok},${item.status}\n`;
        });

        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `laporan_stok_${new Date().toISOString().split('T')[0]}.csv`;
        a.click();
        window.URL.revokeObjectURL(url);
    }

    printReport() {
        window.print();
    }

    showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4';
        errorDiv.textContent = message;
        
        const container = document.querySelector('.bg-white.rounded-b-xl');
        if (container) {
            container.insertBefore(errorDiv, container.firstChild);
        }
    }

    loadMockData() {
        // Fallback mock data if real data fails to load
        console.warn('Loading mock data as fallback');
        // You can implement mock data here if needed
    }
}

// Global functions untuk onclick handlers
window.exportToExcel = function() {
    if (window.laporanStok) {
        window.laporanStok.exportToExcel();
    }
};

window.printReport = function() {
    if (window.laporanStok) {
        window.laporanStok.printReport();
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Load Chart.js if not already loaded
    if (typeof Chart === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js';
        script.onload = function() {
            window.laporanStok = new LaporanStok();
        };
        document.head.appendChild(script);
    } else {
        window.laporanStok = new LaporanStok();
    }
});
