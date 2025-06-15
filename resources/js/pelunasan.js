// Pagination state for main table
let currentPage = 1;
let entriesPerPage = 10;
let filteredPelunasanData = [];
let allPelunasanData = [];

document.addEventListener('DOMContentLoaded', function () {
    const btnPilihTransaksi = document.getElementById('btn-pilih-transaksi');
    const modalPilihTransaksi = document.getElementById('modal-pilih-transaksi');
    const btnCloseModal = document.getElementById('btn-close-modal');

    const searchInput = document.getElementById('search-transaksi');
    const entriesSelect = document.getElementById('modal-entries-select');
    const tableBody = modalPilihTransaksi.querySelector('tbody');
    const originalRows = Array.from(tableBody.querySelectorAll('tr'));

    // Main table pagination controls
    const mainEntriesSelect = document.getElementById('entries-select');
    const mainSearchInput = document.getElementById('search-input');

    // Fungsi render ulang table sesuai filter untuk modal
    function renderModalTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const entries = parseInt(entriesSelect.value);
        let filtered = originalRows.filter(row => {
            return row.innerText.toLowerCase().includes(searchTerm);
        });

        tableBody.innerHTML = '';
        filtered.slice(0, entries).forEach(row => {
            tableBody.appendChild(row);
        });
    }

    // Reset ke kondisi awal untuk modal
    function resetModalFilter() {
        searchInput.value = '';
        entriesSelect.value = '10';
        renderModalTable();
    }

    // Buka modal
    btnPilihTransaksi.addEventListener('click', () => {
        resetModalFilter(); // reset saat buka
        modalPilihTransaksi.classList.remove('hidden');
    });

    // Tutup modal
    btnCloseModal.addEventListener('click', () => {
        modalPilihTransaksi.classList.add('hidden');
    });

    // Klik luar modal untuk tutup
    modalPilihTransaksi.addEventListener('click', (e) => {
        if (e.target === modalPilihTransaksi) {
            modalPilihTransaksi.classList.add('hidden');
        }
    });

    // Event handler untuk modal
    searchInput.addEventListener('input', renderModalTable);
    entriesSelect.addEventListener('change', renderModalTable);

    // Initial render saat load halaman untuk modal
    renderModalTable();

    const modal = document.getElementById('modal-form-bayar');
    const btnClose = document.getElementById('btn-close-form');
    const form = document.getElementById('formPelunasan');

    // Field input
    const idTransaksi = document.getElementById('idTransaksi');
    const idTransaksiDisplay = document.getElementById('idTransaksiDisplay')
    const email = document.getElementById('email');
    const tanggalTransaksi = document.getElementById('tanggalTransaksi');
    const jatuhTempo = document.getElementById('jatuhTempo');
    const totalBelanja = document.getElementById('totalBelanja');
    const tanggalPelunasan = document.getElementById('tanggalPelunasan');
    const dibayar = document.getElementById('dibayar');
    const kembalian = document.getElementById('kembalian');

    // Buka modal saat tombol "Bayar" diklik
    document.querySelectorAll('.btn-bayar').forEach(button => {
        button.addEventListener('click', function () {
            const transaksi = JSON.parse(this.dataset.transaksi);

            // Isi field dengan data transaksi
            idTransaksi.value = transaksi.id;
            idTransaksiDisplay.value = transaksi.id;
            email.value = transaksi.email_pelanggan || '-';
            tanggalTransaksi.value = transaksi.tanggal;
            jatuhTempo.value = transaksi.jatuh_tempo || '-';
            totalBelanja.value = transaksi.total_belanja;
            tanggalPelunasan.value = new Date().toISOString().split('T')[0];
            dibayar.value = '';
            kembalian.value = '';

            // Tampilkan modal
            modal.classList.remove('hidden');
        });
    });

    // Tutup modal
    btnClose.addEventListener('click', function () {
        modal.classList.add('hidden');
    });

    // Hitung kembalian otomatis
    dibayar.addEventListener('input', function () {
        const total = parseInt(totalBelanja.value) || 0;
        const bayar = parseInt(dibayar.value) || 0;
        const kembali = bayar - total;
        kembalian.value = kembali > 0 ? kembali : 0;
    });

    // Load initial pelunasan data
    loadPelunasanData();

    // Setup main table pagination event listeners
    setupMainTableEventListeners();

    // Validasi sebelum submit
    form.addEventListener('submit', function (e) {
        const nilaiDibayar = dibayar.value.trim();
        const total = parseInt(totalBelanja.value) || 0;
        const bayar = parseInt(nilaiDibayar) || 0;

        if (nilaiDibayar === '' || isNaN(nilaiDibayar) || bayar <= 0) {
            e.preventDefault();
            window.dispatchEvent(new CustomEvent('show-error', {
                detail: 'Lengkapi Data Terlebih Dahulu!'
            }));
            return false;
        }

        if (bayar < total) {
            e.preventDefault();
            window.dispatchEvent(new CustomEvent('show-error', {
                detail: 'Uang tidak cukup!'
            }));
            return false;
        }
    });
});

