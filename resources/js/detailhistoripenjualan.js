// State management
let currentPage = 1;
let entriesPerPage = 10;
let filteredData = [];
let allData = [];

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Get data from PHP
    allData = window.penjualanData || [];
    allData.sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal));
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
        return (
            transaksi.id.toLowerCase().includes(searchTerm) ||
            transaksi.tanggal.toLowerCase().includes(searchTerm) ||
            transaksi.kasir.toLowerCase().includes(searchTerm) ||
            transaksi.metode_pembayaran.toLowerCase().includes(searchTerm) ||
            transaksi.total_belanja.toString().includes(searchTerm)
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
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';
            row.innerHTML = `
                <td class="px-4 py-2">${transaksi.id}</td>
                <td class="px-4 py-2">${transaksi.tanggal}</td>
                <td class="px-4 py-2">${transaksi.kasir}</td>
                <td class="px-4 py-2">Rp${formatNumber(transaksi.total_belanja)}</td>
                <td class="px-4 py-2">${transaksi.metode_pembayaran}</td>
                <td class="px-4 py-2 text-center">
                    <button onclick="showDetail(${JSON.stringify(transaksi.items).replace(/"/g, '&quot;')})"
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
window.showDetail = function (items) {
    const tbody = document.getElementById('detail-content');
    const totalCell = document.getElementById('detail-total');
    tbody.innerHTML = '';

    let total = 0;

    items.forEach((item, index) => {
        const subtotal = item.harga * item.jumlah;
        total += subtotal;

        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-3 py-2 border text-center">${index + 1}</td>
            <td class="px-3 py-2 border">${item.nama}</td>
            <td class="px-3 py-2 border text-center">${item.jumlah}</td>
            <td class="px-3 py-2 border text-right">Rp${formatNumber(item.harga)}</td>
            <td class="px-3 py-2 border text-right">Rp${formatNumber(subtotal)}</td>
        `;
        tbody.appendChild(row);
    });

    totalCell.innerText = `Rp${formatNumber(total)}`;
    document.getElementById('modal-detail').classList.remove('hidden');
    document.getElementById('modal-detail').classList.add('flex');
};

window.closeModal = function () {
    document.getElementById('modal-detail').classList.add('hidden');
    document.getElementById('modal-detail').classList.remove('flex');
};