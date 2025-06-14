// State management
let currentPage = 1;
let entriesPerPage = 10;
let filteredData = [];
let allData = [];
let pelunasanMap = {};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Get data from PHP
    allData = window.piutangData || [];
    const pelunasanData = window.pelunasanData || [];
    
    // Create pelunasan map for quick lookup
    pelunasanMap = {};
    pelunasanData.forEach(item => {
        pelunasanMap[item.id_transaksi] = item;
    });
    
    filteredData = [...allData];
    
    // Setup event listeners
    setupEventListeners();
    
    // Initial render
    renderTable();
});

function setupEventListeners() {
    // Entries per page selector
    const entriesSelect = document.getElementById('entries-select');
    entriesSelect.addEventListener('change', function() {
        entriesPerPage = parseInt(this.value);
        currentPage = 1;
        renderTable();
    });

    // Search input
    const searchInput = document.getElementById('search-input');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        filterData(searchTerm);
        currentPage = 1;
        renderTable();
    });

    // Pagination buttons
    document.getElementById('prev-btn').addEventListener('click', function() {
        if (currentPage > 1) {
            currentPage--;
            renderTable();
        }
    });

    document.getElementById('next-btn').addEventListener('click', function() {
        const totalPages = Math.ceil(filteredData.length / entriesPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            renderTable();
        }
    });
}

function filterData(searchTerm) {
    if (!searchTerm) {
        filteredData = [...allData];
        return;
    }

    filteredData = allData.filter(transaksi => {
        const isLunas = pelunasanMap[transaksi.id] ? true : false;
        const status = isLunas ? 'lunas' : 'belum lunas';
        
        return (
            transaksi.id.toLowerCase().includes(searchTerm) ||
            transaksi.email_pelanggan.toLowerCase().includes(searchTerm) ||
            transaksi.tanggal.toLowerCase().includes(searchTerm) ||
            transaksi.jatuh_tempo.toLowerCase().includes(searchTerm) ||
            transaksi.total_belanja.toString().includes(searchTerm) ||
            status.includes(searchTerm)
        );
    });
}

