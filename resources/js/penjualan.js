    const totalBelanjaDisplay = document.getElementById('totalBelanjaDisplay');
    const totalBelanjaInput = document.getElementById('inputTotalBelanja');

    document.getElementById('form-penjualan').addEventListener('submit', function (e) {
    const rows = document.querySelectorAll('#order-body tr');
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
    document.getElementById('inputMetode').value = document.getElementById('metodePembayaran').value;
    document.getElementById('inputEmail').value = document.getElementById('emailPelanggan').value;
    document.getElementById('inputJatuhTempo').value = document.getElementById('jatuhTempo').value;
    });


    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('modal-pilih-barang');
        const closeBtn = document.getElementById('btn-close-modal');
        const openBtn = document.getElementById('btn-pilih-barang');
        const searchInput = document.getElementById('search-barang');
        const entriesSelect = document.getElementById('entries-select');
        const tableRows = Array.from(document.querySelectorAll('#barang-table tbody .barang-row'));
        const tbody = document.getElementById('order-body');

        openBtn.addEventListener('click', () => {
            // Reset search input
            searchInput.value = '';

            // Reset entries select ke default (misalnya 5)
            entriesSelect.value = '10';

            // Tampilkan modal
            modal.classList.remove('hidden');

            // Terapkan kembali filter awal
            applySearchAndLimit();
        });

        closeBtn.addEventListener('click', () => modal.classList.add('hidden'));

        // Fitur search
        searchInput.addEventListener('input', () => {
            applySearchAndLimit();
        });

        entriesSelect.addEventListener('change', () => {
            applySearchAndLimit();
        });

        function applySearchAndLimit() {
            const query = searchInput.value.toLowerCase();
            const limit = parseInt(entriesSelect.value);
            let matchCount = 0;

            tableRows.forEach(row => {
                const match = row.innerText.toLowerCase().includes(query);
                if (match && matchCount < limit) {
                    row.style.display = '';
                    matchCount++;
                } else {
                    row.style.display = 'none';
                }
            });
        }

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
                        <input type="number" value="${jumlah}" class="jumlah-input w-16 border px-2 py-1 rounded" data-harga="${harga}" />
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

        function toggleBayarSection() {
            const isTunai = metodeSelect.value === 'Tunai';
            const isPiutang = metodeSelect.value === 'Piutang';

            bayarSection.style.display = isTunai ? 'block' : 'none';
            piutangFields.classList.toggle('hidden', !isPiutang);

            if (isPiutang) {
                // Set jatuh tempo 30 hari dari hari ini
                const today = new Date();
                const jatuhTempo = new Date(today.setDate(today.getDate() + 30));
                jatuhTempoInput.value = jatuhTempo.toISOString().split('T')[0];
            }
        }

        metodeSelect.addEventListener('change', toggleBayarSection);

        // Panggil saat pertama kali halaman dimuat
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