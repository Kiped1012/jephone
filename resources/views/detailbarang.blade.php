@extends('components.layout')

@section('content')
<div class="min-h-screen bg-[#f5f5f5] py-10 px-6">
    <!-- Header biru dengan breadcrumb -->
    <div class="bg-[#274C8E] px-8 py-6 rounded-t-xl text-white space-y-1">
       <h1 class="text-xl font-semibold flex items-center space-x-2 mb-4">
            <a href="{{ route('barang.index') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-200 hover:bg-gray-300 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <span>Detail Barang</span>
        </h1>
        <div class="text-sm mt-2 text-white/80 flex items-center space-x-1">
            <span>Master</span>
            <span>/</span>
            <span>Data Barang</span>
            <span>/</span>
            <span>Detail Barang</span>
        </div>
    </div>

    <!-- Card detail -->
    <div class="min-h-screen bg-[#f5f5f5] py-10 px-6 overflow-x-auto">
        <div class="bg-white rounded-xl shadow px-10 py-8">
            <div class="space-y-5 text-[15px] leading-relaxed text-gray-800">
                <div class="flex">
                    <div class="w-48 font-semibold text-gray-600">Nama</div>
                    <div>: {{ $barang['nama'] }}</div>
                </div>
                <div class="flex">
                    <div class="w-48 font-semibold text-gray-600">Kategori</div>
                    <div>: {{ $barang['kategori'] }}</div>
                </div>
                <div class="flex">
                    <div class="w-48 font-semibold text-gray-600">Stok</div>
                    <div>: {{ $barang['stok'] }}</div>
                </div>
                <div class="flex">
                    <div class="w-48 font-semibold text-gray-600">Harga Beli</div>
                    <div>: Rp{{ number_format($barang['harga_beli']) }}</div>
                </div>
                <div class="flex">
                    <div class="w-48 font-semibold text-gray-600">Harga Jual</div>
                    <div>: Rp{{ number_format($barang['harga_jual']) }}</div>
                </div>
                <div class="flex">
                    <div class="w-48 font-semibold text-gray-600">Supplier</div>
                    <div>: {{ $barang['supplier'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
