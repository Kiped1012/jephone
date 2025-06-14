document.addEventListener('DOMContentLoaded', function () {
    const totalBelanjaDisplay = document.getElementById('totalBelanjaDisplay');
    const totalBelanjaInput = document.getElementById('inputTotalBelanja');
    const inputTanggal = document.getElementById('inputTanggal');
    const inputDibayar = document.getElementById('inputDibayar');
    const inputKembalian = document.getElementById('inputKembalian');

    const formPembelian = document.getElementById('form-pembelian');
    const tbody = document.getElementById('order-body');

    const dibayarInput = document.getElementById('dibayarInput');
    const kembalianOutput = document.getElementById('kembalianOutput');
    const kembalianError = document.getElementById('kembalianError');
    const tanggalTransaksi = document.getElementById('tanggalTransaksi');
    const penanggungJawab = document.querySelector('input[name="penanggung_jawab"]');

    formPembelian.addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent default submission untuk debugging
        
        const rows = document.querySelectorAll('#order-body tr');
        const dibayar = dibayarInput.value;

        console.log('Rows count:', rows.length);
        console.log('Dibayar:', dibayar);
        console.log('Tanggal:', tanggalTransaksi.value);
        console.log('Penanggung Jawab:', penanggungJawab.value);

        let showError = false;

        if (rows.length === 0 || !dibayar || parseInt(dibayar) <= 0 || !tanggalTransaksi.value || !penanggungJawab.value) {
            showError = true;
        }

        if (showError) {
            window.dispatchEvent(new CustomEvent('show-error', {
                detail: "Lengkapi Data Terlebih Dahulu!"
            }));
            return;
        }

        // Simpan ke input hidden
        const items = [];
        rows.forEach(row => {
            const nama = row.children[1].textContent;
            const harga = parseInt(row.children[2].textContent.replace(/[^\d]/g, ''));
            const jumlah = parseInt(row.querySelector('.jumlah-input').value);
            const total = harga * jumlah;
            items.push({ nama, harga, jumlah, total });
        });

        const totalBelanja = parseInt(totalBelanjaDisplay.value.replace(/[^\d]/g, '')) || 0;
        
        if (parseInt(dibayar) < totalBelanja) {
            window.dispatchEvent(new CustomEvent('show-error', {
                detail: "Uang tidak cukup!"
            }));
            return;
        }

        const kembalian = parseInt(dibayar) - totalBelanja;

        // Set semua input hidden dengan nama yang benar
        document.getElementById('inputItems').value = JSON.stringify(items);
        document.getElementById('inputTotalBelanja').value = totalBelanja;
        document.getElementById('inputDibayar').value = dibayar;
        document.getElementById('inputKembalian').value = kembalian;
        document.getElementById('inputTanggal').value = tanggalTransaksi.value;

        console.log('Data yang akan dikirim:');
        console.log('Items:', JSON.stringify(items));
        console.log('Total Belanja:', totalBelanja);
        console.log('Dibayar:', dibayar);
        console.log('Kembalian:', kembalian);
        console.log('Tanggal:', tanggalTransaksi.value);

        // Submit form setelah semua data siap
        this.submit();
    });

    // Modal logika dengan pagination
    const modal = document.getElementById('modal-pilih-barang');
    const closeBtn = document.getElementById('btn-close-modal');
    const openBtn = document.getElementById('btn-pilih-barang');
    const searchInput = document.getElementById('search-barang');
    const entriesSelect = document.getElementById('entries-select');
    const tableRows = Array.from(document.querySelectorAll('#barang-table tbody .barang-row'));

    // State untuk pagination
    let currentPage = 1;
    let filteredRows = [];
    let totalPages = 1;

    openBtn.addEventListener('click', () => {
        // Reset search input
        searchInput.value = '';
        // Reset entries select ke default (misalnya 10)
        entriesSelect.value = '10';
        // Reset ke halaman pertama
        currentPage = 1;
        // Tampilkan modal
        modal.classList.remove('hidden');
        // Terapkan kembali filter awal
        applySearchAndLimit();
    });

    closeBtn.addEventListener('click', () => modal.classList.add('hidden'));

    // Fitur search
    searchInput.addEventListener('input', () => {
        currentPage = 1; // Reset ke halaman pertama saat search
        applySearchAndLimit();
    });

    entriesSelect.addEventListener('change', () => {
        currentPage = 1; // Reset ke halaman pertama saat ganti entries
        applySearchAndLimit();
    });

    function applySearchAndLimit() {
        const query = searchInput.value.toLowerCase();
        const limit = parseInt(entriesSelect.value);

        // Filter rows berdasarkan search query
        filteredRows = tableRows.filter(row => {
            return row.innerText.toLowerCase().includes(query);
        });

        // Hitung total halaman
        totalPages = Math.ceil(filteredRows.length / limit);
        if (totalPages === 0) totalPages = 1;

        // Pastikan currentPage tidak melebihi totalPages
        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        // Hitung index start dan end untuk halaman saat ini
        const startIndex = (currentPage - 1) * limit;
        const endIndex = startIndex + limit;

        // Sembunyikan semua rows terlebih dahulu
        tableRows.forEach(row => {
            row.style.display = 'none';
        });

        // Tampilkan rows untuk halaman saat ini
        let nomorUrut = startIndex + 1;
        filteredRows.slice(startIndex, endIndex).forEach(row => {
            row.style.display = '';
            row.querySelector('td:first-child').textContent = nomorUrut++;
        });

        // Update pagination info dan controls
        updatePaginationInfo();
        updatePaginationControls();
    }

    function updatePaginationInfo() {
        // Cari atau buat element untuk info pagination
        let paginationInfo = document.getElementById('pagination-info');
        if (!paginationInfo) {
            paginationInfo = document.createElement('div');
            paginationInfo.id = 'pagination-info';
            paginationInfo.className = 'text-sm text-gray-600 mt-2';
            
            // Insert setelah tabel
            const tableContainer = document.querySelector('#barang-table').parentElement;
            tableContainer.appendChild(paginationInfo);
        }

        const limit = parseInt(entriesSelect.value);
        const startIndex = (currentPage - 1) * limit + 1;
        const endIndex = Math.min(currentPage * limit, filteredRows.length);
        
        paginationInfo.textContent = `Showing ${startIndex} to ${endIndex} of ${filteredRows.length} entries`;
    }

    function updatePaginationControls() {
        // Cari atau buat element untuk pagination controls
        let paginationControls = document.getElementById('pagination-controls');
        if (!paginationControls) {
            paginationControls = document.createElement('div');
            paginationControls.id = 'pagination-controls';
            paginationControls.className = 'flex justify-center items-center gap-2 mt-4';
            
            // Insert setelah info pagination
            const paginationInfo = document.getElementById('pagination-info');
            paginationInfo.parentElement.appendChild(paginationControls);
        }

        // Clear existing controls
        paginationControls.innerHTML = '';

        // Jika hanya 1 halaman, tidak perlu pagination
        if (totalPages <= 1) {
            return;
        }

        // Previous button
        const prevBtn = document.createElement('button');
        prevBtn.textContent = 'Previous';
        prevBtn.className = `px-3 py-1 text-sm border rounded ${currentPage === 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}`;
        prevBtn.disabled = currentPage === 1;
        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                applySearchAndLimit();
            }
        });
        paginationControls.appendChild(prevBtn);

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
            paginationControls.appendChild(firstBtn);
            
            if (startPage > 2) {
                const ellipsis = document.createElement('span');
                ellipsis.textContent = '...';
                ellipsis.className = 'px-2 py-1 text-sm text-gray-500';
                paginationControls.appendChild(ellipsis);
            }
        }

        // Page number buttons
        for (let i = startPage; i <= endPage; i++) {
            const pageBtn = createPageButton(i);
            paginationControls.appendChild(pageBtn);
        }

        // Last page if not visible
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const ellipsis = document.createElement('span');
                ellipsis.textContent = '...';
                ellipsis.className = 'px-2 py-1 text-sm text-gray-500';
                paginationControls.appendChild(ellipsis);
            }
            
            const lastBtn = createPageButton(totalPages);
            paginationControls.appendChild(lastBtn);
        }

        // Next button
        const nextBtn = document.createElement('button');
        nextBtn.textContent = 'Next';
        nextBtn.className = `px-3 py-1 text-sm border rounded ${currentPage === totalPages ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}`;
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                applySearchAndLimit();
            }
        });
        paginationControls.appendChild(nextBtn);
    }

    function createPageButton(pageNumber) {
        const pageBtn = document.createElement('button');
        pageBtn.textContent = pageNumber;
        pageBtn.className = `px-3 py-1 text-sm border rounded ${pageNumber === currentPage ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'}`;
        pageBtn.addEventListener('click', () => {
            currentPage = pageNumber;
            applySearchAndLimit();
        });
        return pageBtn;
    }

    // Initial load
    entriesSelect.dispatchEvent(new Event('change'));

    document.querySelectorAll('.pilih-barang').forEach(button => {
        button.addEventListener('click', function () {
            const nama = this.dataset.nama;
            const harga = parseInt(this.dataset.harga);
            const jumlah = parseInt(this.closest('tr').querySelector('.jumlah').value);
            const total = harga * jumlah;

            const newRow = document.createElement('tr');
            newRow.classList.add('hover:bg-gray-50', 'border-b');

            newRow.innerHTML = `
                <td class="px-4 py-2 border"></td>
                <td class="px-4 py-2 border">${nama}</td>
                <td class="px-4 py-2 border">Rp${harga.toLocaleString()}</td>
                <td class="px-4 py-2 border">
                    <input type="number" min="1" value="${jumlah}" class="jumlah-input w-16 border px-2 py-1 rounded" data-harga="${harga}" />
                </td>
                <td class="px-4 py-2 border total-item">Rp${total.toLocaleString()}</td>
                <td class="px-4 py-2 border text-center">
                    <button class="hapus-barang text-red-500 hover:text-red-700" data-nama="${nama}">🗑️</button>
                </td>
            `;

            tbody.appendChild(newRow);
            updateTotalBelanja();
            reorderRows();
            modal.classList.add('hidden');

            this.disabled = true;
            this.classList.add('opacity-50', 'cursor-not-allowed');
        });
    });

    tbody.addEventListener('input', function (e) {
        if (e.target.classList.contains('jumlah-input')) {
            const input = e.target;
            const harga = parseInt(input.dataset.harga);
            const jumlah = parseInt(input.value);
            const totalCell = input.closest('tr').querySelector('.total-item');
            const total = harga * jumlah;

            totalCell.textContent = `Rp${total.toLocaleString()}`;
            updateTotalBelanja();
        }
    });

    tbody.addEventListener('click', function (e) {
        if (e.target.classList.contains('hapus-barang')) {
            const row = e.target.closest('tr');
            const nama = e.target.dataset.nama;
            row.remove();
            updateTotalBelanja();
            reorderRows();

            document.querySelectorAll(`.pilih-barang[data-nama="${nama}"]`).forEach(btn => {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            });
        }
    });

    dibayarInput.addEventListener('input', hitungKembalian);

    function hitungKembalian() {
        const dibayar = parseInt(dibayarInput.value) || 0;
        const total = parseInt(totalBelanjaDisplay.value.replace(/[^\d]/g, '')) || 0;
        const kembali = dibayar - total;

        kembalianOutput.value = `Rp${(kembali >= 0 ? kembali : 0).toLocaleString()}`;
        kembalianError.classList.toggle('hidden', kembali >= 0);
    }

    function updateTotalBelanja() {
        let total = 0;
        document.querySelectorAll('.total-item').forEach(cell => {
            const angka = parseInt(cell.textContent.replace(/[^\d]/g, '')) || 0;
            total += angka;
        });
        totalBelanjaDisplay.value = `Rp${total.toLocaleString()}`;
        hitungKembalian();
    }

    function reorderRows() {
        document.querySelectorAll('#order-body tr').forEach((row, index) => {
            row.querySelector('td:first-child').textContent = index + 1;
        });
    }

    document.getElementById('btn-batal').addEventListener('click', function () {
        document.querySelectorAll('.pilih-barang').forEach(btn => {
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
        });
        tbody.innerHTML = '';
        totalBelanjaDisplay.value = 'Rp0';
        dibayarInput.value = '';
        kembalianOutput.value = '';
        kembalianError.classList.add('hidden');
    });
});