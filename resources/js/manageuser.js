// Pagination state for user management table
let currentPage = 1;
let entriesPerPage = 10;
let filteredUserData = [];
let allUserData = [];

document.addEventListener('DOMContentLoaded', function () {
    // Get table elements
    const tableBody = document.querySelector('tbody');
    const originalRows = Array.from(tableBody.querySelectorAll('tr'));
    
    // Get control elements
    const entriesSelect = document.getElementById('items');
    const searchInput = document.getElementById('search');
    
    // Convert table rows to data objects for easier manipulation
    convertTableRowsToData(originalRows);
    
    // Setup event listeners
    setupEventListeners();
    
    // Initial render
    renderTable();
});

// Convert existing table rows to data objects
function convertTableRowsToData(rows) {
    allUserData = rows.map((row, index) => {
        const cells = row.querySelectorAll('td');
        return {
            no: index + 1,
            nama: cells[1]?.textContent?.trim() || '',
            email: cells[2]?.textContent?.trim() || '',
            role: cells[3]?.textContent?.trim() || '',
            actions: cells[4]?.innerHTML || ''
        };
    });
    
    filteredUserData = [...allUserData];
}

// Setup event listeners
function setupEventListeners() {
    // Entries per page selector
    const entriesSelect = document.getElementById('items');
    entriesSelect.addEventListener('change', function() {
        entriesPerPage = parseInt(this.value);
        currentPage = 1;
        renderTable();
    });

    // Search input
    const searchInput = document.getElementById('search');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        filterUserData(searchTerm);
        currentPage = 1;
        renderTable();
    });

    // Add pagination controls to the page
    addPaginationControls();
}

// Filter user data based on search term
function filterUserData(searchTerm) {
    if (!searchTerm) {
        filteredUserData = [...allUserData];
        return;
    }

    filteredUserData = allUserData.filter(user => {
        return (
            user.nama.toLowerCase().includes(searchTerm) ||
            user.email.toLowerCase().includes(searchTerm) ||
            user.role.toLowerCase().includes(searchTerm)
        );
    });
}

// Render table with current data and pagination
function renderTable() {
    const tbody = document.querySelector('tbody');
    
    // Calculate pagination
    const totalEntries = filteredUserData.length;
    const totalPages = Math.ceil(totalEntries / entriesPerPage);
    const startIndex = (currentPage - 1) * entriesPerPage;
    const endIndex = Math.min(startIndex + entriesPerPage, totalEntries);
    const currentData = filteredUserData.slice(startIndex, endIndex);

    // Clear table body
    tbody.innerHTML = '';

    if (currentData.length === 0) {
        // Show no data message
        const noDataRow = document.createElement('tr');
        noDataRow.innerHTML = `
            <td colspan="5" class="py-8 text-center text-gray-500">
                Tidak ada data user yang sesuai dengan pencarian.
            </td>
        `;
        tbody.appendChild(noDataRow);
    } else {
        // Populate table rows
        currentData.forEach((user, index) => {
            const row = document.createElement('tr');
            row.className = 'border-b hover:bg-gray-50';
            row.innerHTML = `
                <td class="py-2 px-3">${startIndex + index + 1}</td>
                <td class="py-2 px-3">${user.nama}</td>
                <td class="py-2 px-3">${user.email}</td>
                <td class="py-2 px-3">${user.role}</td>
                <td class="py-2 px-3 flex gap-2">${user.actions}</td>
            `;
            tbody.appendChild(row);
        });
    }

    // Update pagination
    updatePagination(totalPages, startIndex, endIndex, totalEntries);
}

// Add pagination controls to the page
function addPaginationControls() {
    // Perbaikan: Gunakan selector yang sesuai dengan HTML
    const tableContainer = document.querySelector('.bg-white.shadow-md.rounded-xl');
    
    // Check if pagination already exists
    if (document.getElementById('pagination-container')) {
        return;
    }
    
    const paginationHTML = `
        <div id="pagination-container" class="px-6 py-4 border-t border-gray-200">
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
    `;
    
    // Perbaikan: Tambahkan setelah table container, bukan di dalam
    tableContainer.insertAdjacentHTML('beforeend', paginationHTML);
    
    // Add event listeners for pagination buttons
    document.getElementById('prev-btn').addEventListener('click', function() {
        if (currentPage > 1) {
            currentPage--;
            renderTable();
        }
    });

    document.getElementById('next-btn').addEventListener('click', function() {
        const totalPages = Math.ceil(filteredUserData.length / entriesPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            renderTable();
        }
    });
}

// Update pagination controls
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
        renderTable();
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

// Function to refresh data after user operations (add/edit/delete)
function refreshUserData() {
    // Re-fetch data from the current table
    const tableBody = document.querySelector('tbody');
    const currentRows = Array.from(tableBody.querySelectorAll('tr'));
    
    if (currentRows.length > 0 && !currentRows[0].querySelector('td[colspan]')) {
        convertTableRowsToData(currentRows);
        renderTable();
    }
}

// Export functions for external use if needed
window.userManagement = {
    refreshUserData,
    renderTable
};