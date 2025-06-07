document.addEventListener('DOMContentLoaded', () => {
    const namaSelect = document.getElementById('namaBarang');
    const supplierInput = document.getElementById('supplier');
    const kategoriInput = document.getElementById('kategori');
    const idBarangInput = document.getElementById('idBarang');

    if (!window.masterBarang) return;

    // Isi opsi Nama Barang
    window.masterBarang.forEach(barang => {
        const option = document.createElement('option');
        option.value = barang.nama;
        option.textContent = barang.nama;
        namaSelect.appendChild(option);
    });

    // Saat barang dipilih
    namaSelect.addEventListener('change', function () {
        const selected = window.masterBarang.find(b => b.nama === this.value);
        if (selected) {
            supplierInput.value = selected.supplier;
            kategoriInput.value = selected.kategori;
            idBarangInput.value = selected.id_brg;
        } else {
            supplierInput.value = '';
            kategoriInput.value = '';
            idBarangInput.value = '';
        }
    });

    document.getElementById('btnSimpan').addEventListener('click', function (e) {
        const stok = document.querySelector('[name="stok"]').value.trim();
        const hargaBeli = document.querySelector('[name="harga_beli"]').value.trim();
        const hargaJual = document.querySelector('[name="harga_jual"]').value.trim();

        if (!stok || !hargaBeli || !hargaJual) {
            e.preventDefault(); // Cegah submit
            window.dispatchEvent(new CustomEvent('show-error', {
                detail: 'Lengkapi form terlebih dahulu!'
            }));
        }
    });
});
