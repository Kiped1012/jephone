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

    // Modal logika
    const modal = document.getElementById('modal-pilih-barang');
    const closeBtn = document.getElementById('btn-close-modal');
    const openBtn = document.getElementById('btn-pilih-barang');
    const searchInput = document.getElementById('search-barang');
    const entriesSelect = document.getElementById('entries-select');
    const tableRows = Array.from(document.querySelectorAll('#barang-table tbody .barang-row'));

    openBtn.addEventListener('click', () => {
        searchInput.value = '';
        entriesSelect.value = '10';
        modal.classList.remove('hidden');
        applySearchAndLimit();
    });

    closeBtn.addEventListener('click', () => modal.classList.add('hidden'));

    searchInput.addEventListener('input', applySearchAndLimit);
    entriesSelect.addEventListener('change', applySearchAndLimit);

    function applySearchAndLimit() {
        const query = searchInput.value.toLowerCase();
        const limit = parseInt(entriesSelect.value);
        let matchCount = 0;
        let nomor = 1;

        tableRows.forEach(row => {
            const cocok = row.innerText.toLowerCase().includes(query);
            if (cocok && matchCount < limit) {
                row.style.display = '';
                row.querySelector('td:first-child').textContent = nomor++;
                matchCount++;
            } else {
                row.style.display = 'none';
            }
        });
    }

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