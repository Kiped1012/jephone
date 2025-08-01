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
        const namaLower = nama.toLowerCase();
        const kategoriLower = kategoriData.map(k => k.toLowerCase());

        if (nama === '') {
            return window.dispatchEvent(new CustomEvent('show-error', {
                detail: 'Nama kategori tidak boleh kosong.'
            }));
        }

        if (kategoriLower.includes(namaLower)) {
            return window.dispatchEvent(new CustomEvent('show-error', {
                detail: 'Kategori sudah ada.'
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
            window.dispatchEvent(new CustomEvent('show-success', {
                detail: 'Berhasil menyimpan data kategori.'
            }));
            document.getElementById('namaKategori').value = '';
            document.getElementById('formKategori').classList.add('hidden');
        });
    });

    function updateKategoriTable() {
        const tbody = document.getElementById('kategoriTable');
        tbody.innerHTML = '';
        kategoriData.forEach(nama => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="px-4 py-2 border">${nama}</td>
                <td class="px-4 py-2 border text-center">
                    <button onclick="hapusKategori('${nama}')" 
                    class="bg-red-600 hover:bg-red-700 text-white text-xs font-semibold py-1 px-3 rounded-full shadow">
                        Hapus
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
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
            // Validasi jika kategori sudah ada
        if (supplierData.includes(nama)) {
            return window.dispatchEvent(new CustomEvent('show-error', {
                detail: 'Supplier sudah ada.'
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
            window.dispatchEvent(new CustomEvent('show-success', {
                detail: 'Berhasil menyimpan data supplier.'
            }));
            document.getElementById('namaSupplier').value = '';
            document.getElementById('formSupplier').classList.add('hidden');
        });
    });

    function updateSupplierTable() {
        const tbody = document.getElementById('supplierTable');
        tbody.innerHTML = '';
        supplierData.forEach(nama => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="px-4 py-2 border">${nama}</td>
                <td class="px-4 py-2 border text-center">
                    <button onclick="hapusSupplier('${nama}')" 
                    class="bg-red-600 hover:bg-red-700 text-white text-xs font-semibold py-1 px-3 rounded-full shadow">
                        Hapus
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
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

        // Cek duplikasi nama
        const isDuplicate = barangData.some(b => b.nama.toLowerCase() === nama.toLowerCase());
        if (isDuplicate) {
            return window.dispatchEvent(new CustomEvent('show-error', {
                detail: 'Nama barang sudah terdaftar.'
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
        .then((res) => {
            if (res.success && res.data) {
                barangData.push(res.data); // Ambil data dari response
                updateBarangTable();
                window.dispatchEvent(new CustomEvent('show-success', {
                    detail: 'Berhasil menyimpan data barang.'
                }));
                document.getElementById('namaBarang').value = '';
                document.getElementById('kategoriBarang').value = '';
                document.getElementById('supplierBarang').value = '';
                document.getElementById('formBarang').classList.add('hidden');
            } else {
                window.dispatchEvent(new CustomEvent('show-error', {
                    detail: 'Gagal menyimpan barang. Coba lagi.'
                }));
            }
        });
    });

    function updateBarangTable() {
        const tbody = document.getElementById('barangTable');
        tbody.innerHTML = '';
        barangData.forEach((item, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="px-4 py-2 border">${item.id_brg}</td>
                <td class="px-4 py-2 border">${item.nama}</td>
                <td class="px-4 py-2 border">${item.kategori}</td>
                <td class="px-4 py-2 border">${item.supplier}</td>
                <td class="px-4 py-2 border text-center">
                    <button onclick="hapusBarang('${item.id_brg}')" 
                    class="bg-red-600 hover:bg-red-700 text-white text-xs font-semibold py-1 px-3 rounded-full shadow">
                        Hapus
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
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

// Hapus fungsi
window.hapusBarang = function(id){
    if (!confirm('Yakin ingin menghapus barang ini?')) return;
    fetch('/masterdata/barang/delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ id })
    })
    .then(res => res.json())
    .then(() => {
        barangData = barangData.filter(b => b.id_brg !== id);
        updateBarangTable();
        showSuccess('Barang berhasil dihapus.');
    })
    .catch(() => showError('Gagal menghapus barang.'));
}

window.hapusKategori = function(nama) {
    if (!confirm('Yakin ingin menghapus kategori ini?')) return;
    fetch('/masterdata/kategori/delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ nama })
    })
    .then(res => res.json())
    .then(() => {
        kategoriData = kategoriData.filter(k => k !== nama);
        updateKategoriTable();
        renderSelectOptions();
        showSuccess('Kategori berhasil dihapus.');
    })
    .catch(() => showError('Gagal menghapus kategori.'));
}

window.hapusSupplier = function(nama) {
    if (!confirm('Yakin ingin menghapus supplier ini?')) return;
    fetch('/masterdata/suppliers/delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ nama })
    })
    .then(res => res.json())
    .then(() => {
        supplierData = supplierData.filter(s => s !== nama);
        updateSupplierTable();
        renderSelectOptions();
        showSuccess('Supplier berhasil dihapus.');
    })
    .catch(() => showError('Gagal menghapus supplier.'));
}

// Notifikasi
function showError(msg) {
    window.dispatchEvent(new CustomEvent('show-error', { detail: msg }));
}
function showSuccess(msg) {
    window.dispatchEvent(new CustomEvent('show-success', { detail: msg }));
}


});
