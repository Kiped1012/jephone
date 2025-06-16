// Variabel global untuk data dan chart
let cashFlowChart = null;
let allTransactions = [];
let filteredTransactions = [];
let currentPage = 1;
let entriesPerPage = 10;
let searchQuery = '';

// Data akan diinjeksi dari blade template
let barang, penjualan, pembelian, pelunasan;

// Fungsi untuk inisialisasi data
function initializeData(barangData, penjualanData, pembelianData, pelunasanData) {
    barang = barangData;
    penjualan = penjualanData;
    pembelian = pembelianData;
    pelunasan = pelunasanData || []; // Default ke array kosong jika tidak ada data
}

// Set timezone offset ke UTC+7 (WIB)
function updateClock() {
    const now = new Date();
    const utc = now.getTime() + (now.getTimezoneOffset() * 60000);
    const wib = new Date(utc + (7 * 60 * 60000));
    const day = String(wib.getDate()).padStart(2, '0');
    const month = String(wib.getMonth() + 1).padStart(2, '0');
    const year = wib.getFullYear();
    const hour = String(wib.getHours()).padStart(2, '0');
    const minute = String(wib.getMinutes()).padStart(2, '0');
    
    document.getElementById('update-time').textContent = `Update: ${day}/${month}/${year} ${hour}:${minute}`;
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

// Process data untuk arus kas
function processTransactions() {
    allTransactions = [];
    
    // Process penjualan (kas masuk)
    penjualan.forEach(transaksi => {
        if (transaksi.metode_pembayaran === 'Tunai') {
            allTransactions.push({
                tanggal: transaksi.tanggal,
                jenis: 'Penjualan',
                id: transaksi.id,
                keterangan: `Penjualan Tunai`,
                kasmasuk: transaksi.total_belanja,
                kaskeluar: 0,
                penanggung_jawab: transaksi.kasir
            });
        }
    });

    // Process pelunasan piutang (kas masuk dari piutang yang sudah dibayar)
    pelunasan.forEach(pelunasanItem => {
        // Cari transaksi penjualan yang sesuai dengan id_transaksi
        const transaksiPenjualan = penjualan.find(p => p.id === pelunasanItem.id_transaksi);
        
        if (transaksiPenjualan && transaksiPenjualan.metode_pembayaran === 'Piutang') {
            // Pastikan ada tanggal pelunasan
            if (pelunasanItem.tanggal_pelunasan) {
                allTransactions.push({
                    tanggal: pelunasanItem.tanggal_pelunasan,
                    jenis: 'Pelunasan Piutang',
                    id: pelunasanItem.id_transaksi,
                    keterangan: `Pelunasan Piutang`,
                    kasmasuk: parseInt(pelunasanItem.dibayar) - parseInt(pelunasanItem.kembalian || 0),
                    kaskeluar: 0,
                    penanggung_jawab: transaksiPenjualan.kasir || 'N/A'
                });
            }
        }
    });

    // Process pembelian (kas keluar)
    pembelian.forEach(transaksi => {
        allTransactions.push({
            tanggal: transaksi.tanggal,
            jenis: 'Pembelian',
            id: transaksi.id_transaksi,
            keterangan: `Pembelian Barang`,
            kasmasuk: 0,
            kaskeluar: parseInt(transaksi.total_belanja),
            penanggung_jawab: transaksi.penanggung_jawab
        });
    });

    // Sort by date
    allTransactions.sort((a, b) => new Date(a.tanggal) - new Date(b.tanggal));
    
    // Calculate running balance
    let runningBalance = 0;
    allTransactions.forEach(transaction => {
        runningBalance += transaction.kasmasuk - transaction.kaskeluar;
        transaction.saldo = runningBalance;
    });
}

// Filter transactions
function filterTransactions(month = '', year = '') {
    return allTransactions.filter(transaction => {
        const transactionDate = new Date(transaction.tanggal);
        const transactionMonth = String(transactionDate.getMonth() + 1).padStart(2, '0');
        const transactionYear = String(transactionDate.getFullYear());
    
        const monthMatch = !month || transactionMonth === month;
        const yearMatch = !year || transactionYear === year;
    
        return monthMatch && yearMatch;
    });
}

// Search transactions
function searchTransactions(transactions, query) {
    if (!query) return transactions;
    
    const lowerQuery = query.toLowerCase();
    return transactions.filter(transaction => {
        return transaction.jenis.toLowerCase().includes(lowerQuery) ||
               transaction.id.toString().toLowerCase().includes(lowerQuery) ||
               transaction.keterangan.toLowerCase().includes(lowerQuery) ||
               new Date(transaction.tanggal).toLocaleDateString('id-ID').includes(lowerQuery);
    });
}

// Get paginated data
function getPaginatedData(transactions) {
    const startIndex = (currentPage - 1) * entriesPerPage;
    const endIndex = startIndex + entriesPerPage;
    return transactions.slice(startIndex, endIndex);
}

// Update pagination info
function updatePaginationInfo(totalEntries, filteredEntries) {
    const startIndex = Math.min((currentPage - 1) * entriesPerPage + 1, filteredEntries);
    const endIndex = Math.min(currentPage * entriesPerPage, filteredEntries);
    
    document.getElementById('showingStart').textContent = filteredEntries > 0 ? startIndex : 0;
    document.getElementById('showingEnd').textContent = endIndex;
    document.getElementById('totalEntries').textContent = filteredEntries;
    
    // Show filtered info if search is active
    if (searchQuery || filteredEntries !== totalEntries) {
        document.getElementById('filteredInfo').classList.remove('hidden');
        document.getElementById('totalEntriesUnfiltered').textContent = totalEntries;
    } else {
        document.getElementById('filteredInfo').classList.add('hidden');
    }
}

// Update pagination controls
function updatePaginationControls(filteredEntries) {
    const totalPages = Math.ceil(filteredEntries / entriesPerPage);
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');
    const pageNumbers = document.getElementById('pageNumbers');
    
    // Update previous/next buttons
    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled = currentPage === totalPages || totalPages === 0;
    
    // Clear and rebuild page numbers
    pageNumbers.innerHTML = '';
    
    if (totalPages > 0) {
        // Calculate page range to show
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, currentPage + 2);
        
        // Adjust range if near beginning or end
        if (endPage - startPage < 4) {
            if (startPage === 1) {
                endPage = Math.min(totalPages, startPage + 4);
            } else if (endPage === totalPages) {
                startPage = Math.max(1, endPage - 4);
            }
        }
        
        // Add first page and ellipsis if needed
        if (startPage > 1) {
            addPageButton(1);
            if (startPage > 2) {
                const ellipsis = document.createElement('span');
                ellipsis.textContent = '...';
                ellipsis.className = 'px-3 py-2 text-sm text-gray-500';
                pageNumbers.appendChild(ellipsis);
            }
        }
        
        // Add page numbers
        for (let i = startPage; i <= endPage; i++) {
            addPageButton(i);
        }
        
        // Add last page and ellipsis if needed
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const ellipsis = document.createElement('span');
                ellipsis.textContent = '...';
                ellipsis.className = 'px-3 py-2 text-sm text-gray-500';
                pageNumbers.appendChild(ellipsis);
            }
            addPageButton(totalPages);
        }
    }
}

