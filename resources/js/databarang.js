document.addEventListener("DOMContentLoaded", function () {
    const tableBody = document.querySelector("tbody");
    const rows = Array.from(tableBody.querySelectorAll("tr"));
    const searchInput = document.querySelector("input[placeholder='Search...']");
    const selectPerPage = document.querySelector("select");
    const showText = document.querySelector("div.text-sm.text-gray-700 > div");
    const paginationContainer = document.getElementById("pagination");

    // Sorting variables
    let currentSort = { column: 'nama', direction: 'asc' }; // Default sort by nama A-Z
    const sortableColumns = {
        'nama': 1,     // index kolom nama
        'kategori': 2, // index kolom kategori  
        'supplier': 6  // index kolom supplier
    };

    let originalRows = [...rows]; // Semua data asli dari awal
    let currentData = [...originalRows];
    let currentPage = 1;
    let perPage = parseInt(selectPerPage.value);
    let totalPages = 1;
    let filteredData = [];

    // Initialize sorting - sort by nama A-Z on load
    function initializeSorting() {
        sortData('nama', 'asc');
        updateSortIndicators();
    }

    function sortData(column, direction) {
        const columnIndex = sortableColumns[column];
        
        currentData.sort((a, b) => {
            const aValue = a.querySelectorAll("td")[columnIndex].textContent.trim().toLowerCase();
            const bValue = b.querySelectorAll("td")[columnIndex].textContent.trim().toLowerCase();
            
            if (direction === 'asc') {
                return aValue.localeCompare(bValue);
            } else {
                return bValue.localeCompare(aValue);
            }
        });
        
        currentSort = { column, direction };
    }

    function updateSortIndicators() {
        // Remove all existing sort indicators
        document.querySelectorAll('.sort-indicator').forEach(indicator => {
            indicator.remove();
        });
        
        // Add sort indicators to headers
        Object.keys(sortableColumns).forEach(column => {
            const headerCell = document.querySelector(`th:nth-child(${sortableColumns[column] + 1})`);
            if (headerCell) {
                headerCell.style.cursor = 'pointer';
                headerCell.style.userSelect = 'none';
                headerCell.style.position = 'relative';
                
                // Add sort indicator
                const indicator = document.createElement('span');
                indicator.className = 'sort-indicator ml-1';
                indicator.style.fontSize = '12px';
                indicator.style.color = '#6b7280';
                
                if (currentSort.column === column) {
                    indicator.textContent = currentSort.direction === 'asc' ? '↑' : '↓';
                    indicator.style.color = '#3b82f6';
                } else {
                    indicator.textContent = '↕';
                }
                
                headerCell.appendChild(indicator);
                
                // Add click event for sorting
                headerCell.onclick = () => {
                    let newDirection = 'asc';
                    if (currentSort.column === column && currentSort.direction === 'asc') {
                        newDirection = 'desc';
                    }
                    
                    sortData(column, newDirection);
                    updateSortIndicators();
                    currentPage = 1; // Reset to first page after sorting
                    renderTable();
                };
            }
        });
    }

    function renderTable() {
        // Clear tbody
        tableBody.innerHTML = "";

        const searchTerm = searchInput.value.toLowerCase();

        // Filter data by search
        filteredData = currentData.filter((row) => {
            const cells = row.querySelectorAll("td");
            return (
                cells[1].textContent.toLowerCase().includes(searchTerm) ||
                cells[2].textContent.toLowerCase().includes(searchTerm) ||
                cells[6].textContent.toLowerCase().includes(searchTerm)
            );
        });

        // Hitung total halaman
        totalPages = Math.ceil(filteredData.length / perPage);
        if (totalPages === 0) totalPages = 1;

        // Pastikan currentPage tidak melebihi totalPages
        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        // Pagination
        const start = (currentPage - 1) * perPage;
        const paginatedData = filteredData.slice(start, start + perPage);

        // Render rows
        paginatedData.forEach((row, index) => {
            // Update nomor urut
            row.querySelector("td").textContent = start + index + 1;
            tableBody.appendChild(row);
        });

        // Update pagination info
        updatePaginationInfo();
        // Update pagination controls
        updatePaginationControls();
    }

    function updatePaginationInfo() {
        // Update info text
        showText.querySelector("span")?.remove();
        const info = document.createElement("span");
        info.className = "ml-2 text-gray-500";
        
        const start = (currentPage - 1) * perPage + 1;
        const end = Math.min(currentPage * perPage, filteredData.length);
        
        info.textContent = `Showing ${start} to ${end} of ${filteredData.length} entries`;
        showText.appendChild(info);
    }

    function updatePaginationControls() {
        paginationContainer.innerHTML = "";

        // Jika hanya 1 halaman, tidak perlu pagination
        if (totalPages <= 1) {
            return;
        }

        // Previous button
        const prevBtn = document.createElement("button");
        prevBtn.textContent = "Previous";
        prevBtn.className = `px-3 py-1 text-sm border rounded ${currentPage === 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}`;
        prevBtn.disabled = currentPage === 1;
        prevBtn.addEventListener("click", () => {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
            }
        });
        paginationContainer.appendChild(prevBtn);

        // Page numbers
        const maxVisiblePages = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

        // Adjust startPage if endPage is at the limit
        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }

        // First page if not visible
        if (startPage > 1) {
            const firstBtn = createPageButton(1);
            paginationContainer.appendChild(firstBtn);
            
            if (startPage > 2) {
                const ellipsis = document.createElement("span");
                ellipsis.textContent = "...";
                ellipsis.className = "px-2 py-1 text-sm text-gray-500";
                paginationContainer.appendChild(ellipsis);
            }
        }

        // Page number buttons
        for (let i = startPage; i <= endPage; i++) {
            const pageBtn = createPageButton(i);
            paginationContainer.appendChild(pageBtn);
        }

        // Last page if not visible
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const ellipsis = document.createElement("span");
                ellipsis.textContent = "...";
                ellipsis.className = "px-2 py-1 text-sm text-gray-500";
                paginationContainer.appendChild(ellipsis);
            }
            
            const lastBtn = createPageButton(totalPages);
            paginationContainer.appendChild(lastBtn);
        }

        // Next button
        const nextBtn = document.createElement("button");
        nextBtn.textContent = "Next";
        nextBtn.className = `px-3 py-1 text-sm border rounded ${currentPage === totalPages ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}`;
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.addEventListener("click", () => {
            if (currentPage < totalPages) {
                currentPage++;
                renderTable();
            }
        });
        paginationContainer.appendChild(nextBtn);
    }

    function createPageButton(pageNumber) {
        const pageBtn = document.createElement("button");
        pageBtn.textContent = pageNumber;
        pageBtn.className = `px-3 py-1 text-sm border rounded ${pageNumber === currentPage ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'}`;
        pageBtn.addEventListener("click", () => {
            currentPage = pageNumber;
            renderTable();
        });
        return pageBtn;
    }

    // Event: Change items per page
    selectPerPage.addEventListener("change", () => {
        perPage = parseInt(selectPerPage.value);
        currentPage = 1; // Reset ke halaman pertama
        renderTable();
    });

    // Event: Search
    searchInput.addEventListener("input", () => {
        currentPage = 1; // Reset ke halaman pertama saat search
        renderTable();
    });

    // Event: Edit & Delete button (delegated)
    tableBody.addEventListener("click", function (e) {
        const row = e.target.closest("tr");
        const index = row.getAttribute("data-index"); // penting! pastikan di Blade tiap row punya data-index
        const itemName = row.querySelector("td:nth-child(2)").textContent.trim();

        if (e.target.textContent.includes("✏️")) {
            window.location.href = `/barang/${index}/edit`;
        }

        if (e.target.textContent.includes("🗑️")) {
            if (confirm(`Hapus data: ${itemName}?`)) {
                fetch(`/barang/${index}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.dispatchEvent(new CustomEvent('show-success', { detail: data.message }));
                        
                        // Remove row from original data
                        originalRows = originalRows.filter(r => r !== row);
                        currentData = [...originalRows];
                        
                        // Re-apply current sorting after deletion
                        sortData(currentSort.column, currentSort.direction);
                        
                        // Jika halaman saat ini kosong setelah delete, pindah ke halaman sebelumnya
                        const newFilteredData = currentData.filter((row) => {
                            const searchTerm = searchInput.value.toLowerCase();
                            const cells = row.querySelectorAll("td");
                            return (
                                cells[1].textContent.toLowerCase().includes(searchTerm) ||
                                cells[2].textContent.toLowerCase().includes(searchTerm) ||
                                cells[6].textContent.toLowerCase().includes(searchTerm)
                            );
                        });
                        
                        const newTotalPages = Math.ceil(newFilteredData.length / perPage);
                        if (currentPage > newTotalPages && newTotalPages > 0) {
                            currentPage = newTotalPages;
                        }
                        
                        renderTable();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus data');
                });
            }
        }
    });

    // Initialize everything
    initializeSorting();
    renderTable();
});