function renderTable() {
    const tbody = document.getElementById('table-body');
    const noDataDiv = document.getElementById('no-data');
    
    // Calculate pagination
    const totalEntries = filteredData.length;
    const totalPages = Math.ceil(totalEntries / entriesPerPage);
    const startIndex = (currentPage - 1) * entriesPerPage;
    const endIndex = Math.min(startIndex + entriesPerPage, totalEntries);
    const currentData = filteredData.slice(startIndex, endIndex);

    // Clear table body
    tbody.innerHTML = '';

    if (currentData.length === 0) {
        noDataDiv.classList.remove('hidden');
        tbody.style.display = 'none';
    } else {
        noDataDiv.classList.add('hidden');
        tbody.style.display = '';

        // Populate table rows
        currentData.forEach(transaksi => {
            const isLunas = pelunasanMap[transaksi.id] ? true : false;
            const status = isLunas ? 'Lunas' : 'Belum Lunas';
            const statusColor = isLunas ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100';
            
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';
            row.innerHTML = `
                <td class="px-4 py-2">${transaksi.id}</td>
                <td class="px-4 py-2">${transaksi.email_pelanggan}</td>
                <td class="px-4 py-2">${transaksi.tanggal}</td>
                <td class="px-4 py-2">${transaksi.jatuh_tempo}</td>
                <td class="px-4 py-2">Rp${formatNumber(transaksi.total_belanja)}</td>
                <td class="px-4 py-2">
                    <span class="px-2 py-1 rounded-full text-xs font-medium ${statusColor}">
                        ${status}
                    </span>
                </td>
                <td class="px-4 py-2 text-center">
                    <button onclick="showDetail(${JSON.stringify(transaksi).replace(/"/g, '&quot;')})"
                            class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-xs">
                        Detail
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    // Update pagination
    updatePagination(totalPages, startIndex, endIndex, totalEntries);
}

function updatePagination(totalPages, startIndex, endIndex, totalEntries) {
    // Update info text
    const infoText = document.getElementById('info-text');
    if (totalEntries === 0) {
        infoText.textContent = 'Showing 0 to 0 of 0 entries';
    } else {
        infoText.textContent = `Showing ${startIndex + 1} to ${endIndex} of ${totalEntries} entries`;
    }

    // Update pagination buttons
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    
    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled = currentPage === totalPages || totalPages === 0;

    // Update page numbers
    updatePageNumbers(totalPages);
}

function updatePageNumbers(totalPages) {
    const paginationNumbers = document.getElementById('pagination-numbers');
    paginationNumbers.innerHTML = '';

    if (totalPages <= 1) return;

    // Calculate which page numbers to show
    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, currentPage + 2);

    // Adjust if we're near the beginning or end
    if (currentPage <= 3) {
        endPage = Math.min(5, totalPages);
    }
    if (currentPage >= totalPages - 2) {
        startPage = Math.max(1, totalPages - 4);
    }

    // Add first page and ellipsis if needed
    if (startPage > 1) {
        addPageButton(1, paginationNumbers);
        if (startPage > 2) {
            addEllipsis(paginationNumbers);
        }
    }

    // Add page numbers
    for (let i = startPage; i <= endPage; i++) {
        addPageButton(i, paginationNumbers);
    }

    // Add ellipsis and last page if needed
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            addEllipsis(paginationNumbers);
        }
        addPageButton(totalPages, paginationNumbers);
    }
}

function addPageButton(pageNum, container) {
    const button = document.createElement('button');
    button.textContent = pageNum;
    button.className = `px-3 py-2 text-sm border rounded ${
        pageNum === currentPage 
            ? 'bg-blue-500 text-white border-blue-500' 
            : 'border-gray-300 hover:bg-gray-50'
    }`;
    
    button.addEventListener('click', function() {
        currentPage = pageNum;
        renderTable();
    });
    
    container.appendChild(button);
}

function addEllipsis(container) {
    const span = document.createElement('span');
    span.textContent = '...';
    span.className = 'px-2 py-2 text-sm text-gray-500';
    container.appendChild(span);
}

function formatNumber(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}

// Modal functions
window.showDetail = function(data) {
    // Set info umum
    document.getElementById('detail-id').textContent = data.id;
    document.getElementById('detail-email').textContent = data.email_pelanggan;
    document.getElementById('detail-tanggal').textContent = data.tanggal;
    document.getElementById('detail-jatuh-tempo').textContent = data.jatuh_tempo;

    // Pelunasan
    const pelunasan = pelunasanMap[data.id];
    const tanggalPelunasan = pelunasan ? pelunasan.tanggal_pelunasan : '-';
    document.getElementById('detail-pelunasan').textContent = tanggalPelunasan;

    // Status Waktu
    let statusWaktu = 'Belum Lunas';
    let statusClass = 'text-gray-600 font-semibold';
    
    if (tanggalPelunasan !== '-') {
        const jatuhTempo = new Date(data.jatuh_tempo);
        const pelunasanDate = new Date(tanggalPelunasan);
        
        if (pelunasanDate <= jatuhTempo) {
            statusWaktu = 'Tepat Waktu';
            statusClass = 'text-green-600 font-semibold';
        } else {
            statusWaktu = 'Terlambat';
            statusClass = 'text-red-600 font-semibold';
        }
    }
    
    document.getElementById('detail-status-waktu').textContent = statusWaktu;
    document.getElementById('detail-status-waktu').className = statusClass;

    // Barang dalam transaksi
    const barang = data.items || [];
    const tbody = document.getElementById('detail-content');
    tbody.innerHTML = '';
    let total = 0;

    barang.forEach((item, index) => {
        const rowTotal = item.jumlah * item.harga;
        total += rowTotal;

        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-3 py-2 border text-center">${index + 1}</td>
            <td class="px-3 py-2 border">${item.nama}</td>
            <td class="px-3 py-2 border text-center">${item.jumlah}</td>
            <td class="px-3 py-2 border text-right">Rp${formatNumber(item.harga)}</td>
            <td class="px-3 py-2 border text-right">Rp${formatNumber(rowTotal)}</td>
        `;
        tbody.appendChild(row);
    });

    document.getElementById('detail-total').textContent = `Rp${formatNumber(total)}`;
    document.getElementById('modal-detail').classList.remove('hidden');
};

window.closeModal = function() {
    document.getElementById('modal-detail').classList.add('hidden');
};