@extends('components.layout')

@section('content')
<div class="flex-1 p-6 bg-[#f4f6f8] min-h-screen">
    <div class="flex flex-col lg:flex-row gap-6">
        {{-- Bagian Kiri: Tabel Barang --}}
        <div class="flex-1 bg-white rounded-xl shadow">
            <div class="bg-[#234e9a] px-6 py-4 flex justify-between items-center text-white rounded-t-xl">
                <div>
                    <h1 class="text-lg font-semibold">🛒 Orders</h1>
                    <p class="text-sm opacity-80">Transaksi / Penjualan</p>
                </div>
                <button id="btn-pilih-barang" class="bg-white text-[#234e9a] font-medium px-4 py-2 text-sm rounded hover:bg-gray-100">
                    Pilih Barang
                </button>
            </div>

            <div class="p-6 overflow-x-auto">
                <table class="w-full text-sm text-left border border-gray-200">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 border">No</th>
                            <th class="px-4 py-3 border">Nama Barang</th>
                            <th class="px-4 py-3 border">Satuan</th>
                            <th class="px-4 py-3 border">Harga</th>
                            <th class="px-4 py-3 border">Jumlah</th>
                            <th class="px-4 py-3 border">Total Belanja</th>
                            <th class="px-4 py-3 border">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="order-body" class="text-gray-800">
                      
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Bagian Kanan: Form Pembayaran --}}
        <div class="w-full lg:w-1/3 bg-white rounded-xl shadow p-6 h-fit">
            <h2 class="text-lg font-semibold text-[#234e9a] mb-4">🧾 Orders</h2>

            <div class="space-y-4 text-sm">
                <div>
                    <label class="block mb-1 font-medium">Tanggal Transaksi*</label>
                    <input type="date" value="{{ date('Y-m-d') }}" class="w-full border px-3 py-2 rounded" />
                </div>
                <div>
                    <label class="block mb-1 font-medium">Kasir*</label>
                    <input type="text" value="hakim" readonly class="w-full border px-3 py-2 rounded" />
                </div>
                <div>
                    <label class="block mb-1 font-medium">Total Belanja*</label>
                    <input type="number" name="total_belanja" readonly value="Rp. 0" class="w-full border px-3 py-2 rounded bg-gray-100" />
                </div>
                <div>
                    <label class="block mb-1 font-medium">Di Bayar*</label>
                    <input type="number" class="w-full border px-3 py-2 rounded" />
                </div>
                <div>
                    <label class="block mb-1 font-medium">Kembalian*</label>
                    <input type="text" readonly class="w-full border px-3 py-2 rounded bg-gray-100" />
                </div>
                <div class="flex justify-between mt-4">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Bayar</button>
                    <button class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Batal</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pilih Barang -->
<div id="modal-pilih-barang" class="fixed inset-0 bg-black bg-opacity-30 flex justify-center items-center z-50 hidden">
    <div class="bg-white w-[90%] max-w-5xl rounded-xl p-6 shadow-lg relative">

        <!-- Tombol Close -->
        <button
            id="btn-close-modal"
            class="absolute top-4 right-4 text-gray-500 hover:text-red-500 text-xl font-bold">
            &times;
        </button>

        <h2 class="text-xl font-bold mb-4">Pilih Barang</h2>

        <!-- Filter -->
        <div class="flex flex-col lg:flex-row justify-between mb-4 gap-4">
            <div>
                <label class="text-sm font-medium mr-2">Show</label>
                <select id="entries-select" class="border px-2 py-1 rounded text-sm">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span class="text-sm ml-1">entries</span>
            </div>
            <div>
                <input
                    type="text"
                    id="search-barang"
                    placeholder="Search barang..."
                    class="border px-3 py-1 rounded text-sm w-full lg:w-64"
                />
            </div>
        </div>

        <!-- Tabel -->
        <div class="overflow-auto max-h-[60vh]">
            <table class="w-full text-sm text-left border" id="barang-table">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2 border">No</th>
                        <th class="px-3 py-2 border">Nama</th>
                        <th class="px-3 py-2 border">Kategori</th>
                        <th class="px-3 py-2 border">Harga</th>
                        <th class="px-3 py-2 border">Stok</th>
                        <th class="px-3 py-2 border">Jumlah</th>
                        <th class="px-3 py-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($barangs as $index => $barang)
                    <tr class="hover:bg-gray-50 border-b barang-row">
                        <td class="px-3 py-2 border">{{ $index + 1 }}</td>
                        <td class="px-3 py-2 border">{{ $barang['nama'] }}</td>
                        <td class="px-3 py-2 border">{{ $barang['kategori'] }}</td>
                        <td class="px-3 py-2 border">{{ $barang['harga_jual'] }}</td>
                        <td class="px-3 py-2 border">{{ $barang['stok'] }}</td>
                        <td class="px-3 py-2 border">
                            <input type="number" min="1" value="1" class="jumlah w-16 border px-2 py-1 rounded" />
                        </td>
                        <td class="px-3 py-2 border">
                            <button
                                class="pilih-barang bg-blue-500 text-white px-3 py-1 rounded"
                                data-nama="{{ $barang['nama'] }}"
                                data-harga="{{ $barang['harga_jual'] }}"
                            >
                                Pilih
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
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
                    <td class="px-4 py-2 border">pcs</td>
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
        }

        function reorderRows() {
            document.querySelectorAll('#order-body tr').forEach((row, index) => {
                row.querySelector('td:first-child').textContent = index + 1;
            });
        }
    });
</script>

@endsection
