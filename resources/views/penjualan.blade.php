@extends('components.layout')
@if (session('success'))
    <div 
        x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 3000)"
        class="fixed right-4 top-4 z-50 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-md max-w-sm w-full"
    >
        <div class="flex justify-between items-center">
            <span class="text-sm">{{ session('success') }}</span>
            <button @click="show = false" class="ml-4 text-green-700 hover:text-green-900 font-bold">&times;</button>
        </div>
    </div>
@endif
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
                            <th class="px-4 py-3 border">Harga</th>
                            <th class="px-4 py-3 border">Jumlah</th>
                            <th class="px-4 py-3 border">Total</th>
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
                    <label class="block mb-1 font-medium">Tanggal Transaksi</label>
                    <input type="date" value="{{ date('Y-m-d') }}" class="w-full border px-3 py-2 rounded" />
                </div>
                <div>
                    <label class="block mb-1 font-medium">Kasir</label>
                    <input type="text" value="{{ session('username') }}" readonly class="w-full border px-3 py-2 rounded" />
                </div>
                <div>
                    <label class="block mb-1 font-medium">Total Belanja</label>
                    <input type="text" id="totalBelanjaDisplay" name="total_belanja" readonly value="Rp0" class="w-full border px-3 py-2 rounded bg-gray-100 font-semibold text-left" />
                </div>
                <div>
                    <label class="block mb-1 font-medium">Metode Pembayaran</label>
                    <select id="metodePembayaran" name="metode_pembayaran" class="w-full border px-3 py-2 rounded">
                        <option value="" disabled selected>-- Pilih --</option>
                        <option value="Tunai">Tunai</option>
                        <option value="Piutang">Piutang</option>
                    </select>
                </div>
                <div id="bayarSection">
                    <div>
                        <label class="block mb-1 font-medium">Di Bayar</label>
                        <div class="flex rounded border overflow-hidden">
                            <span class="bg-gray-100 px-3 flex items-center text-sm text-gray-600">Rp</span>
                            <input type="number" id="dibayarInput" class="w-full px-3 py-2 text-sm focus:outline-none" />
                        </div>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Kembalian</label>
                        <input type="text" id="kembalianOutput" readonly class="w-full border px-3 py-2 rounded bg-gray-100 font-semibold" />
                        <p id="kembalianError" class="text-red-500 text-sm mt-1 hidden">Uang tidak cukup</p>
                    </div>
                </div>
                <div id="piutangFields" class="space-y-4 hidden">
                    <div>
                        <label class="block mb-1 font-medium">Email Pelanggan</label>
                        <input type="email" id="emailPelanggan" name="email_pelanggan" class="w-full border px-3 py-2 rounded" placeholder="contoh@email.com" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Jatuh Tempo</label>
                        <input type="date" id="jatuhTempo" name="jatuh_tempo" readonly class="w-full border px-3 py-2 rounded bg-gray-100" />
                    </div>
                </div>
                <div class="flex justify-between mt-4">
                    <form action="{{ route('order.store') }}" method="POST" id="form-penjualan">
                        @csrf
                        <input type="hidden" name="items" id="inputItems">
                        <input type="hidden" name="kasir" value="{{ session('username') }}">
                        <input type="hidden" name="tanggal" value="{{ date('Y-m-d') }}">
                        <input type="hidden" name="total_belanja" id="inputTotalBelanja">
                        <input type="hidden" name="metode_pembayaran" id="inputMetode">
                        <input type="hidden" name="email_pelanggan" id="inputEmail">
                        <input type="hidden" name="jatuh_tempo" id="inputJatuhTempo">

                        <div class="flex gap-4 mt-4">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Bayar</button>
                            <button type="button" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Batal</button>
                        </div>
                    </form>
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

@vite(['resources/js/penjualan.js'])

@endsection
