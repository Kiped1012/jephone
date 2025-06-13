const totalBelanjaDisplay = document.getElementById('totalBelanjaDisplay');
const totalBelanjaInput = document.getElementById('inputTotalBelanja');

document.getElementById('form-penjualan').addEventListener('submit', function (e) {
    const rows = document.querySelectorAll('#order-body tr');
    const metode = document.getElementById('metodePembayaran').value;
    const dibayar = document.getElementById('dibayarInput').value;
    const email = document.getElementById('emailPelanggan').value;

    let showError = false;

    if (rows.length === 0) {
        showError = true;
    } else if (!metode) {
        showError = true;
    } else if (metode === 'Tunai' && (!dibayar || parseInt(dibayar) <= 0)) {
        showError = true;
    } else if (metode === 'Piutang' && (!email || email.trim() === '')) {
        showError = true;
    }

    if (showError) {
        e.preventDefault();
        window.dispatchEvent(new CustomEvent('show-error', {
            detail: "Lengkapi Data Terlebih Dahulu!"
        }));
        return;
    }

    // Simpan data
    const items = [];

    rows.forEach(row => {
        const nama = row.children[1].textContent;
        const harga = parseInt(row.children[2].textContent.replace(/[^\d]/g, ''));
        const jumlah = parseInt(row.querySelector('.jumlah-input').value);
        const total = harga * jumlah;

        items.push({ nama, harga, jumlah, total });
    });

    document.getElementById('inputItems').value = JSON.stringify(items);
    totalBelanjaInput.value = totalBelanjaDisplay.value.replace(/[^\d]/g, '');
    document.getElementById('inputTanggal').value = document.getElementById('tanggalTransaksi').value;
    document.getElementById('inputMetode').value = metode;
    document.getElementById('inputDibayar').value = dibayar;
    document.getElementById('inputKembalian').value = document.getElementById('kembalianOutput').value;
    document.getElementById('inputEmail').value = email;
    document.getElementById('inputJatuhTempo').value = document.getElementById('jatuhTempo').value;

    // Cek apakah uang cukup SETELAH totalBelanjaInput.value diperbarui
    const totalBelanja = parseInt(totalBelanjaInput.value.replace(/[^\d]/g, '')) || 0;
    const kembalian = parseInt(dibayar) - totalBelanja;

    if (metode === 'Tunai' && kembalian < 0) {
        e.preventDefault();
        window.dispatchEvent(new CustomEvent('show-error', {
            detail: "Uang tidak cukup!"
        }));
        return;
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modal-pilih-barang');
    const closeBtn = document.getElementById('btn-close-modal');
    const openBtn = document.getElementById('btn-pilih-barang');
    const searchInput = document.getElementById('search-barang');
    const entriesSelect = document.getElementById('entries-select');
    const tableRows = Array.from(document.querySelectorAll('#barang-table tbody .barang-row'));
    const tbody = document.getElementById('order-body');

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

    // Pilih barang
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

            // Disable tombol "Pilih" di modal
            this.disabled = true;
            this.classList.add('opacity-50', 'cursor-not-allowed');
        });
    });

    // Update total ketika jumlah berubah
    tbody.addEventListener('input', function (e) {
        if (e.target.classList.contains('jumlah-input')) {
            const input = e.target;
            const harga = parseInt(input.dataset.harga);
            const jumlah = parseInt(input.value);
            const total = harga * jumlah;

            const totalCell = input.closest('tr').querySelector('.total-item');
            totalCell.textContent = `Rp${total.toLocaleString()}`;
            updateTotalBelanja();
        }
    });

    // Hapus barang dari tabel order
    tbody.addEventListener('click', function (e) {
        if (e.target.classList.contains('hapus-barang')) {
            const row = e.target.closest('tr');
            const nama = e.target.dataset.nama;
            row.remove();
            updateTotalBelanja();
            reorderRows();

            // Re-enable tombol "Pilih" yang sesuai
            document.querySelectorAll(`.pilih-barang[data-nama="${nama}"]`).forEach(btn => {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            });
        }
    });

    const dibayarInput = document.getElementById('dibayarInput');
    const kembalianOutput = document.getElementById('kembalianOutput');
    const totalBelanjaInput = document.querySelector('input[name="total_belanja"]');
    const kembalianError = document.getElementById('kembalianError');

    function hitungKembalian() {
        const dibayar = parseInt(dibayarInput.value) || 0;
        const totalBelanja = parseInt(totalBelanjaInput.value.replace(/[^\d]/g, '')) || 0;
        const kembalian = dibayar - totalBelanja;

        kembalianOutput.value = `Rp${(kembalian >= 0 ? kembalian : 0).toLocaleString()}`;
        kembalianError.classList.toggle('hidden', kembalian >= 0);
    }

    dibayarInput.addEventListener('input', hitungKembalian);

    function updateTotalBelanja() {
        let total = 0;
        document.querySelectorAll('.total-item').forEach(cell => {
            const angka = parseInt(cell.textContent.replace(/[^\d]/g, '')) || 0;
            total += angka;
        });
        const totalInput = document.querySelector('input[name="total_belanja"]');
        if (totalInput) {
            totalInput.value = `Rp${total.toLocaleString()}`;
        }

        // Hitung ulang kembalian jika dibayar sudah diisi
        hitungKembalian();
    }

    function reorderRows() {
        document.querySelectorAll('#order-body tr').forEach((row, index) => {
            row.querySelector('td:first-child').textContent = index + 1;
        });
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const metodeSelect = document.getElementById('metodePembayaran');
    const bayarSection = document.getElementById('bayarSection');
    const piutangFields = document.getElementById('piutangFields');
    const jatuhTempoInput = document.getElementById('jatuhTempo');
    const tanggalTransaksiInput = document.getElementById('tanggalTransaksi');

    function toggleBayarSection() {
        const isTunai = metodeSelect.value === 'Tunai';
        const isPiutang = metodeSelect.value === 'Piutang';

        bayarSection.style.display = isTunai ? 'block' : 'none';
        piutangFields.classList.toggle('hidden', !isPiutang);

        if (isPiutang) {
            const tanggalTransaksi = new Date(tanggalTransaksiInput.value);
            if (!isNaN(tanggalTransaksi.getTime())) {
                const jatuhTempo = new Date(tanggalTransaksi);
                jatuhTempo.setDate(jatuhTempo.getDate() + 30);
                jatuhTempoInput.value = jatuhTempo.toISOString().split('T')[0];
            } else {
                jatuhTempoInput.value = '';
            }
        }
    }

    metodeSelect.addEventListener('change', toggleBayarSection);
    tanggalTransaksiInput.addEventListener('change', toggleBayarSection);
    toggleBayarSection();
});

document.addEventListener('DOMContentLoaded', function () {
    const batalBtn = document.querySelector('button.bg-red-500');
    const tbody = document.getElementById('order-body');

    batalBtn.addEventListener('click', function () {
        // Hapus semua baris di tabel order
        tbody.innerHTML = '';

        // Reset total belanja
        totalBelanjaDisplay.value = 'Rp0';
        totalBelanjaInput.value = 0;

        // Reset metode pembayaran
        document.getElementById('metodePembayaran').value = '';

        // Reset bayar dan kembalian
        document.getElementById('dibayarInput').value = '';
        document.getElementById('kembalianOutput').value = '';
        document.getElementById('kembalianError').classList.add('hidden');

        // Reset email dan jatuh tempo
        document.getElementById('emailPelanggan').value = '';
        document.getElementById('jatuhTempo').value = '';

        // Sembunyikan section piutang & bayar
        document.getElementById('bayarSection').style.display = 'none';
        document.getElementById('piutangFields').classList.add('hidden');

        // Enable kembali tombol pilih di modal
        document.querySelectorAll('.pilih-barang').forEach(btn => {
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
        });
    });
});