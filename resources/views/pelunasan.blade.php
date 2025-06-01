@extends('components.layout')

{{-- Notifikasi sukses --}}
@if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
        class="fixed right-4 top-4 z-50 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-md max-w-sm w-full">
        <div class="flex justify-between items-center">
            <span class="text-sm">{{ session('success') }}</span>
            <button @click="show = false" class="ml-4 text-green-700 hover:text-green-900 font-bold">&times;</button>
        </div>
    </div>
@endif

{{-- Notifikasi error --}}
<div x-data="{ show: false, message: '' }"
    x-show="show"
    x-on:show-error.window="message = $event.detail; show = true; setTimeout(() => show = false, 3000)"
    class="fixed right-4 top-4 z-50 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-md max-w-sm w-full"
    x-cloak>
    <div class="flex justify-between items-center">
        <span class="text-sm" x-text="message"></span>
        <button @click="show = false" class="ml-4 text-red-700 hover:text-red-900 font-bold">&times;</button>
    </div>
</div>

@section('content')
<div class="flex-1 p-6 bg-[#f4f6f8] min-h-screen">
    {{-- Header --}}
    <div class="bg-[#234e9a] px-6 py-4 flex justify-between items-center text-white rounded-t-xl shadow mb-4">
        <div>
            <h1 class="text-lg font-semibold">📄 Pelunasan Piutang</h1>
            <p class="text-sm opacity-80">Transaksi / Pelunasan Piutang</p>
        </div>
        <button id="btn-pilih-transaksi" class="bg-white text-[#234e9a] font-medium px-4 py-2 text-sm rounded hover:bg-gray-100">
            Pilih Transaksi
        </button>
    </div>

    {{-- Tabel Pelunasan --}}
    <div class="bg-white p-6 rounded-xl shadow overflow-x-auto">
        <table class="w-full text-sm text-left border border-gray-200">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-3 border">No</th>
                    <th class="px-4 py-3 border">ID Transaksi</th>
                    <th class="px-4 py-3 border">Tanggal Transaksi</th>
                    <th class="px-4 py-3 border">Jatuh Tempo</th>
                    <th class="px-4 py-3 border">Tanggal Pelunasan</th>
                    <th class="px-4 py-3 border">Status</th>
                </tr>
            </thead>
            <tbody id="pelunasan-body" class="text-gray-800">
               
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Pilih Transaksi --}}
<div id="modal-pilih-transaksi" class="fixed inset-0 bg-black bg-opacity-30 flex justify-center items-center z-50 hidden">
    <div class="bg-white w-[90%] max-w-4xl rounded-xl p-6 shadow-lg relative">
        <button id="btn-close-modal" class="absolute top-4 right-4 text-gray-500 hover:text-red-500 text-xl font-bold">
            &times;
        </button>

        <h2 class="text-xl font-bold mb-4">Daftar Transaksi Piutang</h2>
        
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
                    id="search-transaksi"
                    placeholder="Search transaksi..."
                    class="border px-3 py-1 rounded text-sm w-full lg:w-64"
                    />
            </div>
        </div>

        <div class="overflow-auto max-h-[60vh]">
            <table class="w-full text-sm text-left border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2 border">ID</th>
                        <th class="px-3 py-2 border">Email</th>
                        <th class="px-3 py-2 border">Tanggal</th>
                        <th class="px-3 py-2 border">Jatuh Tempo</th>
                        <th class="px-3 py-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($piutang as $index => $transaksi)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-3 py-2 border">{{ $transaksi['id'] }}</td>
                        <td class="px-3 py-2 border">{{ $transaksi['email_pelanggan'] }}</td>
                        <td class="px-3 py-2 border">{{ $transaksi['tanggal'] }}</td>
                        <td class="px-3 py-2 border">{{ $transaksi['jatuh_tempo'] }}</td>
                        <td class="px-3 py-2 border">
                            <button
                                class="btn-bayar bg-blue-600 text-white px-3 py-1 rounded"
                                data-transaksi="{{ json_encode($transaksi) }}">
                                Bayar
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Form Pelunasan --}}
<div id="modal-form-bayar" class="fixed inset-0 bg-black bg-opacity-30 flex justify-center items-center z-50 hidden">
    <div class="bg-white w-full max-w-md rounded-xl p-6 shadow relative">
        <button id="btn-close-form" class="absolute top-4 right-4 text-gray-500 hover:text-red-500 text-xl font-bold">
            &times;
        </button>

        <h2 class="text-xl font-semibold mb-4">Form Pelunasan</h2>
        <form action="{{ route('pelunasan.store') }}" method="POST" id="formPelunasan">
            @csrf
            <input type="hidden" name="id_transaksi" id="idTransaksi">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="col-span-1">
                    <label>ID Transaksi</label>
                    <input type="text" id="idTransaksiDisplay" readonly class="w-full border px-3 py-2 rounded bg-gray-100" />
                </div>
                <div class="col-span-1">
                    <label>Email</label>
                    <input type="email" id="email" name="email" readonly class="w-full border px-3 py-2 rounded" />
                </div>
                <div class="col-span-1">
                    <label>Tanggal Transaksi</label>
                    <input type="text" id="tanggalTransaksi" name="tanggal_transaksi" readonly class="w-full border px-3 py-2 rounded" />
                </div>
                <div class="col-span-1">
                    <label>Jatuh Tempo</label>
                    <input type="text" id="jatuhTempo" name="jatuh_tempo" readonly class="w-full border px-3 py-2 rounded" />
                </div>
                <div>
                    <label>Total Belanja</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 bg-gray-100 border border-r-0 rounded-l">Rp</span>
                        <input type="text" id="totalBelanja" name="total_belanja" readonly class="w-full border px-3 py-2 rounded-r bg-gray-100" />
                    </div>
                </div>
                <div class="col-span-1">
                    <label>Tanggal Pelunasan</label>
                    <input type="date" id="tanggalPelunasan" name="tanggal_pelunasan" class="w-full border px-3 py-2 rounded" value="{{ date('Y-m-d') }}" />
                </div>
                <div>
                    <label>Dibayar</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 bg-gray-100 border border-r-0 rounded-l">Rp</span>
                        <input type="number" name="dibayar" id="dibayar" class="w-full border px-3 py-2 rounded-r" />
                    </div>
                </div>
                <div>
                    <label>Kembalian</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 bg-gray-100 border border-r-0 rounded-l">Rp</span>
                        <input type="text" id="kembalian" name="kembalian" readonly class="w-full border px-3 py-2 rounded-r bg-gray-100" />
                    </div>
                </div>
                <div class="col-span-2">
                    <button type="submit" class="mt-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">
                        Bayar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@vite(['resources/js/pelunasan.js'])
@endsection