// Setup event listeners for main table pagination
function setupMainTableEventListeners() {
    // Entries per page selector
    const entriesSelect = document.getElementById('entries-select');
    entriesSelect.addEventListener('change', function() {
        entriesPerPage = parseInt(this.value);
        currentPage = 1;
        renderMainTable();
    });

    // Search input
    const searchInput = document.getElementById('search-input');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        filterMainTableData(searchTerm);
        currentPage = 1;
        renderMainTable();
    });

    // Pagination buttons
    document.getElementById('prev-btn').addEventListener('click', function() {
        if (currentPage > 1) {
            currentPage--;
            renderMainTable();
        }
    });

    document.getElementById('next-btn').addEventListener('click', function() {
        const totalPages = Math.ceil(filteredPelunasanData.length / entriesPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            renderMainTable();
        }
    });
}

// Filter data for main table
function filterMainTableData(searchTerm) {
    if (!searchTerm) {
        filteredPelunasanData = [...allPelunasanData];
        return;
    }

    filteredPelunasanData = allPelunasanData.filter(item => {
        const pelunasanDate = new Date(item.tanggal_pelunasan);
        const jatuhTempo = new Date(item.jatuh_tempo_piutang);
        const status = pelunasanDate <= jatuhTempo ? 'tepat waktu' : 'terlambat';
        
        return (
            item.id_transaksi.toLowerCase().includes(searchTerm) ||
            item.tanggal_piutang.toLowerCase().includes(searchTerm) ||
            item.jatuh_tempo_piutang.toLowerCase().includes(searchTerm) ||
            item.tanggal_pelunasan.toLowerCase().includes(searchTerm) ||
            status.includes(searchTerm)
        );
    });
}

// Load pelunasan data from API
function loadPelunasanData() {
    fetch('/data/pelunasan')
        .then(response => response.json())
        .then(data => {
            allPelunasanData = data;
            data.sort((a, b) => new Date(b.tanggal_piutang) - new Date(a.tanggal_piutang));
            filteredPelunasanData = [...data];
            renderMainTable();
        })
        .catch(error => {
            console.error('Gagal memuat data pelunasan:', error);
            showNoDataMessage();
        });
}

// Render main table with pagination
function renderMainTable() {
    const tbody = document.getElementById('pelunasan-body');
    const noDataDiv = document.getElementById('no-data');
    
    // Calculate pagination
    const totalEntries = filteredPelunasanData.length;
    const totalPages = Math.ceil(totalEntries / entriesPerPage);
    const startIndex = (currentPage - 1) * entriesPerPage;
    const endIndex = Math.min(startIndex + entriesPerPage, totalEntries);
    const currentData = filteredPelunasanData.slice(startIndex, endIndex);

    // Clear table body
    tbody.innerHTML = '';

    if (currentData.length === 0) {
        noDataDiv.classList.remove('hidden');
        tbody.style.display = 'none';
    } else {
        noDataDiv.classList.add('hidden');
        tbody.style.display = '';

        // Populate table rows
        currentData.forEach((item, index) => {
            const pelunasanDate = new Date(item.tanggal_pelunasan);
            const jatuhTempo = new Date(item.jatuh_tempo_piutang);
            const status = pelunasanDate <= jatuhTempo ? 'Tepat Waktu' : 'Terlambat';
            const statusClass = pelunasanDate <= jatuhTempo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';

            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';
            row.innerHTML = `
                <td class="px-4 py-2">${startIndex + index + 1}</td>
                <td class="px-4 py-2">${item.id_transaksi}</td>
                <td class="px-4 py-2">${item.tanggal_piutang}</td>
                <td class="px-4 py-2">${item.jatuh_tempo_piutang}</td>
                <td class="px-4 py-2">${item.tanggal_pelunasan}</td>
                <td class="px-4 py-2">
                    <span class="px-2 py-1 rounded-xl text-xs font-semibold ${statusClass}">
                        ${status}
                    </span>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    // Update pagination
    updateMainTablePagination(totalPages, startIndex, endIndex, totalEntries);
}

// Show no data message
function showNoDataMessage() {
    const tbody = document.getElementById('pelunasan-body');
    const noDataDiv = document.getElementById('no-data');
    
    tbody.innerHTML = '';
    noDataDiv.classList.remove('hidden');
    tbody.style.display = 'none';
    
    // Update info
    document.getElementById('info-text').textContent = 'Showing 0 to 0 of 0 entries';
    
    // Disable pagination buttons
    document.getElementById('prev-btn').disabled = true;
    document.getElementById('next-btn').disabled = true;
    document.getElementById('pagination-numbers').innerHTML = '';
}

// Update pagination for main table
function updateMainTablePagination(totalPages, startIndex, endIndex, totalEntries) {
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

// Update page numbers
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

// Add page button
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
        renderMainTable();
    });
    
    container.appendChild(button);
}

// Add ellipsis
function addEllipsis(container) {
    const span = document.createElement('span');
    span.textContent = '...';
    span.className = 'px-2 py-2 text-sm text-gray-500';
    container.appendChild(span);
}