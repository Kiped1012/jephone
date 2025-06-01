document.addEventListener('DOMContentLoaded', function () {
    const btnPilihTransaksi = document.getElementById('btn-pilih-transaksi');
    const modalPilihTransaksi = document.getElementById('modal-pilih-transaksi');
    const btnCloseModal = document.getElementById('btn-close-modal');

    const searchInput = document.getElementById('search-transaksi');
    const entriesSelect = document.getElementById('entries-select');
    const tableBody = modalPilihTransaksi.querySelector('tbody');
    const originalRows = Array.from(tableBody.querySelectorAll('tr'));

    // Fungsi render ulang table sesuai filter
    function renderTable() {
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

    // Reset ke kondisi awal
    function resetFilter() {
        searchInput.value = '';
        entriesSelect.value = '10';
        renderTable();
    }

    // Buka modal
    btnPilihTransaksi.addEventListener('click', () => {
        resetFilter(); // reset saat buka
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

    // Event handler
    searchInput.addEventListener('input', renderTable);
    entriesSelect.addEventListener('change', renderTable);

    // Initial render saat load halaman
    renderTable();

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

    fetch('/data/pelunasan')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('pelunasan-body');
            tbody.innerHTML = '';

            data.forEach((item, index) => {
                const pelunasanDate = new Date(item.tanggal_pelunasan);
                const jatuhTempo = new Date(item.jatuh_tempo_piutang);
                const status = pelunasanDate <= jatuhTempo ? 'Tepat Waktu' : 'Terlambat';
                const statusClass = pelunasanDate <= jatuhTempo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-4 py-2 border">${index + 1}</td>
                    <td class="px-4 py-2 border">${item.id_transaksi}</td>
                    <td class="px-4 py-2 border">${item.tanggal_piutang}</td>
                    <td class="px-4 py-2 border">${item.jatuh_tempo_piutang}</td>
                    <td class="px-4 py-2 border">${item.tanggal_pelunasan}</td>
                    <td class="px-4 py-2 border">
                        <span class="px-2 py-1 rounded-xl text-xs font-semibold ${statusClass}">
                            ${status}
                        </span>
                    </td>
                `;
                tbody.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Gagal memuat data pelunasan:', error);
        });
    // Validasi sebelum submit
    form.addEventListener('submit', function (e) {
        const nilaiDibayar = dibayar.value.trim();

        if (nilaiDibayar === '' || isNaN(nilaiDibayar) || parseInt(nilaiDibayar) <= 0) {
            e.preventDefault(); // Cegah pengiriman form

            // Tampilkan notifikasi error pakai Alpine.js
            window.dispatchEvent(new CustomEvent('show-error', {
                detail: 'Lengkapi Data Terlebih Dahulu!'
            }));

            return false;
        }
    });
});
