document.addEventListener('DOMContentLoaded', () => {
    let kategoriData = [];
    let supplierData = [];
    let barangData = [];

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // === Load Data Awal ===
    fetch('/masterdata/kategori').then(res => res.json()).then(data => {
        kategoriData = data;
        updateKategoriTable();
    });

    fetch('/masterdata/suppliers').then(res => res.json()).then(data => {
        supplierData = data;
        updateSupplierTable();
    });

    fetch('/masterdata/barang').then(res => res.json()).then(data => {
        barangData = data;
        updateBarangTable();
    });

    // === Modal Controls ===
    document.querySelectorAll('.btn-close-modal').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.dataset.target;
            document.getElementById(target).classList.add('hidden');
        });
    });

    document.getElementById('btn-open-barang').addEventListener('click', () => {
        renderSelectOptions();
        document.getElementById('formBarang').classList.remove('hidden');
    });

    document.getElementById('btn-open-kategori').addEventListener('click', () => {
        document.getElementById('formKategori').classList.remove('hidden');
    });

    document.getElementById('btn-open-supplier').addEventListener('click', () => {
        document.getElementById('formSupplier').classList.remove('hidden');
    });

    // === Simpan Kategori ===
    document.getElementById('simpanKategori').addEventListener('click', () => {
        const nama = document.getElementById('namaKategori').value.trim();
        if (nama === '') {
            return window.dispatchEvent(new CustomEvent('show-error', {
                detail: 'Nama kategori tidak boleh kosong.'
            }));
        }


        fetch('/masterdata/kategori', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ nama })
        })
        .then(res => res.json())
        .then(() => {
            kategoriData.push(nama);
            kategoriData = [...new Set(kategoriData)];
            updateKategoriTable();
            document.getElementById('namaKategori').value = '';
            document.getElementById('formKategori').classList.add('hidden');
        });
    });

    function updateKategoriTable() {
        const tbody = document.getElementById('kategoriTable');
        tbody.innerHTML = '';
        kategoriData.forEach(nama => {
            const row = document.createElement('tr');
            row.innerHTML = `<td class="border px-4 py-2">${nama}</td>`;
            tbody.appendChild(row);
        });
    }

    // === Simpan Supplier ===
    document.getElementById('simpanSupplier').addEventListener('click', () => {
        const nama = document.getElementById('namaSupplier').value.trim();
        if (nama === '') {
            return window.dispatchEvent(new CustomEvent('show-error', {
                detail: 'Nama supplier tidak boleh kosong.'
            }));
        }
        fetch('/masterdata/suppliers', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ nama })
        })
        .then(res => res.json())
        .then(() => {
            supplierData.push(nama);
            supplierData = [...new Set(supplierData)];
            updateSupplierTable();
            document.getElementById('namaSupplier').value = '';
            document.getElementById('formSupplier').classList.add('hidden');
        });
    });

    function updateSupplierTable() {
        const tbody = document.getElementById('supplierTable');
        tbody.innerHTML = '';
        supplierData.forEach(nama => {
            const row = document.createElement('tr');
            row.innerHTML = `<td class="border px-4 py-2">${nama}</td>`;
            tbody.appendChild(row);
        });
    }

    // === Simpan Barang ===
    document.getElementById('simpanBarang').addEventListener('click', () => {
        const nama = document.getElementById('namaBarang').value.trim();
        const kategori = document.getElementById('kategoriBarang').value;
        const supplier = document.getElementById('supplierBarang').value;

        if (!nama || !kategori || !supplier) {
            return window.dispatchEvent(new CustomEvent('show-error', {
                detail: 'Semua field wajib diisi.'
            }));
        }

        fetch('/masterdata/barang', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ nama, kategori, supplier })
        })
        .then(res => res.json())
        .then(() => {
            const id = barangData.length + 1;
            barangData.push({ id, nama, kategori, supplier });
            updateBarangTable();
            document.getElementById('namaBarang').value = '';
            document.getElementById('formBarang').classList.add('hidden');
        });
    });

    function updateBarangTable() {
        const tbody = document.getElementById('barangTable');
        tbody.innerHTML = '';
        barangData.forEach((item, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="border px-4 py-2">${item.id_brg || 'BRG-' + (index + 1)}</td>
                <td class="border px-4 py-2">${item.nama}</td>
                <td class="border px-4 py-2">${item.kategori}</td>
                <td class="border px-4 py-2">${item.supplier}</td>
            `;
            tbody.appendChild(row);
        });
    }

    function renderSelectOptions() {
        const kategoriSelect = document.getElementById('kategoriBarang');
        const supplierSelect = document.getElementById('supplierBarang');

        kategoriSelect.innerHTML = '<option value="">Pilih Kategori</option>';
        supplierSelect.innerHTML = '<option value="">Pilih Supplier</option>';

        kategoriData.forEach(nama => {
            const opt = document.createElement('option');
            opt.value = nama;
            opt.textContent = nama;
            kategoriSelect.appendChild(opt);
        });

        supplierData.forEach(nama => {
            const opt = document.createElement('option');
            opt.value = nama;
            opt.textContent = nama;
            supplierSelect.appendChild(opt);
        });
    }
});
