document.addEventListener("DOMContentLoaded", function () {
    const tableBody = document.querySelector("tbody");
    const rows = Array.from(tableBody.querySelectorAll("tr"));
    const searchInput = document.querySelector("input[placeholder='Search...']");
    const selectPerPage = document.querySelector("select");
    const showText = document.querySelector("div.text-sm.text-gray-700 > div");
    const paginationContainer = document.getElementById("pagination");

    let currentData = [...rows];
    let currentPage = 1;
    let perPage = parseInt(selectPerPage.value);

    function renderTable() {
        // Clear tbody
        tableBody.innerHTML = "";

        const searchTerm = searchInput.value.toLowerCase();

        // Filter data by search
        const filteredData = currentData.filter((row) => {
            const cells = row.querySelectorAll("td");
            return (
                cells[1].textContent.toLowerCase().includes(searchTerm) ||
                cells[2].textContent.toLowerCase().includes(searchTerm) ||
                cells[6].textContent.toLowerCase().includes(searchTerm)
            );
        });

        // Pagination
        const start = (currentPage - 1) * perPage;
        const paginatedData = filteredData.slice(start, start + perPage);

        // Render rows
        paginatedData.forEach((row, index) => {
            // Update nomor urut
            row.querySelector("td").textContent = start + index + 1;
            tableBody.appendChild(row);
        });

        // Update info
        showText.querySelector("span")?.remove();
        const info = document.createElement("span");
        info.className = "ml-2 text-gray-500";
        info.textContent = `Menampilkan ${paginatedData.length} dari ${filteredData.length} data`;
        showText.appendChild(info);

        renderPagination(filteredData.length);
    }

    // Event: Change items per page
    selectPerPage.addEventListener("change", () => {
        perPage = parseInt(selectPerPage.value);
        currentPage = 1;
        renderTable();
    });

    // Event: Search
    searchInput.addEventListener("input", () => {
        currentPage = 1;
        renderTable();
    });

    // Event: Edit & Delete button (delegated)
    tableBody.addEventListener("click", function (e) {
        const row = e.target.closest("tr");
        const itemName = row.querySelector("td:nth-child(2)").textContent.trim();

        if (e.target.textContent.includes("✏️")) {
            alert(`Edit data: ${itemName}`);
            // Redirect / logic edit di sini
        }

        if (e.target.textContent.includes("🗑️")) {
            if (confirm(`Hapus data: ${itemName}?`)) {
                row.remove();
                currentData = Array.from(document.querySelectorAll("tbody tr"));
                renderTable();
            }
        }
    });

    // Initial render
    renderTable();

    function renderPagination(filteredCount) {
        paginationContainer.innerHTML = "";

        const totalPages = Math.ceil(filteredCount / perPage);

        const prevButton = document.createElement("button");
        prevButton.textContent = "<";
        prevButton.disabled = currentPage === 1;
        prevButton.className = "px-2 py-1 border rounded disabled:opacity-50";
        prevButton.addEventListener("click", () => {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
            }
        });

        const nextButton = document.createElement("button");
        nextButton.textContent = ">";
        nextButton.disabled = currentPage === totalPages;
        nextButton.className = "px-2 py-1 border rounded disabled:opacity-50";
        nextButton.addEventListener("click", () => {
            if (currentPage < totalPages) {
                currentPage++;
                renderTable();
            }
        });

        const pageInfo = document.createElement("span");
        pageInfo.textContent = `Hal ${currentPage} dari ${totalPages}`;
        pageInfo.className = "text-sm";

        paginationContainer.appendChild(prevButton);
        paginationContainer.appendChild(pageInfo);
        paginationContainer.appendChild(nextButton);
    }

});