// Add page button
function addPageButton(pageNum) {
    const button = document.createElement('button');
    button.textContent = pageNum;
    button.className = `px-3 py-2 text-sm font-medium border rounded-md ${
        pageNum === currentPage 
            ? 'bg-blue-600 text-white border-blue-600' 
            : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50'
    }`;
    button.addEventListener('click', () => {
        currentPage = pageNum;
        updateReport();
    });
    document.getElementById('pageNumbers').appendChild(button);
}

// Update summary cards
function updateSummary(transactions) {
    const totalKasMasuk = transactions.reduce((sum, t) => sum + t.kasmasuk, 0);
    const totalKasKeluar = transactions.reduce((sum, t) => sum + t.kaskeluar, 0);
    const saldoAkhir = totalKasMasuk - totalKasKeluar;

    document.getElementById('totalKasMasuk').textContent = formatCurrency(totalKasMasuk);
    document.getElementById('totalKasKeluar').textContent = formatCurrency(totalKasKeluar);
    document.getElementById('saldoAkhir').textContent = formatCurrency(saldoAkhir);
}

// Update table
function updateTable(transactions, allFilteredTransactions) {
    const tbody = document.getElementById('cashFlowTableBody');
    tbody.innerHTML = '';

    // Get paginated data
    const paginatedTransactions = getPaginatedData(transactions);

    paginatedTransactions.forEach(transaction => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        
        // Tentukan class berdasarkan jenis transaksi
        let jenisClass;
        if (transaction.jenis === 'Penjualan') {
            jenisClass = 'bg-green-100 text-green-800';
        } else if (transaction.jenis === 'Pelunasan Piutang') {
            jenisClass = 'bg-blue-100 text-blue-800';
        } else {
            jenisClass = 'bg-red-100 text-red-800';
        }
        
        const saldoClass = transaction.saldo >= 0 ? 'text-green-600' : 'text-red-600';
        
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${new Date(transaction.tanggal).toLocaleDateString('id-ID')}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-medium rounded-full ${jenisClass}">
                    ${transaction.jenis}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                ${transaction.id}
            </td>
            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                ${transaction.keterangan}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600">
                ${transaction.kasmasuk > 0 ? formatCurrency(transaction.kasmasuk) : '-'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600">
                ${transaction.kaskeluar > 0 ? formatCurrency(transaction.kaskeluar) : '-'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium ${saldoClass}">
                ${formatCurrency(transaction.saldo)}
            </td>
        `;
        tbody.appendChild(row);
    });

    // Update pagination info and controls
    updatePaginationInfo(allFilteredTransactions.length, transactions.length);
    updatePaginationControls(transactions.length);
}

// Update chart
function updateChart(transactions) {
    const ctx = document.getElementById('cashFlowChart').getContext('2d');
    
    // Group by date
    const dailyData = {};
    transactions.forEach(transaction => {
        const date = transaction.tanggal;
        if (!dailyData[date]) {
            dailyData[date] = { kasmasuk: 0, kaskeluar: 0, saldo: 0 };
        }
        dailyData[date].kasmasuk += transaction.kasmasuk;
        dailyData[date].kaskeluar += transaction.kaskeluar;
    });

    // Calculate running balance for chart
    let runningBalance = 0;
    Object.keys(dailyData).sort().forEach(date => {
        runningBalance += dailyData[date].kasmasuk - dailyData[date].kaskeluar;
        dailyData[date].saldo = runningBalance;
    });

    const labels = Object.keys(dailyData).sort().map(date => 
        new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit' })
    );
    const kasMasukData = Object.keys(dailyData).sort().map(date => dailyData[date].kasmasuk);
    const kasKeluarData = Object.keys(dailyData).sort().map(date => dailyData[date].kaskeluar);
    const saldoData = Object.keys(dailyData).sort().map(date => dailyData[date].saldo);

    if (cashFlowChart) {
        cashFlowChart.destroy();
    }

    cashFlowChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Kas Masuk',
                    data: kasMasukData,
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    fill: false,
                    tension: 0.1
                },
                {
                    label: 'Kas Keluar',
                    data: kasKeluarData,
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    fill: false,
                    tension: 0.1
                },
                {
                    label: 'Saldo',
                    data: saldoData,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: false,
                    tension: 0.1,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Tanggal'
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Kas Masuk/Keluar (IDR)'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Saldo (IDR)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
}

// Main update function
function updateReport() {
    const selectedMonth = document.getElementById('filterMonth').value;
    const selectedYear = document.getElementById('filterYear').value;
    
    // Apply date filters
    filteredTransactions = filterTransactions(selectedMonth, selectedYear);
    
    // Apply search filter
    const searchedTransactions = searchTransactions(filteredTransactions, searchQuery);
    
    // Update summary (based on all filtered transactions, not paginated ones)
    updateSummary(filteredTransactions);
    
    // Update table with pagination
    updateTable(searchedTransactions, filteredTransactions);
    
    // Update chart (based on all filtered transactions)
    updateChart(filteredTransactions);
}

// Handle entries per page change
function handleEntriesPerPageChange() {
    entriesPerPage = parseInt(document.getElementById('entriesPerPage').value);
    currentPage = 1; // Reset to first page
    updateReport();
}

// Handle search
function handleSearch() {
    searchQuery = document.getElementById('searchTable').value;
    currentPage = 1; // Reset to first page
    updateReport();
}

// Handle pagination navigation
function handlePrevPage() {
    if (currentPage > 1) {
        currentPage--;
        updateReport();
    }
}

function handleNextPage() {
    const searchedTransactions = searchTransactions(filteredTransactions, searchQuery);
    const totalPages = Math.ceil(searchedTransactions.length / entriesPerPage);
    if (currentPage < totalPages) {
        currentPage++;
        updateReport();
    }
}

// Initialize application
function initApp() {
    // Event listeners for filters
    document.getElementById('applyFilter').addEventListener('click', updateReport);
    
    // Event listeners for table controls
    document.getElementById('entriesPerPage').addEventListener('change', handleEntriesPerPageChange);
    document.getElementById('searchTable').addEventListener('input', handleSearch);
    
    // Event listeners for pagination
    document.getElementById('prevPage').addEventListener('click', handlePrevPage);
    document.getElementById('nextPage').addEventListener('click', handleNextPage);
    
    // Process data dan update report
    processTransactions();
    updateReport();
    
    // Update clock
    updateClock();
    setInterval(updateClock, 1000);
}

// Export functions untuk penggunaan global
window.LaporanArusKas = {
    initializeData,
    initApp
};